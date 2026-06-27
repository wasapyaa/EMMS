<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Idea;
use App\Models\IdeaVote;
use Illuminate\Support\Facades\DB;

class IdeaController extends Controller
{
    public function index(Request $request)
    {
        $s_id = $request->query('s_id');
        $sort = $request->query('sort', 'hot'); // hot | top | new | all_time

        // Determine the date cutoff for sliding window
        $cutoffDays = match($sort) {
            'hot'      => 7,
            'top'      => 30,
            'all_time' => null,
            'new'      => null,
            default    => 7,
        };

        // Build subquery for recent votes count (sliding window)
        $ideasQuery = Idea::with('student:s_id,name');

        // Exclude ideas that the current student has reported
        if ($s_id) {
            $reportedIds = DB::table('idea_reports')->where('s_id', $s_id)->pluck('idea_id');
            $ideasQuery->whereNotIn('id', $reportedIds);
        }

        // Exclude ideas that have 5 or more total reports
        $ideasQuery->where(function ($q) {
            $q->whereRaw('(select count(*) from idea_reports where idea_reports.idea_id = ideas.id) < 5');
        });

        // For sorting, compute a net score (likes - dislikes) within the window
        if ($sort === 'new') {
            $ideasQuery->orderByDesc('created_at');
        } elseif ($cutoffDays !== null) {
            $cutoff = now()->subDays($cutoffDays);
            // Add a computed net_score for window-based sorting
            $ideasQuery->addSelect([
                'net_score' => DB::table('idea_votes')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = 'like' THEN 1 WHEN type = 'dislike' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn('idea_votes.idea_id', 'ideas.id')
                    ->where('idea_votes.created_at', '>=', $cutoff)
            ])->orderByDesc('net_score')->orderByDesc('created_at');
        } else {
            // all_time net score
            $ideasQuery->addSelect([
                'net_score' => DB::table('idea_votes')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = 'like' THEN 1 WHEN type = 'dislike' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn('idea_votes.idea_id', 'ideas.id')
            ])->orderByDesc('net_score')->orderByDesc('created_at');
        }

        $ideas = $ideasQuery->get()->map(function ($idea) use ($s_id) {
            $likes    = IdeaVote::where('idea_id', $idea->id)->where('type', 'like')->count();
            $dislikes = IdeaVote::where('idea_id', $idea->id)->where('type', 'dislike')->count();
            $netScore = $likes - $dislikes;

            // Check current user's vote status
            $userVote = null;
            if ($s_id) {
                $existingVote = IdeaVote::where('idea_id', $idea->id)->where('s_id', $s_id)->first();
                if ($existingVote) {
                    $userVote = $existingVote->type; // 'like' or 'dislike'
                }
            }

            return [
                'id'           => $idea->id,
                'title'        => $idea->title,
                'description'  => $idea->description,
                'status'       => $idea->status,
                'author'       => $idea->student ? $idea->student->name : 'Unknown',
                'likes'        => $likes,
                'dislikes'     => $dislikes,
                'net_score'    => $netScore,
                'user_vote'    => $userVote,  // null, 'like', or 'dislike'
                'has_reported' => $s_id ? DB::table('idea_reports')->where('idea_id', $idea->id)->where('s_id', $s_id)->exists() : false,
                'created_at'   => $idea->created_at->diffForHumans(),
                'is_new'       => $idea->created_at->diffInDays(now()) <= 7,
            ];
        });

        return response()->json([
            'status' => 'success',
            'sort'   => $sort,
            'data'   => $ideas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            's_id'        => 'required|integer',
            'title'       => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $idea = Idea::create([
            's_id'        => $request->s_id,
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => 'pending'
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Idea submitted successfully!',
            'data'    => $idea
        ]);
    }

    public function vote(Request $request, $id)
    {
        $request->validate([
            's_id' => 'required|integer',
            'type' => 'required|in:like,dislike'
        ]);

        $type = $request->type;
        $vote = IdeaVote::where('idea_id', $id)->where('s_id', $request->s_id)->first();

        if ($vote) {
            if ($vote->type === $type) {
                // Same button pressed again → remove vote (toggle off)
                $vote->delete();
                $action = 'removed';
            } else {
                // Switch from like to dislike or vice versa
                $vote->type = $type;
                $vote->save();
                $action = $type;
            }
        } else {
            // New vote
            IdeaVote::create([
                'idea_id' => $id,
                's_id'    => $request->s_id,
                'type'    => $type
            ]);
            $action = $type;
        }

        $likes    = IdeaVote::where('idea_id', $id)->where('type', 'like')->count();
        $dislikes = IdeaVote::where('idea_id', $id)->where('type', 'dislike')->count();
        $userVote = IdeaVote::where('idea_id', $id)->where('s_id', $request->s_id)->value('type');

        return response()->json([
            'status'    => 'success',
            'action'    => $action,
            'likes'     => $likes,
            'dislikes'  => $dislikes,
            'net_score' => $likes - $dislikes,
            'user_vote' => $userVote  // null if removed
        ]);
    }

    public function report(Request $request, $id)
    {
        $request->validate([
            's_id' => 'required|integer'
        ]);

        $exists = DB::table('idea_reports')
            ->where('idea_id', $id)
            ->where('s_id', $request->s_id)
            ->exists();

        if (!$exists) {
            DB::table('idea_reports')->insert([
                'idea_id'    => $id,
                's_id'       => $request->s_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Idea reported successfully'
        ]);
    }
}

