@props([
    'title' => 'Monthly Spending Trends',
    'subtitle' => 'Capsule breakdown of cash flow volume',
    'chartApiRoute' => route('api.dashboard.chart'),
])

<div
    x-data="capsuleChart('{{ $chartApiRoute }}')"
    class="flat-card p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded shadow-xs space-y-4"
>
    <!-- Header with Custom Dropdown Pill -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300 font-bold shrink-0">
                <i class="fas fa-chart-simple text-sm"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $title }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            </div>
        </div>

        <!-- Custom Alpine.js Dropdown Pill Menu -->
        <div class="relative">
            <button
                type="button"
                @click="dropdownOpen = !dropdownOpen"
                class="px-3.5 py-1.5 text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 btn-rounded border border-slate-200/90 dark:border-slate-700/80 flex items-center gap-2 hover:bg-slate-200/70 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer shadow-2xs"
            >
                <span x-text="rangeLabels[activeRange] || 'Last 7 Days'"></span>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }"></i>
            </button>

            <!-- Dropdown Options Menu -->
            <div
                x-show="dropdownOpen"
                @click.outside="dropdownOpen = false"
                x-cloak
                x-transition:enter="transition ease-out duration-150 transform"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                class="absolute right-0 mt-1.5 w-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded shadow-xl py-1.5 z-30 space-y-0.5"
            >
                <template x-for="(label, key) in rangeLabels" :key="key">
                    <button
                        type="button"
                        @click="selectRange(key)"
                        class="w-full text-left px-3.5 py-1.5 text-xs font-medium flex items-center justify-between transition-colors"
                        :class="activeRange === key ? 'text-primary bg-primary/10 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                    >
                        <span x-text="label"></span>
                        <i x-show="activeRange === key" class="fas fa-check text-[10px] text-primary"></i>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <!-- Chart Canvas -->
    <div class="relative h-72 w-full pt-2">
        <canvas x-ref="canvas"></canvas>
    </div>
</div>

<script>
    function capsuleChart(apiUrl) {
        return {
            activeRange: '7d',
            dropdownOpen: false,
            chartInstance: null,
            rangeLabels: {
                '7d': 'Last 7 Days',
                '30d': 'Last 30 Days',
                '6m': 'Last 6 Months',
                '1y': 'Last 1 Year'
            },

            init() {
                this.loadChart();
            },

            selectRange(key) {
                this.activeRange = key;
                this.dropdownOpen = false;
                this.loadChart();
            },

            async loadChart() {
                try {
                    const response = await fetch(`${apiUrl}?range=${this.activeRange}`);
                    const data = await response.json();

                    const isDark = document.documentElement.classList.contains('dark');
                    const ctx = this.$refs.canvas.getContext('2d');

                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }

                    const maxVal = Math.max(...data.data, 100);
                    const trackMax = maxVal * 1.25;
                    const trackData = data.data.map(() => trackMax);

                    const trackBg = isDark ? 'rgba(30, 41, 59, 0.6)' : 'rgba(241, 245, 249, 0.9)';
                    const barColor = '#f97316'; // Warm orange capsule fill

                    this.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [
                                {
                                    label: 'Track Background',
                                    data: trackData,
                                    backgroundColor: trackBg,
                                    borderRadius: 30,
                                    borderSkipped: false,
                                    barPercentage: 0.55,
                                    categoryPercentage: 0.8,
                                    grouped: false,
                                    order: 2,
                                    tooltip: { enabled: false }
                                },
                                {
                                    label: 'Expenses (₹)',
                                    data: data.data,
                                    backgroundColor: barColor,
                                    borderRadius: 30,
                                    borderSkipped: false,
                                    barPercentage: 0.55,
                                    categoryPercentage: 0.8,
                                    grouped: false,
                                    order: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    filter: (item) => item.datasetIndex === 1,
                                    callbacks: {
                                        label: (ctx) => ` Expense: ₹${ctx.parsed.y.toLocaleString('en-IN', {minimumFractionDigits: 2})}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        font: { size: 11, weight: '600' },
                                        color: isDark ? '#94a3b8' : '#64748b'
                                    }
                                },
                                y: {
                                    grid: {
                                        color: isDark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)',
                                        borderDash: [4, 4]
                                    },
                                    ticks: {
                                        font: { size: 11, weight: '600' },
                                        color: isDark ? '#94a3b8' : '#64748b',
                                        callback: function(value) {
                                            if (value >= 1000) return '₹' + (value / 1000).toFixed(0) + 'K';
                                            return '₹' + value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } catch (e) {
                    console.error('Failed to load capsule chart data', e);
                }
            }
        }
    }
</script>
