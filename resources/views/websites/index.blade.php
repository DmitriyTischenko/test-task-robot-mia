<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click Tracker - Websites</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Tracked Websites</h1>
        <a href="{{ route('websites.create') }}"
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Website
        </a>
    </div>

    <div class="grid gap-6">
        @foreach($websites as $website)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $website->name }}</h2>
                        <p class="text-gray-600">{{ $website->domain }}</p>
                        <p class="text-sm text-gray-500">Tracking ID: {{ $website->tracking_id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-blue-600">{{ $website->click_count }}</p>
                        <p class="text-gray-600">Total Clicks</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('websites.analytics', $website) }}"
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        View Analytics
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
</body>
</html>
