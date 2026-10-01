<x-app-layout title="Dashboard" active="dashboard">
    <div
        x-data="{
            {{-- ── Add/Edit EXPENSE state ── --}}
            addExpenseModalOpen: false,
            editExpenseModalOpen: false,
            editExpense: {
                id: null,
                title: '',
                amount: '',
                category_id: '',
                payment_method: '',
                date: '',
                notes: '',
                receiptUrl: null,
                updateUrl: '',
                deleteUrl: ''
            },
            openEditExpenseModal(data) {
                this.editExpense = {
                    id: data.id,
                    title: data.title,
                    amount: data.amount,
                    category_id: data.category_id,
                    payment_method: data.payment_method,
                    date: data.date,
                    notes: data.notes || '',
                    receiptUrl: data.receiptUrl || null,
                    updateUrl: data.updateUrl,
                    deleteUrl: data.deleteUrl
                };
                this.editExpenseModalOpen = true;
            },

            {{-- ── Add/Edit INCOME state ── --}}
            addIncomeModalOpen: false,
            editIncomeModalOpen: false,
            editIncome: {
                id: null,
                title: '',
                amount: '',
                source: '',
                payment_method: '',
                date: '',
                notes: '',
                updateUrl: '',
                deleteUrl: ''
            },
            openEditIncomeModal(data) {
                this.editIncome = {
                    id: data.id,
                    title: data.title,
                    amount: data.amount,
                    source: data.source,
                    payment_method: data.payment_method,
                    date: data.date,
                    notes: data.notes || '',
                    updateUrl: data.updateUrl,
                    deleteUrl: data.deleteUrl
                };
                this.editIncomeModalOpen = true;
            }
        }"
        @open-edit-expense.window="openEditExpenseModal($event.detail)"
        @open-edit-income.window="openEditIncomeModal($event.detail)"
        class="space-y-6"
    >

        <!-- Header Greeting & CTA -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                    Good morning, {{ explode(' ', $user->name)[0] }} 👋
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Here's your financial overview for {{ \Carbon\Carbon::now()->format('F Y') }}.
                </p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    @click="addIncomeModalOpen = true"
                    class="relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none select-none cursor-pointer px-4 py-2 text-sm gap-2 border border-emerald-600/40 text-emerald-700 dark:text-emerald-400 bg-white dark:bg-slate-900 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 shadow-xs"
                >
                    <i class="fas fa-plus-circle text-current"></i>
                    <span>Add Income</span>
                </button>
                <button
                    type="button"
                    @click="addExpenseModalOpen = true"
                    class="relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none select-none cursor-pointer px-4 py-2 text-sm gap-2 bg-primary text-white bg-primary-hover border border-transparent shadow-xs shadow-primary/20"
                >
                    <i class="fas fa-plus text-current"></i>
                    <span>Add Expense</span>
                </button>
            </div>
        </div>

        <!-- Budget Warning Alert Banner if limit exceeded -->
        @if($isOverWarningLimit)
            <div class="p-4 bg-amber-50 text-amber-900 dark:bg-amber-950/40 dark:text-amber-200 border border-amber-200 dark:border-amber-800/50 btn-rounded flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
                    <div>
                        <p class="text-sm font-semibold">Budget Warning Alert!</p>
                        <p class="text-xs text-amber-700 dark:text-amber-300">
                            You have consumed {{ number_format($budgetUsedPercent, 1) }}% of your monthly budget of ₹{{ number_format($budget, 2) }}.
                        </p>
                    </div>
                </div>
                <x-button :href="route('profile.edit')" variant="outline" size="sm">
                    Adjust Budget
                </x-button>
            </div>
        @endif

        <!-- Stats Grid (Flat Aesthetic) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Income -->
            <x-stat-card
                title="Total Income"
                value="+ ₹{{ number_format($totalIncome, 2) }}"
                subtitle="All-time recorded earnings"
                icon="fas fa-wallet"
            />

            <!-- 2. This Month Expenses -->
            <x-stat-card
                title="This Month Expenses"
                value="₹{{ number_format($thisMonthExpense, 2) }}"
                :change="abs(round($percentChange, 1)) . '% from last month'"
                :changeType="$percentChange > 0 ? 'negative' : ($percentChange < 0 ? 'positive' : 'neutral')"
                subtitle="{{ \Carbon\Carbon::now()->format('F Y') }}"
                icon="fas fa-receipt"
            />

            <!-- 3. Net Savings / Cash Flow -->
            <x-stat-card
                title="Net Cash Flow / Savings"
                value="{{ ($netSavings >= 0 ? '+ ' : '- ') }}₹{{ number_format(abs($netSavings), 2) }}"
                subtitle="Total Income minus Total Expenses"
                icon="fas fa-piggy-bank"
            />

            <!-- 4. Remaining Budget -->
            <x-stat-card
                title="Remaining Budget"
                value="₹{{ number_format($remainingBudget, 2) }}"
                subtitle="{{ number_format($budgetUsedPercent, 1) }}% of ₹{{ number_format($budget, 0) }} used"
                icon="fas fa-calculator"
            />
        </div>

        <!-- Charts Grid (Flat Layout Integration) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Capsule Bar Chart Component (2 Columns) -->
            <div class="lg:col-span-2">
                <x-spending-chart />
            </div>

            <!-- Spending by Category Doughnut Chart (1 Column) -->
            <div class="flat-card p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded flex flex-col justify-between shadow-xs" x-data="doughnutChartComponent()">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-1">Spending by Category</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Current month distribution</p>
                </div>

                @if($categoryBreakdown->count() > 0)
                    <div class="relative h-56 w-full flex items-center justify-center">
                        <canvas id="categoryDoughnutChart"></canvas>
                    </div>
                @else
                    <x-empty-state
                        title="No spending data"
                        description="Add your first expense to see category breakdown."
                        icon="fas fa-chart-pie"
                    />
                @endif
            </div>
        </div>

        <!-- Recent Transactions Section -->
        <div class="flat-card p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Recent Transactions</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Your latest spending activities</p>
                </div>
                <x-button :href="route('expenses.index')" variant="outline" size="sm" icon="fas fa-arrow-right">
                    View all expenses
                </x-button>
            </div>

            @if($recentTransactions->count() > 0)
                <div class="flat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/80 shadow-xs">
                    @foreach($recentTransactions as $expense)
                        <x-transaction-card :transaction="$expense" />
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="No recent expenses"
                    description="Start tracking your spending by adding your first expense."
                    actionUrl="{{ route('expenses.create') }}"
                    actionLabel="Add your first expense"
                />
            @endif
        <!-- ADD & EDIT EXPENSE MODALS -->
        @include('expenses.partials.modals', [
            'categories' => $expenseCategories,
            'addModalVar' => 'addExpenseModalOpen',
            'editModalVar' => 'editExpenseModalOpen'
        ])

        <!-- ADD & EDIT INCOME MODALS -->
        @include('incomes.partials.modals', [
            'categories' => $incomeCategories,
            'addModalVar' => 'addIncomeModalOpen',
            'editModalVar' => 'editIncomeModalOpen'
        ])

    </div>

    <!-- Doughnut Chart Script Logic -->
    <script>
        function doughnutChartComponent() {
            return {
                doughnutChart: null,

                init() {
                    this.loadDoughnutChart();
                },

                loadDoughnutChart() {
                    const canvas = document.getElementById('categoryDoughnutChart');
                    if (!canvas) return;

                    const categories = @js($categoryBreakdown->pluck('category'));
                    const totals = @js($categoryBreakdown->pluck('total'));
                    const palette = ['#2563eb', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#6366f1', '#64748b'];

                    if (this.doughnutChart) {
                        this.doughnutChart.destroy();
                    }

                    this.doughnutChart = new Chart(canvas.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: categories,
                            datasets: [{
                                data: totals,
                                backgroundColor: palette.slice(0, categories.length),
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { boxWidth: 10, font: { size: 11 } }
                                }
                            },
                            cutout: '68%'
                        }
                    });
                }
            }
        }
    </script>
</x-app-layout>
