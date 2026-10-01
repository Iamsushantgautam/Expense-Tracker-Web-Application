@props([
    'transaction' => null,
])

@php
    $txn = $transaction;
    if (!$txn) return;

    // Determine if transaction is Income or Expense
    if (isset($txn->transaction_type)) {
        $isIncome = $txn->transaction_type === 'income';
    } elseif ($txn instanceof \App\Models\Income || isset($txn->source)) {
        $isIncome = true;
    } else {
        $isIncome = false;
    }

    $title = $txn->title ?? 'Untitled Transaction';
    $amount = (float) ($txn->amount ?? 0);
    $categoryOrSource = $txn->category_or_source ?? $txn->category ?? $txn->source ?? 'General';
    $date = $txn->date ?? now();
    $paymentMethod = $txn->payment_method ?? 'Cash';
    $notes = $txn->notes ?? null;
    $receiptUrl = $txn->receipt_url ?? null;
    $id = $txn->id;

    $editUrl = $isIncome ? route('incomes.edit', $id) : route('expenses.edit', $id);
    $deleteUrl = $isIncome ? route('incomes.destroy', $id) : route('expenses.destroy', $id);

    // Contextual icon picker based on category icon or name
    $catIcon = null;
    if (isset($txn->category_icon) && !empty($txn->category_icon)) {
        $catIcon = $txn->category_icon;
    } elseif (isset($txn->category) && is_object($txn->category) && !empty($txn->category->icon)) {
        $catIcon = $txn->category->icon;
    }

    if ($catIcon) {
        $iconClass = str_starts_with($catIcon, 'fa') ? $catIcon : 'fas fa-' . $catIcon;
    } else {
        $lowerCat = strtolower($categoryOrSource);
        if ($isIncome) {
            $iconClass = 'fas fa-arrow-down';
            if (str_contains($lowerCat, 'salary')) $iconClass = 'fas fa-wallet';
            elseif (str_contains($lowerCat, 'freelance')) $iconClass = 'fas fa-laptop';
            elseif (str_contains($lowerCat, 'investment')) $iconClass = 'fas fa-chart-line';
            elseif (str_contains($lowerCat, 'bonus') || str_contains($lowerCat, 'gift')) $iconClass = 'fas fa-gift';
            elseif (str_contains($lowerCat, 'business')) $iconClass = 'fas fa-briefcase';
        } else {
            $iconClass = 'fas fa-arrow-up';
            if (str_contains($lowerCat, 'food') || str_contains($lowerCat, 'dining')) $iconClass = 'fas fa-utensils';
            elseif (str_contains($lowerCat, 'grocery') || str_contains($lowerCat, 'shopping')) $iconClass = 'fas fa-shopping-cart';
            elseif (str_contains($lowerCat, 'wifi') || str_contains($lowerCat, 'data') || str_contains($lowerCat, 'bill') || str_contains($lowerCat, 'util')) $iconClass = 'fas fa-wifi';
            elseif (str_contains($lowerCat, 'phone') || str_contains($lowerCat, 'mobile') || str_contains($lowerCat, 'airtime')) $iconClass = 'fas fa-mobile-alt';
            elseif (str_contains($lowerCat, 'transport') || str_contains($lowerCat, 'car')) $iconClass = 'fas fa-car';
            elseif (str_contains($lowerCat, 'health')) $iconClass = 'fas fa-heartbeat';
            elseif (str_contains($lowerCat, 'subscrip')) $iconClass = 'fas fa-sync';
        }
    }
@endphp

<div class="group relative px-4 py-3.5 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800/80 hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-all duration-200 flex items-center justify-between gap-4">
    
    <!-- Left Section: Circular Icon + Title/Subtitles -->
    <div class="flex items-center gap-3.5 min-w-0 flex-1">
        <!-- Circular Soft Avatar Icon -->
        <div class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center text-sm font-semibold transition-transform group-hover:scale-105 {{ $isIncome ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50' : 'bg-rose-50 text-rose-500 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50' }}">
            <i class="{{ $iconClass }}"></i>
        </div>

        <!-- Title & Sub-details -->
        <div class="min-w-0 space-y-0.5 flex-1">
            <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate group-hover:text-primary transition-colors">
                {{ $title }}
            </h4>

            <!-- Date & Payment Method Subtitle -->
            <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                <span>{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</span>
                <span class="mx-1">•</span>
                <span>{{ $paymentMethod }}</span>
                @if($notes)
                    <span class="mx-1">•</span>
                    <span class="italic">"{{ $notes }}"</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Right Section: Amount + Category Subtitle & Hover Actions -->
    <div class="flex items-center gap-4 shrink-0">
        <!-- Amount & Category Name -->
        <div class="text-right">
            <p class="text-sm font-bold font-mono tracking-tight {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                {{ $isIncome ? '+' : '-' }}₹{{ number_format($amount, 2) }}
            </p>
            <p class="text-[11px] font-medium {{ $isIncome ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">
                {{ $categoryOrSource }}
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex items-center gap-1 opacity-70 group-hover:opacity-100 transition-opacity">
            <button
                type="button"
                x-data
                data-event="{{ $isIncome ? 'open-edit-income' : 'open-edit-expense' }}"
                data-id="{{ $id }}"
                data-title="{{ addslashes($title) }}"
                data-amount="{{ $amount }}"
                data-field="{{ $isIncome ? 'source' : 'category_id' }}"
                data-field-value="{{ $isIncome ? addslashes($categoryOrSource) : ($txn->category_id ?? '') }}"
                data-payment="{{ addslashes($paymentMethod) }}"
                data-date="{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}"
                data-notes="{{ addslashes($notes ?? '') }}"
                data-receipt="{{ $receiptUrl ?? '' }}"
                data-update-url="{{ $editUrl }}"
                data-delete-url="{{ $deleteUrl }}"
                @click="
                    const d = $el.dataset;
                    const payload = {
                        id: d.id,
                        title: d.title,
                        amount: d.amount,
                        payment_method: d.payment,
                        date: d.date,
                        notes: d.notes,
                        receiptUrl: d.receipt || null,
                        updateUrl: d.updateUrl,
                        deleteUrl: d.deleteUrl
                    };
                    payload[d.field] = d.fieldValue;
                    $dispatch(d.event, payload);
                "
                class="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-primary hover:bg-slate-200/60 dark:hover:bg-slate-700 transition-all"
                title="Edit Transaction"
            >
                <i class="fas fa-pen text-[10px]"></i>
            </button>

            @if($receiptUrl)
                <a
                    href="{{ $receiptUrl }}"
                    target="_blank"
                    class="w-7 h-7 rounded-full flex items-center justify-center text-primary hover:bg-primary/10 transition-all"
                    title="View Receipt"
                >
                    <i class="fas fa-paperclip text-[10px]"></i>
                </a>
            @endif

            <button
                type="button"
                x-data
                @click="$dispatch('open-delete-modal', { id: {{ $id }}, title: '{{ addslashes($title) }}', action: '{{ $deleteUrl }}' })"
                class="w-7 h-7 rounded-full flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-100/60 dark:hover:bg-rose-950/40 transition-all"
                title="Delete Transaction"
            >
                <i class="fas fa-trash-alt text-[10px]"></i>
            </button>
        </div>
    </div>

</div>
