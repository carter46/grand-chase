<footer class="bg-inverse-surface py-12 md:py-stack-lg border-t border-outline-variant/10 text-on-primary">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-stack-lg mb-8 md:mb-stack-lg">
        <div>
            @include('includes.public-brand', [
                'brandClass' => 'inline-flex items-center mb-stack-md',
                'brandImgClass' => 'h-8 w-auto object-contain brightness-0 invert',
                'brandTextClass' => 'font-label-bold text-on-primary uppercase tracking-wider',
            ])
            <p class="text-body-sm opacity-70 leading-relaxed">
                {{ $settings->site_name }} provides institutional reliability and secure global financial services.
            </p>
        </div>
        <div>
            <h4 class="font-label-bold uppercase mb-stack-md text-primary">Personal</h4>
            <ul class="space-y-stack-sm text-body-sm opacity-80">
                <li><a href="{{ url('personal') }}" class="hover:text-primary">Personal Banking</a></li>
                <li><a href="{{ url('loans') }}" class="hover:text-primary">Loans</a></li>
                <li><a href="{{ url('cards') }}" class="hover:text-primary">Cards</a></li>
                <li><a href="{{ url('investments') }}" class="hover:text-primary">Investments</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-label-bold uppercase mb-stack-md text-primary">Business</h4>
            <ul class="space-y-stack-sm text-body-sm opacity-80">
                <li><a href="{{ url('business') }}" class="hover:text-primary">Business Banking</a></li>
                <li><a href="{{ url('about') }}" class="hover:text-primary">About Us</a></li>
                <li><a href="{{ url('apps') }}" class="hover:text-primary">Mobile App</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-label-bold uppercase mb-stack-md text-primary">Support</h4>
            <ul class="space-y-stack-sm text-body-sm opacity-80">
                <li><a href="{{ url('contact') }}" class="hover:text-primary">Contact</a></li>
                <li><a href="{{ url('privacy') }}" class="hover:text-primary">Privacy Policy</a></li>
                <li><a href="{{ url('terms') }}" class="hover:text-primary">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-outline-variant/10 pt-stack-md">
        <div class="max-w-[1200px] mx-auto px-4 md:px-gutter flex flex-col sm:flex-row justify-between items-center gap-2 text-caption opacity-50">
            <span>&copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.</span>
            <div class="flex gap-stack-md">
                <a href="{{ url('privacy') }}" class="hover:opacity-100">Privacy</a>
                <a href="{{ url('terms') }}" class="hover:opacity-100">Terms</a>
                <a href="{{ url('contact') }}" class="hover:opacity-100">Contact</a>
            </div>
        </div>
    </div>
</footer>
