<?php

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// Access control

test('admin can access member list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin/members')
        ->assertOk();
});

test('member user gets 403 on member list', function () {
    $user = User::factory()->create(['role' => 'member']);

    $this->actingAs($user)
        ->get('/admin/members')
        ->assertForbidden();
});

test('guest is redirected to login from member list', function () {
    $this->get('/admin/members')
        ->assertRedirect('/login');
});

// List display

test('member list shows member data', function () {
    $admin = User::factory()->admin()->create();
    $member = Member::factory()->active()->create();

    $this->actingAs($admin)
        ->get('/admin/members')
        ->assertOk()
        ->assertSee($member->member_number)
        ->assertSee($member->user->name)
        ->assertSee($member->market);
});

test('member list filters by status', function () {
    $admin = User::factory()->admin()->create();
    $active = Member::factory()->active()->create();
    $pending = Member::factory()->pending()->create();

    $this->actingAs($admin)
        ->get('/admin/members?status=pending')
        ->assertOk()
        ->assertSee($pending->member_number)
        ->assertDontSee($active->member_number);
});

test('member list searches by name', function () {
    $admin = User::factory()->admin()->create();
    $target = Member::factory()->create([
        'user_id' => User::factory()->create(['name' => 'John Doe']),
    ]);
    $other = Member::factory()->create([
        'user_id' => User::factory()->create(['name' => 'Jane Smith']),
    ]);

    $this->actingAs($admin)
        ->get('/admin/members?search=John')
        ->assertOk()
        ->assertSee($target->member_number)
        ->assertDontSee($other->member_number);
});

// Detail page

test('admin can view member detail page', function () {
    $admin = User::factory()->admin()->create();
    $member = Member::factory()->active()->create();

    $this->actingAs($admin)
        ->get('/admin/members/'.$member->member_number)
        ->assertOk()
        ->assertSee($member->member_number)
        ->assertSee($member->user->name)
        ->assertSee($member->market);
});

// Actions

test('admin can approve a pending member', function () {
    $admin = User::factory()->admin()->create();
    $member = Member::factory()->pending()->create();

    $this->actingAs($admin);

    $component = Livewire::test('pages::admin.members.show', ['member' => $member]);

    $component->call('approve');

    $member->refresh();
    expect($member->status)->toBe('active')
        ->and($member->joined_at)->not->toBeNull();
});

test('admin can reject a pending member', function () {
    $admin = User::factory()->admin()->create();
    $member = Member::factory()->pending()->create();

    $this->actingAs($admin);

    $component = Livewire::test('pages::admin.members.show', ['member' => $member]);

    $component->call('reject');

    expect($member->fresh()->trashed())->toBeTrue();
});

test('approve on active member returns 403', function () {
    $admin = User::factory()->admin()->create();
    $member = Member::factory()->active()->create();

    $this->actingAs($admin);

    $component = Livewire::test('pages::admin.members.show', ['member' => $member]);

    $component->call('approve')
        ->assertStatus(403);
});
