<?php

namespace App\Livewire\Evaluator;

use App\Livewire\LoginOrRegister;
use App\Models\AccessToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EvaluatorLogin extends Component
{
    protected $layout = 'components.layouts.guest';

    public function mount(Request $request)
    {
        $urlToken = $request->token ?? null;

        $this->validateContactCredentials($urlToken);
    }

    public function validateContactCredentials($token)
    {
        $token = AccessToken::whereToken($token)->first();

        if (! $token) {
            session()->put('wrong_token');

            return $this->redirect(LoginOrRegister::class, navigate: true);
        }

        $is_token_expired = Carbon::parse($token->expires_at)->isPast();
        $contact = $token->contact ?? null;
        $jiri = $token->jiri ?? null;

        if (! $token || $is_token_expired || ! $contact || ! $jiri) {
            return $this->redirect(LoginOrRegister::class, navigate: true);
        }

        Auth::guard('contact')->login($contact);

        session()->put('welcome', 'contact');
        session()->put('jiri', $jiri);

        return $this->redirect(route('evaluator.dashboard', ['jiri' => $jiri->slug]), navigate: true);
    }

    public function render()
    {
        return view('livewire.evaluator.evaluator-login')->layout($this->layout);
    }
}
