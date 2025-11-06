<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\VirtualAddress;

class VirtualAddressService
{
    /**
     * Generate a unique virtual address code.
     *
     * @return string
     */
    public function generateUniqueCode(): string
    {
        do {
            // Generate code: VA-XXXXXXXX (8 random digits)
            $code = 'VA-' . strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8));
        } while (VirtualAddress::where('address_code', $code)->exists());

        return $code;
    }

    /**
     * Generate full virtual address for a customer.
     *
     * @param  Customer  $customer
     * @param  string  $addressCode
     * @return string
     */
    public function generateFullAddress(Customer $customer, string $addressCode): string
    {
        $siteName = gs('site_name') ?? 'CourierLab';

        // Build formatted virtual address
        $fullAddress = "{$customer->fullname}\n";
        $fullAddress .= "Virtual Address Code: {$addressCode}\n";
        $fullAddress .= "{$siteName} Warehouse\n";

        if ($customer->city) {
            $fullAddress .= "{$customer->city}";
            if ($customer->state) {
                $fullAddress .= ", {$customer->state}";
            }
            $fullAddress .= "\n";
        }

        if ($customer->postal_code) {
            $fullAddress .= "Postal Code: {$customer->postal_code}\n";
        }

        $fullAddress .= "Kingdom of Saudi Arabia";

        return $fullAddress;
    }

    /**
     * Assign a virtual address to a customer.
     *
     * @param  Customer  $customer
     * @return VirtualAddress
     */
    public function assignToCustomer(Customer $customer): VirtualAddress
    {
        // Check if customer already has an active virtual address
        $existingAddress = $customer->virtualAddress;

        if ($existingAddress) {
            return $existingAddress;
        }

        // Generate unique code
        $code = $this->generateUniqueCode();

        // Generate full address
        $fullAddress = $this->generateFullAddress($customer, $code);

        // Create virtual address
        $virtualAddress = VirtualAddress::create([
            'customer_id' => $customer->id,
            'address_code' => $code,
            'full_address' => $fullAddress,
            'status' => 'active',
            'assigned_at' => now(),
        ]);

        // Send notification to customer
        notify($customer, 'CUSTOMER_VIRTUAL_ADDRESS_ASSIGNED', [
            'fullname' => $customer->fullname,
            'virtual_address_code' => $code,
            'virtual_address_full' => $fullAddress,
        ]);

        return $virtualAddress;
    }

    /**
     * Cancel inactive virtual addresses (no orders in past year).
     *
     * @return int Number of addresses cancelled
     */
    public function cancelInactiveAddresses(): int
    {
        $inactiveAddresses = VirtualAddress::active()
            ->inactiveCustomers()
            ->get();

        $count = 0;
        foreach ($inactiveAddresses as $address) {
            $address->cancel('No orders in the past 12 months');
            $count++;
        }

        return $count;
    }

    /**
     * Reactivate a customer's virtual address or create a new one.
     *
     * @param  Customer  $customer
     * @return VirtualAddress
     */
    public function reactivate(Customer $customer): VirtualAddress
    {
        // Check for existing cancelled address
        $cancelledAddress = $customer->virtualAddresses()
            ->where('status', 'cancelled')
            ->latest()
            ->first();

        if ($cancelledAddress) {
            // Reactivate the cancelled address
            $cancelledAddress->status = 'active';
            $cancelledAddress->assigned_at = now();
            $cancelledAddress->cancelled_at = null;
            $cancelledAddress->cancellation_reason = null;
            $cancelledAddress->save();

            return $cancelledAddress;
        }

        // Otherwise, assign a new virtual address
        return $this->assignToCustomer($customer);
    }
}
