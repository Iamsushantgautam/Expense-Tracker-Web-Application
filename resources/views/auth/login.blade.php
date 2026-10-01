<x-guest-layout title="Sign In">
    <div class="w-full" x-data="{
        fillCredentials(email, password) {
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            if (emailInput && passInput) {
                emailInput.value = email;
                passInput.value = password;
                emailInput.dispatchEvent(new Event('input'));
                passInput.dispatchEvent(new Event('input'));
            }
        }
    }">
        {{-- Header --}}
        <div class="mb-6 text-left">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                Sign in to your account
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Welcome back! Please enter your details below.
            </p>
        </div>

        @if(session('info'))
            <div class="mb-4">
                <x-alert type="info" :message="session('info')" />
            </div>
        @endif

        @if(session('status'))
            <div class="mb-4">
                <x-alert type="success" :message="session('status')" />
            </div>
        @endif

        {{-- Form (no card box) --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <x-input
                name="email"
                label="Email Address"
                type="email"
                placeholder="you@example.com"
                icon="fas fa-envelope"
                required
                autofocus
            />

            <x-input
                name="password"
                label="Password"
                type="password"
                placeholder="••••••••"
                icon="fas fa-lock"
                required
            />

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer hover:text-slate-900 dark:hover:text-slate-200 transition-colors">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary focus:ring-primary">
                    <span>Remember me</span>
                </label>

                <a href="{{ route('password.request') }}" class="font-medium text-primary hover:underline">
                    Forgot password?
                </a>
            </div>

            <x-button type="submit" variant="primary" size="lg" class="w-full mt-2 font-semibold shadow-md shadow-primary/20" icon="fas fa-right-to-bracket">
                Sign In
            </x-button>

            <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-4 pt-2">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold text-primary hover:underline ml-1">Create Account</a>
            </p>
        </form>
    </div>
</x-guest-layout>
