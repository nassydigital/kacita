<?php

use App\Models\BursaryApplication;
use App\Models\Member;
use App\Models\School;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});

test('member user gets 403 on admin dashboard', function () {
    $user = User::factory()->create(['role' => 'member']);

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

test('guest is redirected to login', function () {
    $this->get('/admin')
        ->assertRedirect('/login');
});

test('admin dashboard displays stats', function () {
    $admin = User::factory()->admin()->create();

    Member::factory()->count(3)->create();
    Member::factory()->pending()->count(2)->create();
    Subscription::factory()->active()->count(4)->create();
    School::factory()->count(2)->create();
    Student::factory()->count(5)->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSeeInOrder(['Total Members', '5'])
        ->assertSeeInOrder(['Active Subscriptions', '4'])
        ->assertSeeInOrder(['Pending Members', '2'])
        ->assertSeeInOrder(['Total Schools', '2'])
        ->assertSeeInOrder(['Total Students', '5']);
});

test('admin dashboard displays pending members', function () {
    $admin = User::factory()->admin()->create();
    $member = Member::factory()->pending()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee($member->user->name)
        ->assertSee($member->member_number);
});

test('admin dashboard displays pending bursary applications', function () {
    $admin = User::factory()->admin()->create();
    $application = BursaryApplication::factory()->pending()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee($application->student->name)
        ->assertSee($application->school->name);
});
