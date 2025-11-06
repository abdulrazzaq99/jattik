-- --------------------------------------------------------
-- Migration: Customer Authentication & Virtual Address System
-- Date: 2025-11-05
-- Description: Adds customer authentication, OTP verification, and virtual address management
-- --------------------------------------------------------

-- Step 1: Add authentication and verification fields to customers table
ALTER TABLE `customers`
ADD COLUMN `password` VARCHAR(255) NULL DEFAULT NULL AFTER `mobile`,
ADD COLUMN `username` VARCHAR(40) NULL DEFAULT NULL UNIQUE AFTER `lastname`,
ADD COLUMN `email_verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `email`,
ADD COLUMN `mobile_verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `mobile`,
ADD COLUMN `otp_code` VARCHAR(6) NULL DEFAULT NULL AFTER `password`,
ADD COLUMN `otp_expiry` TIMESTAMP NULL DEFAULT NULL AFTER `otp_code`,
ADD COLUMN `otp_type` ENUM('email', 'sms') NULL DEFAULT NULL AFTER `otp_expiry`,
ADD COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active, 2=banned' AFTER `state`,
ADD COLUMN `country_code` VARCHAR(10) NULL DEFAULT '+966' COMMENT 'KSA default +966' AFTER `mobile_verified_at`,
ADD COLUMN `postal_code` VARCHAR(20) NULL DEFAULT NULL AFTER `state`,
ADD COLUMN `remember_token` VARCHAR(100) NULL DEFAULT NULL AFTER `password`,
ADD COLUMN `last_login_at` TIMESTAMP NULL DEFAULT NULL AFTER `status`,
ADD COLUMN `last_order_at` TIMESTAMP NULL DEFAULT NULL AFTER `last_login_at`;

-- Step 2: Create virtual_addresses table
CREATE TABLE `virtual_addresses` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `customer_id` BIGINT(20) UNSIGNED NOT NULL,
  `address_code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Unique virtual address code (e.g., VA-12345678)',
  `full_address` TEXT NOT NULL COMMENT 'Complete formatted virtual address',
  `status` ENUM('active', 'inactive', 'cancelled') NOT NULL DEFAULT 'active',
  `assigned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
  `cancellation_reason` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Reason for cancellation (e.g., 1 year inactivity)',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_customer_id` (`customer_id`),
  INDEX `idx_address_code` (`address_code`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Create otp_logs table for tracking OTP attempts
CREATE TABLE `otp_logs` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `customer_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  `mobile` VARCHAR(40) NULL DEFAULT NULL,
  `otp_code` VARCHAR(6) NOT NULL,
  `otp_type` ENUM('email', 'sms', 'whatsapp') NOT NULL,
  `purpose` ENUM('registration', 'login', 'password_reset') NOT NULL,
  `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `attempts` INT(11) NOT NULL DEFAULT 0 COMMENT 'Number of verification attempts',
  `status` ENUM('pending', 'verified', 'expired', 'failed') NOT NULL DEFAULT 'pending',
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_customer_id` (`customer_id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_mobile` (`mobile`),
  INDEX `idx_status` (`status`),
  INDEX `idx_expires_at` (`expires_at`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Create customer_login_logs table for security tracking
CREATE TABLE `customer_login_logs` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `customer_id` BIGINT(20) UNSIGNED NOT NULL,
  `login_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logout_at` TIMESTAMP NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `login_method` ENUM('otp_email', 'otp_sms', 'otp_whatsapp', 'password') NOT NULL,
  `session_id` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_customer_id` (`customer_id`),
  INDEX `idx_login_at` (`login_at`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 5: Add indexes to customers table for performance
ALTER TABLE `customers`
ADD INDEX `idx_email` (`email`),
ADD INDEX `idx_mobile` (`mobile`),
ADD INDEX `idx_username` (`username`),
ADD INDEX `idx_status` (`status`),
ADD INDEX `idx_last_order_at` (`last_order_at`);

-- Step 6: Insert notification templates for OTP
INSERT INTO `notification_templates` (`id`, `act`, `name`, `subject`, `email_body`, `sms_body`, `shortcodes`, `email_status`, `email_sent_from_name`, `email_sent_from_address`, `sms_status`, `sms_sent_from`, `created_at`, `updated_at`) VALUES
(NULL, 'CUSTOMER_OTP_REGISTRATION', 'Customer OTP - Registration', 'Verify Your Account - {{site_name}}',
'<div>Hi {{fullname}},</div><div><br></div><div>Thank you for registering with {{site_name}}!</div><div><br></div><div>Your One-Time Password (OTP) for account verification is:</div><div><br></div><div style=\"text-align: center;\"><h2>{{otp_code}}</h2></div><div><br></div><div>This OTP will expire in {{otp_validity}} minutes.</div><div><br></div><div>If you did not request this, please ignore this email.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
'Hi {{fullname}}, Your {{site_name}} verification OTP is: {{otp_code}}. Valid for {{otp_validity}} minutes.',
'{"fullname":"Customer full name","otp_code":"One-time password code","otp_validity":"OTP validity in minutes","site_name":"Site name"}',
1, NULL, NULL, 1, NULL, NOW(), NOW()),

(NULL, 'CUSTOMER_OTP_LOGIN', 'Customer OTP - Login', 'Login Verification - {{site_name}}',
'<div>Hi {{fullname}},</div><div><br></div><div>Your One-Time Password (OTP) for login is:</div><div><br></div><div style=\"text-align: center;\"><h2>{{otp_code}}</h2></div><div><br></div><div>This OTP will expire in {{otp_validity}} minutes.</div><div><br></div><div>Login Details:</div><div>- IP Address: {{ip_address}}</div><div>- Time: {{login_time}}</div><div><br></div><div>If you did not attempt to log in, please contact support immediately.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
'Hi {{fullname}}, Your {{site_name}} login OTP is: {{otp_code}}. Valid for {{otp_validity}} minutes.',
'{"fullname":"Customer full name","otp_code":"One-time password code","otp_validity":"OTP validity in minutes","ip_address":"Login IP address","login_time":"Login attempt time","site_name":"Site name"}',
1, NULL, NULL, 1, NULL, NOW(), NOW()),

(NULL, 'CUSTOMER_VIRTUAL_ADDRESS_ASSIGNED', 'Virtual Address Assigned', 'Your Virtual Address - {{site_name}}',
'<div>Hi {{fullname}},</div><div><br></div><div>Welcome to {{site_name}}! Your unique virtual address has been assigned:</div><div><br></div><div style=\"background: #f5f5f5; padding: 15px; border-left: 4px solid #4CAF50;\"><strong>Virtual Address Code:</strong> {{virtual_address_code}}<br><strong>Full Address:</strong><br>{{virtual_address_full}}</div><div><br></div><div>You can use this virtual address for all your courier deliveries.</div><div><br></div><div><strong>Important:</strong> This address will remain active as long as you have orders within the last 12 months.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
'Hi {{fullname}}, Your virtual address: {{virtual_address_code}}. Use this for all deliveries. - {{site_name}}',
'{"fullname":"Customer full name","virtual_address_code":"Virtual address code","virtual_address_full":"Complete virtual address","site_name":"Site name"}',
1, NULL, NULL, 1, NULL, NOW(), NOW()),

(NULL, 'CUSTOMER_VIRTUAL_ADDRESS_CANCELLED', 'Virtual Address Cancelled', 'Virtual Address Cancellation Notice - {{site_name}}',
'<div>Hi {{fullname}},</div><div><br></div><div>Your virtual address <strong>{{virtual_address_code}}</strong> has been cancelled due to inactivity.</div><div><br></div><div><strong>Reason:</strong> No orders in the past 12 months.</div><div>Cancelled on: {{cancellation_date}}</div><div><br></div><div>If you wish to resume using our services, you can log in to reactivate your account and receive a new virtual address.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
'Hi {{fullname}}, Your virtual address {{virtual_address_code}} has been cancelled due to inactivity. Login to reactivate. - {{site_name}}',
'{"fullname":"Customer full name","virtual_address_code":"Virtual address code","cancellation_date":"Date of cancellation","site_name":"Site name"}',
1, NULL, NULL, 1, NULL, NOW(), NOW());

-- Step 7: Update existing customers to set default status (optional)
UPDATE `customers` SET `status` = 1 WHERE `status` IS NULL;

-- --------------------------------------------------------
-- End of Migration
-- --------------------------------------------------------
