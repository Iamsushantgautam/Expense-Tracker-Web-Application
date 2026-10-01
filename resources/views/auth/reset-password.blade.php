<x-guest-layout title="Reset Password">
    <div class="w-full">
        <!-- Header Title -->
        <div class="mb-6 text-left">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Set New Password</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Please enter your new password below to reset your account credentials.</p>
        </div>

        <div class="flat-card p-6 sm:p-8 space-y-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded shadow-sm">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $token }}">

                <x-input
                    name="email"
                    label="Email Address"
                    type="email"
                    placeholder="you@example.com"
                    icon="fas fa-envelope"
                    :value="old('email', $email)"
                    required
                    autofocus
                />

                <x-input
                    name="password"
                    label="New Password"
                    type="password"
                    placeholder="••••••••"
                    icon="fas fa-lock"
                    required
                />

                <x-input
                    name="password_confirmation"
                    label="Confirm New Password"
                    type="password"
                    placeholder="••••••••"
                    icon="fas fa-lock"
                    required
                />

                <x-button type="submit" variant="primary" size="lg" class="w-full mt-2 font-semibold shadow-md shadow-primary/20" icon="fas fa-key">
                    Reset Password
                </x-button>

                <div class="text-center text-xs text-slate-500 dark:text-slate-400 mt-4 pt-2">
                    <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Back to Sign In</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
