@php
    $phone = trim((string) ($settings->whatsapp ?? ''));
    $email = trim((string) ($settings->contact_email ?? ''));
    $bankAddress = trim((string) ($settings->address ?? ''));
    $location = trim((string) ($settings->location ?? ''));
    $phoneHref = $phone !== '' ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : null;
    $emailHref = $email !== '' ? 'mailto:' . $email : null;
@endphp
<div class="flex flex-col w-full">
{{-- Hero: no overlapping cards, so the intro copy stays fully visible --}}
<section class="relative min-h-[52vh] md:min-h-[58vh] flex items-end overflow-hidden bg-on-surface pb-16 md:pb-20">
    <div class="absolute inset-0 z-0">
        <div class="bg-cover bg-center w-full h-full opacity-60" style="background-image: url('{{ asset('assets/images/image44.jpg') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-on-surface via-on-surface/85 to-on-surface/40"></div>
    </div>
    <div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-gutter w-full">
        <div class="max-w-2xl">
            <span class="font-label-bold text-primary-fixed-dim tracking-[0.2em] uppercase mb-4 block">Client Relations</span>
            <h1 class="text-hero-medium text-white mb-4">Contact {{ $settings->site_name }}</h1>
            <p class="font-body-lg text-surface-variant max-w-xl">
                Reach our team for personal banking, commercial services, or security support. Use the details below or send a secure message.
            </p>
        </div>
    </div>
</section>

{{-- Primary contact details --}}
<section class="bg-surface-container-low border-b border-surface-container">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter py-10 md:py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10">
            <div class="flex gap-4 min-w-0">
                <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">call</span>
                </div>
                <div class="min-w-0">
                    <h2 class="font-label-bold uppercase tracking-wider text-on-surface-variant text-xs mb-2">Phone</h2>
                    @if ($phone !== '')
                        <a href="{{ $phoneHref }}" class="font-headline-sm text-on-surface hover:text-primary break-all">{{ $phone }}</a>
                        <p class="font-body-sm text-on-surface-variant mt-1">Mon–Fri, 8AM–8PM</p>
                    @else
                        <p class="font-body-md text-on-surface-variant">Phone number not set in admin settings.</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-4 min-w-0">
                <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">mail</span>
                </div>
                <div class="min-w-0">
                    <h2 class="font-label-bold uppercase tracking-wider text-on-surface-variant text-xs mb-2">Email</h2>
                    @if ($email !== '')
                        <a href="{{ $emailHref }}" class="font-headline-sm text-on-surface hover:text-primary break-all">{{ $email }}</a>
                        <p class="font-body-sm text-on-surface-variant mt-1">General inquiries &amp; account support</p>
                    @else
                        <p class="font-body-md text-on-surface-variant">Contact email not set in admin settings.</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-4 min-w-0">
                <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-primary/10 text-primary">
                    <span class="material-symbols-outlined">location_on</span>
                </div>
                <div class="min-w-0">
                    <h2 class="font-label-bold uppercase tracking-wider text-on-surface-variant text-xs mb-2">Bank Address</h2>
                    @if ($bankAddress !== '' || $location !== '')
                        @if ($bankAddress !== '')
                            <p class="font-headline-sm text-on-surface whitespace-pre-line">{{ $bankAddress }}</p>
                        @endif
                        @if ($location !== '')
                            <p class="font-body-sm text-on-surface-variant mt-1">{{ $location }}</p>
                        @endif
                    @else
                        <p class="font-body-md text-on-surface-variant">Bank address not set in admin settings.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Departments + form --}}
<section class="max-w-[1200px] mx-auto px-4 md:px-gutter py-12 md:py-16">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
        <div class="lg:col-span-7 flex flex-col gap-6 min-w-0">
            <div>
                <h2 class="font-headline-lg text-on-surface mb-2">How can we help?</h2>
                <p class="font-body-md text-on-surface-variant">Choose a department. Each channel has a clear purpose so you get the right desk.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <article class="bg-surface-container-lowest border border-surface-container p-6 flex flex-col min-w-0">
                    <span class="material-symbols-outlined text-primary text-[28px] mb-4">person_pin</span>
                    <h3 class="font-headline-md text-on-surface mb-2">Personal Banking</h3>
                    <p class="font-body-sm text-on-surface-variant mb-5 flex-1">Everyday accounts, cards, and personal credit.</p>
                    <div class="border-t border-surface-container pt-4 space-y-2 min-w-0">
                        @if ($phone !== '')
                            <a class="flex items-start gap-2 text-on-surface hover:text-primary min-w-0" href="{{ $phoneHref }}">
                                <span class="material-symbols-outlined text-sm shrink-0 mt-0.5">call</span>
                                <span class="break-all text-sm font-label-bold">{{ $phone }}</span>
                            </a>
                        @endif
                        <p class="font-body-sm text-on-surface-variant">Retail desk · Mon–Fri 8AM–8PM</p>
                    </div>
                </article>

                <article class="bg-surface-container-lowest border border-surface-container p-6 flex flex-col min-w-0">
                    <span class="material-symbols-outlined text-primary text-[28px] mb-4">corporate_fare</span>
                    <h3 class="font-headline-md text-on-surface mb-2">Business Banking</h3>
                    <p class="font-body-sm text-on-surface-variant mb-5 flex-1">Treasury, commercial lending, and merchant services.</p>
                    <div class="border-t border-surface-container pt-4 space-y-2 min-w-0">
                        @if ($email !== '')
                            <a class="flex items-start gap-2 text-on-surface hover:text-primary min-w-0" href="{{ $emailHref }}?subject=Business%20Banking">
                                <span class="material-symbols-outlined text-sm shrink-0 mt-0.5">mail</span>
                                <span class="break-all text-sm font-label-bold">{{ $email }}</span>
                            </a>
                        @endif
                        <p class="font-body-sm text-on-surface-variant">Commercial desk · Dedicated account support</p>
                    </div>
                </article>

                <article class="bg-surface-container-lowest border border-surface-container p-6 flex flex-col min-w-0">
                    <span class="material-symbols-outlined text-primary text-[28px] mb-4">account_balance_wallet</span>
                    <h3 class="font-headline-md text-on-surface mb-2">Wealth Management</h3>
                    <p class="font-body-sm text-on-surface-variant mb-5 flex-1">Portfolio planning and investment advisory.</p>
                    <div class="border-t border-surface-container pt-4 space-y-2 min-w-0">
                        @if ($email !== '')
                            <a class="flex items-start gap-2 text-on-surface hover:text-primary min-w-0" href="{{ $emailHref }}?subject=Wealth%20Management">
                                <span class="material-symbols-outlined text-sm shrink-0 mt-0.5">mail</span>
                                <span class="break-all text-sm font-label-bold">{{ $email }}</span>
                            </a>
                        @endif
                        <p class="font-body-sm text-on-surface-variant">Advisory · By appointment</p>
                    </div>
                </article>

                <article class="bg-on-surface p-6 flex flex-col text-white min-w-0">
                    <div class="flex items-center justify-between mb-4">
                        <span class="material-symbols-outlined text-primary-fixed-dim text-[28px]">shield_lock</span>
                        <span class="flex items-center gap-2 font-caption uppercase tracking-wider text-white/60">
                            <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                            Live
                        </span>
                    </div>
                    <h3 class="font-headline-md mb-2">24/7 Security Support</h3>
                    <p class="font-body-sm text-white/70 mb-5 flex-1">Fraud reports, lost cards, and urgent account lockouts.</p>
                    <div class="border-t border-white/10 pt-4 space-y-2 min-w-0">
                        @if ($phone !== '')
                            <a class="flex items-start gap-2 text-primary-fixed-dim min-w-0" href="{{ $phoneHref }}">
                                <span class="material-symbols-outlined text-sm shrink-0 mt-0.5">support_agent</span>
                                <span class="break-all text-sm font-label-bold">{{ $phone }}</span>
                            </a>
                        @elseif ($email !== '')
                            <a class="flex items-start gap-2 text-primary-fixed-dim min-w-0" href="{{ $emailHref }}?subject=Security%20Support">
                                <span class="material-symbols-outlined text-sm shrink-0 mt-0.5">mail</span>
                                <span class="break-all text-sm font-label-bold">{{ $email }}</span>
                            </a>
                        @endif
                        <p class="font-body-sm text-white/40 uppercase tracking-widest">Always available</p>
                    </div>
                </article>
            </div>

            @if ($bankAddress !== '')
                <div class="bg-surface-container-low border border-surface-container p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-start gap-4 md:gap-8">
                        <div class="shrink-0 w-12 h-12 flex items-center justify-center bg-primary text-on-primary">
                            <span class="material-symbols-outlined">account_balance</span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-headline-md text-on-surface mb-2">Headquarters</h3>
                            <p class="font-body-md text-on-surface whitespace-pre-line">{{ $bankAddress }}</p>
                            @if ($location !== '')
                                <p class="font-body-sm text-on-surface-variant mt-2">{{ $location }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-5">
            <div class="bg-white border border-surface-container p-6 md:p-8 shadow-sm lg:sticky lg:top-24">
                <div class="mb-6">
                    <h2 class="font-headline-lg text-on-surface uppercase tracking-tight">Secure Inquiry</h2>
                    <div class="h-1 w-12 bg-primary mt-2"></div>
                    <p class="font-body-sm text-on-surface-variant mt-4">Do not include account numbers or passwords in this form.</p>
                </div>
                @if (Session::has('success'))
                    <div class="mb-4 p-4 bg-primary/10 border border-primary text-primary font-body-sm">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('message'))
                    <div class="mb-4 p-4 bg-error-container border border-error text-on-error-container font-body-sm">{{ Session::get('message') }}</div>
                @endif
                <form method="POST" action="{{ route('homesendcontact') }}" class="flex flex-col gap-4" id="contactForm">
                    @csrf
                    <div class="flex flex-col gap-2">
                        <label class="font-label-bold text-on-surface uppercase text-[12px]" for="fullname">Full Name</label>
                        <input id="fullname" name="fullname" required class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md" placeholder="Your full name" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-bold text-on-surface uppercase text-[12px]" for="email">Email Address</label>
                        <input id="email" name="email" required class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md" placeholder="name@example.com" type="email"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-bold text-on-surface uppercase text-[12px]" for="phone">Phone Number</label>
                        <input id="phone" name="phone" class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md" placeholder="+1 (555) 000-0000" type="text"/>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-bold text-on-surface uppercase text-[12px]" for="subject">Department</label>
                        <select id="subject" name="subject" class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md appearance-none cursor-pointer">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Personal Banking">Personal Banking</option>
                            <option value="Business Banking">Business Banking</option>
                            <option value="Wealth Management">Wealth Management</option>
                            <option value="Cybersecurity & Fraud">Cybersecurity &amp; Fraud</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-bold text-on-surface uppercase text-[12px]" for="message">Message</label>
                        <textarea id="message" name="message" required class="w-full px-4 py-3 bg-surface-container-low border border-surface-container-highest focus:outline-none focus:border-primary font-body-md resize-none" placeholder="Briefly describe your request..." rows="4"></textarea>
                    </div>
                    <button class="bg-[#FF4800] text-white font-label-bold uppercase py-4 mt-2 hover:bg-primary transition-all flex items-center justify-center gap-3 group" type="submit">
                        Send Secure Message
                        <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
</div>
