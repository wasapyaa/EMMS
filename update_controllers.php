<?php
$file = 'app/Http/Controllers/OrganizerController.php';
$content = file_get_contents($file);
$pos = strpos($content, 'public function ideas(Request $request)');
if ($pos !== false) {
    $newContent = substr($content, 0, $pos) . 'public function ideas(Request $request)
    {
        $organizerId = session(\'organizer_id\');
        if (!$organizerId) return redirect(\'/login\');

        $sort = $request->query(\'sort\', \'trending\');
        $cutoffDays = null;
        
        if ($sort === \'trending\') {
            $cutoffDays = 7;
        } elseif ($sort === \'top_month\') {
            $cutoffDays = 30;
        }

        $ideasQuery = \App\Models\Idea::with(\'student:s_id,name\')
            ->addSelect([
                \'*\',
                \'net_score\' => \Illuminate\Support\Facades\DB::table(\'idea_votes\')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = \'like\' THEN 1 WHEN type = \'dislike\' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn(\'idea_votes.idea_id\', \'ideas.id\'),
                \'reports_count\' => \Illuminate\Support\Facades\DB::table(\'idea_reports\')
                    ->selectRaw(\'count(*)\')
                    ->whereColumn(\'idea_reports.idea_id\', \'ideas.id\')
            ]);

        if ($sort === \'new\') {
            $ideasQuery->orderByDesc(\'created_at\');
        } elseif ($cutoffDays !== null) {
            $cutoff = now()->subDays($cutoffDays);
            $ideasQuery->addSelect([
                \'window_net_score\' => \Illuminate\Support\Facades\DB::table(\'idea_votes\')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = \'like\' THEN 1 WHEN type = \'dislike\' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn(\'idea_votes.idea_id\', \'ideas.id\')
                    ->where(\'idea_votes.created_at\', \'>=\', $cutoff)
            ])->orderByDesc(\'window_net_score\')->orderByDesc(\'created_at\');
        } else {
            // top_all
            $ideasQuery->orderByDesc(\'net_score\')->orderByDesc(\'created_at\');
        }

        $ideas = $ideasQuery->get();

        return view(\'organizer.ideas.index\', compact(\'ideas\', \'sort\'));
    }
}
';
    file_put_contents($file, $newContent);
    echo "Organizer updated\n";
}

$file2 = 'app/Http/Controllers/AdminController.php';
$content2 = file_get_contents($file2);
$pos2 = strpos($content2, 'public function ideas(Request $request)');
if ($pos2 !== false) {
    $newContent2 = substr($content2, 0, $pos2) . 'public function ideas(Request $request)
    {
        $adminId = session(\'admin_id\');
        if (!$adminId) return redirect(\'/login\');

        $sort = $request->query(\'sort\', \'trending\');
        $cutoffDays = null;
        
        if ($sort === \'trending\') {
            $cutoffDays = 7;
        } elseif ($sort === \'top_month\') {
            $cutoffDays = 30;
        }

        $ideasQuery = \App\Models\Idea::with(\'student:s_id,name\')
            ->addSelect([
                \'*\',
                \'net_score\' => \Illuminate\Support\Facades\DB::table(\'idea_votes\')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = \'like\' THEN 1 WHEN type = \'dislike\' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn(\'idea_votes.idea_id\', \'ideas.id\'),
                \'reports_count\' => \Illuminate\Support\Facades\DB::table(\'idea_reports\')
                    ->selectRaw(\'count(*)\')
                    ->whereColumn(\'idea_reports.idea_id\', \'ideas.id\')
            ]);

        if ($sort === \'new\') {
            $ideasQuery->orderByDesc(\'created_at\');
        } elseif ($cutoffDays !== null) {
            $cutoff = now()->subDays($cutoffDays);
            $ideasQuery->addSelect([
                \'window_net_score\' => \Illuminate\Support\Facades\DB::table(\'idea_votes\')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = \'like\' THEN 1 WHEN type = \'dislike\' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn(\'idea_votes.idea_id\', \'ideas.id\')
                    ->where(\'idea_votes.created_at\', \'>=\', $cutoff)
            ])->orderByDesc(\'window_net_score\')->orderByDesc(\'created_at\');
        } else {
            // top_all
            $ideasQuery->orderByDesc(\'net_score\')->orderByDesc(\'created_at\');
        }

        $ideas = $ideasQuery->get();

        return view(\'admin.ideas.index\', compact(\'ideas\', \'sort\'));
    }
}
';
    file_put_contents($file2, $newContent2);
    echo "Admin updated\n";
}
