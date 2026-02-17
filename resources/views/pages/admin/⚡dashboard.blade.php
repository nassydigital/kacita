<?php

use App\Models\ActivityLog;
use App\Models\BursaryApplication;
use App\Models\Member;
use App\Models\School;
use App\Models\Student;
use App\Models\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    #[Computed]
    public function totalMembers(): int
    {
        return Member::count();
    }

    #[Computed]
    public function activeSubscriptions(): int
    {
        return Subscription::active()->count();
    }

    #[Computed]
    public function pendingMembers(): int
    {
        return Member::pending()->count();
    }

    #[Computed]
    public function pendingBursaryApplications(): int
    {
        return BursaryApplication::pending()->count();
    }

    #[Computed]
    public function totalSchools(): int
    {
        return School::count();
    }

    #[Computed]
    public function totalStudents(): int
    {
        return Student::count();
    }

    #[Computed]
    public function recentPendingMembers()
    {
        return Member::pending()->with('user')->latest()->take(5)->get();
    }

    #[Computed]
    public function recentPendingApplications()
    {
        return BursaryApplication::pending()->with(['student', 'school'])->latest()->take(5)->get();
    }

    #[Computed]
    public function recentActivity()
    {
        return ActivityLog::with('user')->latest()->take(10)->get();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Stats Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <flux:card>
                <flux:heading size="sm">{{ __('Total Members') }}</flux:heading>
                <flux:text class="mt-1 text-3xl font-bold">{{ $this->totalMembers }}</flux:text>
            </flux:card>

            <flux:card>
                <flux:heading size="sm">{{ __('Active Subscriptions') }}</flux:heading>
                <flux:text class="mt-1 text-3xl font-bold">{{ $this->activeSubscriptions }}</flux:text>
            </flux:card>

            <flux:card>
                <flux:heading size="sm">{{ __('Pending Members') }}</flux:heading>
                <flux:text class="mt-1 text-3xl font-bold">{{ $this->pendingMembers }}</flux:text>
            </flux:card>

            <flux:card>
                <flux:heading size="sm">{{ __('Pending Bursary Applications') }}</flux:heading>
                <flux:text class="mt-1 text-3xl font-bold">{{ $this->pendingBursaryApplications }}</flux:text>
            </flux:card>

            <flux:card>
                <flux:heading size="sm">{{ __('Total Schools') }}</flux:heading>
                <flux:text class="mt-1 text-3xl font-bold">{{ $this->totalSchools }}</flux:text>
            </flux:card>

            <flux:card>
                <flux:heading size="sm">{{ __('Total Students') }}</flux:heading>
                <flux:text class="mt-1 text-3xl font-bold">{{ $this->totalStudents }}</flux:text>
            </flux:card>
        </div>

        {{-- Pending Items --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Pending Members --}}
            <flux:card>
                <flux:heading size="sm">{{ __('Pending Members') }}</flux:heading>

                <div class="mt-4 space-y-3">
                    @forelse ($this->recentPendingMembers as $member)
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="font-medium">{{ $member->user->name }}</flux:text>
                                <flux:text class="text-xs">{{ $member->member_number }} &middot; {{ $member->market }}</flux:text>
                            </div>
                            <flux:text class="text-xs">{{ $member->created_at->diffForHumans() }}</flux:text>
                        </div>
                    @empty
                        <flux:text>{{ __('No pending members.') }}</flux:text>
                    @endforelse
                </div>
            </flux:card>

            {{-- Pending Bursary Applications --}}
            <flux:card>
                <flux:heading size="sm">{{ __('Pending Bursary Applications') }}</flux:heading>

                <div class="mt-4 space-y-3">
                    @forelse ($this->recentPendingApplications as $application)
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:text class="font-medium">{{ $application->student->name }}</flux:text>
                                <flux:text class="text-xs">{{ $application->school->name }} &middot; {{ $application->academic_year }}</flux:text>
                            </div>
                            <flux:text class="text-xs">{{ Number::currency($application->amount_requested, 'UGX') }}</flux:text>
                        </div>
                    @empty
                        <flux:text>{{ __('No pending applications.') }}</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>

        {{-- Recent Activity --}}
        <flux:card>
            <flux:heading size="sm">{{ __('Recent Activity') }}</flux:heading>

            <div class="mt-4 space-y-3">
                @forelse ($this->recentActivity as $log)
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text class="font-medium">{{ $log->action }}</flux:text>
                            <flux:text class="text-xs">{{ $log->description }}</flux:text>
                        </div>
                        <div class="text-right">
                            <flux:text class="text-xs">{{ $log->user?->name ?? __('System') }}</flux:text>
                            <flux:text class="text-xs">{{ $log->created_at->diffForHumans() }}</flux:text>
                        </div>
                    </div>
                @empty
                    <flux:text>{{ __('No recent activity.') }}</flux:text>
                @endforelse
            </div>
        </flux:card>
</div>
