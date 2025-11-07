<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show main analytics dashboard (FR-36)
     */
    public function dashboard()
    {
        $dashboardStats = $this->analyticsService->getDashboardStats();
        $shippingCosts = $this->analyticsService->getMonthlyShippingCosts();
        $carrierUsage = $this->analyticsService->getMostUsedCarriers();
        $statusBreakdown = $this->analyticsService->getShipmentStatusBreakdown();
        $revenueChart = $this->analyticsService->getRevenueTrends(30);

        $pageTitle = 'Analytics Dashboard';
        return view('admin.analytics.dashboard', compact(
            'pageTitle',
            'dashboardStats',
            'shippingCosts',
            'carrierUsage',
            'statusBreakdown',
            'revenueChart'
        ));
    }

    /**
     * Show monthly shipping costs chart
     */
    public function shippingCosts()
    {
        $costs = $this->analyticsService->getMonthlyShippingCosts(12);

        $pageTitle = 'Monthly Shipping Costs';
        return view('admin.analytics.shipping_costs', compact('pageTitle', 'costs'));
    }

    /**
     * Show carrier performance
     */
    public function carriers()
    {
        $carrierUsage = $this->analyticsService->getMostUsedCarriers();
        $deliveryPerformance = $this->analyticsService->getDeliveryPerformance();

        $pageTitle = 'Carrier Performance';
        return view('admin.analytics.carriers', compact('pageTitle', 'carrierUsage', 'deliveryPerformance'));
    }

    /**
     * Show regional analytics
     */
    public function regions()
    {
        $popularDestinations = $this->analyticsService->getPopularDestinations(20);
        $regionalDistribution = $this->analyticsService->getRegionalDistribution();
        $peakTimes = $this->analyticsService->getPeakShippingTimes();

        $pageTitle = 'Regional Analytics';
        return view('admin.analytics.regions', compact('pageTitle', 'popularDestinations', 'regionalDistribution', 'peakTimes'));
    }

    /**
     * Export analytics data
     */
    public function exportData(Request $request)
    {
        $format = $request->input('format', 'json');
        $data = $this->analyticsService->exportAnalyticsData();

        if ($format === 'csv') {
            // Convert to CSV
            $filename = 'analytics_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Metric', 'Value']);

                foreach ($data['dashboard_stats'] as $key => $value) {
                    fputcsv($file, [ucwords(str_replace('_', ' ', $key)), $value]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // JSON format (default)
        $filename = 'analytics_' . now()->format('Y-m-d') . '.json';
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get live statistics (AJAX)
     */
    public function liveStats()
    {
        $stats = $this->analyticsService->getDashboardStats();
        $satisfaction = $this->analyticsService->getCustomerSatisfaction();

        return response()->json([
            'stats' => $stats,
            'satisfaction' => $satisfaction,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
