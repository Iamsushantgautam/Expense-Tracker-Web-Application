<x-app-layout title="Admin - Support Management" active="admin">
    <div class="space-y-6" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editTicket: { id: null, ticket_number: '', name: '', email: '', subject: '', message: '', status: 'open' },
        deleteTicket: { id: null, ticket_number: '', subject: '' }
    }">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i class="fas fa-headset text-primary"></i> Support Ticket Management
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Review, resolve, edit, or create support tickets submitted by users
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    @click="showCreateModal = true"
                    class="btn-rounded bg-primary hover:bg-primary-dark text-white text-xs font-semibold px-4 py-2.5 flex items-center gap-2 shadow-sm transition-all duration-150 active:scale-95 cursor-pointer"
                >
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Log New Ticket</span>
                </button>
            </div>
        </div>

        <!-- Filters & Search Bar -->
        <div class="flat-card p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Status Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl">
                <a
                    href="{{ route('admin.support', ['status' => 'all', 'search' => request('search')]) }}"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $activeStatus === 'all' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
                >
                    All Tickets
                </a>
                <a
                    href="{{ route('admin.support', ['status' => 'open', 'search' => request('search')]) }}"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $activeStatus === 'open' ? 'bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
                >
                    Pending / Open
                </a>
                <a
                    href="{{ route('admin.support', ['status' => 'resolved', 'search' => request('search')]) }}"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $activeStatus === 'resolved' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}"
                >
                    Resolved
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.support') }}" class="w-full md:w-80 flex items-center gap-2">
                <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                <div class="relative w-full">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search ticket #, subject, message..."
                        class="w-full pl-9 pr-4 py-2 text-xs rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                    >
                    <i class="fas fa-search absolute left-3 top-2.5 text-xs text-slate-400"></i>
                </div>
                <button type="submit" class="btn-rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 text-xs font-semibold px-3 py-2 transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Support Tickets Table -->
        <div class="flat-card p-5">
            @if($messages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">Ticket #</th>
                                <th class="py-3 px-4">User & Contact</th>
                                <th class="py-3 px-4">Subject & Message</th>
                                <th class="py-3 px-4">Submitted Date</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($messages as $msg)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-xs text-primary whitespace-nowrap">
                                        {{ $msg->ticket_number ?? ('TKT-' . sprintf('%05d', $msg->id)) }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <p class="font-bold text-slate-900 dark:text-slate-100 text-xs">{{ $msg->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $msg->email }}</p>
                                    </td>
                                    <td class="py-3.5 px-4 max-w-md">
                                        <p class="font-semibold text-slate-800 dark:text-slate-200 text-xs mb-0.5">{{ $msg->subject }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">{{ $msg->message }}</p>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs font-mono text-slate-400 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($msg->created_at)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full {{ $msg->status === 'resolved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                                            <i class="fas {{ $msg->status === 'resolved' ? 'fa-check-circle' : 'fa-clock' }} text-[10px] mr-1"></i>
                                            {{ strtoupper($msg->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Toggle Status Form -->
                                            <form method="POST" action="{{ route('admin.support.update', $msg->id) }}" class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                @if($msg->status === 'open')
                                                    <input type="hidden" name="status" value="resolved">
                                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors" title="Mark as Resolved">
                                                        Resolve
                                                    </button>
                                                @else
                                                    <input type="hidden" name="status" value="open">
                                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Reopen Ticket">
                                                        Reopen
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Edit Ticket Button -->
                                            <button
                                                type="button"
                                                @click="
                                                    editTicket = {
                                                        id: {{ $msg->id }},
                                                        ticket_number: '{{ addslashes($msg->ticket_number ?? 'TKT-' . $msg->id) }}',
                                                        name: '{{ addslashes($msg->name) }}',
                                                        email: '{{ addslashes($msg->email) }}',
                                                        subject: '{{ addslashes($msg->subject) }}',
                                                        message: '{{ addslashes(str_replace(["\r", "\n"], [' ', ' '], $msg->message)) }}',
                                                        status: '{{ $msg->status }}'
                                                    };
                                                    showEditModal = true;
                                                "
                                                class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                                                title="Edit Ticket"
                                            >
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>

                                            <!-- Delete Ticket Button -->
                                            <button
                                                type="button"
                                                @click="
                                                    deleteTicket = {
                                                        id: {{ $msg->id }},
                                                        ticket_number: '{{ addslashes($msg->ticket_number ?? 'TKT-' . $msg->id) }}',
                                                        subject: '{{ addslashes($msg->subject) }}'
                                                    };
                                                    showDeleteModal = true;
                                                "
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-colors"
                                                title="Delete Ticket"
                                            >
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                    {{ $messages->links() }}
                </div>
            @else
                <x-empty-state
                    title="No support tickets found"
                    description="No support queries matched your current filter criteria."
                    icon="fas fa-headset"
                />
            @endif
        </div>

        <!-- ───────────────────────────────────────────────────────────── -->
        <!-- CREATE TICKET MODAL -->
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
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-primary"></i> Log New Support Ticket
                    </h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.support.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">User Name</label>
                            <input type="text" name="name" required placeholder="User Name" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                            <input type="email" name="email" required placeholder="user@example.com" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Subject</label>
                        <input type="text" name="subject" required placeholder="Brief description of issue..." class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Message Content</label>
                        <textarea name="message" rows="4" required placeholder="Detailed message description..." class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Initial Ticket Status</label>
                        <select name="status" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="open">Pending / Open</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-primary hover:bg-primary-dark text-white rounded-lg shadow-sm">Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ───────────────────────────────────────────────────────────── -->
        <!-- EDIT TICKET MODAL -->
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
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-lg p-6 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i class="fas fa-edit text-primary"></i> Edit Ticket <span class="font-mono text-primary" x-text="editTicket.ticket_number"></span>
                    </h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form :action="'{{ url('/admin/support') }}/' + editTicket.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">User Name</label>
                            <input type="text" name="name" x-model="editTicket.name" required class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                            <input type="email" name="email" x-model="editTicket.email" required class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Subject</label>
                        <input type="text" name="subject" x-model="editTicket.subject" required class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Message Content</label>
                        <textarea name="message" rows="4" x-model="editTicket.message" required class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ticket Status</label>
                        <select name="status" x-model="editTicket.status" class="w-full text-xs px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <option value="open">Pending / Open</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-primary hover:bg-primary-dark text-white rounded-lg shadow-sm">Save Ticket Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ───────────────────────────────────────────────────────────── -->
        <!-- DELETE TICKET CONFIRMATION MODAL -->
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
                    <i class="fas fa-trash-alt"></i>
                </div>

                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Delete Support Ticket?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Are you sure you want to delete ticket <span class="font-bold font-mono text-slate-900 dark:text-slate-100" x-text="deleteTicket.ticket_number"></span>? This action cannot be undone.
                    </p>
                </div>

                <form :action="'{{ url('/admin/support') }}/' + deleteTicket.id" method="POST" class="pt-2 flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm">Yes, Delete Ticket</button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
