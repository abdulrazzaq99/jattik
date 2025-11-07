<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\RatingService;
use App\Models\CourierInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    protected RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * Show rating form for a shipment
     */
    public function create(CourierInfo $courier)
    {
        $customer = Auth::guard('customer')->user();

        // Check if customer can rate this shipment
        $canRate = $this->ratingService->canRateShipment($customer, $courier);

        if (!$canRate['can_rate']) {
            return redirect()->back()
                ->with('error', $canRate['reason']);
        }

        $pageTitle = 'Rate Your Shipment';
        return view('customer.ratings.create', compact('pageTitle', 'courier'));
    }

    /**
     * Store shipment rating
     */
    public function store(Request $request, CourierInfo $courier)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'speed_rating' => 'nullable|integer|min:1|max:5',
            'packaging_rating' => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'tags' => 'nullable|array',
            'would_recommend' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        $validated['courier_info_id'] = $courier->id;

        try {
            $rating = $this->ratingService->createRating($customer, $validated);

            return redirect()->route('customer.dashboard')
                ->with('success', 'Thank you for your feedback! Your rating has been submitted.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show customer's ratings
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $ratings = $this->ratingService->getCustomerRatings($customer);

        $pageTitle = 'My Ratings';
        return view('customer.ratings.index', compact('pageTitle', 'ratings'));
    }
}
