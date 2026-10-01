<x-app-layout title="Help & Support" active="support">

    {{-- ══════════════════════════════════════════
        PAGE HEADER
    ══════════════════════════════════════════ --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center shrink-0">
                <i class="fas fa-headset text-primary text-sm"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Help & Support</h1>
                <p class="text-xs text-slate-400">Need help or have questions? Contact our team directly.</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
        MAIN GRID
    ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        {{-- ─── LEFT: Submit Ticket Form (2 cols) ─── --}}
        <div class="lg:col-span-2 space-y-5">

            <form method="POST" action="{{ route('support.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input name="name"  label="Your Name"      :value="$user->name"  required />
                    <x-input name="email" label="Email Address"   type="email" :value="$user->email" required />
                </div>

                <x-input
                    name="subject"
                    label="Subject / Topic"
                    placeholder="Brief summary of your query"
                    required
                />

                <div>
                    <label for="message" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Message
                    </label>
                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        placeholder="Describe your issue or feedback in detail..."
                        required
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors resize-none"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ── Attachment drop zone ── --}}
                {{-- ── Multi-image drop zone (up to 5, auto-WebP) ── --}}
                <div x-data="{
                        files: [],
                        dragging: false,
                        fmt(bytes) {
                            return bytes > 1048576
                                ? (bytes/1048576).toFixed(1)+' MB'
                                : (bytes/1024).toFixed(0)+' KB';
                        },
                        addFiles(fileList) {
                            const allowed = 5 - this.files.length;
                            Array.from(fileList).slice(0, allowed).forEach(f => {
                                if (!f.type.startsWith('image/')) return;
                                const reader = new FileReader();
                                reader.onload = e => {
                                    this.files.push({ file: f, preview: e.target.result, size: this.fmt(f.size) });
                                };
                                reader.readAsDataURL(f);
                            });
                        },
                        remove(i) {
                            this.files.splice(i, 1);
                            this.syncInput();
                        },
                        syncInput() {
                            const dt = new DataTransfer();
                            this.files.forEach(f => dt.items.add(f.file));
                            $refs.fileInput.files = dt.files;
                        },
                        onDrop(e) {
                            this.dragging = false;
                            this.addFiles(e.dataTransfer.files);
                            this.syncInput();
                        },
                        onChange(e) {
                            this.addFiles(e.target.files);
                            this.syncInput();
                        }
                    }">

                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Screenshots / Attachments
                            <span class="normal-case font-normal text-slate-400">(Optional · max 5 images)</span>
                        </label>
                        <span class="text-[10px] font-bold text-primary" x-show="files.length > 0" x-text="files.length + '/5'"></span>
                    </div>

                    {{-- Drop zone — always visible, dims when full --}}
                    <div @dragover.prevent="dragging = true"
                         @dragleave.prevent="dragging = false"
                         @drop.prevent="onDrop($event)"
                         @click="files.length < 5 && $refs.fileInput.click()"
                         :class="[
                             dragging ? 'border-primary bg-primary/5 dark:bg-primary/10 scale-[1.01]' : '',
                             files.length >= 5
                                 ? 'border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/20 cursor-not-allowed opacity-60'
                                 : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 hover:border-primary hover:bg-primary/5 dark:hover:bg-primary/10 cursor-pointer'
                         ]"
                         class="flex flex-col items-center justify-center gap-3 py-6 px-4 rounded-xl border-2 border-dashed transition-all duration-200 select-none">

                        <div class="w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 flex items-center justify-center">
                            <i class="fas fa-cloud-arrow-up text-primary text-lg"></i>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <span x-show="files.length < 5">Drop images here or <span class="text-primary">browse</span></span>
                                <span x-show="files.length >= 5" class="text-slate-400">Maximum 5 images reached</span>
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">PNG, JPG, GIF, WEBP — max 5 MB each · auto-converted to WebP</p>
                        </div>
                    </div>

                    {{-- Preview grid --}}
                    <div x-show="files.length > 0" class="mt-3 grid grid-cols-5 gap-2">
                        <template x-for="(item, i) in files" :key="i">
                            <div class="relative group">
                                <div class="aspect-square rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                                    <img :src="item.preview" class="w-full h-full object-cover">
                                </div>
                                {{-- File size label --}}
                                <p class="text-[9px] text-slate-400 text-center mt-0.5 truncate" x-text="item.size"></p>
                                {{-- Remove button --}}
                                <button type="button" @click.stop="remove(i)"
                                        class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                    <i class="fas fa-xmark text-[9px]"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Hidden input (multiple) --}}
                    <input x-ref="fileInput" type="file" name="attachments[]" id="attachments"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           multiple class="hidden"
                           @change="onChange($event)">

                    @error('attachments')
                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end pt-1">
                    <x-button type="submit" variant="primary" icon="fas fa-paper-plane">
                        Submit Support Ticket
                    </x-button>
                </div>
            </form>
        </div>

        {{-- ─── RIGHT: Ticket History (1 col) ─── --}}
        <div class="space-y-5">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center shrink-0">
                    <i class="fas fa-ticket text-emerald-500 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Your Tickets</h2>
                    <p class="text-xs text-slate-400">History of submitted support requests.</p>
                </div>
            </div>

            @if($messages->count() > 0)
                <div class="space-y-3">
                    @foreach($messages as $msg)
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-primary/40 dark:hover:border-primary/30 transition-colors duration-150 space-y-2.5">

                            {{-- Ticket number + status --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 font-mono text-[10px] font-bold text-primary bg-primary/8 dark:bg-primary/15 px-2 py-0.5 rounded-md">
                                    <i class="fas fa-hashtag text-[8px]"></i>
                                    {{ $msg->ticket_number ?? 'TKT-??????' }}
                                </span>
                                <span class="shrink-0 px-2.5 py-0.5 text-[10px] font-bold rounded-full
                                    {{ $msg->status === 'resolved'
                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400' }}">
                                    {{ strtoupper($msg->status) }}
                                </span>
                            </div>

                            {{-- Subject --}}
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200 leading-snug">
                                {{ $msg->subject }}
                            </p>

                            {{-- Message preview --}}
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">
                                {{ $msg->message }}
                            </p>

                            {{-- Footer: attachments + time --}}
                            <div class="flex items-center justify-between pt-1">
                                @php $urls = $msg->attachment_urls ?? []; @endphp
                                @if(count($urls) > 0)
                                    <button type="button"
                                            onclick="this.nextElementSibling.classList.toggle('hidden')"
                                            class="inline-flex items-center gap-1.5 text-[11px] text-primary hover:underline font-semibold">
                                        <i class="fas fa-images text-[9px]"></i>
                                        {{ count($urls) }} attachment{{ count($urls) > 1 ? 's' : '' }}
                                    </button>
                                @else
                                    <span></span>
                                @endif
                                <p class="text-[10px] text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}
                                </p>
                            </div>

                            {{-- Image grid (toggled) --}}
                            @if(count($urls ?? []) > 0)
                                <div class="hidden mt-2 grid grid-cols-3 gap-2">
                                    @foreach($urls as $url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                           class="block aspect-video rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 hover:opacity-90 transition-opacity">
                                            <img src="{{ $url }}" class="w-full h-full object-cover" loading="lazy">
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="No tickets yet"
                    description="You haven't submitted any support requests yet."
                    icon="fas fa-ticket-alt"
                />
            @endif

        </div>
    </div>

</x-app-layout>
