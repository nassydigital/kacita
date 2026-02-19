<?php

use App\Models\Member;
use Livewire\Component;

new class extends Component {
    public Member $member;

    public function mount(Member $member): void
    {
        $this->member = $member->load(['user', 'qrCampaign', 'subscriptions', 'documents']);
    }

    public function approve(): void
    {
        abort_if($this->member->status !== 'pending', 403);

        $this->member->update([
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $this->member->refresh();
    }

    public function reject(): void
    {
        abort_if($this->member->status !== 'pending', 403);

        $this->member->delete();

        $this->redirect(route('admin.members.index'), navigate: true);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.members.index') }}" wire:navigate>
                <flux:icon name="arrow-left" class="size-5" />
            </a>
            <flux:heading size="xl">{{ $member->user->name }}</flux:heading>
            <flux:badge :color="$member->status === 'active' ? 'green' : 'yellow'" size="sm">
                {{ ucfirst($member->status) }}
            </flux:badge>
        </div>

        @if ($member->status === 'pending')
            <div class="flex gap-2">
                <flux:button wire:click="approve" wire:confirm="{{ __('Are you sure you want to approve this member?') }}" variant="primary">
                    {{ __('Approve') }}
                </flux:button>
                <flux:button wire:click="reject" wire:confirm="{{ __('Are you sure you want to reject this member? This will soft-delete the record.') }}" variant="danger">
                    {{ __('Reject') }}
                </flux:button>
            </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Member Info --}}
        <flux:card>
            <flux:heading size="sm">{{ __('Member Information') }}</flux:heading>

            <div class="mt-4 space-y-3">
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('Member Number') }}</flux:text>
                    <flux:text>{{ $member->member_number }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('Email') }}</flux:text>
                    <flux:text>{{ $member->user->email }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('Phone') }}</flux:text>
                    <flux:text>{{ $member->user->phone ?? '—' }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('Market') }}</flux:text>
                    <flux:text>{{ $member->market }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('Region') }}</flux:text>
                    <flux:text>{{ $member->region }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('ID Type') }}</flux:text>
                    <flux:text>{{ $member->id_type ?? '—' }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('ID Number') }}</flux:text>
                    <flux:text>{{ $member->id_number ?? '—' }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('Source') }}</flux:text>
                    <flux:text>{{ $member->registration_source ?? '—' }}</flux:text>
                </div>
                <div class="flex justify-between">
                    <flux:text class="font-medium">{{ __('Registered') }}</flux:text>
                    <flux:text>{{ $member->created_at->format('M d, Y') }}</flux:text>
                </div>
                @if ($member->joined_at)
                    <div class="flex justify-between">
                        <flux:text class="font-medium">{{ __('Joined') }}</flux:text>
                        <flux:text>{{ $member->joined_at->format('M d, Y') }}</flux:text>
                    </div>
                @endif
            </div>
        </flux:card>

        {{-- QR Campaign --}}
        @if ($member->qrCampaign)
            <flux:card>
                <flux:heading size="sm">{{ __('QR Campaign') }}</flux:heading>

                <div class="mt-4 space-y-3">
                    <div class="flex justify-between">
                        <flux:text class="font-medium">{{ __('Campaign Name') }}</flux:text>
                        <flux:text>{{ $member->qrCampaign->name }}</flux:text>
                    </div>
                    <div class="flex justify-between">
                        <flux:text class="font-medium">{{ __('Code') }}</flux:text>
                        <flux:text>{{ $member->qrCampaign->code }}</flux:text>
                    </div>
                    <div class="flex justify-between">
                        <flux:text class="font-medium">{{ __('Market Location') }}</flux:text>
                        <flux:text>{{ $member->qrCampaign->market_location }}</flux:text>
                    </div>
                </div>
            </flux:card>
        @endif
    </div>

    {{-- Subscriptions --}}
    <flux:card>
        <flux:heading size="sm">{{ __('Subscriptions') }}</flux:heading>

        <flux:table class="mt-4">
            <flux:table.columns>
                <flux:table.column>{{ __('Plan') }}</flux:table.column>
                <flux:table.column>{{ __('Amount') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Start Date') }}</flux:table.column>
                <flux:table.column>{{ __('End Date') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($member->subscriptions as $subscription)
                    <flux:table.row>
                        <flux:table.cell>{{ $subscription->plan_type }}</flux:table.cell>
                        <flux:table.cell>{{ Number::currency($subscription->amount, 'UGX') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$subscription->status === 'active' ? 'green' : ($subscription->status === 'expired' ? 'red' : 'yellow')" size="sm">
                                {{ ucfirst($subscription->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $subscription->start_date->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $subscription->end_date->format('M d, Y') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            <flux:text>{{ __('No subscriptions.') }}</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- Documents --}}
    @if ($member->documents->isNotEmpty())
        <flux:card>
            <flux:heading size="sm">{{ __('Documents') }}</flux:heading>

            <div class="mt-4 space-y-3">
                @foreach ($member->documents as $document)
                    <div class="flex items-center justify-between">
                        <flux:text class="font-medium">{{ $document->filename }}</flux:text>
                        <flux:text>{{ $document->type }}</flux:text>
                    </div>
                @endforeach
            </div>
        </flux:card>
    @endif
</div>
