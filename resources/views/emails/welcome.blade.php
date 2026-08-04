{{-- blade-formatter-disable --}}
@component('mail::message')
# Welcome to {{ $settings->site_name }}, {{$user->name}}!

Your account has been successfully created and is now active.

We're delighted to have you as a member of our banking community. Your account provides access to our comprehensive suite of financial services designed to meet your banking needs.

## Your Account Details
**Account Holder:** {{$user->name}} {{$user->middlename}} {{$user->lastname}}  
**Account Number:** {{$user->usernumber}}  
**Account Type:** {{$user->accounttype}}  
**Registration Date:** {{ \Carbon\Carbon::parse($user->created_at)->format('F j, Y') }}

## Access Your Account

You can access your online banking portal using these credentials:

**Email:** {{$user->email}}  
**Password:** The password you created during registration
                            
                          
                               
@component('mail::button', ['url' => $settings->site_address . '/login'])
Access Your Account
@endcomponent

## Need Assistance?

If you have any questions about your account or our services, our support team is here to help:

- **Email:** {{$settings->contact_email}}
- **Website:** {{ $settings->site_address }}

Thank you for choosing {{ $settings->site_name }} as your trusted financial partner.

Best regards,  
**{{ $settings->site_name }} Team**

---

*This is an automated notification. Please do not reply directly to this email. For support, contact {{$settings->contact_email}}*
@endcomponent
{{-- blade-formatter-disable --}}
