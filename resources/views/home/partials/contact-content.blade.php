@php
    $phone = trim((string) ($settings->whatsapp ?? ''));
    $email = trim((string) ($settings->contact_email ?? ''));
    $bankAddress = trim((string) ($settings->address ?? ''));
    $location = trim((string) ($settings->location ?? ''));
    $phoneHref = $phone !== '' ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : null;
    $emailHref = $email !== '' ? 'mailto:' . $email : null;
@endphp

<div class="flex flex-col w-full">
    {{-- Compact page header --}}
    <section class="bg-on-surface border-b border-white/10">
        <div class="max-w-[1100px] mx-auto px-4 md:px-gutter py-12 md:py-16">
            <h1 class="text-hero-small text-white">Contact Us</h1>
            <p class="font-body-md text-white/70 mt-3 max-w-xl">
                Phone, email, and our bank address are listed below. You can also send a message using the form.
            </p>
        </div>
    </section>

    {{-- Contact details + form --}}
    <section class="bg-surface">
        <div class="max-w-[1100px] mx-auto px-4 md:px-gutter py-14 md:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 lg:gap-20">

                {{-- Details --}}
                <div class="flex flex-col gap-10">
                    <div>
                        <h2 class="font-headline-md text-on-surface mb-2">Get in touch</h2>
                        <p class="font-body-md text-on-surface-variant">
                            Our support team will respond during business hours.
                        </p>
                    </div>

                    <div class="flex flex-col divide-y divide-surface-container">
                        <div class="py-6 first:pt-0">
                            <p class="font-label-bold text-xs uppercase tracking-widest text-on-surface-variant mb-3">Phone</p>
                            @if ($phone !== '')
                                <a href="{{ $phoneHref }}" class="text-2xl md:text-3xl font-headline-md text-on-surface hover:text-primary break-all">
                                    {{ $phone }}
                                </a>
                            @else
                                <p class="text-lg text-on-surface-variant">Not available</p>
                            @endif
                        </div>

                        <div class="py-6">
                            <p class="font-label-bold text-xs uppercase tracking-widest text-on-surface-variant mb-3">Email</p>
                            @if ($email !== '')
                                <a href="{{ $emailHref }}" class="text-xl md:text-2xl font-headline-md text-on-surface hover:text-primary break-all">
                                    {{ $email }}
                                </a>
                            @else
                                <p class="text-lg text-on-surface-variant">Not available</p>
                            @endif
                        </div>

                        <div class="py-6">
                            <p class="font-label-bold text-xs uppercase tracking-widest text-on-surface-variant mb-3">Bank Address</p>
                            @if ($bankAddress !== '' || $location !== '')
                                @if ($bankAddress !== '')
                                    <p class="text-lg md:text-xl font-headline-md text-on-surface whitespace-pre-line leading-relaxed">{{ $bankAddress }}</p>
                                @endif
                                @if ($location !== '')
                                    <p class="font-body-md text-on-surface-variant mt-2">{{ $location }}</p>
                                @endif
                            @else
                                <p class="text-lg text-on-surface-variant">Not available</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="bg-surface-container-low border border-surface-container p-6 md:p-8">
                    <h2 class="font-headline-md text-on-surface mb-1">Send a message</h2>
                    <p class="font-body-sm text-on-surface-variant mb-6">Do not include account numbers or passwords.</p>

                    @if (Session::has('success'))
                        <div class="mb-4 p-3 bg-primary/10 border border-primary text-primary font-body-sm">{{ Session::get('success') }}</div>
                    @endif
                    @if (Session::has('message'))
                        <div class="mb-4 p-3 bg-error-container border border-error text-on-error-container font-body-sm">{{ Session::get('message') }}</div>
                    @endif

                    <form method="POST" action="{{ route('homesendcontact') }}" class="flex flex-col gap-4" id="contactForm">
                        @csrf
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-bold text-on-surface uppercase text-[11px]" for="fullname">Full Name</label>
                            <input id="fullname" name="fullname" required type="text" placeholder="Your full name"
                                class="w-full px-4 py-3 bg-white border border-surface-container-highest focus:outline-none focus:border-primary font-body-md"/>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-bold text-on-surface uppercase text-[11px]" for="email">Email</label>
                            <input id="email" name="email" required type="email" placeholder="name@example.com"
                                class="w-full px-4 py-3 bg-white border border-surface-container-highest focus:outline-none focus:border-primary font-body-md"/>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-bold text-on-surface uppercase text-[11px]" for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" placeholder="+1 (555) 000-0000"
                                class="w-full px-4 py-3 bg-white border border-surface-container-highest focus:outline-none focus:border-primary font-body-md"/>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-bold text-on-surface uppercase text-[11px]" for="subject">Subject</label>
                            <input id="subject" name="subject" type="text" value="General Inquiry"
                                class="w-full px-4 py-3 bg-white border border-surface-container-highest focus:outline-none focus:border-primary font-body-md"/>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-bold text-on-surface uppercase text-[11px]" for="message">Message</label>
                            <textarea id="message" name="message" required rows="4" placeholder="How can we help?"
                                class="w-full px-4 py-3 bg-white border border-surface-container-highest focus:outline-none focus:border-primary font-body-md resize-none"></textarea>
                        </div>
                        <button type="submit"
                            class="mt-2 w-full bg-primary text-on-primary font-label-bold uppercase py-3.5 hover:opacity-90 transition-opacity">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
