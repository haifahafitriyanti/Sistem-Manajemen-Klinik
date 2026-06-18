<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserForm extends Component
{
    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'receptionist';

    public bool $isActive = true;

    public function mount(?int $userId = null): void
    {
        if ($userId) {
            $this->userId = $userId;
            $user = User::findOrFail($userId);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            $this->isActive = (bool) $user->is_active;
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        if ($this->userId) {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
                'password' => ['nullable', 'string', 'min:8'],
                'role' => ['required', 'in:admin,doctor,cashier,receptionist'],
                'isActive' => ['required', 'boolean'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,doctor,cashier,receptionist'],
            'isActive' => ['required', 'boolean'],
        ];
    }

    /**
     * Save user (create or update).
     */
    public function save(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $this->validate();

        if ($this->userId) {
            $user = User::findOrFail($this->userId);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => ($this->userId === auth()->id()) ? $user->role : $this->role,
                'is_active' => $this->isActive,
            ];

            if ($this->password !== '') {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'is_active' => $this->isActive,
            ]);
        }

        $this->dispatch('user-saved');
    }

    public function render()
    {
        return view('livewire.user.user-form');
    }
}
