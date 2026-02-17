<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BursaryApplication;
use App\Models\Document;
use App\Models\Member;
use App\Models\Message;
use App\Models\QrCampaign;
use App\Models\School;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@kacita.org',
        ]);

        // QR campaign
        $campaign = QrCampaign::factory()->create([
            'name' => 'Owino Market Drive',
            'market_location' => 'Owino',
            'created_by' => $admin->id,
        ]);

        // 3 active members with subscriptions and documents
        $activeMembers = Member::factory()
            ->count(3)
            ->active()
            ->sequence(
                ['market' => 'Owino', 'region' => 'Central'],
                ['market' => 'Nakasero', 'region' => 'Central'],
                ['market' => 'Kikuubo', 'region' => 'Central'],
            )
            ->create();

        foreach ($activeMembers as $member) {
            Subscription::factory()->active()->create(['member_id' => $member->id]);
            Document::factory()->create([
                'documentable_id' => $member->id,
                'documentable_type' => Member::class,
            ]);
        }

        // 2 pending members from QR campaign
        Member::factory()
            ->count(2)
            ->pending()
            ->fromQrCampaign($campaign)
            ->create();

        // 3 schools
        $schools = School::factory()->count(3)->create();

        // 5 students across schools
        $students = Student::factory()
            ->count(5)
            ->active()
            ->sequence(
                ['school_id' => $schools[0]->id],
                ['school_id' => $schools[0]->id],
                ['school_id' => $schools[1]->id],
                ['school_id' => $schools[1]->id],
                ['school_id' => $schools[2]->id],
            )
            ->create();

        // Bursary applications — mix of statuses
        BursaryApplication::factory()->pending()->create([
            'student_id' => $students[0]->id,
            'school_id' => $students[0]->school_id,
        ]);
        BursaryApplication::factory()->pending()->create([
            'student_id' => $students[1]->id,
            'school_id' => $students[1]->school_id,
        ]);
        BursaryApplication::factory()->approved()->create([
            'student_id' => $students[2]->id,
            'school_id' => $students[2]->school_id,
            'reviewed_by' => $admin->id,
        ]);
        BursaryApplication::factory()->approved()->create([
            'student_id' => $students[3]->id,
            'school_id' => $students[3]->school_id,
            'reviewed_by' => $admin->id,
        ]);
        BursaryApplication::factory()->rejected()->create([
            'student_id' => $students[4]->id,
            'school_id' => $students[4]->school_id,
            'reviewed_by' => $admin->id,
        ]);

        // Messages
        Message::factory()->draft()->create(['sent_by' => $admin->id]);
        Message::factory()->sent()->create(['sent_by' => $admin->id]);

        // Activity logs
        ActivityLog::factory()->count(5)->create(['user_id' => $admin->id]);
    }
}
