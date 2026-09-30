const reveal = () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal:not(.is-visible)').forEach((el) => observer.observe(el));
};

document.addEventListener('DOMContentLoaded', reveal);
document.addEventListener('livewire:navigated', reveal);
document.addEventListener('livewire:init', () => {
    Livewire.hook('morphed', () => requestAnimationFrame(reveal));
});

document.addEventListener('error', (event) => {
    if (event.target instanceof HTMLImageElement) {
        event.target.style.visibility = 'hidden';
    }
}, true);
