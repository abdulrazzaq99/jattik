<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CourierInfo;
use App\Models\ShipmentRating;
use Illuminate\Support\Facades\DB;

class RatingService
{
    /**
     * Create a shipment rating (FR-33)
     */
    public function createRating(Customer $customer, array $data): ShipmentRating
    {
        // Verify shipment is delivered
        $courier = CourierInfo::findOrFail($data['courier_info_id']);

        if ($courier->status != 3) { // 3 = DELIVERED
            throw new \Exception('Can only rate delivered shipments');
        }

        // Check if already rated
        $existing = ShipmentRating::where('customer_id', $customer->id)
            ->where('courier_info_id', $courier->id)
            ->first();

        if ($existing) {
            throw new \Exception('You have already rated this shipment');
        }

        DB::beginTransaction();

        try {
            $rating = ShipmentRating::create([
                'customer_id' => $customer->id,
                'courier_info_id' => $courier->id,
                'overall_rating' => $data['overall_rating'],
                'speed_rating' => $data['speed_rating'] ?? null,
                'packaging_rating' => $data['packaging_rating'] ?? null,
                'communication_rating' => $data['communication_rating'] ?? null,
                'value_rating' => $data['value_rating'] ?? null,
                'comment' => $data['comment'] ?? null,
                'tags' => $data['tags'] ?? [],
                'would_recommend' => $data['would_recommend'] ?? true,
                'is_public' => $data['is_public'] ?? true,
            ]);

            // Thank customer for feedback
            notify($customer, 'RATING_THANK_YOU', [
                'customer_name' => $customer->fullname,
                'tracking_code' => $courier->code,
            ]);

            DB::commit();
            return $rating;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get customer's ratings
     */
    public function getCustomerRatings(Customer $customer)
    {
        return ShipmentRating::where('customer_id', $customer->id)
            ->with('courierInfo')
            ->latest()
            ->get();
    }

    /**
     * Get public ratings
     */
    public function getPublicRatings(int $perPage = 20)
    {
        return ShipmentRating::public()
            ->with(['customer', 'courierInfo'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get ratings for a specific courier/shipment
     */
    public function getCourierRating(CourierInfo $courier)
    {
        return ShipmentRating::where('courier_info_id', $courier->id)
            ->approved()
            ->with('customer')
            ->first();
    }

    /**
     * Get average ratings
     */
    public function getAverageRatings(): array
    {
        $ratings = ShipmentRating::approved()->get();

        if ($ratings->isEmpty()) {
            return [
                'overall' => 0,
                'speed' => 0,
                'packaging' => 0,
                'communication' => 0,
                'value' => 0,
                'total_ratings' => 0,
                'would_recommend_percentage' => 0,
            ];
        }

        return [
            'overall' => round($ratings->avg('overall_rating'), 1),
            'speed' => round($ratings->avg('speed_rating'), 1),
            'packaging' => round($ratings->avg('packaging_rating'), 1),
            'communication' => round($ratings->avg('communication_rating'), 1),
            'value' => round($ratings->avg('value_rating'), 1),
            'total_ratings' => $ratings->count(),
            'would_recommend_percentage' => round(($ratings->where('would_recommend', true)->count() / $ratings->count()) * 100),
        ];
    }

    /**
     * Get ratings by star level
     */
    public function getRatingDistribution(): array
    {
        $total = ShipmentRating::approved()->count();

        if ($total === 0) {
            return [
                5 => 0,
                4 => 0,
                3 => 0,
                2 => 0,
                1 => 0,
            ];
        }

        $distribution = [];
        for ($star = 5; $star >= 1; $star--) {
            $count = ShipmentRating::approved()
                ->where('overall_rating', $star)
                ->count();

            $distribution[$star] = [
                'count' => $count,
                'percentage' => round(($count / $total) * 100),
            ];
        }

        return $distribution;
    }

    /**
     * Get high-rated shipments
     */
    public function getHighRatedShipments(int $limit = 10)
    {
        return ShipmentRating::highRated(4)
            ->public()
            ->with(['customer', 'courierInfo'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Approve rating (admin action)
     */
    public function approveRating(int $ratingId): bool
    {
        $rating = ShipmentRating::findOrFail($ratingId);
        $rating->approve();

        return true;
    }

    /**
     * Reject/hide rating (admin action)
     */
    public function rejectRating(int $ratingId, string $reason = null): bool
    {
        $rating = ShipmentRating::findOrFail($ratingId);
        $rating->reject($reason);

        return true;
    }

    /**
     * Get ratings pending moderation
     */
    public function getPendingRatings()
    {
        return ShipmentRating::pendingApproval()
            ->with(['customer', 'courierInfo'])
            ->latest()
            ->get();
    }

    /**
     * Get ratings analytics for admin
     */
    public function getRatingsAnalytics(): array
    {
        $thirtyDaysAgo = now()->subDays(30);

        return [
            'average_ratings' => $this->getAverageRatings(),
            'rating_distribution' => $this->getRatingDistribution(),
            'recent_ratings_count' => ShipmentRating::where('created_at', '>=', $thirtyDaysAgo)->count(),
            'pending_moderation' => ShipmentRating::pendingApproval()->count(),
            'total_ratings' => ShipmentRating::count(),
            'public_ratings' => ShipmentRating::public()->count(),
        ];
    }

    /**
     * Check if customer can rate a shipment
     */
    public function canRateShipment(Customer $customer, CourierInfo $courier): array
    {
        // Check if delivered
        if ($courier->status != 3) {
            return [
                'can_rate' => false,
                'reason' => 'Shipment must be delivered before rating',
            ];
        }

        // Check if already rated
        $existing = ShipmentRating::where('customer_id', $customer->id)
            ->where('courier_info_id', $courier->id)
            ->first();

        if ($existing) {
            return [
                'can_rate' => false,
                'reason' => 'You have already rated this shipment',
            ];
        }

        return [
            'can_rate' => true,
            'reason' => null,
        ];
    }
}
