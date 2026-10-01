<x-guest-layout title="Create Account">
    <div x-data="{
        name: '{{ old('name') }}',
        username: '{{ old('username') }}',
        userTouchedUsername: false,
        generateUsername() {
            let base = this.name ? this.name.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_+|_+$/g, '') : 'user';
            if (!base) base = 'user';
            const randomNum = Math.floor(100 + Math.random() * 900);
            this.username = `${base}_${randomNum}`;
            this.userTouchedUsername = true;
        },
        autoUpdateUsername() {
            if (!this.userTouchedUsername) {
                let base = this.name ? this.name.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_+|_+$/g, '') : '';
                if (base) {
                    const randomNum = Math.floor(10 + Math.random() * 90);
                    this.username = `${base}_${randomNum}`;
                }
            }
        }
    }">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div class="mb-4">
                <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">Create your account</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Join Wit Expense Tracker to manage your personal finances easily.</p>
            </div>

            <!-- Full Name -->
            <div>
                <x-input
                    id="name"
                    name="name"
                    label="Full Name"
                    type="text"
                    placeholder="Sushant Gautam"
                    icon="fas fa-user"
                    x-model="name"
                    @input="autoUpdateUsername()"
                    required
                    autofocus
                />
            </div>

            <!-- Username Field with Auto Generate Button -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="username" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <button
                        type="button"
                        @click="generateUsername()"
                        class="text-xs text-primary hover:text-primary-dark font-medium inline-flex items-center gap-1.5 bg-primary/10 hover:bg-primary/20 px-2.5 py-1 rounded transition-colors"
                        title="Auto-generate a unique username"
                    >
                        <i class="fas fa-wand-magic-sparkles text-[10px]"></i>
                        <span>Auto Generate</span>
                    </button>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 dark:text-slate-500">
                        <i class="fas fa-at"></i>
                    </span>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        x-model="username"
                        @input="userTouchedUsername = true"
                        placeholder="sushant_gautam_89"
                        required
                        class="w-full pl-9 pr-3.5 py-2 text-sm bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 btn-rounded focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
                    />
                </div>
                @error('username')
                    <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                @else
                    <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Must be unique (no 2 users can share the same username).</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <x-input
                    name="email"
                    label="Email Address"
                    type="email"
                    placeholder="you@example.com"
                    icon="fas fa-envelope"
                    required
                />
                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Must be unique (no 2 users can share the same email address).</p>
            </div>

            <!-- Password -->
            <x-input
                name="password"
                label="Password"
                type="password"
                placeholder="••••••••"
                icon="fas fa-lock"
                required
            />

            <!-- Confirm Password -->
            <x-input
                name="password_confirmation"
                label="Confirm Password"
                type="password"
                placeholder="••••••••"
                icon="fas fa-lock"
                required
            />

            <x-button type="submit" variant="primary" size="lg" class="w-full mt-2 font-semibold shadow-md shadow-primary/20" icon="fas fa-user-plus">
                Create Account
            </x-button>

            <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-4">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign In</a>
            </p>
        </form>
    </div>
</x-guest-layout>
