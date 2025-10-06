<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebsiteController extends Controller
{
    public function index()
    {
        $websites = Website::withCount('clicks')->get();
        return view('websites.index', compact('websites'));
    }

    public function create()
    {
        return view('websites.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
        ]);

        Website::create([
            'name' => $request->name,
            'domain' => $request->domain,
            'tracking_id' => Str::random(32),
        ]);

        return redirect()->route('websites.index')
            ->with('success', 'Website added successfully');
    }

    public function show(Website $website)
    {
        $website->loadCount(['clicks' => function ($query) {
            $query->where('clicked_at', '>=', now()->subDays(30));
        }]);

        return view('websites.show', compact('website'));
    }

    public function todayClicks(Website $website)
    {
        try {
            $count = $website->clicks()
                ->whereDate('clicked_at', today())
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error getting today clicks: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    public function weekClicks(Website $website)
    {
        try {
            $count = $website->clicks()
                ->where('clicked_at', '>=', now()->subDays(7))
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error getting week clicks: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    public function recentClicks(Website $website, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $clicks = $website->clicks()
                ->orderBy('clicked_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $clicks->items(),
                'meta' => [
                    'current_page' => $clicks->currentPage(),
                    'per_page' => $clicks->perPage(),
                    'total' => $clicks->total(),
                    'last_page' => $clicks->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recent clicks: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}
