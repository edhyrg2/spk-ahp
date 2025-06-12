<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            // var_dump($user);
            // die;

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                $kriteria = Kriteria::all()->count();
                $alternatif = Alternatif::all()->count();
                $userCount = User::where('role', 'user')->count();

                if ($kriteria == 0) {
                    $kriteria = 0;
                }

                if ($alternatif == 0) {
                    $alternatif = 0;
                }

                // if ($user == 0) {
                //     $user = 0;
                // }
                return redirect()->intended('/admin')->with('kriteria', $kriteria)->with('alternatif', $alternatif)->with('userCount', $userCount);
            } elseif ($user->role === 'user') {
                return redirect()->intended('/dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Role tidak dikenali.']);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    public function dashboard()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return view('Admin.homepage');
        } elseif ($user->role === 'user') {
            return view('dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
