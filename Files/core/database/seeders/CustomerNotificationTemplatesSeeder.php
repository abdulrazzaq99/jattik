<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerNotificationTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'act' => 'CUSTOMER_OTP_REGISTRATION',
                'name' => 'Customer OTP - Registration',
                'subject' => 'Verify Your Account - {{site_name}}',
                'email_body' => '<div>Hi {{fullname}},</div><div><br></div><div>Thank you for registering with {{site_name}}!</div><div><br></div><div>Your One-Time Password (OTP) for account verification is:</div><div><br></div><div style="text-align: center;"><h2>{{otp_code}}</h2></div><div><br></div><div>This OTP will expire in {{otp_validity}} minutes.</div><div><br></div><div>If you did not request this, please ignore this email.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
                'sms_body' => 'Hi {{fullname}}, Your {{site_name}} verification OTP is: {{otp_code}}. Valid for {{otp_validity}} minutes.',
                'shortcodes' => '{"fullname":"Customer full name","otp_code":"One-time password code","otp_validity":"OTP validity in minutes","site_name":"Site name"}',
                'email_status' => 1,
                'sms_status' => 1,
            ],
            [
                'act' => 'CUSTOMER_OTP_LOGIN',
                'name' => 'Customer OTP - Login',
                'subject' => 'Login Verification - {{site_name}}',
                'email_body' => '<div>Hi {{fullname}},</div><div><br></div><div>Your One-Time Password (OTP) for login is:</div><div><br></div><div style="text-align: center;"><h2>{{otp_code}}</h2></div><div><br></div><div>This OTP will expire in {{otp_validity}} minutes.</div><div><br></div><div>Login Details:</div><div>- IP Address: {{ip_address}}</div><div>- Time: {{login_time}}</div><div><br></div><div>If you did not attempt to log in, please contact support immediately.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
                'sms_body' => 'Hi {{fullname}}, Your {{site_name}} login OTP is: {{otp_code}}. Valid for {{otp_validity}} minutes.',
                'shortcodes' => '{"fullname":"Customer full name","otp_code":"One-time password code","otp_validity":"OTP validity in minutes","ip_address":"Login IP address","login_time":"Login attempt time","site_name":"Site name"}',
                'email_status' => 1,
                'sms_status' => 1,
            ],
            [
                'act' => 'CUSTOMER_VIRTUAL_ADDRESS_ASSIGNED',
                'name' => 'Virtual Address Assigned',
                'subject' => 'Your Virtual Address - {{site_name}}',
                'email_body' => '<div>Hi {{fullname}},</div><div><br></div><div>Welcome to {{site_name}}! Your unique virtual address has been assigned:</div><div><br></div><div style="background: #f5f5f5; padding: 15px; border-left: 4px solid #4CAF50;"><strong>Virtual Address Code:</strong> {{virtual_address_code}}<br><strong>Full Address:</strong><br>{{virtual_address_full}}</div><div><br></div><div>You can use this virtual address for all your courier deliveries.</div><div><br></div><div><strong>Important:</strong> This address will remain active as long as you have orders within the last 12 months.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
                'sms_body' => 'Hi {{fullname}}, Your virtual address: {{virtual_address_code}}. Use this for all deliveries. - {{site_name}}',
                'shortcodes' => '{"fullname":"Customer full name","virtual_address_code":"Virtual address code","virtual_address_full":"Complete virtual address","site_name":"Site name"}',
                'email_status' => 1,
                'sms_status' => 1,
            ],
            [
                'act' => 'CUSTOMER_VIRTUAL_ADDRESS_CANCELLED',
                'name' => 'Virtual Address Cancelled',
                'subject' => 'Virtual Address Cancellation Notice - {{site_name}}',
                'email_body' => '<div>Hi {{fullname}},</div><div><br></div><div>Your virtual address <strong>{{virtual_address_code}}</strong> has been cancelled due to inactivity.</div><div><br></div><div><strong>Reason:</strong> No orders in the past 12 months.</div><div>Cancelled on: {{cancellation_date}}</div><div><br></div><div>If you wish to resume using our services, you can log in to reactivate your account and receive a new virtual address.</div><div><br></div><div>Thank you,</div><div>{{site_name}} Team</div>',
                'sms_body' => 'Hi {{fullname}}, Your virtual address {{virtual_address_code}} has been cancelled due to inactivity. Login to reactivate. - {{site_name}}',
                'shortcodes' => '{"fullname":"Customer full name","virtual_address_code":"Virtual address code","cancellation_date":"Date of cancellation","site_name":"Site name"}',
                'email_status' => 1,
                'sms_status' => 1,
            ],
        ];

        foreach ($templates as $template) {
            // Check if template already exists
            $exists = DB::table('notification_templates')
                ->where('act', $template['act'])
                ->exists();

            if (!$exists) {
                DB::table('notification_templates')->insert(array_merge($template, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
