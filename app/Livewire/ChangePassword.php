<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Component
{
    public $currentPassword = '';
    public $newPassword = '';
    public $newPassword_confirmation = '';
    public $showCurrentPassword = false;
    public $showNewPassword = false;
    public $showConfirmPassword = false;

    protected $rules = [
        'currentPassword' => 'required|string',
        'newPassword' => 'required|string|min:6|confirmed',
    ];

    protected $messages = [
        'currentPassword.required' => 'Current password is required',
        'newPassword.required' => 'New password is required',
        'newPassword.min' => 'New password must be at least 6 characters',
        'newPassword.confirmed' => 'Passwords do not match',
    ];

    public function changePassword()
    {
        $this->validate();

        $user = auth()->user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Current password is incorrect');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->reset();
        $this->dispatch('notify', message: 'Password changed successfully');
    }

    public function render()
    {
        return view('livewire.change-password')->layout('components.app-layout', ['title' => 'Change Password']);
    }
}
