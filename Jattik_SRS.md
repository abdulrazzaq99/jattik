# Jattik Software Requirements Specification (SRS)

## 1. Introduction

### 1.1 Purpose
This SRS defines the requirements for **Jattik**, a subscription-based shipping management platform tailored for users in the Kingdom of Saudi Arabia (KSA).  
The system enables users to order from remote locations, subscribe, manage shipments, use unique virtual addresses, and interact with customer support through dedicated channels.

### 1.2 Scope
The system will:
- Allow users to subscribe or use pay-per-order models.
- Provide shipment tracking and notifications.
- Offer analytics and reporting tools for administrators.
- Provide customer support via integrated channels (WhatsApp chatbot, contact forms).
- Assign unique virtual addresses (alphanumerical codes) to verified users for use in shipments.
- Annually deactivate virtual addresses that remain unused with no order history.
- Enable address management, including restrictions on address changes once a shipment is in progress.

### 1.3 Definitions
- **Virtual Address:** Unique alphanumeric identifier provided to verified users for routing shipments.
- **Premium Subscriber:** Highest-tier subscription user entitled to included insurance and additional features.
- **Warehouse:** Central hub where shipments are received, consolidated, and dispatched.

---

## 2. Overall Description

### 2.1 System Perspective
The system is a **web application** that integrates with third-party logistics providers.  
It will use **payment gateways (Mada, Apple Pay, STC Pay, credit/debit cards)** for secure online transactions.

### 2.2 User Classes and Characteristics
- **Guest User:** Can browse shipping calculators and services without creating an account.  
- **Registered User:** Can declare shipments, track shipments, manage subscriptions, and receive a virtual address once verified.  
- **Basic User:** Shipments measured per kilogram, limited to 5 shipments for 30 days.  
- **Mid-Tier User:** Shipments measured per 500 grams, limited to 7 shipments for 60 days.  
- **Premium User:** Shipments measured to the gram, includes insurance, limited to 15 shipments for 90 days.  
- **Admin/Warehouse Staff:** Manage shipments, claims, and system analytics.

### 2.3 Operating Environment
- Web-based system.  
- Hosted on cloud infrastructure (AWS KSA, STC Cloud, or similar).  
- Compliance with KSA e-commerce and data privacy regulations (SDAIA, CITC, SAMA).

---

## 3. Functional Requirements

### 3.1 User Account Management
1. User shall be able to create an account.  
2. User shall be able to log in securely (OTP via SMS/WhatsApp/email).  
3. System shall validate KSA phone numbers and addresses.  
4. Users shall be assigned a unique virtual address.  
5. System shall cancel unused virtual addresses with no order history in one year.

### 3.2 Subscription & Payment
6. Users shall be able to choose subscription models (monthly/yearly).  
7. Subscriptions auto-renew unless canceled.  
8. Users can order without subscription (pay-per-use).  
9. Premium subscribers shall get free insurance.  
10. Non-premium users can optionally buy insurance at checkout.  
11. Payments only via approved online gateways (no cash on delivery).  
12. Users must pay between receiving the shipment and before dispatch.  
13. Coupon code field available at checkout.

### 3.3 Shipment Management
14. User shall be able to input and manage multiple addresses.  
15. User can change address only before shipment dispatch.  
16. User can select shipment day (fixed monthly schedule).  
17. Warehouse holds shipments until consolidated (max 90 days).  
18. User can extend shipping date before dispatch.  
19. System shall fetch package calculators from different couriers (Aramex, DHL, etc.).  
20. User can calculate an estimate of shipping fees.  
21. Employee shall calculate shipping fees through the system.

### 3.4 Notifications & Tracking
22. User shall receive notifications when a shipment reaches facility.  
23. User shall receive notifications when a shipment is dispatched.  
24. System integrates scraper/API to fetch logistics updates (Aramex, DHL, etc.).  
25. System notifies user of carrier exceptions (delays, incorrect address).  
26. System shall send a notification with the courier tracking link.  
27. System shall send the user a notification of shipping fees with a payment link.

### 3.5 Customer Support
28. System shall provide a “Contact Us” form.  
29. System shall provide a “How it works” page.  
30. System shall integrate with WhatsApp chatbot for FAQs, OTPs, and order updates.  
31. Users can report issues (e.g., wrong parcel delivered).  
32. Claims must be processed within 10 business days.

### 3.6 Ratings & Feedback
33. User shall be able to rate services after each shipment.

### 3.7 Delivery Options
34. System shall provide at least two speeds of delivery (standard/express).  
35. User shall be able to access a shipping cost calculator.

### 3.8 Analytics (Admin Side)
36. Admin shall access dashboard showing:
   - Monthly shipping costs.
   - Most used carriers.
   - Delivery performance (average delivery times).
   - Popular regions for shipments.

---

## 4. Non-Functional Requirements

### 4.1 Performance
- The system shall support at least 10,000 concurrent users with room for scalability.  
- Shipment updates should reflect within 30 seconds of provider updates.

### 4.2 Security
- Encrypted communication via TLS 1.3.  
- Two-factor authentication for all signups.  
- Compliance with SAMA online payment regulations.  
- Data stored in accordance with Saudi PDPL.

### 4.3 Usability
- Support for both Arabic and English interfaces.  
- Clear error messages and help tooltips.

### 4.4 Availability
- System uptime of 99.9% SLA.  
- Failover mechanisms in case of data center outage.

### 4.5 Legal & Compliance
- Adherence to KSA Consumer Protection Law (returns, refunds, insurance).  
- Returns and refunds policy must be visible at checkout.

---

## 5. External Interfaces
- Payment Gateway APIs (Mada, STC Pay, Apple Pay).  
- WhatsApp Business API for chatbot integration.  
- Email/SMS OTP providers.

---

## 6. Constraints
- The system is restricted to KSA users only for the time being (local phone verification).

---

## 7. Future Enhancements (Optional)
- AI-powered delivery time predictions.  
- Integration with Saudi e-government digital identity (Absher/Nafath).
