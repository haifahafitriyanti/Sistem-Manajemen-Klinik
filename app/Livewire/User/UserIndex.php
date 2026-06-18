<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class UserIndex extends Component
{
    public string $search = '';

    public string $filterRole = '';

    public bool $showForm = false;

    public ?int $editingUserId = null;

    public bool $showResetModal = false;

    public string $newPassword = '';

    public string $resetUserName = '';

    /**
     * Open modal for creating a new user.
     */
    public function openCreate(): void
    {
        $this->editingUserId = null;
        $this->showForm = true;
    }

    /**
     * Open modal for editing a user.
     */
    public function openEdit(int $userId): void
    {
        $this->editingUserId = $userId;
        $this->showForm = true;
    }

    /**
     * Close the form modal.
     */
    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingUserId = null;
    }

    /**
     * Toggle the active status of a user.
     */
    public function toggleActive(int $userId): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($userId === auth()->id()) {
            return;
        }

        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);
    }

    /**
     * Reset password for a user and show the new password in a modal.
     */
    public function resetPassword(int $userId): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if ($userId === auth()->id()) {
            return;
        }

        $user = User::findOrFail($userId);
        $plain = Str::random(8);
        $user->update(['password' => $plain]);

        $this->newPassword = $plain;
        $this->resetUserName = $user->name;
        $this->showResetModal = true;
    }

    /**
     * Close the reset-password result modal.
     */
    public function closeResetModal(): void
    {
        $this->showResetModal = false;
        $this->newPassword = '';
        $this->resetUserName = '';
    }

    /**
     * Handle the 'user-saved' event from UserForm.
     */
    #[On('user-saved')]
    public function onUserSaved(): void
    {
        $this->closeForm();
    }

    public function render()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $users = User::query()
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->get();

        return view('livewire.user.user-index', compact('users'))
            ->layout('layouts.app');
    }
}
