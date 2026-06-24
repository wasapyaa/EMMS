<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Student;
use App\Models\Organizer;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class RankingSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_ranking_snapshot_is_saved_successfully()
    {
        // 1. Create organizer and event to satisfy foreign key constraints
        $organizer = Organizer::create([
            'club_name' => 'IT Club',
            'pic_name' => 'Alice PIC',
            'email' => 'org@example.com',
            'phone' => '01234567890',
            'pass_hash' => 'dummy_hash',
            'status' => 'approved'
        ]);

        $event = Event::create([
            'o_id' => $organizer->o_id,
            'title' => 'Sample Event',
            'description' => 'Desc',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'location_name' => 'Main Hall',
            'location_lat' => 3.123,
            'location_long' => 101.456,
            'radius_meter' => 100,
            'qr_code_token' => 'dummy_token',
            'merit_value' => 10,
            'status' => 'approved'
        ]);

        // 2. Create students manually
        $student1 = Student::create([
            'name' => 'Alice',
            'email' => 'alice@student.uitm.edu.my',
            'phone' => '01234567890',
            'pass_hash' => 'dummy_hash',
            'num_matrics' => '2021111111'
        ]);

        $student2 = Student::create([
            'name' => 'Bob',
            'email' => 'bob@student.uitm.edu.my',
            'phone' => '01234567891',
            'pass_hash' => 'dummy_hash',
            'num_matrics' => '2021222222'
        ]);

        // Add some merit points to merit_logs table linked to the created event
        DB::table('merit_logs')->insert([
            ['s_id' => $student1->s_id, 'points_added' => 10, 'e_id' => $event->e_id],
            ['s_id' => $student2->s_id, 'points_added' => 20, 'e_id' => $event->e_id]
        ]);

        $today = now()->toDateString();
        
        // Assert no snapshots exist yet
        $this->assertEquals(0, DB::table('ranking_snapshots')->count());

        // 3. Trigger snapshot via Model static method
        Student::saveDailyRankingSnapshot();

        // Assert snapshots generated
        $this->assertEquals(2, DB::table('ranking_snapshots')->where('snapshot_date', $today)->count());

        // Bob has 20 points, so Bob should be rank 1
        $bobRank = DB::table('ranking_snapshots')
            ->where('snapshot_date', $today)
            ->where('s_id', $student2->s_id)
            ->value('rank');
        $this->assertEquals(1, $bobRank);

        // Alice has 10 points, so Alice should be rank 2
        $aliceRank = DB::table('ranking_snapshots')
            ->where('snapshot_date', $today)
            ->where('s_id', $student1->s_id)
            ->value('rank');
        $this->assertEquals(2, $aliceRank);

        // 4. Running again shouldn't duplicate
        Student::saveDailyRankingSnapshot();
        $this->assertEquals(2, DB::table('ranking_snapshots')->where('snapshot_date', $today)->count());
    }

    public function test_artisan_command_saves_ranking_snapshot()
    {
        // Create student
        Student::create([
            'name' => 'Charlie',
            'email' => 'charlie@student.uitm.edu.my',
            'phone' => '01234567892',
            'pass_hash' => 'dummy_hash',
            'num_matrics' => '2021333333'
        ]);
        
        $today = now()->toDateString();
        $this->assertEquals(0, DB::table('ranking_snapshots')->count());

        // Call artisan command
        Artisan::call('ranking:snapshot');

        $this->assertEquals(1, DB::table('ranking_snapshots')->where('snapshot_date', $today)->count());
    }
}
