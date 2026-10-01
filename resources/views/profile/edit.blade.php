<x-app-layout title="Profile & Preferences" active="profile">

    @php
        $avatar = $user->profile_pic ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=ffffff&background=2563eb&size=200';
    @endphp

    {{-- Single Alpine scope: avatar preview + edit-mode toggle --}}
    <div x-data="{
            preview: '{{ $avatar }}',
            fileName: '',
            editingInfo: false,
            editingPass: false,
            onChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                this.fileName = file.name;
                const reader = new FileReader();
                reader.onload = ev => this.preview = ev.target.result;
                reader.readAsDataURL(file);
            }
        }">

        {{-- ══════════════════════════════════════════
            HERO BANNER
        ══════════════════════════════════════════ --}}
        <div class="-mx-4 sm:-mx-6 lg:-mx-8 -mt-6 mb-12">

            {{-- Gradient cover with identity row at the bottom --}}
            <div class="bg-gradient-to-br from-primary to-indigo-600 relative overflow-hidden pb-5 px-4 sm:px-6 lg:px-8">
                <div class="absolute -top-10 -right-10 w-56 h-56 bg-white/5 rounded-full"></div>
                <div class="absolute top-6 right-32 w-24 h-24 bg-white/5 rounded-full"></div>
                <div class="absolute -bottom-8 left-1/3 w-40 h-40 bg-black/10 rounded-full"></div>

                {{-- Spacer for banner height --}}
                <div class="h-16"></div>

                {{-- Avatar + name + stats row --}}
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">

                    {{-- Avatar click triggers file input --}}
                    <div class="flex items-end gap-4 flex-1 min-w-0">
                        <div class="relative shrink-0 group cursor-pointer" @click="$refs.picInput.click()">
                            <img :src="preview" alt="{{ $user->name }}"
                                 class="w-20 h-20 rounded-2xl object-cover border-3 border-white/30 transition-opacity duration-200 group-hover:opacity-70">
                            <div class="absolute inset-0 flex items-center justify-center rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <i class="fas fa-camera text-white text-lg"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-white/40">
                                <i class="fas fa-pen text-white text-[8px]"></i>
                            </div>
                        </div>

                        <div class="pb-0.5 min-w-0 flex-1">
                            <h1 class="text-lg font-bold text-white leading-snug break-words">{{ $user->name }}</h1>
                            <p class="text-sm text-white/70">{{ '@' . $user->username }}</p>
                            <p class="text-xs text-white/50">{{ $user->email }}</p>
                            <p class="text-[11px] text-emerald-300 mt-1 font-medium truncate"
                               x-show="fileName" x-text="'📎 ' + fileName + ' — save to apply'"></p>
                        </div>
                    </div>

                    {{-- Quick stats --}}
                    <div class="flex items-center gap-8 pb-0.5">
                        <div class="text-center">
                            <p class="text-base font-bold text-white">₹{{ number_format($user->monthly_budget ?? 0) }}</p>
                            <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Budget / month</p>
                        </div>
                        <div class="text-center">
                            <p class="text-base font-bold text-white">{{ $user->budget_warn_limit ?? 90 }}%</p>
                            <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Warn Limit</p>
                        </div>
                        <div class="text-center">
                            <p class="text-base font-bold text-white">{{ ucfirst($user->theme_color ?? 'Blue') }}</p>
                            <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Theme</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
            MAIN SETTINGS GRID
        ══════════════════════════════════════════ --}}
        <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- ─── LEFT: Personal Info + Password ─── --}}
            <div class="lg:col-span-2 space-y-10">

                {{-- ══ SECTION 1: Personal Information ══ --}}
                <div class="space-y-5">

                    {{-- Section header with Edit / Cancel toggle --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center shrink-0">
                                <i class="fas fa-user-pen text-primary text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Personal Information</h2>
                                <p class="text-xs text-slate-400">Name, username, email and budget settings.</p>
                            </div>
                        </div>

                        {{-- Edit / Cancel toggle button --}}
                        <button type="button"
                                @click="editingInfo = !editingInfo"
                                :class="editingInfo
                                    ? 'text-slate-500 border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'
                                    : 'text-primary border-primary/30 bg-primary/5 hover:bg-primary/10'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all duration-150">
                            <i :class="editingInfo ? 'fas fa-times' : 'fas fa-pen-to-square'" class="text-xs"></i>
                            <span x-text="editingInfo ? 'Cancel' : 'Edit'"></span>
                        </button>
                    </div>

                    {{-- ── VIEW mode (read-only) ── --}}
                    <div x-show="!editingInfo" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                            <div>
                                <dt class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Full Name</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Username</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ '@' . $user->username }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Email Address</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Monthly Budget</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">₹{{ number_format($user->monthly_budget ?? 0, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Warning Alert Limit</dt>
                                <dd class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $user->budget_warn_limit ?? 90 }}%</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- ── EDIT mode (form) ── --}}
                    <div x-show="editingInfo" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            {{-- THE only file input — hero avatar click triggers this --}}
                            <input x-ref="picInput"
                                   type="file"
                                   name="profile_pic"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="hidden"
                                   @change="onChange($event)">

                            {{-- Photo hint --}}
                            <p class="text-xs text-slate-400">
                                <i class="fas fa-circle-info mr-1 text-primary"></i>
                                Click your profile photo above to change it.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <x-input name="name"     label="Full Name"     :value="$user->name"     required />
                                <x-input name="username" label="Username"      :value="$user->username" placeholder="username" icon="fas fa-at" required />
                                <x-input name="email"    label="Email Address" type="email" :value="$user->email" required />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-input name="monthly_budget"    label="Monthly Budget (₹)"      type="number" step="0.01"  :value="$user->monthly_budget"    icon="fas fa-rupee-sign" />
                                <x-input name="budget_warn_limit" label="Warning Alert Limit (%)" type="number" min="1" max="100" :value="$user->budget_warn_limit" icon="fas fa-exclamation-circle" />
                            </div>

                            <input type="hidden" name="theme_color" id="form_theme_color" value="{{ $user->theme_color ?? 'blue' }}">

                            <div class="flex items-center justify-end gap-3 pt-1">
                                <button type="button" @click="editingInfo = false"
                                        class="text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                    Cancel
                                </button>
                                <x-button type="submit" variant="primary" icon="fas fa-save">Save Changes</x-button>
                            </div>
                        </form>
                    </div>

                </div>

                {{-- ══ SECTION 2: Change Password ══ --}}
                <div class="space-y-5 pt-8">

                    {{-- Section header with toggle --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center shrink-0">
                                <i class="fas fa-lock text-rose-500 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Change Password</h2>
                                <p class="text-xs text-slate-400">Keep your account secure with a strong password.</p>
                            </div>
                        </div>

                        <button type="button"
                                @click="editingPass = !editingPass"
                                :class="editingPass
                                    ? 'text-slate-500 border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'
                                    : 'text-rose-500 border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-950/50'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition-all duration-150">
                            <i :class="editingPass ? 'fas fa-times' : 'fas fa-key'" class="text-xs"></i>
                            <span x-text="editingPass ? 'Cancel' : 'Change'"></span>
                        </button>
                    </div>

                    {{-- VIEW: password placeholder --}}
                    <div x-show="!editingPass" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800">
                            <i class="fas fa-circle-check text-emerald-500 text-base shrink-0"></i>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Password is set</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click <strong>Change</strong> to update your password.</p>
                            </div>
                            <div class="ml-auto flex gap-1">
                                @for($i = 0; $i < 8; $i++)
                                    <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- EDIT: password form --}}
                    <div x-show="editingPass" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <x-input name="current_password" label="Current Password" type="password" required />

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-input name="password"              label="New Password"         type="password" required />
                                <x-input name="password_confirmation" label="Confirm New Password" type="password" required />
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-1">
                                <button type="button" @click="editingPass = false"
                                        class="text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                    Cancel
                                </button>
                                <x-button type="submit" variant="outline" icon="fas fa-key">Update Password</x-button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

            {{-- ─── RIGHT: Theme & Display Mode ─── --}}
            <div class="space-y-8" x-data="themeSettings()">

                {{-- ── Color Theme ── --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-950/40 flex items-center justify-center shrink-0">
                            <i class="fas fa-palette text-violet-500 text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Color Theme</h2>
                            <p class="text-xs text-slate-400">App-wide accent color.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="color in colorPresets" :key="color.id">
                            <button type="button" @click="selectColor(color.id)"
                                    :class="currentColor === color.id
                                        ? 'ring-2 ring-offset-2 ring-primary dark:ring-offset-slate-950 shadow-sm'
                                        : 'hover:bg-slate-50 dark:hover:bg-slate-800/50'"
                                    class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 transition-all duration-150 text-left">
                                <span class="w-3 h-3 rounded-full shrink-0" :style="{ backgroundColor: color.hex }"></span>
                                <span class="text-xs text-slate-700 dark:text-slate-300 font-medium" x-text="color.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ── Display Mode ── --}}
                <div class="space-y-4 pt-8">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center shrink-0">
                            <i class="fas fa-circle-half-stroke text-amber-500 text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Display Mode</h2>
                            <p class="text-xs text-slate-400">Light or dark appearance.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="setDarkMode(false)"
                                :class="!isDark
                                    ? 'ring-2 ring-offset-2 ring-primary dark:ring-offset-slate-950 bg-amber-50 dark:bg-amber-950/20'
                                    : 'hover:bg-slate-50 dark:hover:bg-slate-800/50'"
                                class="flex flex-col items-center gap-2 py-4 rounded-xl border border-slate-200 dark:border-slate-800 transition-all duration-150">
                            <i class="fas fa-sun text-amber-400 text-xl"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Light</span>
                        </button>
                        <button type="button" @click="setDarkMode(true)"
                                :class="isDark
                                    ? 'ring-2 ring-offset-2 ring-primary dark:ring-offset-slate-950 bg-indigo-50 dark:bg-indigo-950/20'
                                    : 'hover:bg-slate-50 dark:hover:bg-slate-800/50'"
                                class="flex flex-col items-center gap-2 py-4 rounded-xl border border-slate-200 dark:border-slate-800 transition-all duration-150">
                            <i class="fas fa-moon text-indigo-400 text-xl"></i>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Dark</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- end single x-data wrapper --}}

    {{-- Open edit mode automatically if there are validation errors --}}
    @if($errors->any())
    <script>
        document.addEventListener('alpine:init', () => {
            // Determine which section had errors and auto-open it
            @if($errors->has('name') || $errors->has('email') || $errors->has('username') || $errors->has('monthly_budget') || $errors->has('budget_warn_limit'))
                Alpine.store && null; // handled below via x-init
            @endif
        });
    </script>
    @endif

    {{-- Alpine Theme Logic --}}
    <script>
        function themeSettings() {
            return {
                currentColor: localStorage.getItem('theme_color') || '{{ $user->theme_color ?? "blue" }}',
                isDark: localStorage.getItem('dark_mode') === 'true',
                colorPresets: [
                    { id: 'blue',    name: 'Royal Blue', hex: '#2563eb' },
                    { id: 'emerald', name: 'Emerald',    hex: '#059669' },
                    { id: 'indigo',  name: 'Indigo',     hex: '#4f46e5' },
                    { id: 'violet',  name: 'Violet',     hex: '#7c3aed' },
                    { id: 'rose',    name: 'Rose',        hex: '#e11d48' },
                    { id: 'teal',    name: 'Teal',        hex: '#0d9488' },
                    { id: 'amber',   name: 'Amber',       hex: '#d97706' },
                    { id: 'slate',   name: 'Slate',       hex: '#475569' }
                ],
                selectColor(color) {
                    this.currentColor = color;
                    window.ExpenseTracker.applyTheme(color);
                    document.getElementById('form_theme_color').value = color;
                },
                setDarkMode(dark) {
                    this.isDark = dark;
                    window.ExpenseTracker.applyDarkMode(dark);
                }
            }
        }
    </script>

</x-app-layout>
