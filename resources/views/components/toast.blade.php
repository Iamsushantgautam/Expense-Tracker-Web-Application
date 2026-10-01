<!-- Top-Right Effective Toast Notification Container -->
<div
    x-data="toastManager()"
    @toast.window="addToast($event.detail)"
    class="fixed top-20 right-6 z-50 flex flex-col space-y-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="t.show"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full opacity-0 scale-95"
            x-transition:enter-end="translate-x-0 opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0 opacity-100 scale-100"
            x-transition:leave-end="translate-x-full opacity-0 scale-95"
            class="pointer-events-auto flex items-start gap-3.5 p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border shadow-xl transition-all duration-200"
            :class="{
                'border-l-4 border-l-emerald-500 border-y border-r border-emerald-500/20 text-emerald-950 dark:text-emerald-200': t.type === 'success',
                'border-l-4 border-l-rose-500 border-y border-r border-rose-500/20 text-rose-950 dark:text-rose-200': t.type === 'error',
                'border-l-4 border-l-sky-500 border-y border-r border-sky-500/20 text-sky-950 dark:text-sky-200': t.type === 'info',
                'border-l-4 border-l-amber-500 border-y border-r border-amber-500/20 text-amber-950 dark:text-amber-200': t.type === 'warning'
            }"
        >
            <!-- Toast Icon -->
            <div
                class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                :class="{
                    'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-400': t.type === 'success',
                    'bg-rose-100 text-rose-600 dark:bg-rose-950/80 dark:text-rose-400': t.type === 'error',
                    'bg-sky-100 text-sky-600 dark:bg-sky-950/80 dark:text-sky-400': t.type === 'info',
                    'bg-amber-100 text-amber-600 dark:bg-amber-950/80 dark:text-amber-400': t.type === 'warning'
                }"
            >
                <i
                    class="text-sm"
                    :class="{
                        'fas fa-check-circle': t.type === 'success',
                        'fas fa-exclamation-circle': t.type === 'error',
                        'fas fa-info-circle': t.type === 'info',
                        'fas fa-exclamation-triangle': t.type === 'warning'
                    }"
                ></i>
            </div>

            <!-- Toast Content -->
            <div class="flex-1 min-w-0 pt-0.5">
                <h4
                    class="text-xs font-bold uppercase tracking-wider mb-0.5"
                    :class="{
                        'text-emerald-700 dark:text-emerald-400': t.type === 'success',
                        'text-rose-700 dark:text-rose-400': t.type === 'error',
                        'text-sky-700 dark:text-sky-400': t.type === 'info',
                        'text-amber-700 dark:text-amber-400': t.type === 'warning'
                    }"
                    x-text="t.title"
                ></h4>
                <p class="text-xs font-medium text-slate-700 dark:text-slate-300 leading-snug break-words" x-html="t.message"></p>
            </div>

            <!-- Close Button -->
            <button
                type="button"
                @click="removeToast(t.id)"
                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors p-1 -mr-1 rounded-md"
            >
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            init() {
                // Flash messages from Laravel Session
                @if(session('success'))
                    this.addToast({ type: 'success', title: 'Success', message: '{!! addslashes(session('success')) !!}' });
                @endif
                @if(session('error'))
                    this.addToast({ type: 'error', title: 'Error', message: '{!! addslashes(session('error')) !!}' });
                @endif
                @if(session('info'))
                    this.addToast({ type: 'info', title: 'Information', message: '{!! addslashes(session('info')) !!}' });
                @endif
                @if(session('status'))
                    this.addToast({ type: 'success', title: 'Status', message: '{!! addslashes(session('status')) !!}' });
                @endif
            },
            addToast(data) {
                const id = Date.now() + Math.random();
                const type = data.type || 'info';
                const title = data.title || (type.charAt(0).toUpperCase() + type.slice(1));
                const message = data.message || '';

                const toast = { id, type, title, message, show: true };
                this.toasts.push(toast);

                setTimeout(() => {
                    this.removeToast(id);
                }, 4500);
            },
            removeToast(id) {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index !== -1) {
                    this.toasts[index].show = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 250);
                }
            }
        }
    }
</script>
