<x-guest-layout title="Verify OTP">
    <div class="w-full">

        <!-- Header -->
        <div class="mb-6 text-left">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-primary/10 text-primary mb-4">
                <i class="fas fa-shield-halved text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Enter your OTP</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                We've sent a 6-digit code to
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ Str::mask($email, '*', 2, strlen($email) - 6) }}</span>.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-sm text-rose-700 dark:text-rose-300 flex items-start gap-2">
                <i class="fas fa-circle-exclamation mt-0.5 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.verify.submit') }}" id="otpForm" class="space-y-5">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="otp" id="otpHidden">

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">
                    6-Digit OTP Code
                </label>

                <!-- OTP digit boxes -->
                <div class="flex gap-2.5 sm:gap-3 justify-between" id="otpBoxes">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            inputmode="numeric"
                            maxlength="1"
                            data-index="{{ $i }}"
                            class="otp-digit w-full aspect-square text-center text-2xl font-bold rounded-xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-150"
                            autocomplete="off"
                        >
                    @endfor
                </div>

                <!-- Countdown -->
                <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                    <span>Code expires in</span>
                    <span id="countdown" class="font-semibold text-primary">10:00</span>
                </div>
            </div>

            <x-button type="submit" variant="primary" size="lg" class="w-full font-semibold" icon="fas fa-check-circle">
                Verify OTP
            </x-button>
        </form>

        <div class="mt-5 text-center text-xs text-slate-500 dark:text-slate-400">
            Didn't receive the code?
            <a href="{{ route('password.request') }}" class="font-semibold text-primary hover:underline ml-1">Send again</a>
        </div>

    </div>

    <script>
        // ── OTP digit boxes logic ──
        const boxes = document.querySelectorAll('.otp-digit');
        const hidden = document.getElementById('otpHidden');
        const form   = document.getElementById('otpForm');

        function collectOtp() {
            hidden.value = Array.from(boxes).map(b => b.value).join('');
        }

        boxes.forEach((box, idx) => {
            box.addEventListener('input', (e) => {
                const val = e.target.value.replace(/\D/g, '');
                box.value = val ? val.slice(-1) : '';
                collectOtp();
                if (val && idx < boxes.length - 1) boxes[idx + 1].focus();
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    boxes[idx - 1].focus();
                }
            });

            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData)
                    .getData('text').replace(/\D/g, '').slice(0, 6);
                paste.split('').forEach((ch, i) => { if (boxes[i]) boxes[i].value = ch; });
                collectOtp();
                const next = boxes[Math.min(paste.length, boxes.length - 1)];
                if (next) next.focus();
            });
        });

        form.addEventListener('submit', () => collectOtp());

        // ── Countdown timer (10 min) ──
        let secs = 600;
        const el = document.getElementById('countdown');
        const tick = setInterval(() => {
            secs--;
            const m = String(Math.floor(secs / 60)).padStart(2, '0');
            const s = String(secs % 60).padStart(2, '0');
            el.textContent = m + ':' + s;
            if (secs <= 0) {
                clearInterval(tick);
                el.textContent = 'Expired';
                el.className = 'font-semibold text-rose-500';
            }
        }, 1000);
    </script>
</x-guest-layout>
