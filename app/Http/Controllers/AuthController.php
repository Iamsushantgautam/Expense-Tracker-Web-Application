<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $email = strtolower(trim($request->email));

        $user = User::create([
            'name' => $request->name,
            'username' => strtolower(trim($request->username)),
            'email' => $email,
            'password' => Hash::make($request->password),
            'monthly_budget' => 10000.00,
            'budget_warn_limit' => 90,
            'theme_color' => 'blue',
            'dark_mode' => false,
            'is_admin' => ($email === 'sushantgautamlk6393@gmail.com'),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Account created successfully! Welcome to Wit Expense Tracker.');
    }

    // ─────────────────────────────────────────────────────────────
    //  OTP-BASED PASSWORD RESET FLOW
    // ─────────────────────────────────────────────────────────────

    /** Step 1 – Show: Enter email form */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /** Step 1 – Post: Validate email, generate OTP, send email */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find an account with that email address.'])->withInput();
        }

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP in cache for 10 minutes (keyed by email)
        Cache::put('password_reset_otp_' . $email, Hash::make($otp), now()->addMinutes(10));

        // Send OTP via Resend REST API (no package required)
        $htmlBody = view('emails.password-reset-otp', [
            'otp'      => $otp,
            'userName' => $user->name,
        ])->render();

        Http::withToken(env('RESEND_API_KEY'))
            ->post('https://api.resend.com/emails', [
                'from'    => env('MAIL_FROM_NAME', 'Wit Expense Tracker') . ' <' . env('MAIL_FROM_ADDRESS') . '>',
                'to'      => [$user->email],
                'subject' => 'Your Password Reset OTP – ' . config('app.name'),
                'html'    => $htmlBody,
            ]);

        // Pass email forward via session (masked for display)
        return redirect()->route('password.otp.verify')
            ->with('otp_email', $email);
    }

    /** Step 2 – Show: OTP verification form */
    public function showVerifyOtp(): View|RedirectResponse
    {
        if (!session('otp_email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        return view('auth.verify-otp', [
            'email' => session('otp_email'),
        ]);
    }

    /** Step 2 – Post: Verify OTP */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp'   => ['required', 'digits:6'],
        ]);

        $email = strtolower(trim($request->email));
        $cacheKey = 'password_reset_otp_' . $email;
        $hashedOtp = Cache::get($cacheKey);

        if (!$hashedOtp || !Hash::check($request->otp, $hashedOtp)) {
            return back()
                ->withErrors(['otp' => 'Invalid or expired OTP. Please try again.'])
                ->with('otp_email', $email);
        }

        // OTP verified – allow password reset; replace cache entry with a short-lived verified flag
        Cache::forget($cacheKey);
        Cache::put('password_reset_verified_' . $email, true, now()->addMinutes(10));

        return redirect()->route('password.otp.reset')
            ->with('otp_email', $email);
    }

    /** Step 3 – Show: New password form */
    public function showOtpResetPassword(): View|RedirectResponse
    {
        $email = session('otp_email');

        if (!$email || !Cache::get('password_reset_verified_' . $email)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired or OTP not verified. Please start again.']);
        }

        return view('auth.reset-password-otp', ['email' => $email]);
    }

    /** Step 3 – Post: Save new password */
    public function resetPasswordOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $email = strtolower(trim($request->email));

        if (!Cache::get('password_reset_verified_' . $email)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        $user->forceFill(['password' => Hash::make($request->password)])->save();

        Cache::forget('password_reset_verified_' . $email);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully! You may now sign in with your new password.');
    }

    // ─────────────────────────────────────────────────────────────
    //  LOGOUT
    // ─────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'You have been logged out.');
    }
}
