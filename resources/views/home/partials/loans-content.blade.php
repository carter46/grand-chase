<div class="flex flex-col w-full">
<!-- Hero Section -->
<section class="relative w-full overflow-hidden bg-inverse-surface min-h-[calc(100vh-104px)] md:min-h-[calc(100vh-131px)] flex items-center">
<div class="absolute inset-0 z-0">
<div class="w-full h-full bg-cover bg-center opacity-55" data-alt="A high-end cinematic photograph of a professional business meeting in a modern, glass-walled boardroom overlooking a city skyline at dusk. Two people are shaking hands across a polished mahogany desk, symbolizing a partnership agreement. The lighting is warm and dramatic, highlighting the high-quality textures of tailored suits and architectural details. The color palette features deep charcoal, warm wood tones, and subtle orange accents in the ambient lighting." style="background-image: url('{{ asset('assets/images/hero-loans.jpg') }}')"></div>
<div class="absolute inset-0 bg-gradient-to-r from-inverse-surface via-inverse-surface/80 to-transparent"></div>
</div>
<div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-gutter pt-10 pb-stack-lg md:py-stack-lg">
<div class="max-w-2xl">
<span class="inline-block font-label-bold text-primary uppercase tracking-[0.2em] mb-stack-md">Financial Empowerment</span>
<h1 class="text-hero-medium text-on-primary mb-stack-md">Flexible Lending Solutions for Your Future</h1>
<p class="font-body-lg text-on-primary/80 mb-10 leading-relaxed">
                    Tailored financing options designed to help you achieve your personal and professional goals with confidence. We provide the capital; you provide the vision.
                </p>
<div class="flex flex-wrap gap-stack-md">
<a href="{{ url('register') }}" class="inline-flex items-center bg-primary hover:bg-primary-container text-on-primary px-10 py-4 font-label-bold text-sm tracking-widest transition-all">APPLY NOW</a>
<a href="{{ url('contact') }}" class="inline-flex items-center border border-on-primary text-on-primary hover:bg-on-primary hover:text-inverse-surface px-10 py-4 font-label-bold text-sm tracking-widest transition-all">SPEAK WITH A SPECIALIST</a>
</div>
</div>
</div>
</section>
<!-- Loan Categories Section -->
<section class="py-12 md:py-24 bg-surface">
<div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
<div class="grid grid-cols-1 md:grid-cols-3 gap-0 border border-outline-variant/30">
<!-- Personal Loans -->
<div class="bg-surface-container-lowest p-10 flex flex-col border-r border-outline-variant/30 group hover:bg-surface-container-low transition-colors">
<div class="w-12 h-12 bg-primary/10 flex items-center justify-center mb-stack-lg text-primary">
<span class="material-symbols-outlined text-3xl">person</span>
</div>
<h3 class="font-headline-md text-on-surface mb-stack-md">Personal Loans</h3>
<p class="font-body-md text-on-surface-variant mb-stack-lg flex-grow leading-relaxed">
                        Fixed rates, flexible terms, and fast approval for life's milestones. From debt consolidation to major purchases.
                    </p>
<a class="font-label-bold text-primary flex items-center gap-2 group-hover:gap-4 transition-all" href="{{ url('contact') }}">
                        LEARN MORE <span class="material-symbols-outlined">arrow_forward</span>
</a>
</div>
<!-- Business Loans -->
<div class="bg-surface-container-lowest p-10 flex flex-col border-r border-outline-variant/30 group hover:bg-surface-container-low transition-colors">
<div class="w-12 h-12 bg-primary/10 flex items-center justify-center mb-stack-lg text-primary">
<span class="material-symbols-outlined text-3xl">corporate_fare</span>
</div>
<h3 class="font-headline-md text-on-surface mb-stack-md">Business & Commercial</h3>
<p class="font-body-md text-on-surface-variant mb-stack-lg flex-grow leading-relaxed">
                        Fuel your enterprise with strategic capital, equipment financing, and lines of credit designed for scaling.
                    </p>
<a class="font-label-bold text-primary flex items-center gap-2 group-hover:gap-4 transition-all" href="{{ url('contact') }}">
                        LEARN MORE <span class="material-symbols-outlined">arrow_forward</span>
</a>
</div>
<!-- Mortgage -->
<div class="bg-surface-container-lowest p-10 flex flex-col group hover:bg-surface-container-low transition-colors">
<div class="w-12 h-12 bg-primary/10 flex items-center justify-center mb-stack-lg text-primary">
<span class="material-symbols-outlined text-3xl">home</span>
</div>
<h3 class="font-headline-md text-on-surface mb-stack-md">Mortgage & Equity</h3>
<p class="font-body-md text-on-surface-variant mb-stack-lg flex-grow leading-relaxed">
                        Competitive rates and expert guidance for your home buying or refinancing journey with transparent closings.
                    </p>
<a class="font-label-bold text-primary flex items-center gap-2 group-hover:gap-4 transition-all" href="{{ url('contact') }}">
                        LEARN MORE <span class="material-symbols-outlined">arrow_forward</span>
</a>
</div>
</div>
</div>
</section>
<!-- Lending Excellence Section -->
<section class="py-12 md:py-24 bg-surface-container-lowest overflow-hidden">
<div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
<div class="flex flex-col lg:flex-row items-stretch gap-0">
<div class="lg:w-1/2 relative min-h-[400px]">
<div class="absolute inset-0 bg-cover bg-center" data-alt="An architectural photograph of a minimalist, contemporary high-rise building with sharp geometric lines and vast glass panels. The sun reflects off the glass, creating a high-contrast interplay of light and shadow. The structure represents stability, innovation, and long-term investment. Clear blue sky in the background, professional and clean aesthetic." style="background-image: url('{{ asset('assets/images/loans-architecture.jpg') }}')"></div>
</div>
<div class="lg:w-1/2 bg-inverse-surface p-16 text-on-primary">
<h2 class="font-headline-lg mb-stack-lg">Why Borrow with {{ $settings->site_name }}?</h2>
<ul class="space-y-stack-lg">
<li class="flex items-start gap-stack-md group">
<span class="material-symbols-outlined text-primary text-2xl mt-1">check_circle</span>
<div>
<h4 class="font-label-bold uppercase tracking-wider mb-1">Competitive Interest Rates</h4>
<p class="font-body-md text-on-primary/60">Leveraging our institutional scale to provide you with market-leading APRs.</p>
</div>
</li>
<li class="flex items-start gap-stack-md">
<span class="material-symbols-outlined text-primary text-2xl mt-1">check_circle</span>
<div>
<h4 class="font-label-bold uppercase tracking-wider mb-1">Transparent Terms & No Hidden Fees</h4>
<p class="font-body-md text-on-primary/60">Total clarity on every document. We believe in building trust through honesty.</p>
</div>
</li>
<li class="flex items-start gap-stack-md">
<span class="material-symbols-outlined text-primary text-2xl mt-1">check_circle</span>
<div>
<h4 class="font-label-bold uppercase tracking-wider mb-1">Dedicated Lending Advisors</h4>
<p class="font-body-md text-on-primary/60">Direct access to experts who understand your unique financial landscape.</p>
</div>
</li>
<li class="flex items-start gap-stack-md">
<span class="material-symbols-outlined text-primary text-2xl mt-1">check_circle</span>
<div>
<h4 class="font-label-bold uppercase tracking-wider mb-1">Streamlined Digital Application</h4>
<p class="font-body-md text-on-primary/60">State-of-the-art secure portal for faster decisions and seamless document uploads.</p>
</div>
</li>
</ul>
</div>
</div>
</div>
</section>
<!-- Lending Calculator Widget -->
<section class="py-12 md:py-24 bg-surface">
<div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
<div class="bg-surface-container-lowest border border-outline-variant/30 p-12">
<div class="text-center mb-12">
<h2 class="font-headline-lg text-on-surface mb-stack-sm">Loan Repayment Estimator</h2>
<p class="font-body-md text-on-surface-variant">Plan your financial future with our professional calculator tool.</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
<div class="space-y-10">
<div class="space-y-4">
<div class="flex justify-between items-end">
<label class="font-label-bold text-on-surface uppercase tracking-wider">Loan Amount</label>
<span class="font-headline-md text-primary" id="amount-label">$50,000</span>
</div>
<input class="w-full h-2 bg-surface-container rounded-none appearance-none cursor-pointer accent-primary" id="amount-slider" max="500000" min="5000" step="5000" type="range" value="50000"/>
<div class="flex justify-between text-caption text-on-surface-variant font-label-md">
<span>$5,000</span>
<span>$500,000</span>
</div>
</div>
<div class="space-y-4">
<div class="flex justify-between items-end">
<label class="font-label-bold text-on-surface uppercase tracking-wider">Loan Term (Months)</label>
<span class="font-headline-md text-primary" id="term-label">36</span>
</div>
<input class="w-full h-2 bg-surface-container rounded-none appearance-none cursor-pointer accent-primary" id="term-slider" max="120" min="12" step="12" type="range" value="36"/>
<div class="flex justify-between text-caption text-on-surface-variant font-label-md">
<span>12 MO</span>
<span>120 MO</span>
</div>
</div>
<div class="space-y-4">
<label class="font-label-bold text-on-surface uppercase tracking-wider">Estimated Interest Rate (Fixed)</label>
<div class="bg-surface-container-low p-4 flex items-center justify-between border border-outline-variant/20">
<input class="font-headline-md text-on-surface bg-transparent w-24 outline-none" id="rate-input" max="20" min="1" step="0.25" type="number" value="5.25"/>
<span class="font-headline-md text-on-surface">%</span>
<span class="text-caption text-on-surface-variant italic">Based on prime market rates</span>
</div>
</div>
</div>
<div class="bg-inverse-surface p-10 flex flex-col justify-center items-center text-center">
<span class="font-label-bold text-on-primary/60 uppercase tracking-[0.2em] mb-4">Estimated Monthly Payment</span>
<div class="text-[64px] font-bold text-on-primary leading-none mb-2" id="monthly-payment">$1,504.22</div>
<p class="text-caption text-on-primary/50 italic mb-8">Estimate only — actual terms may vary</p>
<div class="w-full h-[1px] bg-on-primary/10 mb-8"></div>
<div class="grid grid-cols-2 w-full gap-stack-lg">
<div class="text-left">
<p class="text-caption text-on-primary/60 uppercase font-label-bold">Total Interest</p>
<p class="text-body-lg text-on-primary font-bold" id="total-interest">$4,151.92</p>
</div>
<div class="text-right">
<p class="text-caption text-on-primary/60 uppercase font-label-bold">Total Repayment</p>
<p class="text-body-lg text-on-primary font-bold" id="total-payment">$54,151.92</p>
</div>
</div>
<a href="{{ url('register') }}" class="inline-flex items-center justify-center w-full mt-12 bg-primary text-on-primary py-4 font-label-bold uppercase tracking-widest hover:bg-primary-container transition-colors">Apply for this Amount</a>
</div>
</div>
</div>
</div>
</section>
<!-- CTA Section -->
<section class="py-12 md:py-24 bg-inverse-surface border-t border-outline-variant/10">
<div class="max-w-[1200px] mx-auto px-4 md:px-gutter text-center">
<h2 class="font-headline-xl text-on-primary mb-stack-md">Ready to Take the Next Step?</h2>
<p class="font-body-lg text-on-primary/60 mb-12 max-w-2xl mx-auto">Our specialists are standing by to help you structure the ideal lending solution for your personal or business needs.</p>
<div class="flex flex-wrap justify-center gap-stack-lg">
<a href="{{ url('register') }}" class="inline-flex items-center bg-primary hover:bg-primary-container text-on-primary px-12 py-5 font-label-bold text-sm tracking-widest transition-all">GET STARTED</a>
<a href="{{ url('contact') }}" class="inline-flex items-center border border-outline text-on-primary hover:bg-on-primary hover:text-inverse-surface px-12 py-5 font-label-bold text-sm tracking-widest transition-all">VIEW ALL RATES</a>
</div>
</div>
</section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const amountSlider = document.getElementById('amount-slider');
        const termSlider = document.getElementById('term-slider');
        const rateInput = document.getElementById('rate-input');
        const amountLabel = document.getElementById('amount-label');
        const termLabel = document.getElementById('term-label');
        const monthlyPaymentEl = document.getElementById('monthly-payment');
        const totalInterestEl = document.getElementById('total-interest');
        const totalPaymentEl = document.getElementById('total-payment');

        const formatCurrency = (value) => '$' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const calculate = () => {
            const principal = parseFloat(amountSlider.value);
            const annualRate = parseFloat(rateInput.value) || 0;
            const n = parseInt(termSlider.value, 10);
            const r = annualRate / 100 / 12;

            let monthly = 0;
            if (r === 0) {
                monthly = principal / n;
            } else {
                monthly = principal * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
            }

            const totalRepayment = monthly * n;
            const totalInterest = totalRepayment - principal;

            amountLabel.textContent = '$' + principal.toLocaleString();
            termLabel.textContent = n;
            monthlyPaymentEl.textContent = formatCurrency(monthly);
            totalInterestEl.textContent = formatCurrency(totalInterest);
            totalPaymentEl.textContent = formatCurrency(totalRepayment);
        };

        amountSlider.addEventListener('input', calculate);
        termSlider.addEventListener('input', calculate);
        rateInput.addEventListener('input', calculate);

        calculate();
    });
</script>