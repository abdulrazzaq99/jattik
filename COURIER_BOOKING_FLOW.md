# CourierLab - Complete Courier Booking & Shipment Flow Guide

## Table of Contents
1. [System Overview](#system-overview)
2. [How Courier Booking Works](#how-courier-booking-works)
3. [Shipment Management Integration](#shipment-management-integration)
4. [Customer Journey](#customer-journey)
5. [Staff Workflow](#staff-workflow)
6. [Navigation Guide](#navigation-guide)

---

## System Overview

CourierLab has **TWO MAIN SYSTEMS** that work together:

### 1. Traditional Courier Tracking System (Original)
- **Who uses it**: Staff members at branch offices
- **What it does**: Staff manually creates courier records when customers bring packages
- **Tables**: `courier_infos`, `courier_products`, `courier_payments`
- **Workflow**: Walk-in customer → Staff creates courier → Package shipped

### 2. Shipment Management System (NEW - Just Implemented)
- **Who uses it**: Customers (self-service) & Staff
- **What it does**: Customers can calculate rates, manage addresses, track warehouse holdings
- **Tables**: `customer_addresses`, `warehouse_holdings`, `warehouse_packages`, `shipping_quotes`
- **Workflow**: Online customer → Calculate rates → Book shipment → Warehouse consolidation → Shipped

---

## How Courier Booking Works

### Current System (Staff-Based)

**Step 1: Customer Walks Into Branch Office**
```
Customer brings package to CourierLab branch office
↓
Staff member logs into Staff Panel (http://localhost:8080/staff/login)
```

**Step 2: Staff Creates Courier Record**
```
Staff Panel → Courier Management → Send Courier
Route: http://localhost:8080/staff/courier/send
Controller: App\Http\Controllers\Staff\CourierController@create
```

**Staff fills out form:**
- Sender Information (can search existing customers or create new)
- Receiver Information
- Package Details (weight, dimensions, category)
- Destination Branch
- Payment Information

**Step 3: System Processes**
```php
// In CourierController@store
1. Create/validate sender customer
2. Create/validate receiver customer
3. Generate unique tracking code (getTrx())
4. Create courier_infos record (status = 0 QUEUE)
5. Create courier_products records
6. Create courier_payments record
7. Send notification to admin
```

**Step 4: Courier Status Flow**
```
Status 0: QUEUE → Waiting at sender's branch
    ↓
Status 1: DISPATCH → In transit to destination branch
    ↓
Status 2: DELIVERY_QUEUE → Arrived at receiver's branch, awaiting delivery
    ↓
Status 3: DELIVERED → Completed
```

**Step 5: Customer Can Track**
```
Customer visits: http://localhost:8080/customer/track
Enters tracking code
Views real-time status
```

---

## Shipment Management Integration

### NEW Self-Service Booking Flow (For Customers)

**Phase 1: Pre-Booking (Customer Preparation)**

**1. Customer Creates Account**
```
URL: http://localhost:8080/customer/login
- Register with mobile number
- Verify OTP
- Complete profile
```

**2. Customer Adds Addresses** (FR-14)
```
Customer Panel → Shipment Services → My Addresses
URL: http://localhost:8080/customer/addresses
- Add pickup address
- Add delivery address
- Set default address
- Can add multiple addresses for different shipments
```

**3. Customer Calculates Shipping Cost** (FR-20)
```
Customer Panel → Shipment Services → Shipping Calculator
URL: http://localhost:8080/customer/shipment/calculator
- Select origin address
- Select destination address
- Enter package details (weight, dimensions, value)
- Click "Calculate"
- System shows rates from: Aramex, DHL, FedEx, UPS, Local Courier
- Can save preferred quote
```

**Phase 2: Booking & Warehouse** (Staff Assisted)

**4. Customer Sends Package to Warehouse**
```
After calculating rates, customer brings package to CourierLab warehouse
Staff creates warehouse holding for consolidation
```

**5. Staff Creates Warehouse Holding** (FR-17, FR-21)
```
Staff Panel → Warehouse Management → Create Holding
URL: http://localhost:8080/staff/warehouse/create
- Select customer
- Receive package
- Add package details
- System tracks: 90-day max holding period
- Status: HOLDING (0)
```

**6. Customer Views Holdings** (FR-17)
```
Customer Panel → Shipment Services → Warehouse Holdings
URL: http://localhost:8080/customer/shipment/holdings
- View all packages in warehouse
- See days remaining (out of 90 days)
- Check scheduled ship date
```

**7. Customer Extends Ship Date** (FR-18)
```
From Holdings page → Click "Extend Date"
URL: http://localhost:8080/customer/shipment/extend-date/{holding_id}
- View available shipping schedules
- Select new date (must be within 90 days)
- Submit extension request
- Only allowed if not yet dispatched
```

**8. Customer Views Shipping Schedules** (FR-16)
```
Customer Panel → Shipment Services → Shipping Schedules
URL: http://localhost:8080/customer/shipment/schedules
- See fixed monthly shipping dates (e.g., 5th, 15th, 25th of each month)
- See cutoff dates (last date to book for that schedule)
- Next 3 upcoming schedules displayed
```

**Phase 3: Consolidation & Shipping**

**9. Staff Consolidates Holding**
```
Staff Panel → Warehouse → View Holding → Consolidate
- Add all packages for customer
- Calculate total weight, volume
- Mark as READY (status = 1)
- Lock customer addresses (can't change after this)
```

**10. Staff Calculates Official Shipping Fee** (FR-21)
```
Staff Panel → Warehouse → Calculate Fee
URL: http://localhost:8080/staff/warehouse/{holding}/calculate-fee
- Select courier (Aramex, DHL, etc.)
- Select origin & destination addresses
- System auto-calculates base fee
- Staff can add:
  - Handling fee
  - Customs fee
  - Discount
- Save official quote
```

**11. Staff Marks as Shipped**
```
Staff Panel → Warehouse → Mark as Shipped
- Enter actual ship date
- Status changes to SHIPPED (2)
- Customer receives notification (if implemented)
```

**12. Customer Tracks Shipment**
```
Customer Panel → My Couriers → Received Couriers
- View real-time tracking
- See courier status updates
```

---

## Customer Journey

### Complete End-to-End Example

**Scenario:** Sarah wants to ship a package from New York to Los Angeles

**Day 1 - Preparation**
```
1. Sarah registers at http://localhost:8080/customer/login
2. Verifies mobile with OTP
3. Goes to "Shipment Services" → "My Addresses"
4. Adds her New York address (pickup)
5. Adds her LA address (delivery)
```

**Day 2 - Price Checking**
```
1. Goes to "Shipment Services" → "Shipping Calculator"
2. Fills in:
   - Origin: New York address
   - Destination: LA address
   - Weight: 5 kg
   - Dimensions: 30 x 20 x 15 cm
   - Declared value: $200
3. Clicks "Calculate Estimates"
4. Sees quotes from all couriers:
   - Aramex: $45.50
   - DHL: $52.00
   - FedEx: $48.75
   - UPS: $51.25
   - Local: $38.00
5. Saves the Local Courier quote
```

**Day 3 - Drop Off Package**
```
1. Sarah brings package to CourierLab warehouse
2. Staff member receives it
3. Staff creates warehouse holding:
   - Customer: Sarah
   - Package: 5kg, 30x20x15cm
   - Received date: Today
   - Max holding date: Today + 90 days
   - Scheduled ship date: 15th of this month
```

**Day 4 - Sarah Checks Holdings**
```
1. Logs into Customer Panel
2. Goes to "Shipment Services" → "Warehouse Holdings"
3. Sees her package:
   - Days remaining: 86/90
   - Status: Holding
   - Scheduled ship: 15th (11 days from now)
```

**Day 8 - Sarah Extends Date**
```
1. Sarah needs to delay shipping
2. From Holdings → Clicks "Extend Date"
3. Views "Shipping Schedules" - sees dates: 15th, 25th
4. Selects 25th of the month
5. System validates: within 90 days ✓
6. Extension approved
```

**Day 14 - Consolidation**
```
1. Staff reviews holdings for 25th shipment
2. Consolidates Sarah's holding
3. Status changes to "Ready for Shipment"
4. Sarah's addresses are now LOCKED (can't change)
```

**Day 14 - Fee Calculation**
```
1. Staff calculates official fee:
   - Base rate: $30 (Local Courier)
   - Weight fee (5kg × $2/kg): $10
   - Handling fee: $5
   - Insurance (1% of $200): $2
   - Subtotal: $47
   - Discount: $5 (staff discretion)
   - Total: $42
2. Quote saved to system
```

**Day 25 - Shipment**
```
1. Staff marks as shipped
2. Creates courier_info record linking to warehouse holding
3. Sarah receives tracking number
4. Can track at "Track Courier"
```

---

## Staff Workflow

### Daily Tasks for Warehouse Staff

**Morning Routine:**
```
1. Login to Staff Panel
2. Go to Warehouse → Dashboard
   - View holdings nearing expiry (warn customers)
   - View today's scheduled shipments
   - View consolidation queue
```

**Receiving Packages:**
```
For each walk-in customer:
1. Warehouse → Create Holding
2. Enter customer details (or search existing)
3. Add package information
4. Print receipt with holding code
5. Give to customer
```

**Pre-Shipment Processing:**
```
1. Warehouse → Holdings List
2. Filter by scheduled ship date
3. For each holding:
   - Consolidate if ready
   - Calculate official shipping fee
   - Verify addresses
   - Generate shipping labels
```

**Shipping Day:**
```
1. Warehouse → Ready Holdings
2. Mark each as "Shipped"
3. Hand over to courier (Aramex/DHL/etc.)
4. Upload tracking numbers
```

**Monitoring:**
```
1. Warehouse → Expiring Holdings
   - Red alert: < 7 days remaining
   - Yellow warning: < 14 days
   - Contact customers to schedule shipment
```

---

## Navigation Guide

### Customer Panel Navigation

After logging in at `http://localhost:8080/customer/login`, you'll see this sidebar:

```
Dashboard
├─ Total Sent
├─ Total Received
└─ Recent Couriers

Track Courier
└─ Enter tracking code to track

My Couriers
├─ Sent Couriers (packages you sent)
└─ Received Couriers (packages sent to you)

Subscription ⭐
├─ View Plans
├─ My Subscription (if active)
└─ History

Shipment Services 🚚 [NEW!]
├─ Shipping Calculator (calculate rates from all couriers)
├─ My Addresses (manage pickup/delivery addresses)
├─ Warehouse Holdings (view packages in warehouse)
├─ Shipping Schedules (see monthly shipping dates)
└─ My Quotes (saved shipping quotes)

Payments 💳
└─ Payment History

Profile 👤
└─ Edit Profile

Logout
```

### Staff Panel Navigation

After logging in at `http://localhost:8080/staff/login`:

```
Dashboard
├─ Branch statistics
├─ Today's couriers
└─ Pending deliveries

Courier Management
├─ Send Courier (create new courier)
├─ Courier List
├─ Sent Queue
├─ Dispatch
├─ Upcoming
├─ Delivery Queue
└─ Delivered

Warehouse Management 📦 [NEW!]
├─ Dashboard (statistics & alerts)
├─ Create Holding (receive packages)
├─ Holdings List (all active holdings)
├─ Expiring Holdings (< 14 days remaining)
├─ Calculate Fees (official quote generation)
└─ Consolidation Queue

Cash Collection
└─ Income tracking

Customers
└─ Search Customers

Support Tickets
├─ View Tickets
└─ Create Ticket

Profile
└─ Edit Profile
```

**Note:** Staff sidebar is dynamic and controlled by admin. If you don't see "Warehouse Management", ask admin to add it to staff menu permissions.

---

## Integration Points

### How Old & New Systems Work Together

**1. Warehouse Holding → Courier Info**
```php
// When staff marks holding as shipped
$holding->status = WarehouseHolding::STATUS_SHIPPED;

// System creates courier_info record
$courierInfo = CourierInfo::create([
    'warehouse_holding_id' => $holding->id,
    'sender_customer_id' => $holding->customer_id,
    'code' => getTrx(), // Generate tracking number
    'status' => Status::COURIER_QUEUE,
    // ... other fields
]);
```

**2. Shared Customer Records**
```
Both systems use the same `customers` table
- Customer registered for subscription → Can book shipments
- Customer created by staff for courier → Can login and view holdings
```

**3. Address Management**
```
New shipment system uses customer_addresses table
Old courier system uses fields in courier_infos table
When creating courier from holding, copy addresses from customer_addresses
```

**4. Quote → Payment**
```
Shipping quote (new system) → Becomes courier_payment (old system)
Staff quote is authoritative for billing
```

---

## Quick Reference

### Key URLs

**Customer Access:**
- Login: http://localhost:8080/customer/login
- Dashboard: http://localhost:8080/customer/dashboard
- Addresses: http://localhost:8080/customer/addresses
- Calculator: http://localhost:8080/customer/shipment/calculator
- Holdings: http://localhost:8080/customer/shipment/holdings
- Schedules: http://localhost:8080/customer/shipment/schedules
- Quotes: http://localhost:8080/customer/shipment/quotes
- Track: http://localhost:8080/customer/track

**Staff Access:**
- Login: http://localhost:8080/staff/login
- Dashboard: http://localhost:8080/staff/dashboard
- Create Courier: http://localhost:8080/staff/courier/send
- Warehouse: http://localhost:8080/staff/warehouse
- Create Holding: http://localhost:8080/staff/warehouse/create
- Expiring: http://localhost:8080/staff/warehouse/expiring/list

### Database Tables Reference

**Customer & Auth:**
- `customers` - Customer accounts
- `customer_addresses` - Shipping addresses (NEW)

**Courier System (Original):**
- `courier_infos` - Courier tracking records
- `courier_products` - Items in courier
- `courier_payments` - Payment records

**Warehouse System (NEW):**
- `warehouse_holdings` - Consolidation tracking
- `warehouse_packages` - Individual packages in holdings
- `shipment_schedules` - Fixed monthly ship dates
- `shipping_quotes` - Rate calculations
- `courier_configurations` - Courier API settings

**Subscription System:**
- `subscription_plans` - Available plans
- `customer_subscriptions` - Active subscriptions
- `payments` - Payment records
- `coupons` - Discount coupons
- `insurance_policies` - Insurance coverage

---

## Frequently Asked Questions

### Q: Where do customers book couriers?
**A:** Customers don't book directly. They use the Shipping Calculator to get quotes, then bring packages to warehouse. Staff creates the actual courier booking.

### Q: What's the difference between "Send Courier" and "Create Holding"?
**A:**
- **Send Courier**: Immediate shipment, package leaves today, no warehouse storage
- **Create Holding**: Package stored in warehouse, consolidated later, ships on scheduled date

### Q: Can customers create courier bookings themselves?
**A:** Not in the current implementation. The shipment calculator is for quotes only. Actual booking requires staff interaction for package verification and payment.

### Q: Why do we need warehouse holdings?
**A:** For consolidation - customers can send multiple packages over time, warehouse stores them, then ships all together on a scheduled date to save on shipping costs.

### Q: What happens after 90 days?
**A:** Holdings expire. The holding status changes to EXPIRED (3), and warehouse contacts customer to either ship immediately or forfeit the package.

### Q: Can I integrate real courier APIs?
**A:** Yes! The `CourierCalculatorService` has placeholders for Aramex, DHL, FedEx, UPS APIs. Add your API keys to `courier_configurations` table and update the service methods.

---

## Next Steps

1. **Login as Customer**: http://localhost:8080/customer/login
2. **Explore Shipment Services Menu** in the sidebar
3. **Add Your First Address**
4. **Try the Shipping Calculator**
5. **Login as Staff**: http://localhost:8080/staff/login
6. **Create a Test Warehouse Holding**
7. **Calculate Shipping Fee**
8. **Mark as Shipped**

The entire flow is now connected and ready to test!
