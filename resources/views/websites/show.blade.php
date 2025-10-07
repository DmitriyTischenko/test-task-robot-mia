<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - {{ $website->name }}</title>
    <script src="https://cdn.tailwindcss.com  "></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js  "></script>
    <style>
        .chart-container {
            height: 300px;
            position: relative;
        }
        .heatmap-container {
            height: 350px;
            position: relative;
        }
        /* Optional: Style for disabled buttons */
        .pagination-button:disabled {
            @apply opacity-50 cursor-not-allowed;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('websites.index') }}" class="inline-flex items-center text-blue-500 hover:text-blue-700 mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Websites
        </a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $website->name }} - Analytics</h1>
        <p class="text-gray-600">{{ $website->domain }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Clicks</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $website->clicks_count ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Today</p>
                    <p class="text-xl font-semibold text-gray-900" id="today-clicks">0</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Last 7 Days</p>
                    <p class="text-xl font-semibold text-gray-900" id="week-clicks">0</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Heatmap -->
        <div class="bg-white rounded-lg shadow-sm p-5 border">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Click Heatmap</h2>
                    <p class="text-sm text-gray-600">Visualization over website screenshot</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-400 rounded-full mr-1"></div>
                            <span class="text-gray-600">Low</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
                            <span class="text-gray-600">High</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="heatmap-container rounded-lg border-2 border-gray-200 bg-gray-100 relative overflow-hidden">

                <canvas id="heatmapOverlay" class="absolute top-0 left-0 w-full h-full pointer-events-none"></canvas>

                <!-- Loading State -->
                <div id="heatmap-loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 hidden">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading heatmap data...</p>
                    </div>
                </div>

                <!-- No Data State -->
                <div id="heatmap-no-data" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 hidden">
                    <div class="text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <p class="font-medium text-lg">No click data available</p>
                        <p class="text-sm mt-2">Start clicking on your website to see the heatmap</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Hourly Activity -->
        <div class="bg-white rounded-lg shadow-sm p-5 border">
            <h2 class="text-lg font-semibold mb-3 text-gray-800">Hourly Activity</h2>
            <p class="text-sm text-gray-600 mb-4">Clicks distribution throughout the day</p>
            <div class="chart-container">
                <canvas id="hourlyChart"></canvas>
                <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading chart...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Clicks Table with Pagination -->
    <div class="bg-white rounded-lg shadow-sm p-5 border mt-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Recent Clicks</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-3 text-gray-600 font-medium">Time</th>
                    <th class="text-left py-2 px-3 text-gray-600 font-medium">Page</th>
                    <th class="text-left py-2 px-3 text-gray-600 font-medium">Coordinates</th>
                    <th class="text-left py-2 px-3 text-gray-600 font-medium">Device</th>
                </tr>
                </thead>
                <tbody id="recent-clicks">
                <tr>
                    <td colspan="4" class="text-center py-8 text-gray-500">Loading recent clicks...</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div id="pagination-controls" class="mt-4 flex items-center justify-between">
            <button id="prev-page" class="pagination-button bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded disabled:bg-gray-300">
                Previous
            </button>
            <div class="text-gray-700">
                Page <span id="current-page-display">1</span> of <span id="last-page-display">1</span>
            </div>
            <button id="next-page" class="pagination-button bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded disabled:bg-gray-300">
                Next
            </button>
        </div>
        <!-- Optional: Loading indicator for pagination buttons -->
        <div id="pagination-loading" class="mt-4 text-center hidden">
            <div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
        </div>
    </div>

    <!-- Tracking Instructions -->
    <div class="bg-blue-50 rounded-lg p-5 border border-blue-200 mt-6">
        <h2 class="text-lg font-semibold mb-3 text-blue-800">Tracking Setup</h2>
        <div class="space-y-4">
            <div>
                <h3 class="font-medium text-blue-700 mb-2">Your Tracking ID:</h3>
                <code class="bg-blue-100 text-blue-800 px-3 py-2 rounded text-sm font-mono block">
                    {{ $website->tracking_id }}
                </code>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-medium text-blue-700 mb-2">1. Add to your website's head:</h3>
                    <code class="bg-gray-800 text-green-400 p-3 rounded text-sm font-mono block overflow-x-auto">
                        &lt;meta name="click-tracker-id" content="{{ $website->tracking_id }}"&gt;
                    </code>
                </div>
                <div>
                    <h3 class="font-medium text-blue-700 mb-2">2. Add before closing body tag:</h3>
                    <code class="bg-gray-800 text-green-400 p-3 rounded text-sm font-mono block overflow-x-auto">
                        &lt;script src="{{ url('js/click-tracker.js') }}"&gt;&lt;/script&gt;
                    </code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    console.log('Analytics dashboard initialized');

    let heatmapChart = null;
    let hourlyChart = null;
    let currentClicks = [];
    let currentPage = 1;
    let lastPage = 1;
    const perPage = 10;

    const elements = {
        heatmapOverlay: document.getElementById('heatmapOverlay'),
        websiteScreenshot: document.getElementById('website-screenshot'),
        heatmapLoading: document.getElementById('heatmap-loading'),
        heatmapNoData: document.getElementById('heatmap-no-data'),
        screenshotError: document.getElementById('screenshot-error'),
        chartLoading: document.getElementById('chart-loading'),
        todayClicks: document.getElementById('today-clicks'),
        weekClicks: document.getElementById('week-clicks'),
        recentClicks: document.getElementById('recent-clicks'),
        paginationControls: document.getElementById('pagination-controls'),
        paginationLoading: document.getElementById('pagination-loading'),
        prevButton: document.getElementById('prev-page'),
        nextButton: document.getElementById('next-page'),
        currentPageDisplay: document.getElementById('current-page-display'),
        lastPageDisplay: document.getElementById('last-page-display')
    };

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing analytics...');
        initializeDashboard();
    });

    async function initializeDashboard() {
        try {
            showLoadingStates();

            await Promise.all([
                loadScreenshot(),
                loadHeatmapData(),
                loadHourlyStats(),
                loadAdditionalStats(),
                loadRecentClicks(currentPage)
            ]);

            console.log('All analytics data loaded successfully');

        } catch (error) {
            console.error('Error initializing dashboard:', error);
            showErrorStates();
        }
    }

    async function loadScreenshot() {
        if (!elements.websiteScreenshot) return;

        const screenshotUrl = elements.websiteScreenshot.src;

        if (!screenshotUrl || screenshotUrl === window.location.href) {
            showScreenshotFallback();
            return;
        }

        return new Promise((resolve) => {
            elements.websiteScreenshot.onload = () => {
                console.log('Screenshot loaded successfully');
                initializeHeatmapCanvas();
                resolve();
            };

            elements.websiteScreenshot.onerror = () => {
                console.error('Failed to load screenshot');
                elements.screenshotError.classList.remove('hidden');
                initializeHeatmapCanvas();
                resolve();
            };

            if (elements.websiteScreenshot.complete) {
                initializeHeatmapCanvas();
                resolve();
            }
        });
    }

    function showScreenshotFallback() {
        const container = document.getElementById('screenshot-container');
        if (!container) return;

        container.innerHTML = `
        <div class="text-center text-gray-500 p-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <p class="font-medium mb-2">Website Preview</p>
            <p class="text-sm mb-4">Screenshot not available</p>
            <div class="website-placeholder w-full h-48 bg-gradient-to-br from-blue-50 to-gray-100 rounded border-2 border-dashed border-gray-300 flex items-center justify-center">
                <span class="text-gray-400">{{ $website->domain }}</span>
            </div>
        </div>
    `;
        initializeHeatmapCanvas();
    }

    function initializeHeatmapCanvas() {
        if (!elements.heatmapOverlay) return;

        const container = elements.heatmapOverlay.parentElement;
        if (!container) return;

        elements.heatmapOverlay.width = container.clientWidth;
        elements.heatmapOverlay.height = container.clientHeight;

        console.log('Heatmap canvas initialized:', elements.heatmapOverlay.width, 'x', elements.heatmapOverlay.height);
    }

    async function loadHeatmapData() {
        try {
            showElement(elements.heatmapLoading);
            hideElement(elements.heatmapNoData);

            const response = await fetch(`/api/websites/{{ $website->id }}/clicks`);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const clicks = await response.json();
            currentClicks = clicks || [];

            hideElement(elements.heatmapLoading);

            if (currentClicks.length > 0) {
                console.log(`Loaded ${currentClicks.length} clicks for heatmap`);
                drawHeatmap(currentClicks);
            } else {
                showElement(elements.heatmapNoData);
            }

        } catch (error) {
            console.error('Error loading heatmap data:', error);
            hideElement(elements.heatmapLoading);
            showElement(elements.heatmapNoData);
        }
    }

    function drawHeatmap(clicks) {
        if (!elements.heatmapOverlay) return;

        const ctx = elements.heatmapOverlay.getContext('2d');
        const width = elements.heatmapOverlay.width;
        const height = elements.heatmapOverlay.height;

        ctx.clearRect(0, 0, width, height);

        if (clicks.length === 0) return;

        const heatmapData = createHeatmapData(clicks, width, height);

        drawHeatmapGradient(ctx, heatmapData, width, height);

        console.log('Heatmap drawn successfully');
    }

    function createHeatmapData(clicks, width, height) {
        const data = new Array(width * height).fill(0);
        let maxIntensity = 0;

        const screenshot = elements.websiteScreenshot;
        const container = elements.heatmapOverlay.parentElement;

        let scaleX = 1, scaleY = 1, offsetX = 0, offsetY = 0;

        if (screenshot && screenshot.complete && screenshot.naturalWidth > 0) {
            const screenshotRect = screenshot.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();

            scaleX = screenshotRect.width / width;
            scaleY = screenshotRect.height / height;
            offsetX = (screenshotRect.left - containerRect.left) / scaleX;
            offsetY = (screenshotRect.top - containerRect.top) / scaleY;
        }

        clicks.forEach(click => {
            let canvasX, canvasY;

            if (screenshot && screenshot.complete) {
                canvasX = (click.x / click.viewport_width) * (width * scaleX) + offsetX;
                canvasY = (click.y / click.viewport_height) * (height * scaleY) + offsetY;
            } else {
                canvasX = (click.x / click.viewport_width) * width;
                canvasY = (click.y / click.viewport_height) * height;
            }

            canvasX = Math.max(0, Math.min(width - 1, canvasX));
            canvasY = Math.max(0, Math.min(height - 1, canvasY));

            const radius = 12;
            for (let i = -radius; i <= radius; i++) {
                for (let j = -radius; j <= radius; j++) {
                    const pointX = Math.round(canvasX + i);
                    const pointY = Math.round(canvasY + j);
                    const distance = Math.sqrt(i * i + j * j);

                    if (distance <= radius && pointX >= 0 && pointX < width && pointY >= 0 && pointY < height) {
                        const intensity = (radius - distance) / radius;
                        const index = pointY * width + pointX;
                        data[index] += intensity;
                        maxIntensity = Math.max(maxIntensity, data[index]);
                    }
                }
            }
        });

        return { data, maxIntensity };
    }

    function drawHeatmapGradient(ctx, heatmapData, width, height) {
        const { data, maxIntensity } = heatmapData;
        const imageData = ctx.createImageData(width, height);

        for (let i = 0; i < data.length; i++) {
            const intensity = maxIntensity > 0 ? data[i] / maxIntensity : 0;

            if (intensity > 0.1) {
                const [r, g, b, a] = getHeatmapColor(intensity);
                const index = i * 4;

                imageData.data[index] = r;
                imageData.data[index + 1] = g;
                imageData.data[index + 2] = b;
                imageData.data[index + 3] = a;
            }
        }

        ctx.putImageData(imageData, 0, 0);

        ctx.filter = 'blur(8px)';
        ctx.globalCompositeOperation = 'lighter';
        ctx.drawImage(ctx.canvas, 0, 0);
        ctx.filter = 'none';
        ctx.globalCompositeOperation = 'source-over';
    }

    function getHeatmapColor(intensity) {
        let r, g, b, a;

        if (intensity < 0.3) {
            const t = intensity / 0.3;
            r = Math.round(0 * t);
            g = Math.round(150 * t);
            b = Math.round(255 * (1 - t * 0.5));
            a = Math.round(150 * t);
        } else if (intensity < 0.6) {
            const t = (intensity - 0.3) / 0.3;
            r = Math.round(255 * t);
            g = Math.round(255 * (0.8 - t * 0.3));
            b = Math.round(100 * (1 - t));
            a = Math.round(180 + 75 * t);
        } else {
            const t = (intensity - 0.6) / 0.4;
            r = 255;
            g = Math.round(255 * (1 - t * 0.7));
            b = Math.round(50 * (1 - t));
            a = Math.round(200 + 55 * t);
        }

        return [r, g, b, a];
    }

    async function loadHourlyStats() {
        try {
            showElement(elements.chartLoading);

            const response = await fetch(`/api/websites/{{ $website->id }}/hourly-stats`);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const stats = await response.json();

            hideElement(elements.chartLoading);

            if (stats && stats.length > 0) {
                drawHourlyChart(stats);
            } else {
                drawEmptyChart('No hourly data available');
            }

        } catch (error) {
            console.error('Error loading hourly stats:', error);
            hideElement(elements.chartLoading);
            drawEmptyChart('Error loading chart data');
        }
    }

    function drawHourlyChart(stats) {
        const ctx = document.getElementById('hourlyChart').getContext('2d');

        if (hourlyChart) {
            hourlyChart.destroy();
        }

        const hourlyData = Array(24).fill(0);
        const labels = Array.from({length: 24}, (_, i) => {
            if (i === 0) return '12AM';
            if (i < 12) return i + 'AM';
            if (i === 12) return '12PM';
            return (i - 12) + 'PM';
        });

        stats.forEach(stat => {
            if (stat.hour >= 0 && stat.hour < 24) {
                hourlyData[stat.hour] = stat.count;
            }
        });

        hourlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Clicks',
                    data: hourlyData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return `Clicks: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Number of Clicks'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 0,
                            callback: function(value, index) {
                                return index % 3 === 0 ? this.getLabelForValue(value) : '';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Hour of Day'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    function drawEmptyChart(message) {
        const ctx = document.getElementById('hourlyChart').getContext('2d');
        const canvas = ctx.canvas;

        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.fillStyle = '#64748b';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.font = '16px system-ui';

        ctx.fillText(message, canvas.width / 2, canvas.height / 2);
    }

    async function loadAdditionalStats() {
        try {
            const [todayResponse, weekResponse] = await Promise.all([
                fetch(`/api/websites/{{ $website->id }}/today-clicks`),
                fetch(`/api/websites/{{ $website->id }}/week-clicks`)
            ]);

            const todayData = todayResponse.ok ? await todayResponse.json() : { count: 0 };
            const weekData = weekResponse.ok ? await weekResponse.json() : { count: 0 };

            if (elements.todayClicks) elements.todayClicks.textContent = todayData.count || 0;
            if (elements.weekClicks) elements.weekClicks.textContent = weekData.count || 0;

        } catch (error) {
            console.error('Error loading additional stats:', error);
            if (elements.todayClicks) elements.todayClicks.textContent = '0';
            if (elements.weekClicks) elements.weekClicks.textContent = '0';
        }
    }

    async function loadRecentClicks(page = 1) {
        try {
            showElement(elements.paginationLoading);

            currentPage = page;

            const response = await fetch(`/api/websites/{{ $website->id }}/recent-clicks?page=${page}&per_page=${perPage}`);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();
            const clicks = result.data || [];
            const meta = result.meta || { current_page: 1, last_page: 1, total: 0, per_page: perPage };

            currentPage = meta.current_page;
            lastPage = meta.last_page;

            updatePaginationUI();
            displayRecentClicks(clicks);

        } catch (error) {
            console.error('Error loading recent clicks:', error);
            displayRecentClicksError();
        } finally {
            hideElement(elements.paginationLoading);
        }
    }

    function displayRecentClicks(clicks) {
        if (!elements.recentClicks) return;

        if (clicks.length === 0) {
            elements.recentClicks.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-8 text-gray-500">
                    No recent clicks found
                </td>
            </tr>
        `;
            return;
        }

        elements.recentClicks.innerHTML = clicks.map(click => `
        <tr class="border-b hover:bg-gray-50 transition-colors">
            <td class="py-3 px-4 text-gray-600 text-sm">
                ${formatTime(click.clicked_at)}
            </td>
            <td class="py-3 px-4">
                <div class="max-w-xs truncate text-blue-600 hover:text-blue-800 cursor-help"
                     title="${click.url}">
                    ${extractPathname(click.url)}
                </div>
            </td>
            <td class="py-3 px-4 text-gray-600 text-sm">
                ${click.x}, ${click.y}
            </td>
            <td class="py-3 px-4 text-gray-600 text-sm">
                ${click.viewport_width}×${click.viewport_height}
            </td>
        </tr>
    `).join('');
    }

    function displayRecentClicksError() {
        if (!elements.recentClicks) return;

        elements.recentClicks.innerHTML = `
        <tr>
            <td colspan="4" class="text-center py-8 text-red-500">
                Error loading recent clicks
            </td>
        </tr>
    `;
    }

    function updatePaginationUI() {
        if (elements.currentPageDisplay) elements.currentPageDisplay.textContent = currentPage;
        if (elements.lastPageDisplay) elements.lastPageDisplay.textContent = lastPage;
        if (elements.prevButton) elements.prevButton.disabled = currentPage <= 1;
        if (elements.nextButton) elements.nextButton.disabled = currentPage >= lastPage;
    }

    if (elements.prevButton) {
        elements.prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                loadRecentClicks(currentPage - 1);
            }
        });
    }

    if (elements.nextButton) {
        elements.nextButton.addEventListener('click', () => {
            if (currentPage < lastPage) {
                loadRecentClicks(currentPage + 1);
            }
        });
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    function extractPathname(url) {
        try {
            const pathname = new URL(url).pathname;
            return pathname || '/';
        } catch {
            return url.length > 30 ? url.substring(0, 30) + '...' : url;
        }
    }

    function showElement(element) {
        if (element) element.classList.remove('hidden');
    }

    function hideElement(element) {
        if (element) element.classList.add('hidden');
    }

    function showLoadingStates() {
        showElement(elements.heatmapLoading);
        showElement(elements.chartLoading);
    }

    function showErrorStates() {
        hideElement(elements.heatmapLoading);
        hideElement(elements.chartLoading);
        showElement(elements.heatmapNoData);
    }

    window.addEventListener('resize', function() {
        if (elements.heatmapOverlay) {
            initializeHeatmapCanvas();

            if (currentClicks.length > 0) {
                drawHeatmap(currentClicks);
            }
        }

        if (hourlyChart) {
            setTimeout(() => {
                hourlyChart.resize();
            }, 100);
        }
    });

    console.log('Analytics JavaScript loaded successfully');
</script>
</body>
</html>
