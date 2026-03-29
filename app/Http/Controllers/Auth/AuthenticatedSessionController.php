<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserLoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $ua      = $request->userAgent() ?? '';
        $parsed  = UserLoginLog::parseUserAgent($ua);
        $ip      = $request->ip();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $log = UserLoginLog::create([
                'user_id'      => Auth::id(),
                'ip_address'   => $ip,
                'user_agent'   => $ua,
                'device'       => $parsed['device'],
                'browser'      => $parsed['browser'],
                'platform'     => $parsed['platform'],
                'status'       => 'success',
                'logged_in_at' => now(),
            ]);

            // Simpan log ID di session buat update logged_out_at nanti
            session(['login_log_id' => $log->id]);

            return redirect()->intended('/');
        }

        // Catat failed attempt jika user ditemukan
        $user = User::where('email', $credentials['email'])->first();
        if ($user) {
            UserLoginLog::create([
                'user_id'      => $user->id,
                'ip_address'   => $ip,
                'user_agent'   => $ua,
                'device'       => $parsed['device'],
                'browser'      => $parsed['browser'],
                'platform'     => $parsed['platform'],
                'status'       => 'failed',
                'logged_in_at' => now(),
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        // Update logged_out_at
        if ($logId = session('login_log_id')) {
            UserLoginLog::where('id', $logId)->update(['logged_out_at' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
