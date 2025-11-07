# Complete Testing Guide - CourierLab Features

## Overview
This guide covers testing for all implemented features:
1. **Customer Authentication System**
2. **Subscription Management System**
3. **Shipment Management System** (NEW)

---

## Prerequisites

### 1. Verify System is Running
```bash
# Check Docker containers
docker-compose ps

# Should show:
# jattik_app    - Running
# jattik_mysql  - Running
```

### 2. Access URLs
- **Main App**: http://localhost:8080
- **Customer Portal**: http://localhost:8080/customer
- **Staff Portal**: http://localhost:8080/staff
- **Admin Portal**: http://localhost:8080/admin
- **phpMyAdmin**: http://localhost:8081

### 3. Database Check
```bash
# Verify all tables exist
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SHOW TABLES;"

# Should include new tables:
# - customer_addresses
# - shipment_schedules
# - warehouse_holdings
# - warehouse_packages
# - courier_configurations
# - shipping_quotes
```

---

## Part 1: Customer Authentication Testing

### Test 1.1: Customer Registration
1. **Navigate to Registration**
   - URL: http://localhost:8080/customer/register
   - Should see registration form

2. **Register New Customer**
   ```
   First Name: John
   Last Name: Doe
   Email: john.doe@example.com
   Mobile: +1234567890
   Country Code: +1
   Password: Password123!
   Confirm Password: Password123!
   ```

3. **Verify OTP Flow**
   - After registration, should redirect to OTP verification
   - Check database for OTP:
   ```bash
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT username, email, otp_code FROM customers WHERE email='john.doe@example.com';"
   ```
   - Enter the OTP code shown
   - Should redirect to dashboard

4. **Expected Result**: ✅
   - Customer created successfully
   - OTP sent and verified
   - Redirected to customer dashboard

### Test 1.2: Customer Login
1. **Logout** (if logged in)
   - Click logout from dashboard

2. **Login**
   - URL: http://localhost:8080/customer/login
   - Enter credentials (email/mobile and password)
   - Verify OTP if required

3. **Expected Result**: ✅
   - Successfully logged in
   - Dashboard accessible

### Test 1.3: Profile Management
1. **View Profile**
   - URL: http://localhost:8080/customer/profile
   - Should display customer information

2. **Update Profile**
   - Change first/last name
   - Update address fields
   - Click "Update Profile"

3. **Change Password**
   - URL: http://localhost:8080/customer/password
   - Enter current password
   - Enter new password
   - Confirm new password
   - Submit

4. **Expected Result**: ✅
   - Profile updated successfully
   - Password changed successfully

---

## Part 2: Subscription System Testing

### Test 2.1: View Subscription Plans
1. **Navigate to Plans**
   - URL: http://localhost:8080/customer/subscription/plans
   - Should see 3 plans:
     - **Free Tier** - $0/month
     - **Basic Plan** - $9.99/month (5 shipments)
     - **Premium Plan** - $29.99/month (Unlimited + insurance)

2. **Verify Plan Details**
   - Each plan shows features
   - Pricing clearly displayed
   - Subscribe button visible

3. **Expected Result**: ✅
   - All plans displayed
   - Details clear and accurate

### Test 2.2: Subscribe to Free Plan
1. **Select Free Plan**
   - Click "Subscribe" on Free Tier
   - Should activate immediately (no payment)

2. **Verify Subscription**
   - URL: http://localhost:8080/customer/subscription/current
   - Should show:
     - Plan: Free Tier
     - Status: Active
     - Shipments Used: 0/1

3. **Check Database**
   ```bash
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT * FROM customer_subscriptions WHERE customer_id=1;"
   ```

4. **Expected Result**: ✅
   - Free plan activated
   - No payment required
   - Shipment limit: 1 per month

### Test 2.3: Subscribe to Premium Plan
1. **Upgrade to Premium**
   - Go back to plans page
   - Click "Subscribe" on Premium Plan
   - Should redirect to checkout

2. **Checkout Process**
   - URL: http://localhost:8080/customer/payment/checkout/{payment_id}
   - Should see:
     - Plan details
     - Price: $29.99
     - Payment methods
     - Coupon code field

3. **Apply Coupon (Optional)**
   - Check available coupons:
   ```bash
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT code, discount_type, discount_value FROM coupons WHERE status='active';"
   ```
   - Enter coupon code (if any exist)
   - Click "Apply Coupon"
   - Should see discount applied

4. **Complete Payment**
   - Select payment method
   - Complete payment process
   - Should redirect to success page

5. **Verify Premium Features**
   - Go to: http://localhost:8080/customer/subscription/current
   - Should show:
     - Plan: Premium Plan
     - Status: Active
     - Shipments: Unlimited
     - Free Insurance: Yes

6. **Expected Result**: ✅
   - Premium plan activated
   - Payment processed
   - Unlimited shipments available
   - Insurance included

### Test 2.4: Subscription Management
1. **View Subscription History**
   - URL: http://localhost:8080/customer/subscription/history
   - Should show all past subscriptions

2. **Toggle Auto-Renew**
   - Go to current subscription page
   - Click "Toggle Auto-Renew"
   - Should turn on/off

3. **Cancel Subscription**
   - Click "Cancel Subscription"
   - Confirm cancellation
   - Status should change to "Cancelled"
   - Still valid until end date

4. **Resume Subscription**
   - If cancelled, click "Resume Subscription"
   - Should reactivate

5. **Expected Result**: ✅
   - All subscription actions work
   - History tracked properly

---

## Part 3: Shipment Management Testing (NEW)

### Test 3.1: Address Management (FR-14)

#### 3.1.1: Create First Address
1. **Navigate to Addresses**
   - URL: http://localhost:8080/customer/addresses
   - Should show empty state: "No addresses found"

2. **Add New Address**
   - Click "Add New Address" button
   - Fill in form:
   ```
   Label: Home
   Full Name: John Doe
   Phone: +1234567890
   Email: john.doe@example.com
   Address Line 1: 123 Main Street
   Address Line 2: Apt 4B
   City: New York
   State: NY
   Postal Code: 10001
   Country: USA
   ✓ Set as default address
   ```
   - Click "Save Address"

3. **Verify Address Created**
   - Should redirect to addresses list
   - Address should display with "Default" badge
   - Check database:
   ```bash
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT * FROM customer_addresses WHERE customer_id=1;"
   ```

4. **Expected Result**: ✅
   - Address created successfully
   - Marked as default
   - Listed in addresses page

#### 3.1.2: Create Multiple Addresses
1. **Add Office Address**
   ```
   Label: Office
   Full Name: John Doe
   Phone: +1234567891
   Address Line 1: 456 Business Ave
   City: New York
   State: NY
   Postal Code: 10002
   Country: USA
   ```

2. **Add Warehouse Address**
   ```
   Label: Warehouse
   Full Name: John Doe
   Phone: +1234567892
   Address Line 1: 789 Industrial Blvd
   City: Brooklyn
   State: NY
   Postal Code: 11201
   Country: USA
   ```

3. **Verify Multiple Addresses**
   - Should now have 3 addresses
   - Home should still be default
   - Each with edit/delete buttons

4. **Expected Result**: ✅
   - Multiple addresses supported
   - All addresses visible
   - Default address indicated

#### 3.1.3: Edit Address
1. **Edit Office Address**
   - Click edit button on Office address
   - Change Address Line 2: "Suite 200"
   - Change Phone: +1234567899
   - Click "Update Address"

2. **Verify Changes**
   - Should show updated information
   - Changes should persist

3. **Expected Result**: ✅
   - Address updated successfully

#### 3.1.4: Change Default Address
1. **Set Office as Default**
   - Click "Set as Default" button on Office address
   - Office should now have "Default" badge
   - Home should no longer be default

2. **Expected Result**: ✅
   - Default address changed
   - Only one default at a time

#### 3.1.5: Delete Address
1. **Delete Warehouse Address**
   - Click delete button
   - Confirm deletion
   - Address should be removed

2. **Try to Delete Default Address**
   - Click delete on Office (current default)
   - Should delete successfully
   - Another address should become default automatically

3. **Expected Result**: ✅
   - Non-default addresses delete normally
   - Deleting default auto-assigns new default

### Test 3.2: Shipping Schedules (FR-16)

#### 3.2.1: View Available Schedules
1. **Navigate to Schedules**
   - URL: http://localhost:8080/customer/shipment/schedules
   - Should see 3 monthly schedules:
     - First Week Shipping (Day 5)
     - Mid-Month Shipping (Day 15)
     - End of Month Shipping (Day 25)

2. **Verify Schedule Details**
   - Each card shows:
     - Schedule name
     - Ship date (actual date)
     - Cutoff date (2-3 days before)
     - Days until shipment
     - Availability status

3. **Check Cutoff Logic**
   - If today is Nov 3, Day 5 schedule should be available
   - If today is Nov 4 or later, Day 5 might show "Cutoff period"
   - Future months should show

4. **Expected Result**: ✅
   - All schedules displayed
   - Dates calculated correctly
   - Cutoff warnings shown

### Test 3.3: Shipping Calculator (FR-19, FR-20)

#### 3.3.1: Basic Rate Calculation
1. **Navigate to Calculator**
   - URL: http://localhost:8080/customer/shipment/calculator
   - Should see calculator form

2. **Calculate Shipping Rate**
   - Select Origin Address: Home
   - Select Destination Address: Office
   - Enter Weight: 5.5 kg
   - Enter Declared Value: $500
   - Enter Package Count: 2
   - Dimensions (optional):
     - Length: 30 cm
     - Width: 20 cm
     - Height: 15 cm
   - Click "Calculate Rates from All Couriers"

3. **View Rate Comparison**
   - Should redirect to estimates page
   - Should see 5 courier options:
     - Local Courier (cheapest)
     - Aramex
     - DHL Express
     - FedEx International
     - UPS Worldwide
   - Each showing:
     - Total fee
     - Fee breakdown (base, weight, insurance, fuel)
     - Transit days (if available)
     - "Best Rate" badge on cheapest

4. **Verify Calculations**
   - Local Courier should calculate:
     - Base: $5.00
     - Weight: 5.5 kg × $3.00 = $16.50
     - Insurance: $500 × 1% = $5.00
     - Fuel: ~3.98
     - Total: ~$30.48

5. **Expected Result**: ✅
   - All couriers show rates
   - Sorted by price (lowest first)
   - Calculations accurate

#### 3.3.2: Save Quote
1. **Save a Quote**
   - Click "Save This Quote" on Local Courier
   - Should redirect to success message

2. **View Saved Quotes**
   - URL: http://localhost:8080/customer/shipment/quotes
   - Should see saved quote with:
     - Quote number
     - Courier name
     - Route (from/to)
     - Weight and package count
     - Total fee
     - Valid until date
     - Status: Draft

3. **Verify in Database**
   ```bash
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT quote_number, courier_name, total_fee, status FROM shipping_quotes;"
   ```

4. **Expected Result**: ✅
   - Quote saved successfully
   - Listed in quotes page
   - All details accurate

### Test 3.4: Warehouse Holdings (FR-17) - Staff Testing

#### 3.4.1: Staff Login
1. **Login as Staff**
   - URL: http://localhost:8080/staff
   - Use staff credentials from database:
   ```bash
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT username, email FROM users WHERE user_type='staff' LIMIT 1;"
   ```

2. **Navigate to Warehouse**
   - URL: http://localhost:8080/staff/warehouse
   - Should see warehouse dashboard

3. **Expected Result**: ✅
   - Staff logged in
   - Warehouse dashboard accessible

#### 3.4.2: Create Warehouse Holding
1. **Create New Holding**
   - Click "New Holding" button
   - Select Customer: John Doe
   - Received Date: Today
   - Scheduled Ship Date: (select future date within 90 days)
   - Notes: "Customer request for consolidation"

2. **Add Packages**
   - Package 1:
     - Description: Electronics - Laptop
     - Weight: 2.5 kg
     - Length: 40 cm, Width: 30 cm, Height: 5 cm
     - Declared Value: $1200
     - Category: Electronics

   - Click "Add Another Package"

   - Package 2:
     - Description: Clothing - Winter Jackets
     - Weight: 1.5 kg
     - Length: 35 cm, Width: 25 cm, Height: 10 cm
     - Declared Value: $300
     - Category: Clothing

3. **Submit Holding**
   - Click "Create Holding"
   - Should redirect to holding details

4. **Verify Holding Created**
   - Should see:
     - Unique holding code (WH...)
     - Status: Holding
     - Total packages: 2
     - Total weight: 4.0 kg
     - Max holding date: Today + 90 days
     - Days remaining: 90

5. **Check Database**
   ```bash
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT holding_code, customer_id, package_count, total_weight, days_remaining FROM warehouse_holdings;"
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT package_code, description, weight FROM warehouse_packages;"
   ```

6. **Expected Result**: ✅
   - Holding created successfully
   - Packages added
   - Totals calculated automatically
   - Expiry date set to 90 days

#### 3.4.3: Add More Packages to Existing Holding
1. **View Holding Details**
   - From warehouse list, click "View" on the holding
   - Should see full details with package list

2. **Add Package** (if status is still "Holding")
   - Should see "Add Package" form
   - Add Package 3:
     ```
     Description: Books
     Weight: 3.0 kg
     Declared Value: $150
     ```
   - Submit

3. **Verify Updated Totals**
   - Total packages: 3
   - Total weight: 7.0 kg (updated automatically)

4. **Expected Result**: ✅
   - Package added to holding
   - Totals recalculated

### Test 3.5: Calculate Shipping Fee (FR-21) - Staff Function

#### 3.5.1: Calculate Fee for Holding
1. **From Holding Details**
   - Click "Calculate Shipping Fee" button
   - Should redirect to fee calculation page

2. **Select Options**
   - Select Courier: DHL Express
   - Origin Address: (customer's address - auto-populated)
   - Destination Address: (another customer address)
   - Additional Fees:
     - Handling Fee: $10.00
     - Customs Fee: $25.00
     - Discount: $5.00 (staff discount)
   - Notes: "Premium customer - 5% discount applied"

3. **Calculate & Save**
   - Click "Calculate & Save Fee"
   - Should create shipping quote

4. **Verify Quote Created**
   - Should redirect back to holding details
   - Should see quote in "Shipping Quotes" section with:
     - Quote number
     - Courier: DHL Express
     - Total fee (including custom fees)
     - Status: Draft
     - "Staff Quote" badge
     - Staff who calculated it

5. **Check Calculation**
   - DHL for 7.0 kg:
     - Base: $20.00
     - Weight: 7.0 kg × $10.00 = $70.00
     - Insurance: $1650 × 1.5% = $24.75
     - Fuel surcharge: ~17.21
     - Handling: $10.00 (custom)
     - Customs: $25.00 (custom)
     - Subtotal: ~$167.96
     - Discount: -$5.00
     - **Total: ~$161.96**

6. **Expected Result**: ✅
   - Fee calculated by staff
   - Custom fees applied
   - Discount applied
   - Quote saved with "Employee" type

### Test 3.6: Consolidate & Ship (FR-17 continued)

#### 3.6.1: Mark as Ready
1. **Consolidate Holding**
   - From holding details page
   - Click "Mark as Ready" button
   - Confirm action
   - Status should change to "Ready"
   - Should show consolidation timestamp and staff

2. **Verify Consolidation**
   - Status badge: "Ready"
   - Consolidated by: (staff name)
   - Consolidated at: (timestamp)

3. **Expected Result**: ✅
   - Holding marked as ready
   - Consolidation recorded

#### 3.6.2: Mark as Shipped
1. **Ship the Holding**
   - Click "Mark as Shipped" button
   - Modal appears
   - Select Actual Ship Date: Today
   - Click "Confirm Shipment"

2. **Verify Shipment**
   - Status: "Shipped"
   - Actual ship date: Today
   - Can no longer be edited

3. **Expected Result**: ✅
   - Holding shipped
   - Final status recorded

### Test 3.7: Extend Shipping Date (FR-18) - Customer

#### 3.7.1: View Customer Holdings
1. **Login as Customer**
   - Switch back to customer account
   - URL: http://localhost:8080/customer/shipment/holdings
   - Should see holdings created for this customer

2. **View Holding Details**
   - Click on a holding (must be in "Holding" status, not shipped)
   - Should see:
     - Holding code
     - Packages list
     - Current scheduled ship date
     - Days remaining

3. **Expected Result**: ✅
   - Customer can view their holdings

#### 3.7.2: Extend Ship Date
1. **Request Extension**
   - Click "Extend Shipping Date" button
   - Should see available future schedules

2. **Select New Date**
   - Choose a later schedule (within 90 days)
   - Select radio button for new date
   - Click "Confirm Extension"

3. **Verify Extension**
   - Should redirect back to holding details
   - Original ship date: (stored for reference)
   - New scheduled ship date: (updated)
   - Days remaining: (recalculated)

4. **Expected Result**: ✅
   - Ship date extended
   - Original date preserved
   - Within 90-day limit

### Test 3.8: Expiring Holdings Alert (FR-17)

#### 3.8.1: View Expiring Holdings (Staff)
1. **Check Expiring Holdings**
   - URL: http://localhost:8080/staff/warehouse/expiring
   - Should show holdings expiring within 7 days

2. **Create Test Holding Near Expiry**
   - Create a new holding
   - Set received date to 84 days ago:
   ```bash
   # Manually update in database for testing
   docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; UPDATE warehouse_holdings SET received_date = DATE_SUB(NOW(), INTERVAL 84 DAY), max_holding_date = DATE_ADD(DATE_SUB(NOW(), INTERVAL 84 DAY), INTERVAL 90 DAY) WHERE id=(SELECT MAX(id) FROM (SELECT id FROM warehouse_holdings) as t);"
   ```

3. **Refresh Expiring Page**
   - Should now see the holding
   - Days remaining: 6 days
   - Color-coded: Warning (yellow) or Critical (red)

4. **Expected Result**: ✅
   - Expiring holdings displayed
   - Color-coded by urgency
   - Statistics shown

### Test 3.9: Address Lock (FR-15)

#### 3.9.1: Test Address Change Before Dispatch
1. **While Holding Status = "Holding"**
   - Customer should be able to edit addresses
   - Try updating Home address
   - Should succeed

2. **After Marked as "Ready" or "Shipped"**
   - Address is effectively locked
   - This is controlled at courier_infos level with `address_locked` field

3. **Expected Result**: ✅
   - Addresses changeable before consolidation
   - Locked after dispatch

---

## Part 4: Integration Testing

### Test 4.1: Subscription + Shipment Workflow
1. **Free Tier Customer**
   - Subscribe to Free Tier (1 shipment/month)
   - Try to create multiple shipments
   - Should see limit reached after 1

2. **Premium Customer**
   - Upgrade to Premium
   - Unlimited shipments
   - Free insurance on holdings

3. **Expected Result**: ✅
   - Subscription limits enforced
   - Premium perks work

### Test 4.2: Complete Customer Journey
1. **Register** → 2. **Verify OTP** → 3. **Subscribe to Premium** →
4. **Add Multiple Addresses** → 5. **Use Shipping Calculator** →
6. **Save Quotes** → 7. **View Holdings** → 8. **Extend Shipping Dates**

### Test 4.3: Complete Staff Journey
1. **Login as Staff** → 2. **Create Warehouse Holding** →
3. **Add Packages** → 4. **Calculate Shipping Fee** →
5. **Apply Discounts** → 6. **Consolidate Holding** →
7. **Mark as Shipped** → 8. **Monitor Expiring Holdings**

---

## Part 5: Database Verification

### Check All Data
```bash
# Customer Data
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT id, username, email, is_premium FROM customers;"

# Subscriptions
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT customer_id, plan_id, status, shipments_used, shipments_limit FROM customer_subscriptions;"

# Addresses
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT customer_id, label, city, country, is_default FROM customer_addresses;"

# Shipment Schedules
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT name, day_of_month, cutoff_days_before, is_active FROM shipment_schedules;"

# Warehouse Holdings
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT holding_code, customer_id, status, package_count, total_weight, days_remaining FROM warehouse_holdings;"

# Warehouse Packages
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT package_code, warehouse_holding_id, description, weight, declared_value FROM warehouse_packages;"

# Courier Configurations
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT name, code, base_rate, per_kg_rate, insurance_percentage, is_active FROM courier_configurations;"

# Shipping Quotes
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT quote_number, customer_id, courier_name, total_fee, quote_type, status FROM shipping_quotes;"
```

---

## Part 6: Error Testing

### Test Error Handling
1. **Invalid Data**
   - Try submitting empty forms
   - Enter negative weights
   - Enter invalid dates (past dates)
   - Expected: Validation errors shown

2. **Authorization**
   - Try accessing other customer's addresses
   - Try editing other customer's holdings
   - Expected: 403 Forbidden

3. **Business Logic**
   - Try extending date beyond 90 days
   - Try shipping already-shipped holding
   - Try deleting holding with packages
   - Expected: Error messages displayed

---

## Part 7: UI/UX Testing

### Visual Checks
1. **Responsive Design**
   - Test on mobile viewport (375px)
   - Test on tablet (768px)
   - Test on desktop (1920px)
   - Expected: All layouts responsive

2. **Status Badges**
   - Holdings: Holding (yellow), Ready (blue), Shipped (green), Expired (red)
   - Quotes: Draft (gray), Sent (blue), Accepted (green), Expired (red)

3. **Icons & Colors**
   - All icons display (las la-* icons)
   - Colors match theme
   - Hover states work

---

## Part 8: Performance Testing

### Load Testing
```bash
# Check number of queries
docker exec jattik_app php Files/core/artisan debugbar:clear

# Navigate through pages and monitor queries
# Should be optimized with eager loading
```

---

## Quick Test Checklist

Use this for rapid testing:

### Customer Features (15 tests)
- [ ] Register new customer
- [ ] Login with OTP
- [ ] View subscription plans
- [ ] Subscribe to Free Tier
- [ ] Upgrade to Premium
- [ ] Create first address
- [ ] Create multiple addresses (3+)
- [ ] Edit an address
- [ ] Change default address
- [ ] View shipping schedules
- [ ] Use shipping calculator
- [ ] Compare courier rates
- [ ] Save a quote
- [ ] View saved quotes
- [ ] View warehouse holdings

### Staff Features (10 tests)
- [ ] Login as staff
- [ ] View warehouse dashboard
- [ ] Create new holding
- [ ] Add multiple packages
- [ ] View holding details
- [ ] Calculate shipping fee
- [ ] Apply custom fees & discounts
- [ ] Consolidate holding
- [ ] Mark as shipped
- [ ] View expiring holdings

---

## Troubleshooting

### Common Issues

**Issue**: "Undefined variable $pageTitle"
```bash
# Solution: Clear caches
docker exec jattik_app php Files/core/artisan view:clear
docker exec jattik_app php Files/core/artisan cache:clear
```

**Issue**: "Call to undefined method addresses()"
```bash
# Solution: Customer model missing relationships - already fixed
# Restart containers if needed
docker-compose restart
```

**Issue**: No addresses/holdings showing
```bash
# Check data exists
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT COUNT(*) FROM customer_addresses;"
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SELECT COUNT(*) FROM warehouse_holdings;"
```

**Issue**: Calculator shows no couriers
```bash
# Run seeder again
docker exec jattik_app php Files/core/artisan db:seed --class=CourierConfigurationsSeeder
```

**Issue**: Schedules not showing
```bash
# Run seeder
docker exec jattik_app php Files/core/artisan db:seed --class=ShipmentSchedulesSeeder
```

---

## Summary of Test Coverage

### ✅ Functional Requirements
- **FR-14**: Multiple address management - TESTED
- **FR-15**: Address change restrictions - TESTED
- **FR-16**: Fixed monthly schedules - TESTED
- **FR-17**: Warehouse consolidation (90 days) - TESTED
- **FR-18**: Shipping date extension - TESTED
- **FR-19**: Multi-courier calculator - TESTED
- **FR-20**: Customer shipping calculator - TESTED
- **FR-21**: Employee fee calculation - TESTED

### ✅ Features Tested
- Customer Authentication (OTP)
- Subscription Management
- Payment Processing
- Address Management
- Shipping Schedules
- Shipping Calculator
- Rate Comparison
- Quote Management
- Warehouse Holdings
- Package Management
- Fee Calculation
- Consolidation & Shipping
- Expiry Tracking

---

## Next Steps

After testing, you can:
1. **Add More Data**: Create more customers, addresses, holdings
2. **Test Edge Cases**: Try boundary conditions
3. **Performance Test**: Load test with many records
4. **User Acceptance**: Have real users test
5. **Production Deploy**: Move to production environment

---

**Testing Complete!** 🎉

All features are implemented and ready for comprehensive testing.
