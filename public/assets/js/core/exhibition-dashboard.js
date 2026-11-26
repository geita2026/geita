(() => {
    const dashboard = document.querySelector('.exhibition-dashboard');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!dashboard || reducedMotion.matches || !('IntersectionObserver' in window)) {
        return;
    }

    const formatter = new Intl.NumberFormat(document.documentElement.lang || undefined);
    const observer = new IntersectionObserver(entries => {
        for (const entry of entries) {
            if (!entry.isIntersecting) {
                continue;
            }

            const element = entry.target;
            observer.unobserve(element);

            if (reducedMotion.matches) {
                continue;
            }

            if (!element.hasAttribute('data-dashboard-count')) {
                element.classList.add('is-animated');
                continue;
            }

            const total = Number(element.dataset.dashboardCount);
            const startedAt = performance.now();
            const finalValue = element.textContent;

            function tick(now) {
                const progress = Math.min(1, (now - startedAt) / 650);

                if (progress === 1 || reducedMotion.matches) {
                    element.textContent = finalValue;
                    return;
                }

                const eased = 1 - Math.pow(1 - progress, 3);
                element.textContent = formatter.format(Math.round(total * eased));
                requestAnimationFrame(tick);
            }

            if (total > 0) {
                requestAnimationFrame(tick);
            }
        }
    });

    for (const element of dashboard.querySelectorAll('[data-dashboard-count], .dashboard-charts')) {
        observer.observe(element);
    }
})();
