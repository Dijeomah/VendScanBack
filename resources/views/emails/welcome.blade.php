@component('mail::message')
# Welcome to VendScan, {{ $userName }}!

We're excited to have you on board! VendScan helps you digitize your menu and manage your restaurant efficiently with QR code technology.

## Getting Started

Here's what you can do next:

1. **Set Up Your Business** - Add your restaurant/business information
2. **Create Your Menu** - Add categories and menu items
3. **Generate QR Codes** - Create QR codes for your tables
4. **Manage Orders** - Track and manage customer orders in real-time

@component('mail::button', ['url' => $dashboardUrl])
Go to Dashboard
@endcomponent

## Your Current Plan

You're currently on the **Free Plan** which includes:
- 1 Business
- 10 Tables
- 10 Servers
- 50 Menu Items

Need more? Upgrade to Pro or Enterprise for unlimited resources and advanced features!

## Need Help?

If you have any questions or need assistance, feel free to reach out to our support team.

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
