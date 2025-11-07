<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\WarehouseService;
use App\Services\ShippingQuoteService;
use App\Models\WarehouseHolding;
use App\Models\WarehousePackage;
use App\Models\Customer;
use App\Models\CourierConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    protected WarehouseService $warehouseService;
    protected ShippingQuoteService $quoteService;

    public function __construct(
        WarehouseService $warehouseService,
        ShippingQuoteService $quoteService
    ) {
        $this->warehouseService = $warehouseService;
        $this->quoteService = $quoteService;
    }

    /**
     * Display warehouse holdings for staff's branch.
     */
    public function index()
    {
        $staff = Auth::user();
        $branchId = $staff->branch_id;

        $holdings = WarehouseHolding::where('branch_id', $branchId)
            ->with(['customer', 'packages'])
            ->latest()
            ->paginate(20);

        $statistics = $this->warehouseService->getHoldingStatistics($branchId);

        $pageTitle = 'Warehouse Holdings';
        return view('staff.warehouse.index', compact('pageTitle', 'holdings', 'statistics'));
    }

    /**
     * Show form to create new holding.
     */
    public function create()
    {
        $staff = Auth::user();
        $customers = Customer::active()->get();

        $pageTitle = 'Create Warehouse Holding';
        return view('staff.warehouse.create', compact('pageTitle', 'customers'));
    }

    /**
     * Store new warehouse holding.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'received_date' => 'nullable|date',
            'scheduled_ship_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
            'packages' => 'required|array|min:1',
            'packages.*.description' => 'required|string|max:255',
            'packages.*.weight' => 'required|numeric|min:0.1',
            'packages.*.length' => 'nullable|numeric|min:0',
            'packages.*.width' => 'nullable|numeric|min:0',
            'packages.*.height' => 'nullable|numeric|min:0',
            'packages.*.declared_value' => 'nullable|numeric|min:0',
            'packages.*.category' => 'nullable|string|max:100',
        ]);

        $staff = Auth::user();
        $customer = Customer::findOrFail($validated['customer_id']);

        try {
            $holding = $this->warehouseService->createHolding(
                $customer,
                $staff->branch,
                $validated
            );

            return redirect()->route('staff.warehouse.show', $holding)
                ->with('success', 'Warehouse holding created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating holding: ' . $e->getMessage());
        }
    }

    /**
     * Show holding details.
     */
    public function show(WarehouseHolding $holding)
    {
        $staff = Auth::user();

        // Ensure holding belongs to staff's branch
        if ($holding->branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $holding->load(['customer', 'packages', 'shippingQuotes']);

        $pageTitle = 'Holding Details';
        return view('staff.warehouse.show', compact('pageTitle', 'holding'));
    }

    /**
     * Add package to holding.
     */
    public function addPackage(Request $request, WarehouseHolding $holding)
    {
        $staff = Auth::user();

        // Ensure holding belongs to staff's branch
        if ($holding->branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'declared_value' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->warehouseService->addPackageToHolding($holding, $validated);

            return back()->with('success', 'Package added successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error adding package: ' . $e->getMessage());
        }
    }

    /**
     * Remove package from holding.
     */
    public function removePackage(WarehouseHolding $holding, WarehousePackage $package)
    {
        $staff = Auth::user();

        // Ensure holding belongs to staff's branch
        if ($holding->branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure package belongs to this holding
        if ($package->warehouse_holding_id !== $holding->id) {
            abort(403, 'Package does not belong to this holding.');
        }

        try {
            $this->warehouseService->removePackage($package);

            return back()->with('success', 'Package removed successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error removing package: ' . $e->getMessage());
        }
    }

    /**
     * Consolidate holding.
     */
    public function consolidate(WarehouseHolding $holding)
    {
        $staff = Auth::user();

        // Ensure holding belongs to staff's branch
        if ($holding->branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->warehouseService->consolidateHolding($holding, $staff->id);

            return back()->with('success', 'Holding consolidated and ready for shipment');
        } catch (\Exception $e) {
            return back()->with('error', 'Error consolidating holding: ' . $e->getMessage());
        }
    }

    /**
     * Mark holding as shipped.
     */
    public function markAsShipped(Request $request, WarehouseHolding $holding)
    {
        $staff = Auth::user();

        // Ensure holding belongs to staff's branch
        if ($holding->branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'actual_ship_date' => 'nullable|date',
        ]);

        try {
            $shipDate = isset($validated['actual_ship_date'])
                ? \Carbon\Carbon::parse($validated['actual_ship_date'])
                : null;

            $this->warehouseService->markAsShipped($holding, $shipDate);

            return back()->with('success', 'Holding marked as shipped');
        } catch (\Exception $e) {
            return back()->with('error', 'Error marking as shipped: ' . $e->getMessage());
        }
    }

    /**
     * Show holdings nearing expiry.
     */
    public function nearingExpiry()
    {
        $staff = Auth::user();
        $holdings = $this->warehouseService->getHoldingsNearingExpiry($staff->branch_id);

        $pageTitle = 'Expiring Holdings';
        return view('staff.warehouse.expiring', compact('pageTitle', 'holdings'));
    }

    /**
     * Show form to calculate shipping fee (FR-21).
     */
    public function showCalculateFee(WarehouseHolding $holding)
    {
        $staff = Auth::user();

        // Ensure holding belongs to staff's branch
        if ($holding->branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $couriers = CourierConfiguration::active()->get();
        $customerAddresses = $holding->customer->addresses()->active()->get();

        $pageTitle = 'Calculate Shipping Fee';
        return view('staff.warehouse.calculate_fee', compact('pageTitle', 'holding', 'couriers', 'customerAddresses'));
    }

    /**
     * Calculate and save shipping fee (FR-21).
     */
    public function calculateFee(Request $request, WarehouseHolding $holding)
    {
        $staff = Auth::user();

        // Ensure holding belongs to staff's branch
        if ($holding->branch_id !== $staff->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'courier_configuration_id' => 'required|exists:courier_configurations,id',
            'origin_address_id' => 'required|exists:customer_addresses,id',
            'destination_address_id' => 'required|exists:customer_addresses,id',
            'handling_fee' => 'nullable|numeric|min:0',
            'customs_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $courier = CourierConfiguration::findOrFail($validated['courier_configuration_id']);
            $originAddress = \App\Models\CustomerAddress::findOrFail($validated['origin_address_id']);
            $destinationAddress = \App\Models\CustomerAddress::findOrFail($validated['destination_address_id']);

            $addresses = [
                'origin' => $originAddress,
                'destination' => $destinationAddress,
                'origin_id' => $originAddress->id,
                'destination_id' => $destinationAddress->id,
            ];

            $quote = $this->quoteService->createQuoteForHolding($holding, $courier, $addresses);

            // Update quote with employee-calculated fees
            $quote->update([
                'calculated_by_staff_id' => $staff->id,
                'handling_fee' => $validated['handling_fee'] ?? 0,
                'customs_fee' => $validated['customs_fee'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Apply discount if provided
            if (isset($validated['discount_amount']) && $validated['discount_amount'] > 0) {
                $this->quoteService->applyDiscount($quote, $validated['discount_amount'], 'Staff discount');
            }

            // Recalculate total
            $quote->total_fee = $quote->subtotal - $quote->discount_amount;
            $quote->save();

            return redirect()->route('staff.warehouse.show', $holding)
                ->with('success', 'Shipping fee calculated and saved');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error calculating fee: ' . $e->getMessage());
        }
    }
}
