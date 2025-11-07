<?php

namespace App\Services;

use App\Models\CourierInfo;
use App\Models\Payment;
use App\Models\ShipmentRating;
use App\Models\CourierConfiguration;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get monthly shipping costs (FR-36)
     */
    public function getMonthlyShippingCosts(int $months = 12): array
    {
        $startDate = now()->subMonths($months)->startOfMonth();

        $costs = Payment::where('created_at', '>=', $startDate)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill in missing months with zero
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthData = $costs->where('month', $month)->first();

            $result[] = [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'total' => $monthData ? (float) $monthData->total : 0,
            ];
        }

        return $result;
    }

    /**
     * Get most used carriers (FR-36)
     */
    public function getMostUsedCarriers(): array
    {
        $carriers = CourierInfo::whereNotNull('courier_configuration_id')
            ->with('courierConfiguration')
            ->select('courier_configuration_id', DB::raw('COUNT(*) as shipment_count'))
            ->groupBy('courier_configuration_id')
            ->orderBy('shipment_count', 'desc')
            ->limit(10)
            ->get();

        $total = $carriers->sum('shipment_count');

        return $carriers->map(function ($carrier) use ($total) {
            return [
                'carrier_name' => $carrier->courierConfiguration->name ?? 'Unknown',
                'shipment_count' => $carrier->shipment_count,
                'percentage' => $total > 0 ? round(($carrier->shipment_count / $total) * 100, 1) : 0,
            ];
        })->toArray();
    }

    /**
     * Get delivery performance by carrier (FR-36)
     */
    public function getDeliveryPerformance(): array
    {
        $carriers = CourierConfiguration::all();
        $performance = [];

        foreach ($carriers as $carrier) {
            $shipments = CourierInfo::where('courier_configuration_id', $carrier->id)
                ->where('status', 3) // Delivered
                ->whereNotNull('created_at')
                ->get();

            if ($shipments->isEmpty()) {
                continue;
            }

            // Calculate average delivery time
            $totalDays = 0;
            $count = 0;

            foreach ($shipments as $shipment) {
                if ($shipment->created_at && $shipment->updated_at) {
                    $days = $shipment->created_at->diffInDays($shipment->updated_at);
                    $totalDays += $days;
                    $count++;
                }
            }

            $avgDays = $count > 0 ? round($totalDays / $count, 1) : 0;

            // Calculate on-time delivery rate
            $onTimeCount = $shipments->filter(function ($shipment) {
                if (!$shipment->estimated_delivery_date) {
                    return true;
                }
                return $shipment->updated_at <= $shipment->estimated_delivery_date;
            })->count();

            $onTimeRate = $shipments->count() > 0 ? round(($onTimeCount / $shipments->count()) * 100, 1) : 0;

            $performance[] = [
                'carrier_name' => $carrier->name,
                'average_delivery_days' => $avgDays,
                'on_time_rate' => $onTimeRate,
                'total_shipments' => $shipments->count(),
            ];
        }

        return $performance;
    }

    /**
     * Get popular destination regions (FR-36)
     */
    public function getPopularDestinations(int $limit = 10): array
    {
        $destinations = CourierInfo::with('receiverBranch')
            ->select('receiver_branch_id', DB::raw('COUNT(*) as shipment_count'))
            ->groupBy('receiver_branch_id')
            ->orderBy('shipment_count', 'desc')
            ->limit($limit)
            ->get();

        return $destinations->map(function ($dest) {
            return [
                'branch_name' => $dest->receiverBranch->name ?? 'Unknown',
                'city' => $dest->receiverBranch->city ?? '',
                'country' => $dest->receiverBranch->country ?? '',
                'shipment_count' => $dest->shipment_count,
            ];
        })->toArray();
    }

    /**
     * Get regional shipment distribution
     */
    public function getRegionalDistribution(): array
    {
        // Get shipments grouped by destination country
        $distribution = CustomerAddress::whereHas('quotesAsDestination')
            ->select('country', DB::raw('COUNT(*) as count'))
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get();

        $total = $distribution->sum('count');

        return $distribution->map(function ($item) use ($total) {
            return [
                'country' => $item->country,
                'count' => $item->count,
                'percentage' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0,
            ];
        })->toArray();
    }

    /**
     * Get revenue trends
     */
    public function getRevenueTrends(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $revenue = Payment::where('created_at', '>=', $startDate)
            ->where('status', 1) // Completed payments
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing dates with zero
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $revenue->where('date', $date)->first();

            $result[] = [
                'date' => Carbon::parse($date)->format('M d'),
                'revenue' => $dayData ? (float) $dayData->total : 0,
            ];
        }

        return $result;
    }

    /**
     * Get customer satisfaction metrics
     */
    public function getCustomerSatisfaction(): array
    {
        $ratingService = new RatingService();
        $averageRatings = $ratingService->getAverageRatings();

        $recentRatings = ShipmentRating::where('created_at', '>=', now()->subDays(30))
            ->approved()
            ->get();

        $trend = 'stable';
        if ($recentRatings->count() > 0) {
            $recentAvg = $recentRatings->avg('overall_rating');
            $previousAvg = ShipmentRating::where('created_at', '<', now()->subDays(30))
                ->where('created_at', '>=', now()->subDays(60))
                ->approved()
                ->avg('overall_rating');

            if ($recentAvg > $previousAvg) {
                $trend = 'improving';
            } elseif ($recentAvg < $previousAvg) {
                $trend = 'declining';
            }
        }

        return [
            'average_rating' => $averageRatings['overall'],
            'total_ratings' => $averageRatings['total_ratings'],
            'would_recommend' => $averageRatings['would_recommend_percentage'],
            'trend' => $trend,
            'recent_ratings_count' => $recentRatings->count(),
        ];
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        return [
            'total_shipments' => CourierInfo::count(),
            'today_shipments' => CourierInfo::where('created_at', '>=', $today)->count(),
            'month_shipments' => CourierInfo::where('created_at', '>=', $thisMonth)->count(),
            'active_shipments' => CourierInfo::whereIn('status', [1, 2])->count(), // In transit + Delivery queue
            'delivered_today' => CourierInfo::where('status', 3)->where('updated_at', '>=', $today)->count(),
            'total_revenue' => Payment::where('status', 1)->sum('amount'),
            'month_revenue' => Payment::where('status', 1)->where('created_at', '>=', $thisMonth)->sum('amount'),
            'average_rating' => round(ShipmentRating::approved()->avg('overall_rating') ?? 0, 1),
            'total_customers' => \App\Models\Customer::count(),
            'active_customers' => \App\Models\Customer::where('status', 1)->count(),
        ];
    }

    /**
     * Get shipment status breakdown
     */
    public function getShipmentStatusBreakdown(): array
    {
        $statuses = [
            0 => 'Queue',
            1 => 'In Transit',
            2 => 'Delivery Queue',
            3 => 'Delivered',
        ];

        $total = CourierInfo::count();
        $breakdown = [];

        foreach ($statuses as $code => $label) {
            $count = CourierInfo::where('status', $code)->count();
            $breakdown[] = [
                'status' => $label,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }

        return $breakdown;
    }

    /**
     * Get peak shipping hours/days
     */
    public function getPeakShippingTimes(): array
    {
        // By day of week
        $byDay = CourierInfo::selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                return [
                    'day' => $days[$item->day - 1] ?? 'Unknown',
                    'count' => $item->count,
                ];
            });

        // By hour
        $byHour = CourierInfo::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => sprintf('%02d:00', $item->hour),
                    'count' => $item->count,
                ];
            });

        return [
            'by_day' => $byDay->toArray(),
            'by_hour' => $byHour->toArray(),
        ];
    }

    /**
     * Export analytics data
     */
    public function exportAnalyticsData(string $format = 'array'): array
    {
        return [
            'monthly_costs' => $this->getMonthlyShippingCosts(),
            'carrier_usage' => $this->getMostUsedCarriers(),
            'delivery_performance' => $this->getDeliveryPerformance(),
            'popular_destinations' => $this->getPopularDestinations(),
            'customer_satisfaction' => $this->getCustomerSatisfaction(),
            'dashboard_stats' => $this->getDashboardStats(),
            'status_breakdown' => $this->getShipmentStatusBreakdown(),
            'peak_times' => $this->getPeakShippingTimes(),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
