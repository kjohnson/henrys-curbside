/**
 * Starts the footer truck's drive (.is-driving in app.css) the first time its footer
 * scrolls into view. It drives once; the observer disconnects after triggering.
 */
export function driveWhenVisible(truck) {
    const trigger = truck.closest('footer') ?? truck;

    if (!('IntersectionObserver' in window)) {
        truck.classList.add('is-driving');
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
            truck.classList.add('is-driving');
            observer.disconnect();
        }
    });

    observer.observe(trigger);
}
