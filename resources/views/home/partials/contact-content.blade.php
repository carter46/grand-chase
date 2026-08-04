@php
    $contactValue = $settings->whatsapp ?? $settings->phone ?? $settings->contact_email;
    $contactIsEmail = $contactValue && filter_var($contactValue, FILTER_VALIDATE_EMAIL);
    $contactHref = $contactValue ? ($contactIsEmail ? 'mailto:' . $contactValue : 'tel:' . preg_replace('/[^\d+]/', '', $contactValue)) : '#';
@endphp
<div class="flex flex-col w-full">
<!-- Hero Section -->
<section class="relative min-h-[calc(100vh-104px)] md:min-h-[calc(100vh-131px)] flex items-center overflow-hidden bg-on-surface">
<div class="absolute inset-0 z-0">
<div class="bg-cover bg-center w-full h-full opacity-60" data-alt="High-angle cinematic shot of a modern glass skyscraper reflecting a clear blue sky at dawn. The architecture is sharp and geometric, conveying a sense of stability and institutional power. The lighting is cold and professional with hints of warm sunrise oranges hitting the steel frames. 8k resolution, architectural photography style." style="background-image: url('{{ asset('assets/images/image44.jpg') }}')"></div>
<div class="absolute inset-0 bg-gradient-to-r from-on-surface via-on-surface/80 to-transparent"></div>
</div>
<div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-gutter w-full pt-10">
<div class="max-w-2xl">
<span class="font-label-bold text-primary-fixed-dim tracking-[0.2em] uppercase mb-stack-md block">Institutional Reliability</span>
<h1 class="text-hero-medium text-white mb-stack-md">Global Support &amp; Client Relations</h1>
<p class="font-body-lg text-surface-variant max-w-xl">
          {{ $settings->site_name }} provides dedicated advisory and technical support for our global partners. Experience the precision of institutional management paired with the nuance of personal service.
        </p>
</div>
</div>
</section>
<!-- Department Grid & Form Section -->
<section class="max-w-[1200px] mx-auto px-4 md:px-gutter -mt-12 md:-mt-24 relative z-20 pb-12 md:pb-stack-lg">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Contact Cards Column -->
<div class="lg:col-span-7 flex flex-col gap-gutter min-w-0">
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter min-w-0">
<!-- Personal Banking -->
<div class="bg-surface-container-lowest p-stack-lg shadow-xl hover:shadow-2xl transition-shadow flex flex-col group min-w-0 overflow-hidden">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary text-[32px]">person_pin</span>
<span class="font-caption text-on-surface-variant bg-surface-container px-2 py-1 uppercase tracking-wider">Retail</span>
</div>
<h3 class="font-headline-md text-on-surface mb-2">Personal Banking</h3>
<p class="font-body-sm text-on-surface-variant mb-6">Everyday accounts, mortgages, and private credit facilities.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-surface-container pt-4 min-w-0">
@if ($contactValue)
<a class="font-label-bold text-on-surface hover:text-primary transition-colors flex items-start gap-2 min-w-0" href="{{ $contactHref }}">
<span class="material-symbols-outlined text-sm shrink-0 mt-0.5">{{ $contactIsEmail ? 'mail' : 'call' }}</span>
<span class="break-all text-sm leading-snug">{{ $contactValue }}</span>
</a>
@endif
<span class="font-body-sm text-on-secondary-container">Mon–Fri: 8AM - 8PM EST</span>
</div>
</div>
<!-- Business Banking -->
<div class="bg-surface-container-lowest p-stack-lg shadow-xl hover:shadow-2xl transition-shadow flex flex-col group min-w-0 overflow-hidden">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary text-[32px]">corporate_fare</span>
<span class="font-caption text-on-surface-variant bg-surface-container px-2 py-1 uppercase tracking-wider">Commercial</span>
</div>
<h3 class="font-headline-md text-on-surface mb-2">Business Banking</h3>
<p class="font-body-sm text-on-surface-variant mb-6">Treasury management, commercial lending, and merchant services.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-surface-container pt-4 min-w-0">
@if ($settings->contact_email)
<a class="font-label-bold text-on-surface hover:text-primary transition-colors flex items-start gap-2 min-w-0" href="mailto:{{ $settings->contact_email }}">
<span class="material-symbols-outlined text-sm shrink-0 mt-0.5">mail</span>
<span class="break-all text-sm leading-snug">{{ $settings->contact_email }}</span>
</a>
@endif
<span class="font-body-sm text-on-secondary-container">Dedicated Account Support</span>
</div>
</div>
<!-- Wealth Management -->
<div class="bg-surface-container-lowest p-stack-lg shadow-xl hover:shadow-2xl transition-shadow flex flex-col group min-w-0 overflow-hidden">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary text-[32px]">account_balance_wallet</span>
<span class="font-caption text-on-surface-variant bg-surface-container px-2 py-1 uppercase tracking-wider">Advisory</span>
</div>
<h3 class="font-headline-md text-on-surface mb-2">Wealth Management</h3>
<p class="font-body-sm text-on-surface-variant mb-6">Strategic portfolio planning and bespoke investment strategies.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-surface-container pt-4 min-w-0">
@if ($contactValue)
<a class="font-label-bold text-on-surface hover:text-primary transition-colors flex items-start gap-2 min-w-0" href="{{ $contactHref }}">
<span class="material-symbols-outlined text-sm shrink-0 mt-0.5">{{ $contactIsEmail ? 'mail' : 'call' }}</span>
<span class="break-all text-sm leading-snug">{{ $contactValue }}</span>
</a>
@endif
<span class="font-body-sm text-on-secondary-container">Consultation by Appointment</span>
</div>
</div>
<!-- 24/7 Security -->
<div class="bg-on-surface p-stack-lg shadow-xl flex flex-col text-white min-w-0 overflow-hidden">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary-fixed-dim text-[32px]">shield_lock</span>
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-error animate-pulse"></div>
<span class="font-caption uppercase tracking-wider text-white/60">Live Response</span>
</div>
</div>
<h3 class="font-headline-md mb-2">24/7 Security Support</h3>
<p class="font-body-sm text-white/70 mb-6">Immediate assistance for fraud reports, lost cards, or account lockouts.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-white/10 pt-4 min-w-0">
@if ($contactValue)
<a class="font-label-bold text-primary-fixed-dim flex items-start gap-2 min-w-0" href="{{ $contactHref }}">
<span class="material-symbols-outlined shrink-0 mt-0.5">{{ $contactIsEmail ? 'mail' : 'support_agent' }}</span>
<span class="break-all text-sm leading-snug">{{ $contactValue }}</span>
</a>
@endif
<span class="font-body-sm text-white/40 uppercase tracking-widest">Always Available</span>
</div>
</div>
</div>
</div>
<!-- Secure Form Column -->
<div class="lg:col-span-5">
<div class="bg-white p-stack-lg shadow-2xl sticky top-24">
<div class="mb-stack-lg">
<h2 class="font-headline-lg text-on-surface uppercase tracking-tight">Secure Inquiry</h2>
<div class="h-1 w-12 bg-primary mt-2"></div>
<p class="font-body-sm text-on-surface-variant mt-4">For your security, do not include account numbers or social security details in this form.</p>
</div>
@if (Session::has('success'))
<div class="mb-stack-md p-4 bg-primary/10 border border-primary text-primary font-body-sm">{{ Session::get('success') }}</div>
@endif
@if (Session::has('message'))
<div class="mb-stack-md p-4 bg-error-container border border-error text-on-error-container font-body-sm">{{ Session::get('message') }}</div>
@endif
<form method="POST" action="{{ route('homesendcontact') }}" class="flex flex-col gap-stack-md" id="contactForm">
@csrf
<div class="flex flex-col gap-2">
<label class="font-label-bold text-on-surface uppercase text-[12px]" for="fullname">Full Legal Name</label>
<input id="fullname" name="fullname" required class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md transition-all" placeholder="Johnathan Doe" type="text"/>
</div>
<div class="flex flex-col gap-2">
<label class="font-label-bold text-on-surface uppercase text-[12px]" for="email">Corporate Email Address</label>
<input id="email" name="email" required class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md transition-all" placeholder="name@company.com" type="email"/>
</div>
<div class="flex flex-col gap-2">
<label class="font-label-bold text-on-surface uppercase text-[12px]" for="phone">Phone Number</label>
<input id="phone" name="phone" class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md transition-all" placeholder="+1 (555) 000-0000" type="text"/>
</div>
<div class="flex flex-col gap-2">
<label class="font-label-bold text-on-surface uppercase text-[12px]" for="subject">Department of Interest</label>
<select id="subject" name="subject" class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md transition-all appearance-none cursor-pointer">
<option value="General Inquiry">General Inquiry</option>
<option value="Wealth Management">Wealth Management</option>
<option value="Commercial Credit">Commercial Credit</option>
<option value="Cybersecurity & Fraud">Cybersecurity &amp; Fraud</option>
</select>
</div>
<div class="flex flex-col gap-2">
<label class="font-label-bold text-on-surface uppercase text-[12px]" for="message">Detailed Message</label>
<textarea id="message" name="message" required class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md transition-all resize-none" placeholder="Briefly describe your request..." rows="4"></textarea>
</div>
<button class="bg-[#FF4800] text-white font-label-bold uppercase py-4 mt-4 hover:bg-primary transition-all flex items-center justify-center gap-3 group" type="submit">
              Send Secure Message
              <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">arrow_forward</span>
</button>
</form>
</div>
</div>
</div>
</section>
<!-- Find a Branch CTA -->
<section class="max-w-[1200px] mx-auto px-4 md:px-gutter py-stack-lg">
<div class="bg-on-surface p-12 flex flex-col md:flex-row items-center justify-between gap-stack-lg overflow-hidden relative">
<!-- Decorative element -->
<div class="absolute -right-12 -bottom-12 w-64 h-64 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
<div class="relative z-10 text-center md:text-left">
<h2 class="font-headline-lg text-white">Need local assistance?</h2>
<p class="font-body-lg text-white/60 mt-2">Locate an {{ $settings->site_name }} Branch or secure ATM near you.</p>
</div>
<div class="relative z-10 flex flex-col sm:flex-row gap-4 w-full md:w-auto">
<div class="flex bg-white/10 backdrop-blur-md p-1 items-center border border-white/20">
<span class="material-symbols-outlined text-white/40 px-3">location_on</span>
<input class="bg-transparent text-white font-body-md py-3 outline-none w-full sm:w-48 placeholder:text-white/30" placeholder="Zip code or City" type="text"/>
<a href="{{ url('contact') }}" class="inline-flex items-center bg-white text-on-surface font-label-bold uppercase px-6 py-3 hover:bg-primary-fixed-dim transition-colors whitespace-nowrap">Find Branch</a>
</div>
</div>
</div>
</section>
</div>