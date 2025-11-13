<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ShipmentScheduleService;
use App\Services\WarehouseService;
use App\Services\ShippingQuoteService;
use App\Services\CourierCalculatorService;
use App\Models\WarehouseHolding;
use App\Models\CustomerAddress;
use App\Models\CourierConfiguration;
use App\Models\ShipmentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShipmentManagementController extends Controller
{
    protected ShipmentScheduleService $scheduleService;
    protected WarehouseService $warehouseService;
    protected ShippingQuoteService $quoteService;
    protected CourierCalculatorService $calculatorService;

    public function __construct(
        ShipmentScheduleService $scheduleService,
        WarehouseService $warehouseService,
        ShippingQuoteService $quoteService,
        CourierCalculatorService $calculatorService
    ) {
        $this->scheduleService = $scheduleService;
        $this->warehouseService = $warehouseService;
        $this->quoteService = $quoteService;
        $this->calculatorService = $calculatorService;
    }

    /**
     * Show available shipping schedules (FR-16).
     */
    public function showSchedules()
    {
        $customer = Auth::guard('customer')->user();
        $availableDates = $this->scheduleService->getAvailableShippingDates();

        $pageTitle = 'Shipping Schedules';
        return view('customer.shipment.schedules', compact('pageTitle', 'availableDates'));
    }

    /**
     * Show warehouse holdings (FR-17).
     */
    public function showHoldings()
    {
        $customer = Auth::guard('customer')->user();
        $holdings = $this->warehouseService->getCustomerHoldings($customer);

        $pageTitle = 'My Warehouse Holdings';
        return view('customer.shipment.holdings', compact('pageTitle', 'holdings'));
    }

    /**
     * Show single holding details.
     */
    public function showHolding(WarehouseHolding $holding)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure customer owns this holding
        if ($holding->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        $holding->load(['packages', 'branch']);

        $pageTitle = 'Holding Details';
        return view('customer.shipment.holding_details', compact('pageTitle', 'holding'));
    }

    /**
     * Show form to extend shipping date (FR-18).
     */
    public function showExtendDate(WarehouseHolding $holding)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure customer owns this holding
        if ($holding->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        // Can only extend if not yet shipped
        if ($holding->status == WarehouseHolding::STATUS_SHIPPED) {
            return back()->with('error', 'Cannot extend date for shipped holdings');
        }

        $availableDates = $this->scheduleService->getAvailableShippingDates();

        $pageTitle = 'Extend Shipping Date';
        return view('customer.shipment.extend_date', compact('pageTitle', 'holding', 'availableDates'));
    }

    /**
     * Process date extension (FR-18).
     */
    public function extendDate(Request $request, WarehouseHolding $holding)
    {
        $customer = Auth::guard('customer')->user();

        // Ensure customer owns this holding
        if ($holding->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'new_ship_date' => 'required|date|after:today',
        ]);

        try {
            $newDate = Carbon::parse($validated['new_ship_date']);

            // Check if new date is within max holding period
            if ($newDate->greaterThan($holding->max_holding_date)) {
                return redirect()->route('customer.shipment.holding.extend', $holding)
                    ->with('error', 'Selected date exceeds maximum holding period (90 days)');
            }

            $success = $this->warehouseService->extendHoldingDate($holding, $newDate);

            if ($success) {
                return redirect()->route('customer.shipment.holdings')
                    ->with('success', 'Shipping date extended successfully');
            } else {
                return redirect()->route('customer.shipment.holding.extend', $holding)
                    ->with('error', 'Failed to extend shipping date');
            }
        } catch (\Exception $e) {
            return redirect()->route('customer.shipment.holding.extend', $holding)
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show shipping calculator (FR-20).
     */
    public function showCalculator()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer->addresses()->active()->get();
        $couriers = CourierConfiguration::active()->get();

        $pageTitle = 'Shipping Calculator';
        return view('customer.shipment.calculator', compact('pageTitle', 'addresses', 'couriers'));
    }

    /**
     * Calculate shipping estimate (FR-20).
     */
    public function calculateEstimate(Request $request)
    {
        $validated = $request->validate([
            'origin_address_id' => 'required|exists:customer_addresses,id',
            'destination_address_id' => 'required|exists:customer_addresses,id',
            'weight' => 'required|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'declared_value' => 'nullable|numeric|min:0',
            'package_count' => 'nullable|integer|min:1',
        ]);

        $customer = Auth::guard('customer')->user();

        // Verify addresses belong to customer
        $originAddress = CustomerAddress::where('id', $validated['origin_address_id'])
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $destinationAddress = CustomerAddress::where('id', $validated['destination_address_id'])
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $shipmentData = [
            'weight' => $validated['weight'],
            'declared_value' => $validated['declared_value'] ?? 0,
            'package_count' => $validated['package_count'] ?? 1,
            'origin_address' => $originAddress,
            'destination_address' => $destinationAddress,
            'dimensions' => [
                'length' => $validated['length'] ?? null,
                'width' => $validated['width'] ?? null,
                'height' => $validated['height'] ?? null,
            ],
        ];

        try {
            $quotes = $this->quoteService->getMultipleQuotes($customer, $shipmentData);

            $pageTitle = 'Shipping Estimates';
            return view('customer.shipment.estimates', compact('pageTitle', 'quotes', 'shipmentData'));
        } catch (\Exception $e) {
            return redirect()->route('customer.shipment.calculator')
                ->withInput()
                ->with('error', 'Error calculating estimates: ' . $e->getMessage());
        }
    }

    /**
     * Save a quote.
     */
    public function saveQuote(Request $request)
    {
        $validated = $request->validate([
            'courier_configuration_id' => 'required|exists:courier_configurations,id',
            'origin_address_id' => 'required|exists:customer_addresses,id',
            'destination_address_id' => 'required|exists:customer_addresses,id',
            'weight' => 'required|numeric|min:0.1',
            'volume' => 'nullable|numeric|min:0',
            'declared_value' => 'nullable|numeric|min:0',
            'package_count' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $customer = Auth::guard('customer')->user();

        // Verify addresses belong to customer
        $originAddress = CustomerAddress::where('id', $validated['origin_address_id'])
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $destinationAddress = CustomerAddress::where('id', $validated['destination_address_id'])
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        try {
            $validated['origin_address'] = $originAddress;
            $validated['destination_address'] = $destinationAddress;

            $quote = $this->quoteService->createCustomerQuote($customer, $validated);

            return redirect()->route('customer.shipment.quotes')
                ->with('success', 'Quote saved successfully');
        } catch (\Exception $e) {
            return redirect()->route('customer.shipment.calculator')
                ->withInput()
                ->with('error', 'Error saving quote: ' . $e->getMessage());
        }
    }

    /**
     * Show customer's quotes.
     */
    public function showQuotes()
    {
        $customer = Auth::guard('customer')->user();
        $quotes = $customer->shippingQuotes()
            ->with(['courierConfiguration', 'originAddress', 'destinationAddress'])
            ->latest()
            ->paginate(20);

        $pageTitle = 'My Shipping Quotes';
        return view('customer.shipment.quotes', compact('pageTitle', 'quotes'));
    }
}
