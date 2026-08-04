<footer class="w-full border-t border-outline-variant/30 bg-surface-container-low py-8 mt-auto">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="font-caption text-on-surface-variant">&copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.</p>
        <div class="flex items-center gap-6">
            <a href="{{ url('privacy') }}" class="font-label-md text-on-surface-variant hover:text-primary transition-colors">Privacy</a>
            <a href="{{ url('terms') }}" class="font-label-md text-on-surface-variant hover:text-primary transition-colors">Terms</a>
            <a href="{{ url('contact') }}" class="font-label-md text-on-surface-variant hover:text-primary transition-colors">Contact</a>
        </div>
    </div>
</footer>
