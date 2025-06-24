<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Mary\Traits\Toast;

class Login extends Component
{
    use Toast;

    public $title = 'Dapco APP';
    public ?string $email;
    public ?string $password;

    public function authenticate()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'password.required' => 'O campo senha é obrigatório'
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            request()->session()->regenerate();
            $this->success('Login efetuado com sucesso!', position: 'toast-top');
            return Redirect::route('home');
        } else {
            $this->error("Erro ao tentar acessar!", position: 'toast-top');
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
