<x-app-layout title="Reports & Analytics" active="reports">
    <div class="space-y-6">

        <!-- Header & Year Selector -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                    Financial Reports & Analytics
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Analyze your spending patterns, top categories, and monthly performance
                </p>
            </div>

            <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2">
                <label for="year" class="text-xs font-semibold text-slate-500 uppercase">Year:</label>
                <select
                    id="year"
                    name="year"
                    onchange="this.form.submit()"
                    class="px-3 py-1.5 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 btn-rounded font-semibold text-slate-900 dark:text-slate-100 outline-none"
                >
                    @for($y = \Carbon\Carbon::now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        <!-- Annual Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-stat-card
                title="Year Total Spending"
                value="₹{{ number_format($yearTotal, 2) }}"
                subtitle="Total recorded in {{ $selectedYear }}"
                icon="fas fa-coins"
            />

            <x-stat-card
                title="Monthly Budget Limit"
                value="₹{{ number_format($user->monthly_budget, 2) }}"
                subtitle="₹{{ number_format($user->monthly_budget * 12, 2) }} Annual Target"
                icon="fas fa-bullseye"
            />

            <x-stat-card
                title="Annual Budget Variance"
                value="₹{{ number_format(($user->monthly_budget * 12) - $yearTotal, 2) }}"
                :change="(($user->monthly_budget * 12) - $yearTotal >= 0) ? 'Under Budget' : 'Over Budget'"
                :changeType="(($user->monthly_budget * 12) - $yearTotal >= 0) ? 'positive' : 'negative'"
                subtitle="Estimated net variance"
                icon="fas fa-chart-line"
            />
        </div>

        <!-- Monthly Bar Chart & Top Categories Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Monthly Spending Bar Chart (2 Cols) -->
            <div class="lg:col-span-2 flat-card p-5">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Monthly Spending Trend ({{ $selectedYear }})</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Total expense per month compared with budget limit</p>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="monthlyBarChart"></canvas>
                </div>
            </div>

            <!-- Top Spending Categories Ranking (1 Col) -->
            <div class="flat-card p-5">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 mb-1">Top Spending Categories</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Highest expense categories for {{ $selectedYear }}</p>

                @if($topCategories->count() > 0)
                    <div class="space-y-3">
                        @foreach($topCategories->take(6) as $index => $cat)
                            @php
                                $percentage = $yearTotal > 0 ? ($cat->total / $yearTotal) * 100 : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                    <span class="text-slate-800 dark:text-slate-200">
                                        {{ $index + 1 }}. {{ $cat->category }}
                                    </span>
                                    <span class="font-mono text-slate-900 dark:text-slate-100">
                                        ₹{{ number_format($cat->total, 2) }} ({{ number_format($percentage, 1) }}%)
                                    </span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 btn-rounded overflow-hidden">
                                    <div class="h-full bg-primary btn-rounded" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <x-empty-state
                        title="No category data"
                        description="No transactions found for the selected year."
                        icon="fas fa-list-ol"
                    />
                @endif
            </div>
        </div>

        <!-- Month-by-Month Detailed Summary Table -->
        <div class="flat-card p-5">
            <div class="mb-4 pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Month-by-Month Summary Table</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Detailed breakdown of transaction count, total spend, budget limit, and savings</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Month</th>
                            <th class="py-3 px-4 text-center">Transactions</th>
                            <th class="py-3 px-4 text-right">Total Spent</th>
                            <th class="py-3 px-4 text-right">Monthly Budget</th>
                            <th class="py-3 px-4 text-right">Net Savings / Deficit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($monthlyData as $row)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors">
                                <td class="py-3 px-4 font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $row['label'] }} {{ $selectedYear }}
                                </td>
                                <td class="py-3 px-4 text-center text-slate-600 dark:text-slate-400 font-mono">
                                    {{ $row['count'] }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-slate-100">
                                    ₹{{ number_format($row['amount'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-500 dark:text-slate-400">
                                    ₹{{ number_format($row['budget'], 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap">
                                    @php
                                        $variance = $row['budget'] - $row['amount'];
                                    @endphp
                                    @if($variance >= 0)
                                        <span class="text-emerald-600 dark:text-emerald-400">+ ₹{{ number_format($variance, 2) }}</span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">− ₹{{ number_format(abs($variance), 2) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Chart.js Monthly Bar Chart Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const labels = @js(array_column($monthlyData, 'label'));
            const amounts = @js(array_column($monthlyData, 'amount'));
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#2563eb';

            const ctx = document.getElementById('monthlyBarChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Expenses (₹)',
                        data: amounts,
                        backgroundColor: primaryColor,
                        borderRadius: 4,
                        maxBarThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` Spent: ₹${ctx.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2})}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#94a3b8' }
                        },
                        y: {
                            grid: { color: 'rgba(226, 232, 240, 0.4)' },
                            ticks: {
                                font: { size: 11 },
                                color: '#94a3b8',
                                callback: (val) => '₹' + val
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
