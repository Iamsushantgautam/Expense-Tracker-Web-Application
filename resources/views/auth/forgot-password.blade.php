<x-guest-layout title="Forgot Password">
    <div class="w-full">
        <!-- Header -->
        <div class="mb-6 text-left">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-4">
                <i class="fas fa-lock-open text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Forgot your password?</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Enter your registered email address and we'll send a 6-digit OTP to verify it's you.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-sm text-rose-700 dark:text-rose-300 flex items-start gap-2">
                <i class="fas fa-circle-exclamation mt-0.5 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <x-input
                name="email"
                label="Email Address"
                type="email"
                placeholder="you@example.com"
                icon="fas fa-envelope"
                :value="old('email')"
                required
                autofocus
            />

            <x-button type="submit" variant="primary" size="lg" class="w-full mt-2 font-semibold" icon="fas fa-paper-plane">
                Send OTP to Email
            </x-button>

            <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-4 pt-2">
                Remembered your password?
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline ml-1">Back to Sign In</a>
            </p>
        </form>
    </div>
</x-guest-layout>
