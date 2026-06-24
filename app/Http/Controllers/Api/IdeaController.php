<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function index(Request $request)
    {
        $s_id = $request->query('s_id');
        
        $ideas = \App\Models\Idea::with('student:s_id,name')
            ->withCount('votes')
            ->orderByDesc('votes_count')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($idea) use ($s_id) {
                return [
                    'id' => $idea->id,
                    'title' => $idea->title,
                    'description' => $idea->description,
                    'status' => $idea->status,
                    'author' => $idea->student ? $idea->student->name : 'Unknown',
                    'votes_count' => $idea->votes_count,
                    'has_voted' => $s_id ? \App\Models\IdeaVote::where('idea_id', $idea->id)->where('s_id', $s_id)->exists() : false,
                    'created_at' => $idea->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $ideas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            's_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $idea = \App\Models\Idea::create([
            's_id' => $request->s_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Idea submitted successfully!',
            'data' => $idea
        ]);
    }

    public function vote(Request $request, $id)
    {
        $request->validate([
            's_id' => 'required|integer'
        ]);

        $vote = \App\Models\IdeaVote::where('idea_id', $id)->where('s_id', $request->s_id)->first();

        if ($vote) {
            $vote->delete();
            $action = 'unvoted';
        } else {
            \App\Models\IdeaVote::create([
                'idea_id' => $id,
                's_id' => $request->s_id
            ]);
            $action = 'voted';
        }

        $votes_count = \App\Models\IdeaVote::where('idea_id', $id)->count();

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'votes_count' => $votes_count
        ]);
    }
}
