@php
    $categories = $categories ?? $expenseCategories ?? \App\Models\Category::where(function ($q) {
        $q->whereNull('user_id')->orWhere('user_id', auth()->id());
    })->where(function ($q) {
        $q->where('type', 'expense')->orWhereNull('type');
    })->get();
    $addOpen = $addModalVar ?? 'addModalOpen';
    $editOpen = $editModalVar ?? 'editModalOpen';
@endphp

        <!-- ADD EXPENSE MODAL -->
        <div 
            x-show="{{ $addOpen }}" 
            x-cloak
            @open-add-expense-modal.window="{{ $addOpen }} = true"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            @keydown.escape.window="{{ $addOpen }} = false"
        >
            <div 
                @click.away="{{ $addOpen }} = false"
                x-show="{{ $addOpen }}"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded shadow-2xl max-w-lg w-full p-6 space-y-5 overflow-y-auto max-h-[90vh]"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm font-bold shrink-0">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Add New Expense</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Record a new financial outgoing transaction</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="{{ $addOpen }} = false" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Amount & Title -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input
                            name="amount"
                            label="Amount (₹)"
                            type="number"
                            step="0.01"
                            placeholder="0.00"
                            icon="fas fa-rupee-sign"
                            required
                        />
                        <x-input
                            name="title"
                            label="Expense Title"
                            placeholder="e.g. Lunch at Restaurant"
                            icon="fas fa-heading"
                            required
                        />
                    </div>

                    <!-- Category & Payment Method -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-select
                            name="category_id"
                            label="Category"
                            placeholder="Select Category"
                            required
                        >
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select
                            name="payment_method"
                            label="Payment Method"
                            placeholder="Select Payment Method"
                            required
                            :selected="old('payment_method', 'UPI')"
                        >
                            <option value="UPI">UPI / GPay / PhonePe</option>
                            <option value="Card">Debit / Credit Card</option>
                            <option value="Cash">Cash</option>
                            <option value="Net Banking">Net Banking</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Other">Other</option>
                        </x-select>
                    </div>

                    <!-- Date -->
                    <x-input
                        name="date"
                        label="Transaction Date"
                        type="date"
                        :value="old('date', date('Y-m-d'))"
                        required
                    />

                    <!-- Notes -->
                    <div>
                        <label for="add-expense-notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Notes / Description
                        </label>
                        <textarea
                            id="add-expense-notes"
                            name="notes"
                            rows="2"
                            placeholder="Add additional transaction details or context..."
                            class="w-full px-3.5 py-2 text-xs bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20 outline-none transition-colors"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Receipt Upload -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Receipt / Bill Document <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <input
                            type="file"
                            name="receipt"
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                            class="w-full px-3.5 py-2 text-xs text-slate-500 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg cursor-pointer file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-600 dark:file:bg-rose-950/40 dark:file:text-rose-400 hover:file:bg-rose-100 transition-all"
                        />
                        @error('receipt')
                            <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button 
                            type="button" 
                            @click="{{ $addOpen }} = false" 
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all"
                        >
                            Cancel
                        </button>
                        <x-button type="submit" variant="primary" class="font-semibold text-xs py-2 px-4 shadow-xs flex items-center gap-2" icon="fas fa-check">
                            Save Expense
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT EXPENSE MODAL -->
        <div 
            x-show="{{ $editOpen }}" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            @keydown.escape.window="{{ $editOpen }} = false"
        >
            <div 
                @click.away="{{ $editOpen }} = false"
                x-show="{{ $editOpen }}"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded shadow-2xl max-w-lg w-full p-6 space-y-5 overflow-y-auto max-h-[90vh]"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-bold shrink-0">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Edit Expense</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="'Updating: ' + (editExpense.title || '...')"></p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="{{ $editOpen }} = false" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" :action="editExpense.updateUrl" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Amount & Title -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Amount (₹) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"><i class="fas fa-rupee-sign"></i></span>
                                <input type="number" name="amount" step="0.01" x-model="editExpense.amount" required
                                    class="w-full pl-8 pr-3.5 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Expense Title <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"><i class="fas fa-heading"></i></span>
                                <input type="text" name="title" x-model="editExpense.title" required
                                    class="w-full pl-8 pr-3.5 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
                            </div>
                        </div>
                    </div>

                    <!-- Category & Payment Method -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" x-model="editExpense.category_id" required
                                class="w-full px-3.5 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" x-model="editExpense.payment_method" required
                                class="w-full px-3.5 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                <option value="UPI">UPI / GPay / PhonePe</option>
                                <option value="Card">Debit / Credit Card</option>
                                <option value="Cash">Cash</option>
                                <option value="Net Banking">Net Banking</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Transaction Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date" x-model="editExpense.date" required
                            class="w-full px-3.5 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" />
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="edit-expense-notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Notes / Description
                        </label>
                        <textarea
                            id="edit-expense-notes"
                            name="notes"
                            rows="2"
                            x-model="editExpense.notes"
                            placeholder="Add additional transaction details..."
                            class="w-full px-3.5 py-2 text-xs bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20 outline-none transition-colors"
                        ></textarea>
                    </div>

                    <!-- Existing Receipt -->
                    <div x-show="editExpense.receiptUrl">
                        <div class="p-2.5 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-between text-xs mb-2">
                            <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium">
                                <i class="fas fa-paperclip text-primary"></i> Current Receipt Attached
                            </span>
                            <a :href="editExpense.receiptUrl" target="_blank" class="text-primary hover:underline font-semibold">View →</a>
                        </div>
                    </div>

                    <!-- Receipt Upload -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Replace Receipt <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <input
                            type="file"
                            name="receipt"
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                            class="w-full px-3.5 py-2 text-xs text-slate-500 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg cursor-pointer file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-slate-200 transition-all"
                        />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button
                            type="button"
                            x-data
                            @click="$dispatch('open-delete-modal', { id: editExpense.id, title: editExpense.title, action: editExpense.deleteUrl }); {{ $editOpen }} = false"
                            class="text-xs text-red-600 dark:text-red-400 font-semibold hover:underline flex items-center gap-1.5"
                        >
                            <i class="fas fa-trash-alt"></i> Delete Expense
                        </button>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                @click="{{ $editOpen }} = false" 
                                class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all"
                            >
                                Cancel
                            </button>
                            <x-button type="submit" variant="primary" class="font-semibold text-xs py-2 px-4 shadow-xs flex items-center gap-2" icon="fas fa-save">
                                Update Expense
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
