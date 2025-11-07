<?php

namespace App\Services;

use App\Models\WarehouseHolding;
use App\Models\WarehousePackage;
use App\Models\Customer;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    /**
     * Create a new warehouse holding (FR-17).
     */
    public function createHolding(Customer $customer, Branch $branch, array $data): WarehouseHolding
    {
        return DB::transaction(function () use ($customer, $branch, $data) {
            $receivedDate = isset($data['received_date']) ? Carbon::parse($data['received_date']) : Carbon::today();

            $holding = WarehouseHolding::create([
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'received_date' => $receivedDate,
                'scheduled_ship_date' => $data['scheduled_ship_date'] ?? null,
                'status' => WarehouseHolding::STATUS_HOLDING,
                'notes' => $data['notes'] ?? null,
            ]);

            // Add packages if provided
            if (isset($data['packages']) && is_array($data['packages'])) {
                foreach ($data['packages'] as $packageData) {
                    $this->addPackageToHolding($holding, $packageData);
                }
            }

            return $holding->fresh();
        });
    }

    /**
     * Add a package to warehouse holding.
     */
    public function addPackageToHolding(WarehouseHolding $holding, array $data): WarehousePackage
    {
        $package = $holding->packages()->create([
            'description' => $data['description'],
            'weight' => $data['weight'],
            'length' => $data['length'] ?? null,
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'volume' => $data['volume'] ?? null,
            'declared_value' => $data['declared_value'] ?? 0,
            'category' => $data['category'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Totals are updated automatically via model events
        return $package;
    }

    /**
     * Update package in holding.
     */
    public function updatePackage(WarehousePackage $package, array $data): WarehousePackage
    {
        $package->update($data);
        return $package->fresh();
    }

    /**
     * Remove package from holding.
     */
    public function removePackage(WarehousePackage $package): bool
    {
        return $package->delete();
    }

    /**
     * Mark holding as ready for shipment.
     */
    public function markAsReady(WarehouseHolding $holding, $scheduledShipDate = null): WarehouseHolding
    {
        $holding->update([
            'status' => WarehouseHolding::STATUS_READY,
            'scheduled_ship_date' => $scheduledShipDate ?? $holding->scheduled_ship_date,
        ]);

        return $holding->fresh();
    }

    /**
     * Consolidate holding and prepare for shipment.
     */
    public function consolidateHolding(WarehouseHolding $holding, $staffId): WarehouseHolding
    {
        return DB::transaction(function () use ($holding, $staffId) {
            $holding->update([
                'status' => WarehouseHolding::STATUS_READY,
                'consolidated_by_staff_id' => $staffId,
                'consolidated_at' => Carbon::now(),
            ]);

            return $holding->fresh();
        });
    }

    /**
     * Mark holding as shipped.
     */
    public function markAsShipped(WarehouseHolding $holding, Carbon $actualShipDate = null): WarehouseHolding
    {
        $holding->update([
            'status' => WarehouseHolding::STATUS_SHIPPED,
            'actual_ship_date' => $actualShipDate ?? Carbon::today(),
        ]);

        return $holding->fresh();
    }

    /**
     * Extend holding period (FR-18).
     */
    public function extendHoldingDate(WarehouseHolding $holding, Carbon $newShipDate): bool
    {
        // Can only extend if not yet shipped
        if ($holding->status == WarehouseHolding::STATUS_SHIPPED) {
            return false;
        }

        // Check if new date is within max holding period
        if ($newShipDate->greaterThan($holding->max_holding_date)) {
            return false;
        }

        $holding->scheduled_ship_date = $newShipDate;
        return $holding->save();
    }

    /**
     * Get holdings nearing expiry (within 7 days).
     */
    public function getHoldingsNearingExpiry($branchId = null)
    {
        $expiryThreshold = Carbon::today()->addDays(7);

        $query = WarehouseHolding::where('status', WarehouseHolding::STATUS_HOLDING)
            ->where('max_holding_date', '<=', $expiryThreshold)
            ->where('max_holding_date', '>=', Carbon::today())
            ->with(['customer', 'packages']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('max_holding_date')->get();
    }

    /**
     * Mark expired holdings.
     */
    public function markExpiredHoldings(): int
    {
        return WarehouseHolding::where('status', WarehouseHolding::STATUS_HOLDING)
            ->where('max_holding_date', '<', Carbon::today())
            ->update(['status' => WarehouseHolding::STATUS_EXPIRED]);
    }

    /**
     * Get holding statistics for a branch.
     */
    public function getHoldingStatistics($branchId): array
    {
        $total = WarehouseHolding::where('branch_id', $branchId)->count();
        $holding = WarehouseHolding::where('branch_id', $branchId)->holding()->count();
        $ready = WarehouseHolding::where('branch_id', $branchId)->ready()->count();
        $shipped = WarehouseHolding::where('branch_id', $branchId)->shipped()->count();
        $expired = WarehouseHolding::where('branch_id', $branchId)->expired()->count();

        $totalWeight = WarehouseHolding::where('branch_id', $branchId)
            ->where('status', WarehouseHolding::STATUS_HOLDING)
            ->sum('total_weight');

        $totalPackages = WarehouseHolding::where('branch_id', $branchId)
            ->where('status', WarehouseHolding::STATUS_HOLDING)
            ->sum('package_count');

        return [
            'total_holdings' => $total,
            'holding' => $holding,
            'ready' => $ready,
            'shipped' => $shipped,
            'expired' => $expired,
            'total_weight' => $totalWeight,
            'total_packages' => $totalPackages,
        ];
    }

    /**
     * Get holdings for customer.
     */
    public function getCustomerHoldings(Customer $customer, $status = null)
    {
        $query = $customer->warehouseHoldings()->with(['packages', 'branch']);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
