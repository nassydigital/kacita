<?php

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
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Factory creation ───────────────────────────────────────────────

test('user factory creates a valid user', function () {
    $user = User::factory()->create();
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->exists)->toBeTrue();
});

test('member factory creates a valid member', function () {
    $member = Member::factory()->create();
    expect($member)->toBeInstanceOf(Member::class)
        ->and($member->exists)->toBeTrue();
});

test('subscription factory creates a valid subscription', function () {
    $subscription = Subscription::factory()->create();
    expect($subscription)->toBeInstanceOf(Subscription::class)
        ->and($subscription->exists)->toBeTrue();
});

test('qr campaign factory creates a valid campaign', function () {
    $campaign = QrCampaign::factory()->create();
    expect($campaign)->toBeInstanceOf(QrCampaign::class)
        ->and($campaign->exists)->toBeTrue();
});

test('school factory creates a valid school', function () {
    $school = School::factory()->create();
    expect($school)->toBeInstanceOf(School::class)
        ->and($school->exists)->toBeTrue();
});

test('student factory creates a valid student', function () {
    $student = Student::factory()->create();
    expect($student)->toBeInstanceOf(Student::class)
        ->and($student->exists)->toBeTrue();
});

test('bursary application factory creates a valid application', function () {
    $app = BursaryApplication::factory()->create();
    expect($app)->toBeInstanceOf(BursaryApplication::class)
        ->and($app->exists)->toBeTrue();
});

test('document factory creates a valid document', function () {
    $doc = Document::factory()->create();
    expect($doc)->toBeInstanceOf(Document::class)
        ->and($doc->exists)->toBeTrue();
});

test('message factory creates a valid message', function () {
    $msg = Message::factory()->create();
    expect($msg)->toBeInstanceOf(Message::class)
        ->and($msg->exists)->toBeTrue();
});

test('activity log factory creates a valid log', function () {
    $log = ActivityLog::factory()->create();
    expect($log)->toBeInstanceOf(ActivityLog::class)
        ->and($log->exists)->toBeTrue();
});

// ─── Relationships ──────────────────────────────────────────────────

test('member belongs to user', function () {
    $member = Member::factory()->create();
    expect($member->user)->toBeInstanceOf(User::class);
});

test('member has many subscriptions', function () {
    $member = Member::factory()->create();
    Subscription::factory()->count(2)->create(['member_id' => $member->id]);
    expect($member->subscriptions)->toHaveCount(2);
});

test('member belongs to qr campaign', function () {
    $campaign = QrCampaign::factory()->create();
    $member = Member::factory()->create(['qr_campaign_id' => $campaign->id]);
    expect($member->qrCampaign)->toBeInstanceOf(QrCampaign::class);
});

test('member has morph many documents', function () {
    $member = Member::factory()->create();
    Document::factory()->create([
        'documentable_id' => $member->id,
        'documentable_type' => Member::class,
    ]);
    expect($member->documents)->toHaveCount(1);
});

test('subscription belongs to member', function () {
    $subscription = Subscription::factory()->create();
    expect($subscription->member)->toBeInstanceOf(Member::class);
});

test('qr campaign has many members', function () {
    $campaign = QrCampaign::factory()->create();
    Member::factory()->count(3)->create(['qr_campaign_id' => $campaign->id]);
    expect($campaign->members)->toHaveCount(3);
});

test('qr campaign belongs to creator', function () {
    $campaign = QrCampaign::factory()->create();
    expect($campaign->creator)->toBeInstanceOf(User::class);
});

test('school has many students', function () {
    $school = School::factory()->create();
    Student::factory()->count(2)->create(['school_id' => $school->id]);
    expect($school->students)->toHaveCount(2);
});

test('school has many bursary applications', function () {
    $school = School::factory()->create();
    $student = Student::factory()->create(['school_id' => $school->id]);
    BursaryApplication::factory()->create([
        'student_id' => $student->id,
        'school_id' => $school->id,
    ]);
    expect($school->bursaryApplications)->toHaveCount(1);
});

test('student belongs to school', function () {
    $student = Student::factory()->create();
    expect($student->school)->toBeInstanceOf(School::class);
});

test('student has many bursary applications', function () {
    $student = Student::factory()->create();
    BursaryApplication::factory()->create([
        'student_id' => $student->id,
        'school_id' => $student->school_id,
    ]);
    expect($student->bursaryApplications)->toHaveCount(1);
});

test('student has morph many documents', function () {
    $student = Student::factory()->create();
    Document::factory()->create([
        'documentable_id' => $student->id,
        'documentable_type' => Student::class,
    ]);
    expect($student->documents)->toHaveCount(1);
});

test('bursary application belongs to student', function () {
    $app = BursaryApplication::factory()->create();
    expect($app->student)->toBeInstanceOf(Student::class);
});

test('bursary application belongs to school', function () {
    $app = BursaryApplication::factory()->create();
    expect($app->school)->toBeInstanceOf(School::class);
});

test('bursary application has morph many documents', function () {
    $app = BursaryApplication::factory()->create();
    Document::factory()->create([
        'documentable_id' => $app->id,
        'documentable_type' => BursaryApplication::class,
    ]);
    expect($app->documents)->toHaveCount(1);
});

test('message belongs to sender', function () {
    $msg = Message::factory()->create();
    expect($msg->sender)->toBeInstanceOf(User::class);
});

test('activity log belongs to user', function () {
    $log = ActivityLog::factory()->create();
    expect($log->user)->toBeInstanceOf(User::class);
});

test('user has one member', function () {
    $user = User::factory()->create();
    Member::factory()->create(['user_id' => $user->id]);
    expect($user->member)->toBeInstanceOf(Member::class);
});

test('user has one student', function () {
    $user = User::factory()->create();
    Student::factory()->create(['user_id' => $user->id]);
    expect($user->student)->toBeInstanceOf(Student::class);
});

test('user has many activity logs', function () {
    $user = User::factory()->create();
    ActivityLog::factory()->count(3)->create(['user_id' => $user->id]);
    expect($user->activityLogs)->toHaveCount(3);
});

test('user has many messages', function () {
    $user = User::factory()->create();
    Message::factory()->count(2)->create(['sent_by' => $user->id]);
    expect($user->messages)->toHaveCount(2);
});

test('user has many qr campaigns', function () {
    $user = User::factory()->create();
    QrCampaign::factory()->count(2)->create(['created_by' => $user->id]);
    expect($user->qrCampaigns)->toHaveCount(2);
});

// ─── Soft deletes ───────────────────────────────────────────────────

test('member supports soft deletes', function () {
    $member = Member::factory()->create();
    $member->delete();
    expect(Member::find($member->id))->toBeNull()
        ->and(Member::withTrashed()->find($member->id))->not->toBeNull();
});

test('subscription supports soft deletes', function () {
    $sub = Subscription::factory()->create();
    $sub->delete();
    expect(Subscription::find($sub->id))->toBeNull()
        ->and(Subscription::withTrashed()->find($sub->id))->not->toBeNull();
});

test('qr campaign supports soft deletes', function () {
    $campaign = QrCampaign::factory()->create();
    $campaign->delete();
    expect(QrCampaign::find($campaign->id))->toBeNull()
        ->and(QrCampaign::withTrashed()->find($campaign->id))->not->toBeNull();
});

test('school supports soft deletes', function () {
    $school = School::factory()->create();
    $school->delete();
    expect(School::find($school->id))->toBeNull()
        ->and(School::withTrashed()->find($school->id))->not->toBeNull();
});

test('student supports soft deletes', function () {
    $student = Student::factory()->create();
    $student->delete();
    expect(Student::find($student->id))->toBeNull()
        ->and(Student::withTrashed()->find($student->id))->not->toBeNull();
});

test('bursary application supports soft deletes', function () {
    $app = BursaryApplication::factory()->create();
    $app->delete();
    expect(BursaryApplication::find($app->id))->toBeNull()
        ->and(BursaryApplication::withTrashed()->find($app->id))->not->toBeNull();
});

test('document supports soft deletes', function () {
    $doc = Document::factory()->create();
    $doc->delete();
    expect(Document::find($doc->id))->toBeNull()
        ->and(Document::withTrashed()->find($doc->id))->not->toBeNull();
});

// ─── Scopes ─────────────────────────────────────────────────────────

test('member active scope returns only active members', function () {
    Member::factory()->active()->create();
    Member::factory()->pending()->create();
    expect(Member::active()->count())->toBe(1);
});

test('member pending scope returns only pending members', function () {
    Member::factory()->active()->create();
    Member::factory()->pending()->create();
    expect(Member::pending()->count())->toBe(1);
});

test('subscription active scope returns active with valid end date', function () {
    Subscription::factory()->active()->create();
    Subscription::factory()->expired()->create();
    expect(Subscription::active()->count())->toBe(1);
});

test('subscription pending scope returns only pending', function () {
    Subscription::factory()->pending()->create();
    Subscription::factory()->active()->create();
    expect(Subscription::pending()->count())->toBe(1);
});

test('subscription expired scope returns expired subscriptions', function () {
    Subscription::factory()->active()->create();
    Subscription::factory()->expired()->create();
    expect(Subscription::expired()->count())->toBe(1);
});

test('student active scope returns only active students', function () {
    Student::factory()->active()->create();
    Student::factory()->pending()->create();
    expect(Student::active()->count())->toBe(1);
});

test('student pending scope returns only pending students', function () {
    Student::factory()->active()->create();
    Student::factory()->pending()->create();
    expect(Student::pending()->count())->toBe(1);
});

test('bursary application pending scope', function () {
    BursaryApplication::factory()->pending()->create();
    BursaryApplication::factory()->approved()->create();
    expect(BursaryApplication::pending()->count())->toBe(1);
});

test('bursary application approved scope', function () {
    BursaryApplication::factory()->pending()->create();
    BursaryApplication::factory()->approved()->create();
    expect(BursaryApplication::approved()->count())->toBe(1);
});

test('bursary application rejected scope', function () {
    BursaryApplication::factory()->rejected()->create();
    BursaryApplication::factory()->pending()->create();
    expect(BursaryApplication::rejected()->count())->toBe(1);
});

test('message draft scope returns only drafts', function () {
    Message::factory()->draft()->create();
    Message::factory()->sent()->create();
    expect(Message::draft()->count())->toBe(1);
});

test('message sent scope returns only sent', function () {
    Message::factory()->draft()->create();
    Message::factory()->sent()->create();
    expect(Message::sent()->count())->toBe(1);
});

// ─── Mass assignment protection ─────────────────────────────────────

test('member_number cannot be mass assigned', function () {
    $member = Member::factory()->create();
    $member->fill(['member_number' => 'HACK']);
    expect($member->isDirty('member_number'))->toBeFalse();
});

test('qr campaign code cannot be mass assigned', function () {
    $campaign = QrCampaign::factory()->create();
    $campaign->fill(['code' => 'HACK']);
    expect($campaign->isDirty('code'))->toBeFalse();
});

test('qr campaign registrations_count cannot be mass assigned', function () {
    $campaign = QrCampaign::factory()->create();
    $campaign->fill(['registrations_count' => 9999]);
    expect($campaign->isDirty('registrations_count'))->toBeFalse();
});

test('student_number cannot be mass assigned', function () {
    $student = Student::factory()->create();
    $student->fill(['student_number' => 'HACK']);
    expect($student->isDirty('student_number'))->toBeFalse();
});

test('bursary reference_number cannot be mass assigned', function () {
    $app = BursaryApplication::factory()->create();
    $app->fill(['reference_number' => 'HACK']);
    expect($app->isDirty('reference_number'))->toBeFalse();
});

test('bursary reviewed_by cannot be mass assigned', function () {
    $app = BursaryApplication::factory()->create();
    $app->fill(['reviewed_by' => 999]);
    expect($app->isDirty('reviewed_by'))->toBeFalse();
});

test('message delivery_count cannot be mass assigned', function () {
    $msg = Message::factory()->create();
    $msg->fill(['delivery_count' => 9999]);
    expect($msg->isDirty('delivery_count'))->toBeFalse();
});

test('message sent_at cannot be mass assigned', function () {
    $msg = Message::factory()->create();
    $msg->fill(['sent_at' => now()]);
    expect($msg->isDirty('sent_at'))->toBeFalse();
});

test('activity log ip_address cannot be mass assigned', function () {
    $log = ActivityLog::factory()->create();
    $log->fill(['ip_address' => '1.2.3.4']);
    expect($log->isDirty('ip_address'))->toBeFalse();
});

// ─── Observer: QR Campaign counter ──────────────────────────────────

test('creating member with campaign increments registrations_count', function () {
    $campaign = QrCampaign::factory()->create();
    expect($campaign->registrations_count)->toBe(0);

    Member::factory()->create(['qr_campaign_id' => $campaign->id]);
    $campaign->refresh();
    expect($campaign->registrations_count)->toBe(1);
});

test('deleting member with campaign decrements registrations_count', function () {
    $campaign = QrCampaign::factory()->create();
    $member = Member::factory()->create(['qr_campaign_id' => $campaign->id]);
    $campaign->refresh();
    expect($campaign->registrations_count)->toBe(1);

    $member->delete();
    $campaign->refresh();
    expect($campaign->registrations_count)->toBe(0);
});

test('restoring member with campaign increments registrations_count', function () {
    $campaign = QrCampaign::factory()->create();
    $member = Member::factory()->create(['qr_campaign_id' => $campaign->id]);
    $member->delete();
    $campaign->refresh();
    expect($campaign->registrations_count)->toBe(0);

    $member->restore();
    $campaign->refresh();
    expect($campaign->registrations_count)->toBe(1);
});

test('creating member without campaign does not affect any counter', function () {
    Member::factory()->create(['qr_campaign_id' => null]);
    // No exception thrown — passes if no error
    expect(true)->toBeTrue();
});

// ─── Factory states ─────────────────────────────────────────────────

test('user admin state sets role to admin', function () {
    $user = User::factory()->admin()->create();
    expect($user->role)->toBe('admin');
});

test('user inactive state sets status to inactive', function () {
    $user = User::factory()->inactive()->create();
    expect($user->status)->toBe('inactive');
});

// ─── Casts ──────────────────────────────────────────────────────────

test('qr campaign registrations_count is cast to integer', function () {
    $campaign = QrCampaign::factory()->create();
    expect($campaign->registrations_count)->toBeInt();
});
