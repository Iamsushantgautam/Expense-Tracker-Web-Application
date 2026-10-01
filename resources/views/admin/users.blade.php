<x-app-layout title="Admin - User Management" active="admin">
    <div class="space-y-6" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editUser: { id: null, name: '', username: '', email: '', monthly_budget: 10000, budget_warn_limit: 90, is_admin: 0 },
        deleteUser: { id: null, name: '', email: '' }
    }">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i class="fas fa-users-cog text-primary"></i> User Management
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Manage registered user accounts, roles, budget parameters, and profile details
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="showCreateModal = true"
                    class="btn-rounded bg-primary hover:bg-primary-dark text-white text-xs font-semibold px-4 py-2.5 flex items-center gap-2 shadow-sm transition-all duration-150 active:scale-95 cursor-pointer"
                >
                    <i class="fas fa-user-plus text-sm"></i>
                    <span>Create New User</span>
                </button>
            </div>
        </div>

        <!-- Filters & Search Bar -->
        <div class="flat-card p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.users') }}" class="w-full md:w-96 flex items-center gap-2">
                <div class="relative w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name, email, or username..."
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                    <i class="fas fa-search absolute left-3 top-2.5 text-xs text-slate-400"></i>
                </div>
                <button type="submit" class="btn-rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-semibold px-3 py-2 transition-colors">
                    Filter
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.users') }}" class="text-xs text-slate-400 hover:text-red-500 font-medium">Reset</a>
                @endif
            </form>
            <div class="text-xs text-slate-500 font-medium">
                Total Accounts: <span class="font-bold text-slate-900 dark:text-slate-100">{{ $users->total() }}</span>
            </div>
        </div>

        <!-- Users Table -->
        <div class="flat-card p-5">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">User Details</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4 text-center">Expenses</th>
                                <th class="py-3 px-4 text-right">Total Logged</th>
                                <th class="py-3 px-4 text-right">Monthly Budget</th>
                                <th class="py-3 px-4 text-right">Joined</th>
                                <th class="py-3 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($users as $u)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $avatar = $u->profile_pic ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&color=ffffff&background=2563eb';
                                            @endphp
                                            <img src="{{ $avatar }}" alt="{{ $u->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                                    {{ $u->name }}
                                                    @if(strtolower(trim($u->email)) === 'sushantgautamlk6393@gmail.com')
                                                        <i class="fas fa-check-circle text-sky-500 text-xs" title="Master Admin"></i>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-400">
                                                    {{ $u->email }} <span class="text-slate-300 dark:text-slate-700">•</span> @<span>{{ $u->username ?? 'user' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full {{ $u->is_admin ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                            <i class="fas {{ $u->is_admin ? 'fa-shield-alt' : 'fa-user' }} text-[10px] mr-1"></i>
                                            {{ $u->is_admin ? 'ADMIN' : 'USER' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-700 dark:text-slate-300">
                                        {{ number_format($u->expenses_count) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-slate-100">
                                        ₹{{ number_format($u->expenses_sum_amount ?? 0, 2) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-slate-600 dark:text-slate-400">
                                        ₹{{ number_format($u->monthly_budget, 2) }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-xs text-slate-400 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($u->created_at)->format('M d, Y') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Edit Button -->
                                            <button
                                                type="button"
                                                @click="
                                                    editUser = {
                                                        id: {{ $u->id }},
                                                        name: '{{ addslashes($u->name) }}',
                                                        username: '{{ addslashes($u->username) }}',
                                                        email: '{{ addslashes($u->email) }}',
                                                        monthly_budget: {{ $u->monthly_budget }},
                                                        budget_warn_limit: {{ $u->budget_warn_limit }},
                                                        is_admin: {{ $u->is_admin ? 1 : 0 }}
                                                    };
                                                    showEditModal = true;
                                                "
                                                class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                                                title="Edit User"
                                            >
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            @if($u->id !== auth()->id() && strtolower(trim($u->email)) !== 'sushantgautamlk6393@gmail.com')
                                                <button
                                                    type="button"
                                                    @click="
                                                        deleteUser = {
                                                            id: {{ $u->id }},
                                                            name: '{{ addslashes($u->name) }}',
                                                            email: '{{ addslashes($u->email) }}'
                                                        };
                                                        showDeleteModal = true;
                                                    "
                                                    class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors"
                                                    title="Delete User Account"
                                                >
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            @else
                                                <span class="p-1.5 text-slate-300 dark:text-slate-700 cursor-not-allowed" title="Protected Account">
                                                    <i class="fas fa-lock text-xs"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                    {{ $users->links() }}
                </div>
            @else
                <x-empty-state
                    title="No users found"
                    description="No user accounts matched your search criteria."
                    icon="fas fa-users-slash"
                />
            @endif
        </div>

        <!-- ───────────────────────────────────────────────────────────── -->
        <!-- CREATE USER MODAL -->
        <!-- ───────────────────────────────────────────────────────────── -->
        <div
            x-show="showCreateModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click.away="showCreateModal = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-user-plus text-primary"></i> Add New User Account
                    </h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. John Doe" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Username</label>
                        <input type="text" name="username" required placeholder="e.g. johndoe" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input type="email" name="email" required placeholder="john@example.com" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Initial Password</label>
                        <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Monthly Budget (₹)</label>
                            <input type="number" step="100" name="monthly_budget" value="10000" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Role</label>
                            <select name="is_admin" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="0">Standard User</option>
                                <option value="1">Administrator</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-primary hover:bg-primary-dark text-white rounded-lg shadow-sm">Create Account</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ───────────────────────────────────────────────────────────── -->
        <!-- EDIT USER MODAL -->
        <!-- ───────────────────────────────────────────────────────────── -->
        <div
            x-show="showEditModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click.away="showEditModal = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-md p-6 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-user-edit text-primary"></i> Edit User Account
                    </h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="'{{ url('/admin/users') }}/' + editUser.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Full Name</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Username</label>
                        <input type="text" name="username" x-model="editUser.username" required class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                        <input type="email" name="email" x-model="editUser.email" required class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">New Password (leave empty to keep current)</label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Monthly Budget (₹)</label>
                            <input type="number" step="100" name="monthly_budget" x-model="editUser.monthly_budget" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Role</label>
                            <select name="is_admin" x-model="editUser.is_admin" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="0">Standard User</option>
                                <option value="1">Administrator</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-primary hover:bg-primary-dark text-white rounded-lg shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ───────────────────────────────────────────────────────────── -->
        <!-- DELETE USER CONFIRMATION MODAL -->
        <!-- ───────────────────────────────────────────────────────────── -->
        <div
            x-show="showDeleteModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div
                @click.away="showDeleteModal = false"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4 text-center"
            >
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto text-lg">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Delete User Account?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Are you sure you want to delete <span class="font-bold text-slate-900 dark:text-slate-100" x-text="deleteUser.name"></span> (<span x-text="deleteUser.email"></span>)? All user transactions and support messages will be permanently removed.
                    </p>
                </div>

                <form :action="'{{ url('/admin/users') }}/' + deleteUser.id" method="POST" class="pt-2 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm">Yes, Delete Account</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
