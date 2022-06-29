<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Mail\Websitemail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
// use Auth;
// use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\Concerns\Has;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }
    public function forget_password()
    {
        return view('admin.forget_password');
    }

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $admin_data = Admin::where('email', $request->email)->first();
        if (!$admin_data) {
            return redirect()->back()->with('error', 'Email adresi bulunamadı');
        }
        $token = hash('sha256', time());

        $admin_data->token = $token;
        $admin_data->update();

        $reset_link = url('admin/reset-password/' . $token . '/' . $request->email);
        $subject = 'Şifreyi Yenile';
        $message = 'Lütfen Aşağıdaki Bağlantıya Tıklayın';
        $message = '<a href="' . $reset_link . '">Buraya Tıklayın</a>';

        \Mail::to($request->email)->send(new Websitemail($subject, $message));

        return redirect()->route('admin.login')->with('success', 'Lütfen Emailinizi Kontrol Ediniz');
    }

    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credential = [
            'email' => $request->email,
            'password' => $request->password

        ];
        if (Auth::guard('admin')->attempt($credential)) {
            return redirect()->route('admin.home');
        } else {
            return redirect()->route('admin.login')->with('error', 'Bilgileri Doğru Giriniz!');
        }
    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
    public function reset_password($token, $email)
    {
        $admin_data = Admin::where('token', $token)->where('email', $email)->first();
        if (!$admin_data) {
            return redirect()->route('admin.login');
        }
        return view('admin.reset_password', compact('token', 'email'));
    }
    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'retype_password' => 'required|same:password'
        ]);
        $admmin_data = Admin::where('token', $request->token)->where('email', $request->email)->first();

        $admmin_data->password = Hash::make($request->password);
        $admmin_data->token = '';
        $admmin_data->update();

        return redirect()->route('admin.login')->with('success', 'Şifreniz Başarıyla Değişti');
    }
}
