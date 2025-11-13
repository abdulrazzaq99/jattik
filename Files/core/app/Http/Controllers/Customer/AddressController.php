<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Display a listing of customer addresses (FR-14).
     */
    public function index()
    {
        $pageTitle = 'My Addresses';
        $customer = Auth::guard('customer')->user();
        $addresses = $this->addressService->getCustomerAddresses($customer);

        return view('customer.address.index', compact('pageTitle', 'addresses'));
    }

    /**
     * Show the form for creating a new address.
     */
    public function create()
    {
        $pageTitle = 'Add New Address';
        return view('customer.address.create', compact('pageTitle'));
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:100',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $customer = Auth::guard('customer')->user();

        try {
            $address = $this->addressService->createAddress($customer, $validated);

            return redirect()->route('customer.addresses.index')
                ->with('success', 'Address added successfully');
        } catch (\Exception $e) {
            return redirect()->route('customer.addresses.create')
                ->withInput()
                ->with('error', 'Failed to add address: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an address.
     */
    public function edit(CustomerAddress $address)
    {
        // Ensure customer owns this address
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $pageTitle = 'Edit Address';
        return view('customer.address.edit', compact('pageTitle', 'address'));
    }

    /**
     * Update an address (FR-15: only if not locked).
     */
    public function update(Request $request, CustomerAddress $address)
    {
        // Ensure customer owns this address
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'label' => 'nullable|string|max:100',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        try {
            $this->addressService->updateAddress($address, $validated);

            return redirect()->route('customer.addresses.index')
                ->with('success', 'Address updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('customer.addresses.edit', $address)
                ->withInput()
                ->with('error', 'Failed to update address: ' . $e->getMessage());
        }
    }

    /**
     * Set an address as default.
     */
    public function setDefault(CustomerAddress $address)
    {
        // Ensure customer owns this address
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->addressService->setAsDefault($address);

            return redirect()->route('customer.addresses.index')->with('success', 'Default address updated');
        } catch (\Exception $e) {
            return redirect()->route('customer.addresses.index')->with('error', 'Failed to set default address: ' . $e->getMessage());
        }
    }

    /**
     * Remove an address.
     */
    public function destroy(CustomerAddress $address)
    {
        // Ensure customer owns this address
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->addressService->deleteAddress($address);

            return redirect()->route('customer.addresses.index')
                ->with('success', 'Address deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('customer.addresses.index')->with('error', 'Failed to delete address: ' . $e->getMessage());
        }
    }
}
