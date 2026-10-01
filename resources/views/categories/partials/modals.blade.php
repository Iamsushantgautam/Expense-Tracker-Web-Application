        <!-- ADD CATEGORY MODAL -->
        <div 
            x-show="addModalOpen" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            @keydown.escape.window="closeAddModal()"
        >
            <div 
                @click.away="closeAddModal()"
                x-show="addModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded shadow-2xl max-w-md w-full p-6 space-y-5 overflow-hidden"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-bold shrink-0">
                            <i class="fas fa-folder-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Add New Category</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Create a custom expense or income category</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="closeAddModal()" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                    @csrf

                    <!-- 1. Category Type Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Category Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                @click="addCategory.type = 'expense'"
                                :class="addCategory.type === 'expense' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-500 text-rose-700 dark:text-rose-300 font-bold ring-2 ring-rose-500/20' : 'bg-slate-50 dark:bg-slate-950/60 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                                class="p-2.5 btn-rounded border text-xs cursor-pointer flex items-center justify-center gap-2 transition-all"
                            >
                                <input type="radio" name="type" value="expense" x-model="addCategory.type" class="hidden">
                                <i class="fas fa-arrow-up-right text-xs"></i>
                                <span>Expense</span>
                            </label>
                            <label
                                @click="addCategory.type = 'income'"
                                :class="addCategory.type === 'income' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-700 dark:text-emerald-300 font-bold ring-2 ring-emerald-500/20' : 'bg-slate-50 dark:bg-slate-950/60 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                                class="p-2.5 btn-rounded border text-xs cursor-pointer flex items-center justify-center gap-2 transition-all"
                            >
                                <input type="radio" name="type" value="income" x-model="addCategory.type" class="hidden">
                                <i class="fas fa-arrow-down-left text-xs"></i>
                                <span>Income</span>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Category Name Input -->
                    <div>
                        <label for="add-category-name-input" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="add-category-name-input"
                            type="text"
                            name="name"
                            x-model="addCategory.name"
                            placeholder="e.g. Subscriptions, Freelance, Software"
                            required
                            class="w-full px-3.5 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        />
                        @error('name')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. Color Picker + Quick Swatches -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Category Color
                        </label>
                        <div class="space-y-2.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <template x-for="color in presetColors" :key="color">
                                    <button 
                                        type="button"
                                        @click="addCategory.color = color"
                                        :style="'background-color: ' + color"
                                        :class="addCategory.color === color ? 'scale-110 ring-2 ring-offset-2 ring-slate-400 dark:ring-slate-500' : 'hover:scale-105 opacity-85 hover:opacity-100'"
                                        class="w-6 h-6 rounded-full transition-all cursor-pointer shadow-xs border border-white/20"
                                    ></button>
                                </template>
                            </div>

                            <div class="flex items-center gap-3 pt-1">
                                <input
                                    type="color"
                                    name="color"
                                    x-model="addCategory.color"
                                    class="w-9 h-9 rounded-lg border border-slate-300 dark:border-slate-700 cursor-pointer p-0.5 bg-slate-50 dark:bg-slate-900 shrink-0"
                                />
                                <span class="text-xs font-mono text-slate-600 dark:text-slate-400 uppercase tracking-wider bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700" x-text="addCategory.color"></span>
                                <span class="text-[11px] text-slate-400">Custom hex code</span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Category Icon Selector (Custom Alpine Dropdown) -->
                    <div x-data="{
                        open: false,
                        iconOptions: [
                            { val: 'tag', label: 'Tag / General', icon: 'tag', emoji: '🏷️' },
                            { val: 'wallet', label: 'Wallet / Cash', icon: 'wallet', emoji: '💼' },
                            { val: 'laptop-code', label: 'Freelance / Tech', icon: 'laptop-code', emoji: '💻' },
                            { val: 'chart-line', label: 'Investment / Stock', icon: 'chart-line', emoji: '📈' },
                            { val: 'briefcase', label: 'Business / Salary', icon: 'briefcase', emoji: '👔' },
                            { val: 'house', label: 'Housing / Rent', icon: 'house', emoji: '🏠' },
                            { val: 'gift', label: 'Bonus / Gift', icon: 'gift', emoji: '🎁' },
                            { val: 'utensils', label: 'Food & Dining', icon: 'utensils', emoji: '🍽️' },
                            { val: 'shopping-cart', label: 'Grocery', icon: 'shopping-cart', emoji: '🛒' },
                            { val: 'car', label: 'Transport / Fuel', icon: 'car', emoji: '🚗' },
                            { val: 'shopping-bag', label: 'Shopping', icon: 'shopping-bag', emoji: '🛍️' },
                            { val: 'file-text', label: 'Bills & Utility', icon: 'file-text', emoji: '📄' },
                            { val: 'film', label: 'Entertainment', icon: 'film', emoji: '🎬' },
                            { val: 'activity', label: 'Health & Medical', icon: 'activity', emoji: '🏥' },
                            { val: 'book-open', label: 'Education', icon: 'book-open', emoji: '📚' },
                            { val: 'bolt', label: 'Utilities', icon: 'bolt', emoji: '⚡' },
                            { val: 'coins', label: 'Coins / Savings', icon: 'coins', emoji: '🪙' },
                            { val: 'coffee', label: 'Coffee & Snacks', icon: 'coffee', emoji: '☕' },
                            { val: 'plane', label: 'Travel & Vacation', icon: 'plane', emoji: '✈️' },
                            { val: 'heart', label: 'Personal Care', icon: 'heart', emoji: '❤️' },
                            { val: 'dumbbell', label: 'Fitness & Gym', icon: 'dumbbell', emoji: '🏋️' }
                        ],
                        get currentOpt() {
                            return this.iconOptions.find(o => o.val === addCategory.icon) || this.iconOptions[0];
                        }
                    }">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Category Icon
                        </label>
                        
                        <div class="relative" @click.outside="open = false">
                            <input type="hidden" name="icon" x-model="addCategory.icon">

                            <!-- Trigger Button -->
                            <button 
                                type="button" 
                                @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-xs"
                            >
                                <span class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-md flex items-center justify-center text-white text-xs shrink-0 transition-colors" :style="'background-color: ' + addCategory.color">
                                        <i :class="'fas fa-' + currentOpt.icon"></i>
                                    </span>
                                    <span class="font-medium truncate text-slate-800 dark:text-slate-200" x-text="currentOpt.emoji + ' ' + currentOpt.label"></span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200 shrink-0" :class="open ? 'rotate-180 text-primary' : ''"></i>
                            </button>

                            <!-- Custom Floating Dropdown Menu -->
                            <div 
                                x-show="open" 
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-2xl max-h-56 overflow-y-auto p-1.5 space-y-0.5"
                            >
                                <template x-for="opt in iconOptions" :key="opt.val">
                                    <button 
                                        type="button"
                                        @click="addCategory.icon = opt.val; open = false"
                                        :class="addCategory.icon === opt.val ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 font-medium'"
                                        class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer"
                                    >
                                        <span class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 text-xs shrink-0">
                                                <i :class="'fas fa-' + opt.icon"></i>
                                            </span>
                                            <span class="truncate" x-text="opt.emoji + ' ' + opt.label"></span>
                                        </span>
                                        <i x-show="addCategory.icon === opt.val" class="fas fa-check text-primary text-xs shrink-0 ml-2"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Live Badge Preview -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 space-y-1.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Live Preview</span>
                        <div class="flex items-center gap-3">
                            <div 
                                class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm shadow-xs shrink-0 transition-all duration-200" 
                                :style="'background-color: ' + addCategory.color"
                            >
                                <i :class="'fas fa-' + addCategory.icon"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate" x-text="addCategory.name || 'Category Name Preview'"></h4>
                                <span 
                                    :class="addCategory.type === 'expense' ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'" 
                                    class="text-[10px] font-medium uppercase tracking-wide" 
                                    x-text="addCategory.type + ' category'"
                                ></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button 
                            type="button" 
                            @click="closeAddModal()" 
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all"
                        >
                            Cancel
                        </button>
                        <x-button type="submit" variant="primary" class="font-semibold text-xs py-2 px-4 shadow-xs flex items-center gap-2" icon="fas fa-plus">
                            Create Category
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT CATEGORY MODAL -->
        <div 
            x-show="editModalOpen" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
            @keydown.escape.window="closeEditModal()"
        >
            <div 
                @click.away="closeEditModal()"
                x-show="editModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded shadow-2xl max-w-md w-full p-6 space-y-5 overflow-hidden"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-bold shrink-0">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Edit Category</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Modify category details and preferences</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="closeEditModal()" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" :action="editCategory.updateUrl" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- 1. Category Type Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Category Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                @click="editCategory.type = 'expense'"
                                :class="editCategory.type === 'expense' ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-500 text-rose-700 dark:text-rose-300 font-bold ring-2 ring-rose-500/20' : 'bg-slate-50 dark:bg-slate-950/60 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                                class="p-2.5 btn-rounded border text-xs cursor-pointer flex items-center justify-center gap-2 transition-all"
                            >
                                <input type="radio" name="type" value="expense" x-model="editCategory.type" class="hidden">
                                <i class="fas fa-arrow-up-right text-xs"></i>
                                <span>Expense</span>
                            </label>
                            <label
                                @click="editCategory.type = 'income'"
                                :class="editCategory.type === 'income' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-500 text-emerald-700 dark:text-emerald-300 font-bold ring-2 ring-emerald-500/20' : 'bg-slate-50 dark:bg-slate-950/60 border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                                class="p-2.5 btn-rounded border text-xs cursor-pointer flex items-center justify-center gap-2 transition-all"
                            >
                                <input type="radio" name="type" value="income" x-model="editCategory.type" class="hidden">
                                <i class="fas fa-arrow-down-left text-xs"></i>
                                <span>Income</span>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Category Name Input -->
                    <div>
                        <label for="edit-category-name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="edit-category-name"
                            type="text"
                            name="name"
                            x-model="editCategory.name"
                            required
                            class="w-full px-3.5 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        />
                    </div>

                    <!-- 3. Color Picker + Preset Swatches -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Category Color
                        </label>
                        <div class="space-y-2.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <template x-for="color in presetColors" :key="color">
                                    <button 
                                        type="button"
                                        @click="editCategory.color = color"
                                        :style="'background-color: ' + color"
                                        :class="editCategory.color === color ? 'scale-110 ring-2 ring-offset-2 ring-slate-400 dark:ring-slate-500' : 'hover:scale-105 opacity-85 hover:opacity-100'"
                                        class="w-6 h-6 rounded-full transition-all cursor-pointer shadow-xs border border-white/20"
                                    ></button>
                                </template>
                            </div>

                            <div class="flex items-center gap-3 pt-1">
                                <input
                                    type="color"
                                    name="color"
                                    x-model="editCategory.color"
                                    class="w-9 h-9 rounded-lg border border-slate-300 dark:border-slate-700 cursor-pointer p-0.5 bg-slate-50 dark:bg-slate-900 shrink-0"
                                />
                                <span class="text-xs font-mono text-slate-600 dark:text-slate-400 uppercase tracking-wider bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700" x-text="editCategory.color"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Category Icon Selector (Custom Alpine Dropdown) -->
                    <div x-data="{
                        open: false,
                        iconOptions: [
                            { val: 'tag', label: 'Tag / General', icon: 'tag', emoji: '🏷️' },
                            { val: 'wallet', label: 'Wallet / Cash', icon: 'wallet', emoji: '💼' },
                            { val: 'laptop-code', label: 'Freelance / Tech', icon: 'laptop-code', emoji: '💻' },
                            { val: 'chart-line', label: 'Investment / Stock', icon: 'chart-line', emoji: '📈' },
                            { val: 'briefcase', label: 'Business / Salary', icon: 'briefcase', emoji: '👔' },
                            { val: 'house', label: 'Housing / Rent', icon: 'house', emoji: '🏠' },
                            { val: 'gift', label: 'Bonus / Gift', icon: 'gift', emoji: '🎁' },
                            { val: 'utensils', label: 'Food & Dining', icon: 'utensils', emoji: '🍽️' },
                            { val: 'shopping-cart', label: 'Grocery', icon: 'shopping-cart', emoji: '🛒' },
                            { val: 'car', label: 'Transport / Fuel', icon: 'car', emoji: '🚗' },
                            { val: 'shopping-bag', label: 'Shopping', icon: 'shopping-bag', emoji: '🛍️' },
                            { val: 'file-text', label: 'Bills & Utility', icon: 'file-text', emoji: '📄' },
                            { val: 'film', label: 'Entertainment', icon: 'film', emoji: '🎬' },
                            { val: 'activity', label: 'Health & Medical', icon: 'activity', emoji: '🏥' },
                            { val: 'book-open', label: 'Education', icon: 'book-open', emoji: '📚' },
                            { val: 'bolt', label: 'Utilities', icon: 'bolt', emoji: '⚡' },
                            { val: 'coins', label: 'Coins / Savings', icon: 'coins', emoji: '🪙' },
                            { val: 'coffee', label: 'Coffee & Snacks', icon: 'coffee', emoji: '☕' },
                            { val: 'plane', label: 'Travel & Vacation', icon: 'plane', emoji: '✈️' },
                            { val: 'heart', label: 'Personal Care', icon: 'heart', emoji: '❤️' },
                            { val: 'dumbbell', label: 'Fitness & Gym', icon: 'dumbbell', emoji: '🏋️' }
                        ],
                        get currentOpt() {
                            return this.iconOptions.find(o => o.val === editCategory.icon) || this.iconOptions[0];
                        }
                    }">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Category Icon
                        </label>
                        
                        <div class="relative" @click.outside="open = false">
                            <input type="hidden" name="icon" x-model="editCategory.icon">

                            <!-- Trigger Button -->
                            <button 
                                type="button" 
                                @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all cursor-pointer shadow-xs"
                            >
                                <span class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-6 h-6 rounded-md flex items-center justify-center text-white text-xs shrink-0 transition-colors" :style="'background-color: ' + editCategory.color">
                                        <i :class="'fas fa-' + currentOpt.icon"></i>
                                    </span>
                                    <span class="font-medium truncate text-slate-800 dark:text-slate-200" x-text="currentOpt.emoji + ' ' + currentOpt.label"></span>
                                </span>
                                <i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200 shrink-0" :class="open ? 'rotate-180 text-primary' : ''"></i>
                            </button>

                            <!-- Custom Floating Dropdown Menu -->
                            <div 
                                x-show="open" 
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-2xl max-h-56 overflow-y-auto p-1.5 space-y-0.5"
                            >
                                <template x-for="opt in iconOptions" :key="opt.val">
                                    <button 
                                        type="button"
                                        @click="editCategory.icon = opt.val; open = false"
                                        :class="editCategory.icon === opt.val ? 'bg-primary/10 text-primary font-bold dark:bg-primary/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 font-medium'"
                                        class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer"
                                    >
                                        <span class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-6 h-6 rounded-md bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-400 text-xs shrink-0">
                                                <i :class="'fas fa-' + opt.icon"></i>
                                            </span>
                                            <span class="truncate" x-text="opt.emoji + ' ' + opt.label"></span>
                                        </span>
                                        <i x-show="editCategory.icon === opt.val" class="fas fa-check text-primary text-xs shrink-0 ml-2"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Live Badge Preview -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-950/60 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 space-y-1.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Live Preview</span>
                        <div class="flex items-center gap-3">
                            <div 
                                class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm shadow-xs shrink-0 transition-all duration-200" 
                                :style="'background-color: ' + editCategory.color"
                            >
                                <i :class="'fas fa-' + editCategory.icon"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate" x-text="editCategory.name || 'Category Name'"></h4>
                                <span 
                                    :class="editCategory.type === 'expense' ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'" 
                                    class="text-[10px] font-medium uppercase tracking-wide" 
                                    x-text="editCategory.type + ' category'"
                                ></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button 
                            type="button" 
                            @click="closeEditModal()" 
                            class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all"
                        >
                            Cancel
                        </button>
                        <x-button type="submit" variant="primary" class="font-semibold text-xs py-2 px-4 shadow-xs flex items-center gap-2" icon="fas fa-save">
                            Save Changes
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
