<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;

class AddressService
{
    /**
     * Create a new address for a customer.
     */
    public function createAddress(Customer $customer, array $data): CustomerAddress
    {
        return DB::transaction(function () use ($customer, $data) {
            // If this is set as default, unset other defaults
            if (isset($data['is_default']) && $data['is_default']) {
                $customer->addresses()->update(['is_default' => false]);
            }

            // If this is the first address, make it default
            if ($customer->addresses()->count() === 0) {
                $data['is_default'] = true;
            }

            return $customer->addresses()->create($data);
        });
    }

    /**
     * Update an existing address.
     */
    public function updateAddress(CustomerAddress $address, array $data): CustomerAddress
    {
        return DB::transaction(function () use ($address, $data) {
            // If setting as default, unset other defaults
            if (isset($data['is_default']) && $data['is_default']) {
                $address->customer->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($data);
            return $address->fresh();
        });
    }

    /**
     * Set an address as default.
     */
    public function setAsDefault(CustomerAddress $address): CustomerAddress
    {
        return DB::transaction(function () use ($address) {
            // Unset other defaults
            $address->customer->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);
            return $address->fresh();
        });
    }

    /**
     * Delete an address.
     */
    public function deleteAddress(CustomerAddress $address): bool
    {
        return DB::transaction(function () use ($address) {
            $wasDefault = $address->is_default;
            $customerId = $address->customer_id;

            $address->delete();

            // If deleted address was default, set another as default
            if ($wasDefault) {
                $newDefault = CustomerAddress::where('customer_id', $customerId)
                    ->where('is_active', true)
                    ->first();

                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            return true;
        });
    }

    /**
     * Get active addresses for a customer.
     */
    public function getCustomerAddresses(Customer $customer)
    {
        return $customer->addresses()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Validate address can be changed (FR-15: only before dispatch).
     */
    public function canChangeAddress($courierId): bool
    {
        $courier = \App\Models\CourierInfo::find($courierId);

        if (!$courier) {
            return false;
        }

        // Address can be changed only if not locked (before dispatch)
        return $courier->address_locked == 0;
    }
}
