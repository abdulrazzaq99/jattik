# CourierLab - Complete Testing & Feature Walkthrough

This comprehensive guide covers all 43 routes, testing procedures, and feature demonstrations for the CourierLab system.

## Table of Contents

- [System Architecture](#system-architecture)
- [All Routes Reference](#all-routes-reference)
- [Testing Prerequisites](#testing-prerequisites)
- [Customer Portal Testing](#customer-portal-testing)
- [Staff Portal Testing](#staff-portal-testing)
- [Admin Portal Testing](#admin-portal-testing)
- [Feature-by-Feature Testing](#feature-by-feature-testing)
- [Database Verification](#database-verification)
- [Troubleshooting](#troubleshooting)

---

## System Architecture

### Multi-Role System Overview

```
┌─────────────────────────────────────────────────────────────┐
│                        Admin Portal                          │
│  - Full system access                                        │
│  - Analytics & Reports                                       │
│  - WhatsApp Bot Management                                   │
│  - Branch & Manager Management                               │
└─────────────────────────────────────────────────────────────┘
                            │
            ┌───────────────┼───────────────┐
            ▼               ▼               ▼
┌─────────────────┐ ┌─────────────┐ ┌─────────────────┐
│  Manager Portal │ │ Staff Portal│ │ Customer Portal │
│  - Branch Mgmt  │ │ - Couriers  │ │ - Self Service  │
│  - Staff Mgmt   │ │ - Tracking  │ │ - Tracking      │
│  - Reports      │ │ - Claims    │ │ - Subscriptions │
└─────────────────┘ └─────────────┘ └─────────────────┘
```

### Authentication Guards

- **Admin**: `auth:admin` middleware, separate guard
- **Manager**: `auth:web` middleware, `user_type='manager'`
- **Staff**: `auth:web` middleware, `user_type='staff'`
- **Customer**: `auth:customer` middleware, separate guard

---

## All Routes Reference

### Customer Routes (18 Routes)

#### Subscription Management (FR-15, FR-16)
```
GET  /customer/subscription/plans             → SubscriptionController@plans
POST /customer/subscription/subscribe         → SubscriptionController@subscribe
GET  /customer/subscription/current           → SubscriptionController@current
POST /customer/subscription/cancel            → SubscriptionController@cancel
GET  /customer/subscription/history           → SubscriptionController@history
```

#### Payment Management (FR-17)
```
GET  /customer/payment/history                → PaymentController@history
GET  /customer/payment/{id}                   → PaymentController@show
POST /customer/payment/coupon/apply           → PaymentController@applyCoupon
```

#### Shipment Services (FR-14, FR-18, FR-19, FR-20)
```
GET  /customer/addresses                      → CustomerController@addresses
POST /customer/addresses/store                → CustomerController@storeAddress
GET  /customer/shipment/calculator            → CustomerController@calculator
GET  /customer/shipment/holdings              → CustomerController@warehouseHoldings
GET  /customer/shipment/schedules             → CustomerController@shippingSchedules
GET  /customer/shipment/quotes                → CustomerController@quotes
```

#### Notifications (FR-22, FR-23, FR-26, FR-27)
```
GET  /customer/notifications                  → NotificationController@index
GET  /customer/notifications/{id}             → NotificationController@show
POST /customer/notifications/{id}/read        → NotificationController@markAsRead
```

#### Support & Claims (FR-28, FR-31, FR-32)
```
GET  /customer/support/issues                 → SupportController@issues
POST /customer/support/issues/create          → SupportController@createIssue
GET  /customer/support/issues/{id}            → SupportController@showIssue
GET  /customer/support/claims                 → SupportController@claims
POST /customer/support/claims/create          → SupportController@createClaim
GET  /customer/support/claims/{id}            → SupportController@showClaim
```

#### Ratings & Contact (FR-28, FR-33)
```
GET  /customer/ratings/create/{id}            → RatingController@create
POST /customer/ratings/store                  → RatingController@store
GET  /customer/contact                        → ContactController@index
POST /customer/contact/store                  → ContactController@store
```

### Staff Routes (11 Routes)

#### Tracking Management (FR-24, FR-25)
```
GET  /staff/tracking/dashboard                → TrackingManagementController@dashboard
GET  /staff/tracking/events                   → TrackingManagementController@events
POST /staff/tracking/refresh/{id}             → TrackingManagementController@refreshTracking
GET  /staff/tracking/exceptions               → TrackingManagementController@exceptions
POST /staff/tracking/notify/{id}              → TrackingManagementController@notifyCustomer
```

#### Claims Management (FR-32)
```
GET  /staff/claims                            → ClaimManagementController@index
GET  /staff/claims/pending                    → ClaimManagementController@pending
GET  /staff/claims/{id}                       → ClaimManagementController@show
GET  /staff/claims/{id}/review                → ClaimManagementController@review
POST /staff/claims/{id}/approve               → ClaimManagementController@approve
POST /staff/claims/{id}/reject                → ClaimManagementController@reject
```

### Admin Routes (14 Routes)

#### Analytics Dashboard (FR-36)
```
GET  /admin/analytics/dashboard               → AnalyticsController@dashboard
GET  /admin/analytics/shipping-costs          → AnalyticsController@shippingCosts
GET  /admin/analytics/carriers                → AnalyticsController@carriers
GET  /admin/analytics/regions                 → AnalyticsController@regions
POST /admin/analytics/export                  → AnalyticsController@export
```

#### WhatsApp Bot Management (FR-30)
```
GET  /admin/whatsapp/conversations            → WhatsAppBotController@conversations
GET  /admin/whatsapp/conversation/{phone}     → WhatsAppBotController@conversation
POST /admin/whatsapp/respond                  → WhatsAppBotController@respond
GET  /admin/whatsapp/messages                 → WhatsAppBotController@messages
GET  /admin/whatsapp/escalated                → WhatsAppBotController@escalated
GET  /admin/whatsapp/settings                 → WhatsAppBotController@settings
POST /admin/whatsapp/test                     → WhatsAppBotController@testConnection
```

#### Public Webhooks
```
POST /webhook/whatsapp                        → WhatsAppBotController@webhook (NO AUTH)
```

---

## Testing Prerequisites

### 1. Database Setup

```bash
# Verify all tables exist
docker exec jattik_mysql mysql -u root -proot courierlab -e "SHOW TABLES;"

# Expected tables (26+ tables):
# - subscription_plans
# - customer_subscriptions
# - payments
# - insurance_policies
# - customer_addresses
# - shipment_schedules
# - warehouse_holdings
# - shipment_notifications
# - tracking_events
# - support_issues
# - claims
# - shipment_ratings
# - whatsapp_messages
# - whatsapp_conversations
# - contact_messages
# - coupons
# - coupon_usage
# ... and existing tables (branches, customers, courier_infos, etc.)
```

### 2. Seed Sample Data

```bash
# Seed subscription plans
docker exec jattik_app php Files/core/artisan db:seed --class=SubscriptionPlansSeeder

# Seed coupons
docker exec jattik_app php Files/core/artisan db:seed --class=CouponsSeeder

# Verify data
docker exec jattik_mysql mysql -u root -proot courierlab -e "SELECT * FROM subscription_plans;"
docker exec jattik_mysql mysql -u root -proot courierlab -e "SELECT code, discount_percentage FROM coupons;"
```

### 3. Create Test Users

```sql
-- Create test customer
INSERT INTO customers (firstname, lastname, email, mobile, password, created_at, updated_at)
VALUES ('Test', 'Customer', 'customer@test.com', '+1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Create test staff
INSERT INTO users (name, username, email, mobile, password, user_type, branch_id, created_at, updated_at)
VALUES ('Test Staff', 'staff', 'staff@test.com', '+1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 1, NOW(), NOW());
```

Password for test users: `password`

### 4. Clear All Caches

```bash
docker exec jattik_app php Files/core/artisan cache:clear
docker exec jattik_app php Files/core/artisan config:clear
docker exec jattik_app php Files/core/artisan route:clear
docker exec jattik_app php Files/core/artisan view:clear
```

---

## Customer Portal Testing

### Test Environment Setup

1. **Login as Customer**
   - URL: `http://localhost:8080/customer/login`
   - Email: `customer@test.com`
   - Password: `password`

### Feature Group 1: Subscription Management (FR-15, FR-16)

#### Test Case 1.1: View Subscription Plans

```
Route: GET /customer/subscription/plans
File: customer/subscription/plans.blade.php:59-62

Steps:
1. Navigate to Subscription > View Plans from sidebar
2. Verify 3 subscription plans displayed (Basic, Standard, Premium)
3. Verify each plan shows:
   - Plan name
   - Price per month
   - Features list
   - "Subscribe" button

Expected Results:
✓ Plans loaded from subscription_plans table
✓ Current user subscription status displayed (if any)
✓ "Premium" badge shown if user has active subscription
```

#### Test Case 1.2: Subscribe to a Plan

```
Route: POST /customer/subscription/subscribe
Controller: SubscriptionController@subscribe:28-63

Steps:
1. Click "Subscribe" on a plan (e.g., Standard - $29.99/month)
2. Payment page opens with plan details
3. Apply coupon code (if available): e.g., "WELCOME20"
4. Complete payment (test mode)
5. Verify subscription activated

Expected Results:
✓ Payment record created in payments table
✓ CustomerSubscription record created
✓ customer.is_premium = 1 (if premium plan)
✓ Redirect to "My Subscription" page

Database Verification:
SELECT * FROM customer_subscriptions WHERE customer_id = 1;
SELECT * FROM payments WHERE customer_id = 1 ORDER BY id DESC LIMIT 1;
```

#### Test Case 1.3: View Current Subscription

```
Route: GET /customer/subscription/current
File: customer/subscription/current.blade.php:66-69

Steps:
1. Navigate to Subscription > My Subscription
2. Verify subscription details displayed:
   - Plan name
   - Start date
   - Next billing date
   - Status (active/cancelled)
   - "Cancel Subscription" button

Expected Results:
✓ Shows active subscription details
✓ Next billing date calculated correctly
✓ Auto-renewal status displayed
```

#### Test Case 1.4: Cancel Subscription

```
Route: POST /customer/subscription/cancel
Controller: SubscriptionController@cancel:81-96

Steps:
1. Click "Cancel Subscription" button
2. Confirm cancellation in modal
3. Verify subscription cancelled

Expected Results:
✓ cancelled_at timestamp set
✓ auto_renew = 0
✓ Subscription remains active until end date
✓ Success notification displayed

Database Verification:
SELECT cancelled_at, auto_renew FROM customer_subscriptions WHERE id = 1;
```

### Feature Group 2: Payment Management (FR-17)

#### Test Case 2.1: View Payment History

```
Route: GET /customer/payment/history
File: customer/payment/history.blade.php:124-128

Steps:
1. Navigate to Payments from sidebar
2. Verify payment list displayed with:
   - Transaction ID
   - Amount
   - Type (subscription/shipment)
   - Date
   - Status badge

Expected Results:
✓ All customer payments listed
✓ Pagination works correctly
✓ Filter by payment type
✓ Search by transaction ID
```

#### Test Case 2.2: View Payment Details

```
Route: GET /customer/payment/{id}
Controller: PaymentController@show:31-42

Steps:
1. Click "View Details" on a payment
2. Verify payment details page shows:
   - Full payment breakdown
   - Original amount
   - Coupon discount (if applied)
   - Final amount
   - Payment method
   - Receipt/invoice download

Expected Results:
✓ Complete payment information displayed
✓ Coupon details shown if discount applied
✓ Related subscription/shipment linked
```

### Feature Group 3: Shipment Services (FR-14, FR-18, FR-19, FR-20)

#### Test Case 3.1: Manage Address Book

```
Route: GET /customer/addresses
File: customer/addresses/index.blade.php:96-100

Steps:
1. Navigate to Shipment Services > My Addresses
2. Click "Add New Address"
3. Fill form:
   - Address label: "Home"
   - Full name: "John Doe"
   - Address line 1: "123 Main St"
   - City, State, Zip, Country
4. Click "Save Address"

Expected Results:
✓ Address saved to customer_addresses table
✓ Address appears in list with edit/delete buttons
✓ Can set as default address

Database Verification:
SELECT * FROM customer_addresses WHERE customer_id = 1;
```

#### Test Case 3.2: Shipping Calculator

```
Route: GET /customer/shipment/calculator
File: customer/shipment/calculator.blade.php:90-94

Steps:
1. Navigate to Shipment Services > Shipping Calculator
2. Enter shipment details:
   - Origin branch
   - Destination branch
   - Weight: 5 kg
   - Dimensions: 30x20x15 cm
3. Select shipping type (Standard/Express)
4. Click "Calculate"

Expected Results:
✓ Estimated cost displayed
✓ Delivery time estimate shown
✓ Insurance options available
✓ Can proceed to create shipment
```

#### Test Case 3.3: View Warehouse Holdings

```
Route: GET /customer/shipment/holdings
File: customer/shipment/holdings.blade.php:102-107

Steps:
1. Navigate to Shipment Services > Warehouse Holdings
2. Verify list shows items stored at warehouse:
   - Item description
   - Storage start date
   - Storage fees
   - Status

Expected Results:
✓ All customer warehouse items listed
✓ Daily storage fees calculated
✓ Option to request delivery
```

#### Test Case 3.4: View Shipping Schedules

```
Route: GET /customer/shipment/schedules
File: customer/shipment/schedules.blade.php:108-113

Steps:
1. Navigate to Shipment Services > Shipping Schedules
2. Verify upcoming scheduled shipments:
   - Scheduled date
   - Origin → Destination
   - Status
   - Cancel button (if not processed)

Expected Results:
✓ All scheduled shipments displayed
✓ Can cancel future schedules
✓ Status badges correct (pending/processing/completed)
```

#### Test Case 3.5: View Quote Requests

```
Route: GET /customer/shipment/quotes
File: customer/shipment/quotes.blade.php:114-119

Steps:
1. Navigate to Shipment Services > My Quotes
2. Verify quote list shows:
   - Quote ID
   - Requested date
   - Route details
   - Quoted amount
   - Status (pending/approved/rejected)

Expected Results:
✓ All quote requests listed
✓ Can view quote details
✓ Staff response visible if replied
```

### Feature Group 4: Notifications (FR-22, FR-23, FR-26, FR-27)

#### Test Case 4.1: View Notifications

```
Route: GET /customer/notifications
File: customer/notifications/index.blade.php:12-13

Steps:
1. Navigate to Notifications from sidebar
2. Verify notification list with statistics:
   - Total notifications
   - Unread count
   - Filter by type (facility arrival, dispatch, exception, etc.)
3. Click on a notification

Expected Results:
✓ Notifications listed newest first
✓ Unread notifications highlighted
✓ Badge shows unread count in sidebar
✓ Filter by notification type works
✓ Auto-refresh every 30 seconds

Database Verification:
SELECT * FROM shipment_notifications WHERE customer_id = 1 ORDER BY created_at DESC;
```

#### Test Case 4.2: Mark Notification as Read

```
Route: POST /customer/notifications/{id}/read
Controller: NotificationController@markAsRead:36-47

Steps:
1. Click on an unread notification
2. Verify notification opens in detail view
3. Verify is_read = 1 in database
4. Return to list, verify notification no longer highlighted

Expected Results:
✓ is_read updated to 1
✓ Unread count decrements
✓ Sidebar badge updates
```

### Feature Group 5: Support & Claims (FR-28, FR-31, FR-32)

#### Test Case 5.1: Report an Issue

```
Route: POST /customer/support/issues/create
File: customer/support/create_issue.blade.php:4-8

Steps:
1. Navigate to Support > My Issues
2. Click "Report New Issue"
3. Fill form:
   - Issue type: "Delay"
   - Priority: "High"
   - Subject: "Package not delivered"
   - Description: "Shipment #12345 delayed by 3 days"
   - Related shipment: Select from dropdown
   - Attachments: Upload 2 files (max 5MB each)
4. Submit form

Expected Results:
✓ Issue created with status = "submitted"
✓ Files uploaded to storage
✓ Staff notified via admin_notifications
✓ Customer receives confirmation notification

Database Verification:
SELECT * FROM support_issues WHERE customer_id = 1 ORDER BY id DESC LIMIT 1;
```

#### Test Case 5.2: View Issue Details

```
Route: GET /customer/support/issues/{id}
File: customer/support/show_issue.blade.php:5-8

Steps:
1. Click on an issue from list
2. Verify issue details page shows:
   - Issue timeline (submitted, assigned, in progress, resolved)
   - Staff assigned (if any)
   - Resolution details (if resolved)
   - Attachments download links

Expected Results:
✓ Timeline shows all status changes with timestamps
✓ Can download attachments
✓ Staff response visible if added
```

#### Test Case 5.3: File a Claim

```
Route: POST /customer/support/claims/create
File: customer/support/create_claim.blade.php:3-7

Steps:
1. Navigate to Support > My Claims
2. Click "File New Claim"
3. Fill claim form:
   - Claim type: "Damaged Package"
   - Related shipment: Select shipment
   - Claimed amount: $150.00
   - Description: "Package arrived damaged, item broken"
   - Evidence: Upload 5 photos
   - Related issue: Select if exists
4. Submit claim

Expected Results:
✓ Claim created with status = "pending"
✓ SLA timer starts (10 business days)
✓ Evidence files uploaded
✓ Claimed amount validated (≤ shipment value + insurance)

Database Verification:
SELECT * FROM claims WHERE customer_id = 1 ORDER BY id DESC LIMIT 1;
SELECT * FROM claim_evidence WHERE claim_id = LAST_INSERT_ID();
```

#### Test Case 5.4: View Claim Status

```
Route: GET /customer/support/claims/{id}
File: customer/support/show_claim.blade.php:6-8

Steps:
1. Click on a claim from list
2. Verify claim details page shows:
   - Status banner (pending/under review/approved/rejected/paid)
   - SLA progress bar (X days remaining / 10 days)
   - Claimed amount vs approved amount
   - Evidence gallery
   - Rejection reason (if rejected)
   - Payment details (if approved)

Expected Results:
✓ SLA countdown accurate
✓ Progress bar color changes (green → yellow → red)
✓ Evidence viewable in modal
✓ All status updates logged
```

#### Test Case 5.5: Contact Support

```
Route: POST /customer/contact/store
File: customer/contact/create.blade.php:10-11

Steps:
1. Navigate to Support > Contact Us
2. Fill contact form (auto-filled with customer data):
   - Name: (pre-filled)
   - Email: (pre-filled)
   - Subject: "General Inquiry"
   - Message: "What are your operating hours?"
3. Submit form

Expected Results:
✓ Contact message saved to contact_messages table
✓ Admin notified
✓ Customer receives confirmation
✓ Can view messages and replies in "My Messages"

Database Verification:
SELECT * FROM contact_messages WHERE customer_id = 1;
```

### Feature Group 6: Ratings & Feedback (FR-33)

#### Test Case 6.1: Rate Delivered Shipment

```
Route: POST /customer/ratings/store
File: customer/ratings/create.blade.php:9

Steps:
1. Navigate to a delivered courier
2. Click "Rate This Delivery"
3. Select star rating (1-5 stars)
4. Add feedback comment
5. Submit rating

Expected Results:
✓ Rating saved with 1-5 stars
✓ Comment saved (optional)
✓ Courier average rating updated
✓ Staff performance metrics updated
✓ Cannot rate same shipment twice

Database Verification:
SELECT * FROM shipment_ratings WHERE customer_id = 1 AND courier_id = 123;
```

---

## Staff Portal Testing

### Test Environment Setup

1. **Login as Staff**
   - URL: `http://localhost:8080/staff/login`
   - Email: `staff@test.com`
   - Password: `password`

### Feature Group 7: Tracking Management (FR-24, FR-25)

#### Test Case 7.1: View Tracking Dashboard

```
Route: GET /staff/tracking/dashboard
File: staff/tracking/dashboard.blade.php:1-2

Steps:
1. Login as staff
2. Navigate to Tracking Management > Dashboard
3. Verify statistics widgets:
   - Total shipments with tracking
   - Active shipments
   - Exceptions (unresolved)
   - Unnotified exceptions
4. Verify recent events table
5. Check auto-refresh (2 minutes)

Expected Results:
✓ Statistics accurate from tracking_events table
✓ Recent events show last 10 updates
✓ Quick action buttons functional
✓ Alert banner if unnotified exceptions exist
✓ Page refreshes automatically
```

#### Test Case 7.2: View All Tracking Events

```
Route: GET /staff/tracking/events
File: staff/tracking/events.blade.php:2-3

Steps:
1. Navigate to Tracking Management > All Events
2. Apply filters:
   - Carrier: "FedEx"
   - Date range: Last 7 days
   - Event type: "In Transit"
3. Search by tracking number
4. Verify pagination

Expected Results:
✓ All tracking events listed
✓ Filters work correctly
✓ Can click to view related courier
✓ Carrier badges colored correctly
✓ Exception events highlighted in red/yellow
```

#### Test Case 7.3: Refresh Tracking

```
Route: POST /staff/tracking/refresh/{id}
Controller: TrackingManagementController@refreshTracking:47-87

Steps:
1. From events list, click "Refresh" on a shipment
2. Verify API call made to courier provider
3. Check new tracking events imported
4. Verify customer notified if status changed

Expected Results:
✓ API call successful (or shows error if failed)
✓ New events inserted into tracking_events table
✓ Customer notification sent if significant update
✓ last_synced_at timestamp updated

Database Verification:
SELECT * FROM tracking_events WHERE courier_id = 123 ORDER BY event_timestamp DESC;
```

#### Test Case 7.4: Manage Exceptions

```
Route: GET /staff/tracking/exceptions
File: staff/tracking/exceptions.blade.php:3-4

Steps:
1. Navigate to Tracking Management > Exceptions
2. View exception list with priority badges:
   - HIGH: Lost, Damaged
   - MEDIUM: Delayed
   - LOW: Address issue, Weather delay
3. Filter by priority
4. Click "Notify Customer" on unnotified exception
5. Click "Mark as Resolved" after handling

Expected Results:
✓ Exceptions sorted by priority
✓ Overdue exceptions highlighted
✓ Notify customer sends notification
✓ customer_notified = 1 after notify
✓ Auto-refresh every 1 minute
✓ Statistics cards show counts

Database Verification:
SELECT * FROM tracking_events WHERE exception_type IS NOT NULL AND customer_notified = 0;
```

### Feature Group 8: Claims Management (FR-32)

#### Test Case 8.1: View All Claims

```
Route: GET /staff/claims
File: staff/claims/index.blade.php:1

Steps:
1. Navigate to Claims Management > All Claims
2. Verify claims list with columns:
   - Claim ID
   - Customer name
   - Claim type
   - Amount claimed
   - Status
   - SLA remaining
3. Filter by status
4. Search by claim ID

Expected Results:
✓ All claims displayed with pagination
✓ SLA countdown shows days remaining
✓ Overdue claims shown in red
✓ Can click to view details
```

#### Test Case 8.2: View Pending Claims

```
Route: GET /staff/claims/pending
File: staff/claims/pending.blade.php:2

Steps:
1. Navigate to Claims Management > Pending Claims
2. Verify alert banners:
   - RED: Overdue claims (>10 days)
   - YELLOW: Nearing SLA (8-10 days)
3. Claims sorted by urgency (oldest first)
4. Statistics cards show counts

Expected Results:
✓ Only pending/under review claims shown
✓ Overdue claims listed in separate section
✓ SLA warnings accurate
✓ Quick review button available
```

#### Test Case 8.3: Review and Approve Claim

```
Route: GET /staff/claims/{id}/review
Route: POST /staff/claims/{id}/approve
File: staff/claims/review.blade.php:3-4
Controller: ClaimManagementController@approve:70-100

Steps:
1. Click "Review" on a pending claim
2. Review page shows:
   - Customer details (left column)
   - Shipment details (right column)
   - Evidence gallery (photos/documents)
   - Approve/Reject forms (side by side)
3. Enter approved amount: $120 (if claimed $150)
4. Add approval notes
5. Click "Approve Claim"

Expected Results:
✓ Claim status updated to "approved"
✓ approved_amount saved
✓ approved_by = staff ID
✓ approved_at timestamp set
✓ Customer notified via notification
✓ SLA timer stops

Database Verification:
SELECT status, approved_amount, approved_by, approved_at FROM claims WHERE id = 1;
SELECT * FROM shipment_notifications WHERE customer_id = X AND type = 'claim_approved';
```

#### Test Case 8.4: Reject Claim

```
Route: POST /staff/claims/{id}/reject
Controller: ClaimManagementController@reject:102-129

Steps:
1. From review page, fill rejection form:
   - Rejection reason: "Insufficient evidence"
   - Detailed explanation: "Photos do not show damage clearly"
2. Click "Reject Claim"

Expected Results:
✓ Claim status = "rejected"
✓ rejection_reason saved
✓ rejected_by = staff ID
✓ rejected_at timestamp set
✓ Customer notified
✓ Cannot reopen rejected claim

Database Verification:
SELECT status, rejection_reason, rejected_by FROM claims WHERE id = 1;
```

---

## Admin Portal Testing

### Test Environment Setup

1. **Login as Admin**
   - URL: `http://localhost:8080/admin/login`
   - Username: `admin`
   - Password: (check database.sql)

### Feature Group 9: Analytics Dashboard (FR-36)

#### Test Case 9.1: View Main Analytics Dashboard

```
Route: GET /admin/analytics/dashboard
File: admin/analytics/dashboard.blade.php:1

Steps:
1. Login as admin
2. Navigate to Analytics > Dashboard
3. Verify 4 main charts:
   - LINE: Monthly shipment volume (last 12 months)
   - BAR: Revenue by month
   - DOUGHNUT: Payment methods distribution
   - PIE: Shipment status distribution
4. Verify statistics cards:
   - Total revenue
   - Total shipments
   - Active customers
   - Average shipment value

Expected Results:
✓ All 4 charts render correctly
✓ Data pulled from last 12 months
✓ Tooltips show exact values on hover
✓ Statistics accurate
✓ Charts use different colors
✓ Export button available
```

#### Test Case 9.2: View Shipping Costs Breakdown

```
Route: GET /admin/analytics/shipping-costs
File: admin/analytics/shipping_costs.blade.php:2

Steps:
1. Navigate to Analytics > Shipping Costs
2. Verify detailed cost table shows:
   - Month
   - Shipping fees
   - Insurance fees
   - Additional fees
   - Total costs
   - Month-over-month change
3. View bar chart visualization
4. Click "Export CSV"

Expected Results:
✓ Monthly costs calculated correctly
✓ Bar chart matches table data
✓ Averages shown at bottom
✓ CSV export contains all data
✓ Can filter by date range
```

#### Test Case 9.3: View Carrier Performance

```
Route: GET /admin/analytics/carriers
File: admin/analytics/carriers.blade.php:3

Steps:
1. Navigate to Analytics > Carriers
2. Verify doughnut chart shows usage distribution
3. Verify bar chart shows average delivery days
4. Review performance table:
   - Carrier name
   - Total shipments
   - On-time delivery %
   - Average delivery days
   - Exception rate
   - Performance badge (Good/Fair/Poor)

Expected Results:
✓ All carriers listed
✓ Performance calculated correctly
✓ On-time % colored (Green ≥90%, Yellow ≥70%, Red <70%)
✓ Charts interactive
✓ Can sort by any column
```

#### Test Case 9.4: View Regional Analytics

```
Route: GET /admin/analytics/regions
File: admin/analytics/regions.blade.php:4

Steps:
1. Navigate to Analytics > Regions
2. View top 4 regions cards (total shipments)
3. Verify pie chart shows regional distribution
4. Verify line chart shows peak shipping times
5. Review detailed table:
   - Region/City
   - Total shipments
   - Total value
   - Average shipment value
   - Popular destinations

Expected Results:
✓ Top regions accurate
✓ Pie chart percentages sum to 100%
✓ Peak times chart shows hourly trends
✓ Table sortable and searchable
✓ Can export data
```

### Feature Group 10: WhatsApp Bot Management (FR-30)

#### Test Case 10.1: View All Conversations

```
Route: GET /admin/whatsapp/conversations
File: admin/whatsapp/conversations.blade.php:1

Steps:
1. Navigate to WhatsApp Bot > Conversations
2. Verify conversation list shows:
   - Phone number
   - Customer name (if linked)
   - Last message preview
   - Message count
   - Escalation status
   - Last activity timestamp
3. Click on a conversation

Expected Results:
✓ All conversations listed
✓ Active conversations highlighted
✓ Escalated conversations shown with warning badge
✓ Can search by phone number
✓ Filter by escalation status
```

#### Test Case 10.2: View Single Conversation

```
Route: GET /admin/whatsapp/conversation/{phone}
File: admin/whatsapp/conversation.blade.php:4

Steps:
1. Click on a conversation
2. Verify chat interface:
   - WhatsApp-style message bubbles
   - Inbound messages (left, gray)
   - Outbound messages (right, blue)
   - Message timestamps
   - Message type badges (OTP, FAQ, Order Update)
   - Escalation indicator if escalated
3. Scroll to bottom (auto-scroll on page load)
4. Type response in text area
5. Click "Send"

Expected Results:
✓ Messages displayed in chat format
✓ Auto-scroll to latest message
✓ Customer info shown if linked
✓ Send button sends WhatsApp message via API
✓ Page auto-refreshes every 10 seconds
✓ New messages appear without reload

Database Verification:
SELECT * FROM whatsapp_messages WHERE phone_number = '+1234567890' ORDER BY created_at;
```

#### Test Case 10.3: Respond to Message

```
Route: POST /admin/whatsapp/respond
Controller: WhatsAppBotController@respond:57-83

Steps:
1. From conversation view, type message
2. Click "Send"
3. Verify message sent via WhatsApp API
4. Check message appears in conversation

Expected Results:
✓ Message saved to database (direction = 'outbound')
✓ WhatsApp API called successfully
✓ is_sent = 1 if API success, 0 if failed
✓ Customer receives message on WhatsApp
✓ Message appears in chat interface
```

#### Test Case 10.4: View All Messages Log

```
Route: GET /admin/whatsapp/messages
File: admin/whatsapp/messages.blade.php:2

Steps:
1. Navigate to WhatsApp Bot > Messages
2. Verify comprehensive message log:
   - Phone number
   - Direction (inbound/outbound)
   - Message content (truncated)
   - Message type
   - Bot intent (if inbound)
   - Timestamp
3. Filter by direction, type, date
4. Search by phone or content

Expected Results:
✓ All messages across all conversations
✓ Filters work correctly
✓ Can click to view full conversation
✓ Pagination works
```

#### Test Case 10.5: View Escalated Conversations

```
Route: GET /admin/whatsapp/escalated
File: admin/whatsapp/escalated.blade.php:3

Steps:
1. Navigate to WhatsApp Bot > Escalated
2. Verify alert showing escalation count
3. Review escalated conversations:
   - Phone number
   - Customer info
   - Last message
   - Escalation reason
   - Escalated timestamp
4. Click "Respond" button
5. Handle escalation, respond to customer

Expected Results:
✓ Only escalated conversations shown
✓ Statistics show total/today/bot success rate
✓ Can respond directly
✓ Escalation reason visible
✓ Empty state if no escalations (shows success message)
```

#### Test Case 10.6: Configure Bot Settings

```
Route: GET /admin/whatsapp/settings
File: admin/whatsapp/settings.blade.php:4

Steps:
1. Navigate to WhatsApp Bot > Settings
2. Verify configuration status (Configured/Not Configured)
3. Review webhook URL
4. Click "Copy" button to copy webhook URL
5. Verify verify token displayed
6. Review bot capabilities list
7. Check statistics cards
8. Click quick action buttons

Expected Results:
✓ Configuration status accurate (checks .env)
✓ Webhook URL displayed correctly
✓ Copy button copies to clipboard (iziToast confirms)
✓ Verify token shown
✓ Bot capabilities listed (OTP, tracking, FAQ, escalation, updates)
✓ Statistics show total/outbound/inbound/OTPs
✓ Quick action buttons navigate correctly
```

#### Test Case 10.7: Webhook Endpoint (External Testing)

```
Route: POST /webhook/whatsapp (PUBLIC - NO AUTH)
Controller: WhatsAppBotController@webhook:85-200

Testing with Postman/cURL:

# Test GET (webhook verification)
curl "http://localhost:8080/webhook/whatsapp?hub.mode=subscribe&hub.verify_token=your_verify_token&hub.challenge=test_challenge"

Expected: Returns hub.challenge value

# Test POST (incoming message)
curl -X POST http://localhost:8080/webhook/whatsapp \
  -H "Content-Type: application/json" \
  -d '{
    "object": "whatsapp_business_account",
    "entry": [{
      "changes": [{
        "value": {
          "messages": [{
            "from": "1234567890",
            "text": {"body": "track 12345"}
          }]
        }
      }]
    }]
  }'

Expected Results:
✓ Webhook processes incoming message
✓ Message saved to whatsapp_messages
✓ Bot processes intent (tracking/OTP/FAQ/escalation)
✓ Response sent automatically
✓ Returns 200 OK
```

---

## Feature-by-Feature Testing

### Functional Requirement Coverage

| FR # | Feature | Routes | Test Cases | Status |
|------|---------|--------|------------|--------|
| FR-14 | Address Book | 2 | TC 3.1 | Ready |
| FR-15 | Subscription Plans | 5 | TC 1.1-1.4 | Ready |
| FR-16 | Subscription Management | 5 | TC 1.1-1.4 | Ready |
| FR-17 | Payment History | 3 | TC 2.1-2.2 | Ready |
| FR-18 | Shipping Calculator | 1 | TC 3.2 | Ready |
| FR-19 | Warehouse Holdings | 1 | TC 3.3 | Ready |
| FR-20 | Shipping Schedules | 1 | TC 3.4 | Ready |
| FR-21 | Quote Requests | 1 | TC 3.5 | Ready |
| FR-22 | Facility Notifications | 3 | TC 4.1-4.2 | Ready |
| FR-23 | Dispatch Notifications | 3 | TC 4.1-4.2 | Ready |
| FR-24 | Courier API Integration | 3 | TC 7.1-7.3 | Ready |
| FR-25 | Exception Handling | 2 | TC 7.4 | Ready |
| FR-26 | Tracking Links | 3 | TC 4.1-4.2 | Ready |
| FR-27 | Fee Quote Notifications | 3 | TC 4.1-4.2 | Ready |
| FR-28 | Contact Us | 2 | TC 5.5 | Ready |
| FR-30 | WhatsApp Bot | 7 | TC 10.1-10.7 | Ready |
| FR-31 | Issue Reporting | 3 | TC 5.1-5.2 | Ready |
| FR-32 | Claims Processing | 6 | TC 5.3-5.4, 8.1-8.4 | Ready |
| FR-33 | Ratings & Feedback | 2 | TC 6.1 | Ready |
| FR-36 | Analytics Dashboard | 5 | TC 9.1-9.4 | Ready |

---

## Database Verification

### Key Tables to Check

```sql
-- 1. Subscription Plans
SELECT * FROM subscription_plans;
-- Expected: 3 plans (Basic, Standard, Premium)

-- 2. Customer Subscriptions
SELECT cs.*, sp.name as plan_name
FROM customer_subscriptions cs
JOIN subscription_plans sp ON cs.plan_id = sp.id
WHERE cs.customer_id = 1;

-- 3. Payments
SELECT * FROM payments
WHERE customer_id = 1
ORDER BY created_at DESC;

-- 4. Customer Addresses
SELECT * FROM customer_addresses
WHERE customer_id = 1 AND deleted_at IS NULL;

-- 5. Shipment Notifications
SELECT * FROM shipment_notifications
WHERE customer_id = 1
ORDER BY created_at DESC
LIMIT 10;

-- 6. Tracking Events
SELECT te.*, ci.code as tracking_code
FROM tracking_events te
JOIN courier_infos ci ON te.courier_id = ci.id
ORDER BY te.event_timestamp DESC
LIMIT 20;

-- 7. Support Issues
SELECT * FROM support_issues
WHERE customer_id = 1
ORDER BY created_at DESC;

-- 8. Claims
SELECT c.*, ci.code as shipment_code
FROM claims c
JOIN courier_infos ci ON c.courier_id = ci.id
ORDER BY c.created_at DESC;

-- 9. Shipment Ratings
SELECT sr.*, ci.code as shipment_code
FROM shipment_ratings sr
JOIN courier_infos ci ON sr.courier_id = ci.id
WHERE sr.customer_id = 1;

-- 10. WhatsApp Messages
SELECT * FROM whatsapp_messages
ORDER BY created_at DESC
LIMIT 50;

-- 11. Coupons
SELECT * FROM coupons WHERE is_active = 1;

-- 12. Coupon Usage
SELECT cu.*, c.code, p.amount
FROM coupon_usage cu
JOIN coupons c ON cu.coupon_id = c.id
JOIN payments p ON cu.payment_id = p.id;
```

### Data Integrity Checks

```sql
-- Check orphaned records
SELECT COUNT(*) as orphaned_subscriptions
FROM customer_subscriptions cs
LEFT JOIN customers c ON cs.customer_id = c.id
WHERE c.id IS NULL;

-- Check SLA violations
SELECT COUNT(*) as overdue_claims
FROM claims
WHERE status IN ('pending', 'under_review')
AND DATEDIFF(NOW(), submitted_at) > 10;

-- Check unnotified exceptions
SELECT COUNT(*) as unnotified
FROM tracking_events
WHERE exception_type IS NOT NULL
AND customer_notified = 0;

-- Check active subscriptions without payments
SELECT cs.*
FROM customer_subscriptions cs
LEFT JOIN payments p ON cs.id = p.subscription_id
WHERE cs.status = 'active'
AND p.id IS NULL;
```

---

## Troubleshooting

### Common Issues

#### Issue 1: Routes Not Found (404)

```bash
# Clear route cache
docker exec jattik_app php Files/core/artisan route:clear

# List all routes to verify
docker exec jattik_app php Files/core/artisan route:list | grep customer
docker exec jattik_app php Files/core/artisan route:list | grep staff
docker exec jattik_app php Files/core/artisan route:list | grep admin
```

#### Issue 2: View Errors (Undefined Variable)

```bash
# Clear view cache
docker exec jattik_app php Files/core/artisan view:clear

# Check logs
docker exec jattik_app tail -f Files/core/storage/logs/laravel.log
```

#### Issue 3: Database Errors

```bash
# Check migrations status
docker exec jattik_app php Files/core/artisan migrate:status

# Re-run specific migration
docker exec jattik_app php Files/core/artisan migrate:refresh --path=database/migrations/2025_11_07_120004_create_claims_table.php
```

#### Issue 4: Sidebar Menu Not Showing

```bash
# Verify JSON files exist
ls -la Files/core/resources/views/admin/partials/sidenav.json
ls -la Files/core/resources/views/staff/partials/sidenav.json

# Clear cache
docker exec jattik_app php Files/core/artisan cache:clear
```

#### Issue 5: WhatsApp Bot Not Working

```bash
# Check .env configuration
cat Files/core/.env | grep WHATSAPP

# Test webhook manually
curl -X POST http://localhost:8080/webhook/whatsapp \
  -H "Content-Type: application/json" \
  -d '{"test": "data"}'

# Check logs
docker exec jattik_app tail -f Files/core/storage/logs/laravel.log | grep WhatsApp
```

---

## Performance Testing

### Load Testing Scenarios

```bash
# Install Apache Bench (if not installed)
apt-get install apache2-utils

# Test 1: Customer login page
ab -n 100 -c 10 http://localhost:8080/customer/login

# Test 2: Notifications list
ab -n 100 -c 10 -H "Cookie: laravel_session=YOUR_SESSION_COOKIE" \
   http://localhost:8080/customer/notifications

# Test 3: Analytics dashboard
ab -n 50 -c 5 -H "Cookie: laravel_session=ADMIN_SESSION" \
   http://localhost:8080/admin/analytics/dashboard
```

### Database Performance

```sql
-- Check slow queries
SHOW FULL PROCESSLIST;

-- Analyze table sizes
SELECT
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'courierlab'
ORDER BY (data_length + index_length) DESC;

-- Check missing indexes
SELECT * FROM tracking_events WHERE customer_id = 1 AND is_read = 0;
EXPLAIN SELECT * FROM tracking_events WHERE customer_id = 1 AND is_read = 0;
```

---

## Security Testing

### Authentication Tests

```bash
# Test 1: Access admin without login
curl -I http://localhost:8080/admin/dashboard
# Expected: 302 Redirect to login

# Test 2: Access staff as customer
# Login as customer, try to access staff routes
# Expected: 403 Forbidden or redirect

# Test 3: CSRF protection
curl -X POST http://localhost:8080/customer/subscription/subscribe \
  -d "plan_id=1"
# Expected: 419 CSRF token mismatch
```

### SQL Injection Tests

```sql
-- Test search inputs with SQL injection attempts
-- Should be protected by Laravel's parameter binding

# Test in search box:
Input: ' OR '1'='1
Expected: No records returned, no SQL error

# Test in notification filter:
Input: '; DROP TABLE shipment_notifications; --
Expected: No effect, treated as string
```

---

## Checklist for Complete Testing

### Phase 1: Setup ✓
- [ ] Docker containers running
- [ ] Database imported
- [ ] Sample data seeded
- [ ] All caches cleared
- [ ] Test users created

### Phase 2: Customer Portal
- [ ] All 18 customer routes tested
- [ ] Subscription flow complete
- [ ] Payment processing works
- [ ] Notifications functional
- [ ] Claims can be filed
- [ ] Ratings work

### Phase 3: Staff Portal
- [ ] All 11 staff routes tested
- [ ] Tracking events sync
- [ ] Exceptions managed
- [ ] Claims reviewed
- [ ] Approve/reject works

### Phase 4: Admin Portal
- [ ] All 14 admin routes tested
- [ ] Analytics charts render
- [ ] WhatsApp bot configured
- [ ] Conversations viewable
- [ ] Webhook tested

### Phase 5: Integration
- [ ] Email notifications send
- [ ] SMS notifications send (if configured)
- [ ] WhatsApp messages send
- [ ] File uploads work
- [ ] CSV exports work

### Phase 6: Database
- [ ] All tables exist
- [ ] Foreign keys valid
- [ ] No orphaned records
- [ ] Indexes present
- [ ] Data consistent

---

## Conclusion

This walkthrough covers all 43 routes and major features implemented in CourierLab. Follow the test cases sequentially for comprehensive validation of the system.

**Next Steps:**
1. Complete event listeners (automation)
2. Create artisan commands (cron jobs)
3. Deploy notification templates
4. Production testing with real courier APIs
5. Load testing and optimization

**Documentation Files:**
- `README.md` - Setup guide
- `COMPLETE_WALKTHROUGH.md` - This file
- `FRONTEND_VIEWS_COMPLETE.md` - View implementation details

**Last Updated:** 2025-11-07
