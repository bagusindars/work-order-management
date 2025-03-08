<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Login extends Component
{
    use Toast;

    public $email;

    public $password;

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.auth')->layoutData([
            'title' => 'Login'
        ]);
    }

    public function submit()
    {
        $credentials = $this->validate();
        
        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return redirect()->route('home');
        }

        $this->error('Invalid credentials.');
    }

    protected function rules()
    {
        return [
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string']
        ];
    }
}
