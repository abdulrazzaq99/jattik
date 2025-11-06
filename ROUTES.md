# CourierLab Routes Documentation

**Total Routes:** 245

## Important Notes

### User Registration

⚠️ **There is NO self-registration/signup for Managers and Staff!**

**How to create users:**
- **Admin** - Default admin exists in database (username: `admin`, password: `admin123`)
- **Manager** - Created by Admin through: `admin/branch-manager/create`
- **Staff** - Created by Manager through: `manager/staff/create` or Admin through branch manager section

### Authentication URLs

| Role | Login URL | Created By |
|------|-----------|------------|
| Admin | http://localhost:8080/admin | Database (default exists) |
| Manager | http://localhost:8080/manager | Admin Panel |
| Staff | http://localhost:8080/staff | Manager Panel |

---

## Table of Contents

1. [Public Routes](#public-routes)
2. [Admin Routes](#admin-routes)
3. [Manager Routes](#manager-routes)
4. [Staff Routes](#staff-routes)
5. [Support Ticket Routes](#support-ticket-routes)

---

## Public Routes

### Homepage & General
```
GET    /                           → Homepage
GET    /blog                       → Blog listing
GET    /blog/{slug}                → Blog post details
GET    /contact                    → Contact page
POST   /contact                    → Submit contact form
GET    /cookie-policy              → Cookie policy page
GET    /cookie/accept              → Accept cookies
GET    /policy/{slug}              → Policy pages (privacy, terms, etc.)
GET    /{slug}                     → Dynamic pages
GET    /change/{lang?}             → Change language
```

### Order Tracking
```
GET    /order-tracking             → Order tracking page
POST   /order-tracking             → Find order by tracking code
```

### Utility
```
GET    /activate                   → System activation (license)
POST   /activate_system_submit     → Submit activation
GET    /maintenance-mode           → Maintenance mode page
GET    /placeholder-image/{size}   → Generate placeholder images
```

---

## Admin Routes

**Base URL:** `/admin`

### Authentication
```
GET    /admin                      → Admin login page
POST   /admin                      → Process login
GET    /admin/logout               → Logout
GET    /admin/password/reset       → Password reset request
POST   /admin/password/reset       → Send reset email
GET    /admin/password/reset/{token} → Reset password form
POST   /admin/password/reset/change  → Change password
GET    /admin/password/code-verify → Verify reset code
POST   /admin/password/verify-code → Submit verification code
```

### Dashboard & Profile
```
GET    /admin/dashboard            → Admin dashboard
GET    /admin/profile              → View profile
POST   /admin/profile              → Update profile
GET    /admin/password             → Change password page
POST   /admin/password             → Update password
```

### Branch Management
```
GET    /admin/branch               → List all branches
POST   /admin/branch/store         → Create/update branch
POST   /admin/branch/status/{id}   → Toggle branch status
```

### Branch Manager Management
```
GET    /admin/branch-manager/list               → List all managers
GET    /admin/branch-manager/create             → Create manager form
POST   /admin/branch-manager/store/{id?}        → Save/update manager
GET    /admin/branch-manager/edit/{id}          → Edit manager
POST   /admin/branch-manager/status/{id}        → Toggle manager status
GET    /admin/branch-manager/dashboard/{id}     → Manager dashboard
GET    /admin/branch-manager/manager/{id}       → View manager details
GET    /admin/branch-manager/staff/{id}         → List manager's staff
GET    /admin/branch-manager/staff/dashboard/{id} → Staff dashboard
```

### Staff Management
```
GET    /admin/staff                → List all staff
POST   /admin/store                → Create admin user
GET    /admin/all                  → List all admins
POST   /admin/remove/{id}          → Remove admin
```

### Courier Management
```
GET    /admin/courier/list                   → All couriers
GET    /admin/courier/details/{id}           → Courier details
GET    /admin/courier/invoice/{id}           → Print invoice
GET    /admin/courier/branch/income          → Branch income report
```

### Courier Types & Units
```
GET    /admin/courier/manage/type              → List courier types
POST   /admin/courier/manage/type/store        → Create/update type
POST   /admin/courier/manage/type/status/{id}  → Toggle type status
GET    /admin/courier/manage/unit              → List units
POST   /admin/courier/manage/unit/store        → Create/update unit
POST   /admin/courier/manage/status/{id}       → Toggle unit status
```

### Customer Management
```
GET    /admin/customer               → List all customers
POST   /admin/customer/import/customers → Import customers (CSV/Excel)
POST   /admin/customer/export/customers → Export customers
```

### General Settings
```
GET    /admin/general-setting        → General settings
POST   /admin/general-setting        → Update general settings
GET    /admin/setting/system-configuration → System configuration
POST   /admin/setting/system-configuration → Update config
GET    /admin/setting/logo-icon      → Logo & favicon settings
POST   /admin/setting/logo-icon      → Update logo/favicon
GET    /admin/maintenance-mode       → Maintenance mode toggle
POST   /admin/maintenance-mode       → Update maintenance mode
```

### Notification Settings
```
GET    /admin/notification/templates                    → List templates
GET    /admin/notification/template/edit/{type}/{id}    → Edit template
POST   /admin/notification/template/update/{type}/{id}  → Update template

# Email Settings
GET    /admin/notification/email/setting      → Email configuration
POST   /admin/notification/email/setting      → Update email config
POST   /admin/notification/email/test         → Send test email
GET    /admin/notification/global/email       → Global email template
POST   /admin/notification/global/email/update → Update global template

# SMS Settings
GET    /admin/notification/sms/setting        → SMS configuration
POST   /admin/notification/sms/setting        → Update SMS config
POST   /admin/notification/sms/test           → Send test SMS
GET    /admin/notification/global/sms         → Global SMS template
POST   /admin/notification/global/sms/update  → Update global template
```

### Frontend Management
```
GET    /admin/frontend/index                  → Frontend manager
GET    /admin/frontend/templates              → Available templates
POST   /admin/frontend/templates              → Activate template
GET    /admin/frontend/frontend-sections/{key?} → Manage sections
POST   /admin/frontend/frontend-content/{key} → Update section content
GET    /admin/frontend/frontend-element/{key}/{id?} → Edit element
POST   /admin/frontend/import-content/{key}   → Import content
POST   /admin/frontend/remove/{id}            → Remove section
GET    /admin/frontend/manage-pages           → Manage pages
POST   /admin/frontend/manage-pages           → Save page
POST   /admin/frontend/manage-pages/update    → Update page
POST   /admin/frontend/manage-pages/delete/{id} → Delete page
GET    /admin/frontend/manage-pages/check-slug/{id?} → Check slug
GET    /admin/frontend/manage-section/{id}    → Manage section
POST   /admin/frontend/manage-section/{id}    → Update section
GET    /admin/frontend/manage-seo/{id}        → SEO settings
POST   /admin/frontend/manage-seo/{id}        → Update SEO
GET    /admin/frontend/frontend-element-seo/{key}/{id} → Element SEO
POST   /admin/frontend/frontend-element-seo/{key}/{id} → Update element SEO
GET    /admin/frontend/frontend-slug-check/{key}/{id?} → Check slug
GET    /admin/seo                             → General SEO settings
```

### Language Management
```
GET    /admin/language                    → List languages
POST   /admin/language                    → Create language
GET    /admin/language/edit/{id}          → Edit language keys
POST   /admin/language/update/{id}        → Update language
POST   /admin/language/delete/{id}        → Delete language
GET    /admin/language/get-keys           → Get translation keys
POST   /admin/language/store/key/{id}     → Add translation key
POST   /admin/language/update/key/{id}    → Update translation
POST   /admin/language/delete/key/{id}    → Delete translation key
POST   /admin/language/import             → Import language file
```

### Extensions
```
GET    /admin/extensions             → List extensions
POST   /admin/extensions/update/{id} → Update extension
POST   /admin/extensions/status/{id} → Toggle extension status
```

### Reports
```
GET    /admin/report/login/history         → Login history
GET    /admin/report/login/ipHistory/{ip}  → IP-based login history
GET    /admin/report/notification/history  → Notification history
GET    /admin/report/email/detail/{id}     → Email details
GET    /admin/request-report               → Request report form
POST   /admin/request-report               → Submit report request
```

### System Management
```
GET    /admin/system/info              → System information
GET    /admin/system/server-info       → Server information
GET    /admin/system/optimize          → Optimize system
GET    /admin/system/optimize-clear    → Clear optimization
GET    /admin/system/system-update     → System update page
POST   /admin/system/system-update     → Process update
GET    /admin/system/system-update/log → Update log
```

### Advanced Settings
```
GET    /admin/custom-css      → Custom CSS editor
POST   /admin/custom-css      → Save custom CSS
GET    /admin/robot           → Robots.txt editor
POST   /admin/robot           → Save robots.txt
GET    /admin/sitemap         → Sitemap settings
POST   /admin/sitemap         → Generate sitemap
GET    /admin/cookie          → Cookie settings
POST   /admin/cookie          → Update cookie settings
GET    /admin/setting/system  → System settings
```

### Support Tickets (Admin)
```
GET    /admin/ticket               → All tickets
GET    /admin/ticket/pending       → Pending tickets
GET    /admin/ticket/answered      → Answered tickets
GET    /admin/ticket/closed        → Closed tickets
GET    /admin/ticket/view/{id}     → View ticket
POST   /admin/ticket/reply/{id}    → Reply to ticket
POST   /admin/ticket/close/{id}    → Close ticket
POST   /admin/ticket/delete/{id}   → Delete ticket
GET    /admin/ticket/download/{attachment_id} → Download attachment
```

### Notifications
```
GET    /admin/notifications                    → All notifications
GET    /admin/notification/read/{id}           → Mark as read
GET    /admin/notifications/read-all           → Mark all as read
POST   /admin/notifications/delete-single/{id} → Delete notification
POST   /admin/notifications/delete-all         → Delete all notifications
```

### Downloads
```
GET    /admin/download-attachments/{file_hash} → Download file
```

---

## Manager Routes

**Base URL:** `/manager`

**Note:** Managers are created by Admins, not through self-registration!

### Authentication
```
GET    /manager                     → Manager login page
POST   /manager                     → Process login
GET    /manager/logout              → Logout
GET    /manager/password/reset      → Password reset request
POST   /manager/password/email      → Send reset email
GET    /manager/password/password/reset/{token} → Reset form
POST   /manager/password/password/reset/change  → Change password
GET    /manager/password/code-verify → Verify code page
POST   /manager/password/verify-code → Submit verification code
```

### Dashboard & Profile
```
GET    /manager/dashboard           → Manager dashboard
GET    /manager/profile             → View profile
POST   /manager/profile/update      → Update profile
GET    /manager/password            → Change password
POST   /manager/password/update     → Update password
```

### Staff Management
```
GET    /manager/staff/list          → List branch staff
GET    /manager/staff/create        → Create staff form
POST   /manager/staff/store         → Save/update staff
GET    /manager/staff/edit/{id}     → Edit staff
POST   /manager/staff/status/{id}   → Toggle staff status
```

### Branch Management
```
GET    /manager/branch/list         → List branches
GET    /manager/branch/income       → Branch income report
```

### Courier Management
```
GET    /manager/courier/list                → All couriers
GET    /manager/courier/sent-queue/list     → Sent queue
GET    /manager/courier/dispatch/list       → Dispatched couriers
GET    /manager/courier/upcoming/list       → Upcoming couriers
GET    /manager/courier/delivery-queue/list → Delivery queue
GET    /manager/courier/sent                → Sent couriers
GET    /manager/courier/delivered           → Delivered couriers
GET    /manager/courier/search              → Search courier
GET    /manager/courier/invoice/{id}        → Print invoice
```

### Support Tickets (Manager)
```
GET    /manager/ticket              → All tickets
GET    /manager/ticket/new          → Create ticket page
POST   /manager/ticket/create       → Submit ticket
GET    /manager/ticket/view/{ticket} → View ticket
POST   /manager/ticket/reply/{ticket} → Reply to ticket
POST   /manager/ticket/close/{ticket} → Close ticket
GET    /manager/ticket/download/{ticket} → Download attachment
```

---

## Staff Routes

**Base URL:** `/staff`

**Note:** Staff are created by Managers or Admins, not through self-registration!

### Authentication
```
GET    /staff                      → Staff login page
POST   /staff                      → Process login
GET    /staff/logout               → Logout
GET    /staff/password/reset       → Password reset request
POST   /staff/password/email       → Send reset email
GET    /staff/password/password/reset/{token} → Reset form
POST   /staff/password/password/reset/change  → Change password
GET    /staff/password/code-verify → Verify code page
POST   /staff/password/verify-code → Submit verification code
```

### Dashboard & Profile
```
GET    /staff/dashboard            → Staff dashboard
GET    /staff/profile              → View profile
POST   /staff/profile/update       → Update profile
GET    /staff/password             → Change password
POST   /staff/password/update      → Update password
```

### Branch Management
```
GET    /staff/branch/list          → List branches
GET    /staff/branch/income        → Branch income report
```

### Courier Management (Full CRUD)
```
# Creating Couriers
GET    /staff/courier/send         → Send courier form (CREATE)
POST   /staff/courier/store        → Save new courier
GET    /staff/customer/search      → Search customer (AJAX)

# Managing Couriers
GET    /staff/courier/list                  → All couriers
GET    /staff/courier/sent/queue            → Sent queue (status 0)
GET    /staff/courier/dispatch              → Dispatched (status 1)
POST   /staff/courier/status/{id}           → Dispatch courier
POST   /staff/courier/dispatch-all          → Dispatch all in queue
GET    /staff/courier/upcoming              → Upcoming to branch
GET    /staff/courier/received/list         → Received list
POST   /staff/courier/receive/{id}          → Mark as received
GET    /staff/courier/delivery/queue        → Delivery queue (status 2)
GET    /staff/courier/delivery/list         → All deliveries
GET    /staff/courier/delivery/list/total   → Delivered total
POST   /staff/courier/delivery/store        → Mark as delivered

# Courier Details & Edit
GET    /staff/courier/details/{id}          → View details
GET    /staff/courier/edit/{id}             → Edit courier
POST   /staff/courier/update/{id}           → Update courier
GET    /staff/courier/invoice/{id}          → Print invoice

# Search & Filters
GET    /staff/courier/search                → Search courier
GET    /staff/courier/date/search           → Search by date
GET    /staff/courier/send/list             → Sent list
GET    /staff/courier/manage/list           → Manage all
GET    /staff/courier/manage/sent.list      → Sent management
GET    /staff/courier/manage/delivered      → Delivered list
```

### Payment Management
```
POST   /staff/courier/payment       → Process payment
GET    /staff/cash/collection       → Cash collection report
```

### Support Tickets (Staff)
```
GET    /staff/ticket               → All tickets
GET    /staff/ticket/new           → Create ticket page
POST   /staff/ticket/create        → Submit ticket
GET    /staff/ticket/view/{ticket} → View ticket
POST   /staff/ticket/reply/{ticket} → Reply to ticket
POST   /staff/ticket/close/{ticket} → Close ticket
POST   /staff/ticket/delete/{id}   → Delete ticket
GET    /staff/ticket/download/{ticket} → Download attachment
```

---

## Support Ticket Routes (Public)

**Base URL:** `/ticket`

Public support system accessible without login.

```
GET    /ticket                      → All tickets
GET    /ticket/new                  → Create ticket page
POST   /ticket/create               → Submit new ticket
GET    /ticket/view/{ticket}        → View ticket
POST   /ticket/reply/{id}           → Reply to ticket
POST   /ticket/close/{id}           → Close ticket
GET    /ticket/download/{attachment_id} → Download attachment
```

---

## Quick Reference

### Creating Users

| User Type | Who Can Create | URL | Method |
|-----------|----------------|-----|--------|
| Admin | System/Database | - | Direct DB |
| Manager | Admin | `/admin/branch-manager/create` | GET + POST |
| Staff | Manager or Admin | `/manager/staff/create` | GET + POST |
| Customer | Auto-created | When staff creates courier | Automatic |

### Login URLs

| Role | URL | Default Credentials |
|------|-----|-------------------|
| Admin | http://localhost:8080/admin | admin / admin123 |
| Manager | http://localhost:8080/manager | Created by admin |
| Staff | http://localhost:8080/staff | Created by manager |

### Common Tasks

**Create a Manager:**
1. Login as Admin → http://localhost:8080/admin
2. Navigate to Branch Manager → `/admin/branch-manager/create`
3. Fill form and submit

**Create a Staff:**
1. Login as Manager → http://localhost:8080/manager
2. Navigate to Staff → `/manager/staff/create`
3. Fill form and submit

**Create a Courier:**
1. Login as Staff → http://localhost:8080/staff
2. Navigate to Send Courier → `/staff/courier/send`
3. Fill courier details, select/create customers
4. Submit form

**Track a Courier:**
1. Visit: http://localhost:8080/order-tracking
2. Enter tracking code
3. View status

---

## Courier Status Flow

```
0 → QUEUE          (Created, waiting to dispatch)
    ↓ Staff clicks "Dispatch"
1 → DISPATCH       (In transit to receiver branch)
    ↓ Receiver staff clicks "Receive"
2 → DELIVERY_QUEUE (Received at receiver branch, waiting for delivery)
    ↓ Staff marks as delivered
3 → DELIVERED      (Delivered to customer)
```

### Staff Routes by Status

- **Queue (0):** `/staff/courier/sent/queue`
- **Dispatch (1):** `/staff/courier/dispatch`
- **Upcoming (1):** `/staff/courier/upcoming` (for receiver branch)
- **Delivery Queue (2):** `/staff/courier/delivery/queue`
- **Delivered (3):** `/staff/courier/delivery/list`

---

## API Routes

```
GET    /sanctum/csrf-cookie         → Get CSRF token for API
```

---

## System Routes

```
GET    /clear                       → Clear application cache
```

---

**Last Updated:** November 4, 2025
**Application:** CourierLab v3.1
**Framework:** Laravel 11
