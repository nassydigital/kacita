<?php

use App\Models\Member;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function members()
    {
        return Member::query()
            ->with('user')
            ->when($this->status, fn ($q, $status) => $q->where('status', $status))
            ->when($this->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('member_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(20);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Members') }}</flux:heading>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or member number...') }}" icon="magnifying-glass" class="sm:max-w-xs" />

        <flux:select wire:model.live="status" class="sm:max-w-xs">
            <flux:select.option value="">{{ __('All Statuses') }}</flux:select.option>
            <flux:select.option value="pending">{{ __('Pending') }}</flux:select.option>
            <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
        </flux:select>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Member #') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Market') }}</flux:table.column>
            <flux:table.column>{{ __('Region') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Source') }}</flux:table.column>
            <flux:table.column>{{ __('Registered') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->members() as $member)
                <flux:table.row wire:key="{{ $member->id }}">
                    <flux:table.cell>
                        <a href="{{ route('admin.members.show', $member) }}" wire:navigate class="text-sm font-medium text-zinc-800 underline dark:text-white">
                            {{ $member->member_number }}
                        </a>
                    </flux:table.cell>
                    <flux:table.cell>{{ $member->user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $member->market }}</flux:table.cell>
                    <flux:table.cell>{{ $member->region }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$member->status === 'active' ? 'green' : 'yellow'" size="sm">
                            {{ ucfirst($member->status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $member->registration_source ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $member->created_at->format('M d, Y') }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">
                        <flux:text>{{ __('No members found.') }}</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div>
        {{ $this->members()->links() }}
    </div>
</div>
