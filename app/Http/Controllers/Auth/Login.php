<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

class Login extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }*/
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'destroy']);
    }

    public function create()
    {
        return view('auth.login.create');
    }

    public function store()
    {
        // Attempt to login
        if (!auth()->attempt(request(['email', 'password']))) {
            flash(trans('auth.failed'))->error();

            return back();
        }

        // Get user object
        $user = auth()->user();

        // Check if user is enabled
        if (!$user->enabled) {
            auth()->logout();

            flash(trans('auth.disabled'))->error();

            return redirect('auth/login');
        }

        // Check if is customer
        if ($user->customer) {
            $path = session('url.intended', 'customers');

            // Path must start with 'customers' prefix
            if (!str_contains($path, 'customers')) {
                $path = 'customers';
            }

            return redirect($path);
        }

        return redirect('/');
    }

    public function destroy()
    {
        auth()->logout();

        return redirect('auth/login');
    }
}
