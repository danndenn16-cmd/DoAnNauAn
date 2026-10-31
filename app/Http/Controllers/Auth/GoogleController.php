<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Bước 1: chuyển người dùng sang trang đăng nhập của Google
    public function redirect()
    {
        if (!config('services.google.client_id')) {
            return redirect()->route('login')->with('error', 'Đăng nhập Google chưa được cấu hình.');
        }

        return Socialite::driver('google')->redirect();
    }

    // Bước 2: Google gọi lại địa chỉ này kèm thông tin người dùng
    public function callback()
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google login failed: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Đăng nhập Google không thành công. Vui lòng thử lại.');
        }

        // Tìm theo google_id, nếu chưa có thì tìm theo email để liên kết tài khoản cũ
        $user = User::where('google_id', $google->getId())->first()
            ?? User::where('email', $google->getEmail())->first();

        if ($user) {
            $user->update([
                'google_id' => $google->getId(),
                'avatar'    => $google->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name'      => $google->getName() ?: 'Người dùng',
                'email'     => $google->getEmail(),
                'google_id' => $google->getId(),
                'avatar'    => $google->getAvatar(),
                'password'  => null,
            ]);
        }

        // Email do Google xác thực nên coi như đã xác minh
        if (!$user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Đăng nhập bằng Google thành công.');
    }
}
