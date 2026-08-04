@php
    $contactValue = $settings->whatsapp ?? $settings->phone ?? $settings->contact_email;
    $contactIsEmail = $contactValue && filter_var($contactValue, FILTER_VALIDATE_EMAIL);
    $contactHref = $contactValue ? ($contactIsEmail ? 'mailto:' . $contactValue : 'tel:' . preg_replace('/[^\d+]/', '', $contactValue)) : '#';
@endphp
<div class="flex flex-col w-full">
<!-- Hero Section -->
<section class="relative h-[614px] min-h-[500px] flex items-center overflow-hidden bg-on-surface">
<div class="absolute inset-0 z-0">
<div class="bg-cover bg-center w-full h-full opacity-40" data-alt="High-angle cinematic shot of a modern glass skyscraper reflecting a clear blue sky at dawn. The architecture is sharp and geometric, conveying a sense of stability and institutional power. The lighting is cold and professional with hints of warm sunrise oranges hitting the steel frames. 8k resolution, architectural photography style." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAb_zi1OYn0n__Mr0Arc-5IgOE4U1h-tITheSVF7oIdjca1MGT9PVLFQHB7cWrDh1P8buCLgu616DVb96CrDC7A1Um-wE1p5CSJ1U40JT6xtwjlIQcco4O--DagrvdjHG77v8PaqmRE65VrycMAV5kwohN16u6TiNZ71nTZk6UD2qzaKnhzr8h6gJy082Goes7c_BYNhZFO9YE39hy-bAn2Ycm3xw-x2d4ddFsXpeI7JvSXpFwgtLZS')"></div>
<div class="absolute inset-0 bg-gradient-to-r from-on-surface via-on-surface/80 to-transparent"></div>
</div>
<div class="relative z-10 max-w-[1200px] mx-auto px-margin-desktop w-full">
<div class="max-w-2xl">
<span class="font-label-bold text-primary-fixed-dim tracking-[0.2em] uppercase mb-stack-md block">Institutional Reliability</span>
<h1 class="font-headline-xl text-white mb-stack-md text-[56px] leading-[1.1]">Global Support &amp; Client Relations</h1>
<p class="font-body-lg text-surface-variant max-w-xl">
          {{ $settings->site_name }} provides dedicated advisory and technical support for our global partners. Experience the precision of institutional management paired with the nuance of personal service.
        </p>
</div>
</div>
</section>
<!-- Department Grid & Form Section -->
<section class="max-w-[1200px] mx-auto px-margin-desktop -mt-24 relative z-20 pb-stack-lg">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
<!-- Contact Cards Column -->
<div class="lg:col-span-7 flex flex-col gap-gutter">
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
<!-- Personal Banking -->
<div class="bg-surface-container-lowest p-stack-lg shadow-xl hover:shadow-2xl transition-shadow flex flex-col group">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary text-[32px]">person_pin</span>
<span class="font-caption text-on-surface-variant bg-surface-container px-2 py-1 uppercase tracking-wider">Retail</span>
</div>
<h3 class="font-headline-md text-on-surface mb-2">Personal Banking</h3>
<p class="font-body-sm text-on-surface-variant mb-6">Everyday accounts, mortgages, and private credit facilities.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-surface-container pt-4">
@if ($contactValue)
<a class="font-label-bold text-on-surface hover:text-primary transition-colors flex items-center gap-2" href="{{ $contactHref }}">
<span class="material-symbols-outlined text-sm">{{ $contactIsEmail ? 'mail' : 'call' }}</span> {{ $contactValue }}
              </a>
@endif
<span class="font-body-sm text-on-secondary-container">Mon–Fri: 8AM - 8PM EST</span>
</div>
</div>
<!-- Business Banking -->
<div class="bg-surface-container-lowest p-stack-lg shadow-xl hover:shadow-2xl transition-shadow flex flex-col group">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary text-[32px]">corporate_fare</span>
<span class="font-caption text-on-surface-variant bg-surface-container px-2 py-1 uppercase tracking-wider">Commercial</span>
</div>
<h3 class="font-headline-md text-on-surface mb-2">Business Banking</h3>
<p class="font-body-sm text-on-surface-variant mb-6">Treasury management, commercial lending, and merchant services.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-surface-container pt-4">
@if ($settings->contact_email)
<a class="font-label-bold text-on-surface hover:text-primary transition-colors flex items-center gap-2" href="mailto:{{ $settings->contact_email }}">
<span class="material-symbols-outlined text-sm">mail</span> {{ $settings->contact_email }}
              </a>
@endif
<span class="font-body-sm text-on-secondary-container">Dedicated Account Support</span>
</div>
</div>
<!-- Wealth Management -->
<div class="bg-surface-container-lowest p-stack-lg shadow-xl hover:shadow-2xl transition-shadow flex flex-col group">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary text-[32px]">account_balance_wallet</span>
<span class="font-caption text-on-surface-variant bg-surface-container px-2 py-1 uppercase tracking-wider">Advisory</span>
</div>
<h3 class="font-headline-md text-on-surface mb-2">Wealth Management</h3>
<p class="font-body-sm text-on-surface-variant mb-6">Strategic portfolio planning and bespoke investment strategies.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-surface-container pt-4">
@if ($contactValue)
<a class="font-label-bold text-on-surface hover:text-primary transition-colors flex items-center gap-2" href="{{ $contactHref }}">
<span class="material-symbols-outlined text-sm">{{ $contactIsEmail ? 'mail' : 'call' }}</span> {{ $contactValue }}
              </a>
@endif
<span class="font-body-sm text-on-secondary-container">Consultation by Appointment</span>
</div>
</div>
<!-- 24/7 Security -->
<div class="bg-on-surface p-stack-lg shadow-xl flex flex-col text-white">
<div class="flex items-center justify-between mb-stack-lg">
<span class="material-symbols-outlined text-primary-fixed-dim text-[32px]">shield_lock</span>
<div class="flex items-center gap-2">
<div class="w-2 h-2 rounded-full bg-error animate-pulse"></div>
<span class="font-caption uppercase tracking-wider text-white/60">Live Response</span>
</div>
</div>
<h3 class="font-headline-md mb-2">24/7 Security Support</h3>
<p class="font-body-sm text-white/70 mb-6">Immediate assistance for fraud reports, lost cards, or account lockouts.</p>
<div class="mt-auto flex flex-col gap-2 border-t border-white/10 pt-4">
@if ($contactValue)
<a class="font-headline-md text-primary-fixed-dim flex items-center gap-2" href="{{ $contactHref }}">
<span class="material-symbols-outlined">{{ $contactIsEmail ? 'mail' : 'support_agent' }}</span> {{ $contactValue }}
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
<!-- Global Offices Section -->
<section class="bg-surface-container-low py-stack-lg overflow-hidden">
<div class="max-w-[1200px] mx-auto px-margin-desktop">
<div class="flex items-end justify-between mb-16">
<div class="flex flex-col">
<h2 class="font-headline-xl text-on-surface">Global Footprint</h2>
<p class="font-body-lg text-on-surface-variant">Our principal operational hubs around the world.</p>
</div>
<div class="hidden md:flex items-center gap-4 text-on-surface-variant font-label-bold uppercase tracking-widest text-[12px]">
<span>London</span>
<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
<span>New York</span>
<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
<span>Singapore</span>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<!-- New York HQ -->
<div class="flex flex-col gap-stack-md group">
<div class="h-64 overflow-hidden relative">
<div class="w-full h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-110" data-alt="Black and white street level view of a grand limestone bank building in New York City's Financial District. The architecture features massive columns and heavy bronze doors. Crisp, high-contrast shadows. The atmosphere is professional, timeless, and prestigious." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAXtT9RyCcOXj_I2UmvHJ_q4uIuWszV6Ha8z72GqQI60VINxH-24pajv5l5Nuv3HHo7iujt3MOnx_78jRvCcSncdE00hy6oQNtmFzvmwBikVkNuU3leZTSfaQV_TfpvuGS1Ba6DzZ9idXEk2D8k0s_17zF4VOAJ2k5dI8_ilKwX4fL7GFhoGiCQ3PIGuYh6vJH1bnmsIMuZ2VsfTf7rvSwILeZ1IrVHekDRokJGViYDWDQdSj1SQ-8G')"></div>
<div class="absolute top-4 left-4 bg-primary text-white font-label-bold uppercase px-3 py-1 text-[10px] tracking-widest">Headquarters</div>
</div>
<div>
<h4 class="font-headline-md text-on-surface">New York City</h4>
@if ($settings->site_address)
<p class="font-body-sm text-on-surface-variant mt-2">{!! nl2br(e($settings->site_address)) !!}</p>
@else
<p class="font-body-sm text-on-surface-variant mt-2">
              450 Park Avenue, Suite 1200<br/>
              New York, NY 10022, USA
            </p>
@endif
<div class="mt-4 flex gap-4">
<span class="font-label-bold text-primary flex items-center gap-1 text-sm"><span class="material-symbols-outlined text-sm">schedule</span> 09:00 – 17:00 EST</span>
</div>
</div>
</div>
<!-- London Hub -->
<div class="flex flex-col gap-stack-md group">
<div class="h-64 overflow-hidden relative">
<div class="w-full h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-110" data-alt="Modern architectural detail of a contemporary steel and glass tower in London's Canary Wharf. Low angle shot looking up at the sky. Sharp reflections, clean lines, corporate luxury aesthetic with a slight blue tint." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBbXax9R_HXzFMFW5CS77RZUpoPSQQcBpSAAHd3WDA4oaHTP5nXJCyp9HMLeYGpK_-XNAtso4zkd5k8ehdr6aJxguo_Kv_Yz2BLIwPWCzFvCb06Ys5ItYBYAeD1ChQPW9xNlwimNcncq7huxpU4ndbijj2WdPMlln6E3dxoezguDEHBpgDzhDYU4rjsLBiXakBcfGwzW6OOJbrDpUxCgYt_8mYs0OzQt9CQSPZ7dEzPgoOz7q1_Gz0D')"></div>
</div>
<div>
<h4 class="font-headline-md text-on-surface">London</h4>
<p class="font-body-sm text-on-surface-variant mt-2">
              10 Upper Bank Street, Canary Wharf<br/>
              London E14 5NP, United Kingdom
            </p>
<div class="mt-4 flex gap-4">
<span class="font-label-bold text-primary flex items-center gap-1 text-sm"><span class="material-symbols-outlined text-sm">schedule</span> 09:00 – 17:00 GMT</span>
</div>
</div>
</div>
<!-- Singapore Hub -->
<div class="flex flex-col gap-stack-md group">
<div class="h-64 overflow-hidden relative">
<div class="w-full h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-110" data-alt="A sophisticated indoor office lobby in Singapore, featuring marble floors, lush interior greenery (biophilic design), and a large digital screen displaying financial charts. Natural sunlight streaming through floor-to-ceiling windows. Ultra-modern corporate elegance." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuA32Yc_B2KZ-mbWsA0XUZLXdpy_2XCSW7oG3zVbA0y9ncmAjGLH_lCHpwNgIbAsD0YjoTF_ai6V2LrlyrlJf4Kc2CyR7nmS1tUb_bGn_9fH6B3iICjy_1ZKKDmMBtXEzW5_aJNVjYYLR7yg6YwfcBfkVepHHoMZdgqZcWrpxw9DuaoMcbxSEnBddJLL2JbhiR66ILV3p-se82cAa-qmUkKMAzmHO7Cr_bNZVZyDKAaugCKxdVDunf9Z')"></div>
</div>
<div>
<h4 class="font-headline-md text-on-surface">Singapore</h4>
<p class="font-body-sm text-on-surface-variant mt-2">
              8 Marina View, Asia Square Tower 1<br/>
              Singapore 018960
            </p>
<div class="mt-4 flex gap-4">
<span class="font-label-bold text-primary flex items-center gap-1 text-sm"><span class="material-symbols-outlined text-sm">schedule</span> 09:00 – 18:00 SGT</span>
</div>
</div>
</div>
</div>
<div class="mt-16 w-full h-[350px] grayscale opacity-80 hover:grayscale-0 transition-all duration-500 shadow-xl" data-location="Manhattan Financial District, New York" style=""></div>
</div>
</section>
<!-- Find a Branch CTA -->
<section class="max-w-[1200px] mx-auto px-margin-desktop py-stack-lg">
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