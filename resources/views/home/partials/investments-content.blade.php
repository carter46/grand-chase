<div class="flex flex-col w-full">
<!-- Hero Section -->
<section class="relative w-full overflow-hidden">
<div class="relative min-h-[420px] md:min-h-[716px] flex items-center justify-center px-margin-mobile md:px-margin-desktop pt-10">
<!-- Background Image with Scrim -->
<div class="absolute inset-0 z-0">
<div class="w-full h-full bg-cover bg-center" data-alt="A sophisticated, high-end wealth management setting. A modern, minimalist office overlook a sprawling global financial district at twilight. Soft amber lighting hits a mahogany desk with a sleek tablet displaying complex global market growth charts. The atmosphere is quiet, professional, and suggests immense stability and prestige, dominated by deep charcoals, rich wood tones, and the bank's signature orange accents in the digital data visualizations." style="background-image: url('{{ asset('storage/app/public/photos/hero-investments.jpg') }}')">
</div>
<div class="absolute inset-0 bg-bank-charcoal/55 backdrop-brightness-75"></div>
</div>
<!-- Content -->
<div class="relative z-10 max-w-4xl text-center flex flex-col items-center gap-stack-lg">
<div class="flex flex-col gap-4">
<span class="font-label-bold text-label-bold tracking-[0.3em] text-bank-orange uppercase animate-fade-in-up">Wealth Management</span>
<h1 class="text-hero-medium text-white uppercase tracking-tight">
                        Secure Your Legacy
                    </h1>
</div>
<p class="font-body-lg text-body-lg text-white/90 max-w-2xl">
                    Tailored investment strategies engineered for the unique requirements of high-net-worth individuals and global institutions. Precision in asset management, grounded in fiduciary excellence.
                </p>
<div class="flex flex-col sm:flex-row gap-4 mt-4">
<a href="{{ url('register') }}" class="inline-flex items-center justify-center bg-bank-orange text-white px-10 py-4 font-label-bold text-label-bold uppercase transition-all hover:brightness-110 active:scale-[0.98]">
                        Start Investing
                    </a>
<a href="{{ url('contact') }}" class="inline-flex items-center justify-center bg-bank-charcoal text-white px-10 py-4 font-label-bold text-label-bold uppercase transition-all hover:bg-black active:scale-[0.98]">
                        View Portfolio Solutions
                    </a>
</div>
</div>
</div>
</section>
<!-- Section 2: Investment Strategies -->
<section class="bg-white py-12 md:py-24 px-margin-mobile">
<div class="max-w-container-max mx-auto">
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<!-- Card 1 -->
<div class="group flex flex-col p-10 border border-surface-container-highest transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
<span class="material-symbols-outlined text-[48px] text-bank-orange mb-8" style="font-variation-settings: 'FILL' 1;">account_balance</span>
<h3 class="font-headline-md text-headline-md mb-4 uppercase tracking-tight text-on-surface">Private Wealth</h3>
<p class="font-body-md text-body-md text-secondary mb-8">
                        Bespoke portfolio construction and management designed to preserve and grow generational wealth for high-net-worth individuals.
                    </p>
<a href="{{ url('contact') }}" class="mt-auto inline-flex items-center gap-2 text-bank-orange font-label-bold text-label-bold uppercase hover:gap-3 transition-all">
                        Learn More <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
<!-- Card 2 -->
<div class="group flex flex-col p-10 border border-surface-container-highest transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
<span class="material-symbols-outlined text-[48px] text-bank-orange mb-8" style="font-variation-settings: 'FILL' 1;">corporate_fare</span>
<h3 class="font-headline-md text-headline-md mb-4 uppercase tracking-tight text-on-surface">Institutional Services</h3>
<p class="font-body-md text-body-md text-secondary mb-8">
                        Comprehensive asset management and scalable retirement solutions tailored for the complex needs of modern corporations.
                    </p>
<a href="{{ url('contact') }}" class="mt-auto inline-flex items-center gap-2 text-bank-orange font-label-bold text-label-bold uppercase hover:gap-3 transition-all">
                        Learn More <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
<!-- Card 3 -->
<div class="group flex flex-col p-10 border border-surface-container-highest transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
<span class="material-symbols-outlined text-[48px] text-bank-orange mb-8" style="font-variation-settings: 'FILL' 1;">language</span>
<h3 class="font-headline-md text-headline-md mb-4 uppercase tracking-tight text-on-surface">Global Markets</h3>
<p class="font-body-md text-body-md text-secondary mb-8">
                        Direct institutional access to international equities, fixed-income markets, and alternative commodities on a global scale.
                    </p>
<a href="{{ url('contact') }}" class="mt-auto inline-flex items-center gap-2 text-bank-orange font-label-bold text-label-bold uppercase hover:gap-3 transition-all">
                        Learn More <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
</div>
</div>
</div>
</section>
<!-- Section 3: The {{ $settings->site_name }} Advantage -->
<section class="bg-surface-container-low py-12 md:py-24">
<div class="max-w-container-max mx-auto px-margin-mobile">
<div class="flex flex-col lg:flex-row gap-20 items-center">
<!-- Image Side -->
<div class="w-full lg:w-1/2 relative">
<div class="absolute -top-6 -left-6 w-24 h-24 bg-bank-orange/10 -z-10"></div>
<div class="relative aspect-[4/5] w-full overflow-hidden">
<img class="w-full h-full object-cover" data-alt="A professional male senior financial advisor in a charcoal grey tailored suit, speaking with authority and confidence. He is gesturing towards a transparent glass screen displaying real-time stock market fluctuations and golden growth curves. The lighting is crisp and professional, emphasizing a clean, institutional environment with high-contrast shadows and sharp focus on the advisor's calm, trustworthy expression." src="{{ asset('storage/app/public/photos/investments-advisor.jpg') }}"/>
</div>
<!-- Stats Overlay -->
<div class="absolute bottom-10 -right-6 bg-bank-charcoal p-8 text-white max-w-[240px] shadow-2xl">
<p class="text-headline-lg font-headline-lg text-bank-orange mb-1">24/7</p>
<p class="text-caption font-caption uppercase tracking-widest text-white/60">Global Market Monitoring</p>
</div>
</div>
<!-- Content Side -->
<div class="w-full lg:w-1/2 flex flex-col gap-stack-lg">
<div class="flex flex-col gap-4">
<span class="font-label-bold text-label-bold text-bank-orange uppercase tracking-wider">The {{ $settings->site_name }} Advantage</span>
<h2 class="font-headline-xl text-headline-xl text-on-surface uppercase tracking-tight">Why Partner with {{ $settings->site_name }} for Your Investments?</h2>
</div>
<div class="flex flex-col gap-8 mt-4">
<!-- Row 1 -->
<div class="flex gap-6 items-start">
<span class="material-symbols-outlined text-bank-orange text-[32px]">verified_user</span>
<div class="flex flex-col gap-1">
<h4 class="font-label-bold text-label-bold uppercase text-on-surface">Fiduciary Excellence</h4>
<p class="font-body-md text-body-md text-secondary">Your interests always come first. Our fee-only model ensures total alignment with your financial objectives.</p>
</div>
</div>
<!-- Row 2 -->
<div class="flex gap-6 items-start">
<span class="material-symbols-outlined text-bank-orange text-[32px]">analytics</span>
<div class="flex flex-col gap-1">
<h4 class="font-label-bold text-label-bold uppercase text-on-surface">Global Research</h4>
<p class="font-body-md text-body-md text-secondary">Proprietary data-driven insights sourced directly from our international analysis desks across four continents.</p>
</div>
</div>
<!-- Row 3 -->
<div class="flex gap-6 items-start">
<span class="material-symbols-outlined text-bank-orange text-[32px]">shield</span>
<div class="flex flex-col gap-1">
<h4 class="font-label-bold text-label-bold uppercase text-on-surface">Risk Mitigation</h4>
<p class="font-body-md text-body-md text-secondary">Sophisticated hedging strategies and algorithmic modeling to protect your capital in volatile market cycles.</p>
</div>
</div>
<!-- Row 4 -->
<div class="flex gap-6 items-start">
<span class="material-symbols-outlined text-bank-orange text-[32px]">support_agent</span>
<div class="flex flex-col gap-1">
<h4 class="font-label-bold text-label-bold uppercase text-on-surface">Dedicated Portfolio Managers</h4>
<p class="font-body-md text-body-md text-secondary">Direct access to our senior experts. No call centers, just personalized elite-tier financial counsel.</p>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Section 4: Final CTA -->
<section class="bg-bank-charcoal py-12 md:py-24">
<div class="max-w-container-max mx-auto px-margin-mobile text-center flex flex-col items-center">
<h2 class="font-headline-xl text-headline-xl text-white uppercase tracking-tight mb-6">Ready to Plan Your Financial Future?</h2>
<p class="font-body-lg text-body-lg text-white/70 max-w-2xl mb-12">
                Connect with a senior advisor today for a comprehensive evaluation of your current portfolio and a roadmap toward achieving your long-term legacy goals.
            </p>
<div class="flex flex-col sm:flex-row gap-6 w-full sm:w-auto">
<a href="{{ url('contact') }}" class="inline-flex items-center justify-center bg-bank-orange text-white px-12 py-5 font-label-bold text-label-bold uppercase transition-all hover:brightness-110">
                    Schedule a Consultation
                </a>
<a href="{{ url('investments') }}" class="inline-flex items-center justify-center border border-white/30 text-white px-12 py-5 font-label-bold text-label-bold uppercase transition-all hover:bg-white hover:text-bank-charcoal">
                    Explore Market Insights
                </a>
</div>
</div>
</section>
</div>
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }
</style>
<script>
    // Smooth reveal on scroll for strategy cards
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100');
                entry.target.classList.remove('opacity-0', 'translate-y-8');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.group.flex.flex-col.p-10').forEach(card => {
        card.classList.add('opacity-0', 'translate-y-8', 'transition-all', 'duration-700');
        observer.observe(card);
    });
</script>