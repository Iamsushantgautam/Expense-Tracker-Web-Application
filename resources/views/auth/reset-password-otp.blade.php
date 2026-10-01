<x-guest-layout title="Create New Password">
    <div class="w-full">
        <!-- Header -->
        <div class="mb-6 text-left">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mb-4">
                <i class="fas fa-key text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Create new password</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                OTP verified! Choose a strong new password for your account.
            </p>
        </div>

        <!-- Success badge -->
        <div class="mb-5 flex items-center gap-2 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs text-emerald-700 dark:text-emerald-300 font-medium">
            <i class="fas fa-circle-check text-emerald-500"></i>
            Identity verified for <strong class="ml-1">{{ $email }}</strong>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-sm text-rose-700 dark:text-rose-300 flex items-start gap-2">
                <i class="fas fa-circle-exclamation mt-0.5 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.reset.submit') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="email" value="{{ $email }}">

            <x-input
                name="password"
                label="New Password"
                type="password"
                placeholder="••••••••"
                icon="fas fa-lock"
                required
                autofocus
            />

            <x-input
                name="password_confirmation"
                label="Confirm New Password"
                type="password"
                placeholder="••••••••"
                icon="fas fa-lock-open"
                required
            />

            <x-button type="submit" variant="primary" size="lg" class="w-full mt-2 font-semibold" icon="fas fa-floppy-disk">
                Save New Password
            </x-button>

            <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-4">
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">
                    <i class="fas fa-arrow-left text-xs mr-1"></i> Back to Sign In
                </a>
            </p>
        </form>
    </div>
</x-guest-layout>
