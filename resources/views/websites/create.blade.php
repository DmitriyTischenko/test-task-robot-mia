<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Website - Click Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('websites.index') }}" class="text-blue-500 hover:text-blue-700">
                ← Back to Websites
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-2">Add New Website</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('websites.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Website Name *
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter website name">
                    @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                        Domain *
                    </label>
                    <input type="url"
                           name="domain"
                           id="domain"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://example.com">
                    @error('domain')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('websites.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Website
                    </button>
                </div>
            </form>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 rounded-lg p-6 mt-8">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">Installation Instructions</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-blue-700">1. Add meta tag to your website's head:</h3>
                    <code class="bg-gray-800 text-green-400 p-2 rounded block mt-1 text-sm">
                        &lt;meta name="click-tracker-id" content="<span id="tracking-id-placeholder">TRACKING_ID_WILL_APPEAR_HERE</span>"&gt;
                    </code>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-700">2. Add JavaScript file before closing body tag:</h3>
                    <code class="bg-gray-800 text-green-400 p-2 rounded block mt-1 text-sm">
                        &lt;script src="{{ url('js/click-tracker.js') }}"&gt;&lt;/script&gt;
                    </code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // This would typically be populated after form submission
    // For now, it's a placeholder
    document.addEventListener('DOMContentLoaded', function() {
        // In a real application, you'd get the tracking ID after form submission
        console.log('After form submission, the tracking ID will appear here');
    });
</script>
</body>
</html>
