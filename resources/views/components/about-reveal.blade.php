@props(['selector', 'transform' => false])

{{-- Scroll-reveal stagger shared by the About leaves (mirrors the ride/show +
     steun-ons script). transform defaults to false: the .activity-promises__item
     cards carry a CSS tilt, so we animate opacity only to avoid flattening them.
     Pass :transform="true" for upright elements. Honours prefers-reduced-motion. --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const els = document.querySelectorAll(@js($selector));
    els.forEach((el, i) => {
        el.style.opacity = '0';
        @if ($transform)
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.45s cubic-bezier(0.25, 1, 0.5, 1), transform 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
        @else
            el.style.transition = 'opacity 0.45s cubic-bezier(0.25, 1, 0.5, 1)';
        @endif
        el.style.transitionDelay = `${i * 80}ms`;
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                @if ($transform)
                    entry.target.style.transform = 'translateY(0)';
                @endif
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    els.forEach((el) => observer.observe(el));
});
</script>
