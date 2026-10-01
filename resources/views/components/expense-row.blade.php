@props([
    'expense'
])

<tr class="border-b border-slate-200 dark:border-slate-800 hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors duration-150 text-sm">
    <td class="py-3.5 px-4 font-medium text-slate-900 dark:text-slate-100">
        <div class="flex items-center gap-3">
            <x-category-badge :name="$expense->category" :color="$expense->categoryModel->color ?? '#2563eb'" :icon="$expense->categoryModel->icon ?? 'tag'" />
            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $expense->title }}</span>
        </div>
    </td>
    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
        {{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}
    </td>
    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-mono bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 btn-rounded">
            {{ $expense->payment_method }}
        </span>
    </td>
    <td class="py-3.5 px-4 font-semibold font-mono text-right text-red-600 dark:text-red-400 whitespace-nowrap">
        − ₹{{ number_format($expense->amount, 2) }}
    </td>
    <td class="py-3.5 px-4 text-right whitespace-nowrap">
        <div class="flex items-center justify-end gap-2">
            @if($expense->receipt_url)
                <a href="{{ $expense->receipt_url }}" target="_blank" class="p-1.5 text-slate-400 hover:text-primary transition-colors" title="View Receipt">
                    <i class="fas fa-paperclip text-sm"></i>
                </a>
            @endif
            <a href="{{ route('expenses.edit', $expense->id) }}" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors" title="Edit Expense">
                <i class="fas fa-pen text-sm"></i>
            </a>
            <button
                type="button"
                x-data
                @click="$dispatch('open-delete-modal', { id: {{ $expense->id }}, title: '{{ addslashes($expense->title) }}', action: '{{ route('expenses.destroy', $expense->id) }}' })"
                class="p-1.5 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                title="Delete Expense"
            >
                <i class="fas fa-trash-alt text-sm"></i>
            </button>
        </div>
    </td>
</tr>
