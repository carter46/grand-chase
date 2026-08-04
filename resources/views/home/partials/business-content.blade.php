@php
    $contactValue = $settings->whatsapp ?? $settings->phone ?? $settings->contact_email;
    $contactIsEmail = $contactValue && filter_var($contactValue, FILTER_VALIDATE_EMAIL);
    $contactHref = $contactValue ? ($contactIsEmail ? 'mailto:' . $contactValue : 'tel:' . preg_replace('/[^\d+]/', '', $contactValue)) : '#';
@endphp
<div class="flex flex-col w-full">
<!-- Hero Section -->
<section class="relative w-full h-[600px] flex items-center overflow-hidden">
<div class="absolute inset-0 z-0">
<div class="bg-cover bg-center w-full h-full transform scale-105" data-alt="A cinematic, high-angle view of a luxury corporate boardroom at twilight. Through floor-to-ceiling windows, a sprawling metropolis glows with office lights. The interior features polished mahogany walls, a long glass conference table reflecting the city skyline, and ergonomic leather chairs. The atmosphere is quiet, powerful, and professional, utilizing deep blues, warm wood tones, and sharp architectural lines." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDBU1uaJPRNBAZ413okURPE0oCXbds2BEbu5cePY6pjbSLJgn_QjHyvtMWxRn5y-yIrgur8tJzKMZH8N_9_XifvQUmXjIXfiXtikSDLAQjz829Ctmzn_tmLFtLYTNPE5Rjc72muGhwr-sNHRqk0t-Gp2qoIMweI_ORXfGnZ6KADYF2M95IVLL9lni3OvJm4dvGl4D3mWGw9Ww39gwmKG0xl92pgvf5veRMCoNJQpLMq_I8Tqt14J5jb')"></div>
<div class="absolute inset-0 bg-inverse-surface/60 backdrop-brightness-75"></div>
</div>
<div class="relative z-10 max-w-[1200px] mx-auto px-gutter w-full">
<div class="max-w-2xl">
<span class="inline-block bg-primary text-on-primary px-3 py-1 font-label-bold text-xs uppercase tracking-widest mb-stack-md">Corporate & Institutional</span>
<h1 class="font-headline-xl text-on-primary mb-stack-md leading-tight">
          Empowering Your Enterprise with Institutional Strength
        </h1>
<p class="font-body-lg text-on-primary opacity-90 mb-stack-lg leading-relaxed">
          Tailored commercial banking solutions, treasury management, and global trade finance for modern corporations.
        </p>
<div class="flex flex-wrap gap-stack-md">
<a href="{{ url('contact') }}" class="inline-flex items-center bg-primary hover:bg-primary-container text-on-primary px-8 py-4 font-label-bold text-sm tracking-widest transition-all shadow-xl">
            CONNECT WITH A RELATIONSHIP MANAGER
          </a>
<a href="{{ url('business') }}" class="inline-flex items-center border border-on-primary text-on-primary hover:bg-on-primary hover:text-inverse-surface px-8 py-4 font-label-bold text-sm tracking-widest transition-all">
            VIEW CORPORATE SERVICES
          </a>
</div>
</div>
</div>
</section>
<!-- Section 1: Core Business Solutions -->
<section class="py-[100px] bg-surface-container-lowest">
<div class="max-w-[1200px] mx-auto px-gutter">
<div class="flex items-end justify-between mb-[64px]">
<div class="max-w-xl">
<p class="text-primary font-label-bold uppercase tracking-widest mb-stack-sm text-sm">Strategic Services</p>
<h2 class="font-headline-lg text-on-surface">Solutions for Every Scale</h2>
</div>
<div class="hidden md:block h-[1px] flex-grow bg-outline-variant/30 ml-stack-lg mb-4"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-stack-lg">
<!-- Commercial Lending -->
<div class="group bg-surface-container-lowest border border-outline-variant/50 p-stack-lg hover:shadow-xl transition-all duration-300 flex flex-col h-full">
<div class="w-12 h-12 bg-primary/10 flex items-center justify-center mb-stack-lg group-hover:bg-primary transition-colors duration-300">
<span class="material-symbols-outlined text-primary group-hover:text-on-primary">account_balance_wallet</span>
</div>
<h3 class="font-headline-md text-on-surface mb-stack-md">Commercial Lending</h3>
<p class="font-body-md text-on-surface-variant mb-stack-lg flex-grow">
            Flexible capital solutions for expansion, equipment, and operations tailored to your industry's unique cycle.
          </p>
<a class="font-label-bold text-primary flex items-center gap-2 group/link uppercase tracking-wider text-sm" href="{{ url('contact') }}">
            Learn More 
            <span class="material-symbols-outlined text-sm group-hover/link:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
<!-- Treasury Management -->
<div class="group bg-surface-container-lowest border border-outline-variant/50 p-stack-lg hover:shadow-xl transition-all duration-300 flex flex-col h-full">
<div class="w-12 h-12 bg-primary/10 flex items-center justify-center mb-stack-lg group-hover:bg-primary transition-colors duration-300">
<span class="material-symbols-outlined text-primary group-hover:text-on-primary">monitoring</span>
</div>
<h3 class="font-headline-md text-on-surface mb-stack-md">Treasury Management</h3>
<p class="font-body-md text-on-surface-variant mb-stack-lg flex-grow">
            Optimize your cash flow with advanced liquidity, automated payment solutions, and real-time monitoring tools.
          </p>
<a class="font-label-bold text-primary flex items-center gap-2 group/link uppercase tracking-wider text-sm" href="{{ url('contact') }}">
            Learn More 
            <span class="material-symbols-outlined text-sm group-hover/link:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
<!-- Merchant Services -->
<div class="group bg-surface-container-lowest border border-outline-variant/50 p-stack-lg hover:shadow-xl transition-all duration-300 flex flex-col h-full">
<div class="w-12 h-12 bg-primary/10 flex items-center justify-center mb-stack-lg group-hover:bg-primary transition-colors duration-300">
<span class="material-symbols-outlined text-primary group-hover:text-on-primary">language</span>
</div>
<h3 class="font-headline-md text-on-surface mb-stack-md">Merchant Services</h3>
<p class="font-body-md text-on-surface-variant mb-stack-lg flex-grow">
            Secure, efficient payment processing for global commerce, supporting multi-currency and cross-border transactions.
          </p>
<a class="font-label-bold text-primary flex items-center gap-2 group/link uppercase tracking-wider text-sm" href="{{ url('contact') }}">
            Learn More 
            <span class="material-symbols-outlined text-sm group-hover/link:translate-x-1 transition-transform">arrow_forward</span>
</a>
</div>
</div>
</div>
</section>
<!-- Section 2: Institutional Trust (Split) -->
<section class="grid grid-cols-1 lg:grid-cols-2 min-h-[600px] bg-surface">
<div class="relative min-h-[400px] lg:min-h-0">
<div class="absolute inset-0 bg-cover bg-center" data-alt="A professional relationship manager in a crisp business suit presenting high-level financial data on a tablet to a group of executives. The background is a brightly lit, contemporary office with glass partitions and mid-century modern furniture. Soft natural light, high-end corporate lifestyle photography, clean and professional aesthetic." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBJKlo8M1MiMMFcidR1ZKi7BjnIkhyndZ_TVB6mXWqnIyXPd4AM4Vq8tIVp4gOIT9A5y3ERMMy6RL5iE6o-3Ciuzwc_tYOsSqXQCBj-5rm5-1zXTIvNFO40vL1eh14CiLCMUbs8ulVqEmg1qhZoGdHLjDB9oPhHI3BJLMVMgY7LIzBgyDn0w0MVMCTACummjtseOxDhbLfexPjWosoT7uTUjZo8D-KnbGCqtuUVTwl1HoBBAxbYthat')"></div>
<!-- Decorative Overlay -->
<div class="absolute bottom-0 right-0 bg-primary w-24 h-24 hidden lg:block -mr-12 -mb-12 z-20"></div>
</div>
<div class="flex items-center justify-center py-24 px-gutter lg:px-24">
<div class="max-w-lg">
<h2 class="font-headline-lg text-on-surface mb-stack-md">Strategic Partnership, Proven Reliability</h2>
<p class="font-body-lg text-on-surface-variant mb-stack-lg">
          At {{ $settings->site_name }}, we don't just provide accounts; we provide the architecture for your financial future. Our advisors are partners in your long-term growth.
        </p>
<ul class="space-y-stack-md">
<li class="flex items-start gap-stack-md">
<span class="material-symbols-outlined text-primary mt-1">check_circle</span>
<div>
<p class="font-label-bold text-on-surface uppercase text-sm tracking-wide">Dedicated Relationship Management</p>
<p class="font-body-sm text-on-surface-variant">A single point of contact who understands your business goals and industry nuances.</p>
</div>
</li>
<li class="flex items-start gap-stack-md">
<span class="material-symbols-outlined text-primary mt-1">check_circle</span>
<div>
<p class="font-label-bold text-on-surface uppercase text-sm tracking-wide">Advanced Fraud Protection & Security</p>
<p class="font-body-sm text-on-surface-variant">Multi-layered defensive protocols and 24/7 institutional-grade security monitoring.</p>
</div>
</li>
<li class="flex items-start gap-stack-md">
<span class="material-symbols-outlined text-primary mt-1">check_circle</span>
<div>
<p class="font-label-bold text-on-surface uppercase text-sm tracking-wide">Global Market Expertise</p>
<p class="font-body-sm text-on-surface-variant">Insightful guidance on international trade, forex risks, and global economic trends.</p>
</div>
</li>
<li class="flex items-start gap-stack-md">
<span class="material-symbols-outlined text-primary mt-1">check_circle</span>
<div>
<p class="font-label-bold text-on-surface uppercase text-sm tracking-wide">Seamless Digital Integration</p>
<p class="font-body-sm text-on-surface-variant">Robust API connectivity and enterprise-level dashboard for complete visibility.</p>
</div>
</li>
</ul>
</div>
</div>
</section>
<!-- Section 3: Closing CTA -->
<section class="relative bg-inverse-surface py-[120px] overflow-hidden">
<!-- Abstract Background Element -->
<div class="absolute top-0 right-0 w-[400px] h-[400px] bg-primary/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
<div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-surface-variant/5 rounded-full blur-[100px] -ml-32 -mb-32"></div>
<div class="max-w-[1200px] mx-auto px-gutter relative z-10 text-center">
<div class="max-w-3xl mx-auto">
<h2 class="font-headline-xl text-on-primary mb-stack-md">Ready to Scale Your Business?</h2>
<p class="font-body-lg text-on-primary opacity-80 mb-[48px]">
          Join the elite institutions that trust {{ $settings->site_name }} for their strategic financial needs and enterprise-grade banking.
        </p>
<div class="flex flex-col items-center gap-stack-lg">
<a href="{{ url('register') }}" class="inline-flex items-center bg-primary hover:bg-primary-container text-on-primary px-12 py-5 font-label-bold text-sm tracking-[0.2em] transition-all shadow-2xl group">
            OPEN A BUSINESS ACCOUNT
          </a>
<div class="flex items-center gap-stack-md text-on-primary/60 font-label-md text-sm">
<span class="material-symbols-outlined text-sm">support_agent</span>
            Talk to a Specialist:
@if ($contactValue)
<a href="{{ $contactHref }}" class="text-on-primary font-bold hover:underline">{{ $contactValue }}</a>
@endif
</div>
</div>
</div>
</div>
</section>
<!-- Trust Bar (Data Visualization Element) -->
<section class="py-stack-lg border-t border-outline-variant/10 bg-surface-container-low">
<div class="max-w-[1200px] mx-auto px-gutter flex flex-wrap justify-center md:justify-between items-center gap-stack-lg opacity-60 grayscale hover:grayscale-0 transition-all duration-700">
<div class="flex flex-col items-center">
<span class="font-headline-md text-on-surface mb-0">94%</span>
<span class="font-caption uppercase tracking-widest text-on-surface-variant">Retention Rate</span>
</div>
<div class="flex flex-col items-center">
<span class="font-headline-md text-on-surface mb-0">$12B+</span>
<span class="font-caption uppercase tracking-widest text-on-surface-variant">Assets Managed</span>
</div>
<div class="flex flex-col items-center">
<span class="font-headline-md text-on-surface mb-0">50+</span>
<span class="font-caption uppercase tracking-widest text-on-surface-variant">Global Markets</span>
</div>
<div class="flex flex-col items-center">
<span class="font-headline-md text-on-surface mb-0">A+</span>
<span class="font-caption uppercase tracking-widest text-on-surface-variant">Credit Rating</span>
</div>
</div>
</section>
</div>
<script>
  // Simple intersection observer for subtle fade-in effects
  const observerOptions = {
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('opacity-100', 'translate-y-0');
        entry.target.classList.remove('opacity-0', 'translate-y-8');
      }
    });
  }, observerOptions);

  document.querySelectorAll('.group').forEach(el => {
    el.classList.add('opacity-0', 'translate-y-8', 'transition-all', 'duration-700');
    observer.observe(el);
  });
</script>