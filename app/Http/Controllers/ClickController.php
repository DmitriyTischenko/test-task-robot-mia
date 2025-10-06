<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClickController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Click data received:', $request->all());

        $clicks = $request->input('clicks', []);

        Log::info('Processing ' . count($clicks) . ' clicks');

        foreach ($clicks as $clickData) {
            try {
                $website = Website::where('tracking_id', $clickData['website_id'])->first();

                if (!$website) {
                    Log::warning('Website not found for ID: ' . $clickData['website_id']);
                    continue;
                }

                Click::create([
                    'website_id' => $website->id,
                    'x' => $clickData['x'],
                    'y' => $clickData['y'],
                    'url' => $clickData['url'],
                    'viewport_width' => $clickData['viewport']['width'],
                    'viewport_height' => $clickData['viewport']['height'],
                    'clicked_at' => $clickData['timestamp'],
                ]);

                Log::info('Click saved for website: ' . $website->name);

            } catch (\Exception $e) {
                Log::error('Error saving click: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Clicks processed: ' . count($clicks)
        ]);
    }

    public function getClickData(Website $website)
    {
        try {
            $clicks = $website->clicks()
                ->where('clicked_at', '>=', now()->subDays(1))
                ->get(['x', 'y', 'viewport_width', 'viewport_height']);

            Log::info('Returning ' . $clicks->count() . ' clicks for website: ' . $website->id);

            return response()->json($clicks);
        } catch (\Exception $e) {
            Log::error('Error getting click data: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function getHourlyStats(Website $website)
    {
        try {
            $stats = $website->clicks()
                ->where('clicked_at', '>=', now()->subDays(7))
                ->selectRaw('HOUR(clicked_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            Log::info('Returning hourly stats for website: ' . $website->id . ' - ' . $stats->count() . ' hours');

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error getting hourly stats: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}
