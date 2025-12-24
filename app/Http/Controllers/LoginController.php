<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index() {
        return view('auth.login');
    }

    public function reloadCaptcha() {
        return response()->json(['captcha' => captcha_img('math')]);
    }

    public function store(Request $request) {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            // 'captcha' => ['required', 'captcha'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            if (Auth::user()->role_id == '1' || Auth::user()->role_id == '2') {
                return redirect('/admin');
            }
            if (Auth::user()->role_id == '3' || Auth::user()->role_id == '4' || Auth::user()->role_id == '5' || Auth::user()->role_id == '6') {
                return redirect('/karyawan');
            }
        }

        return redirect()->route('login')->with('login', 'Email atau password salah.');
    }

    public function logout()
    {
        Session::flush();

        Auth::logout();

        return redirect('login');
    }

    public function change(Request $request) {
        if ($request->newpassword != $request->newpassword_confirmation) {
            return redirect()->back()->with('login', 'Konfirmasi Password Salah!');
        }

        if (User::where('id', $request->id)->update(['password' => Hash::make($request->newpassword)])) {
            return redirect()->back();
        };
    }
}
