/**
 * Starts the footer truck's drive (.is-driving in app.css) the first time at least half of
 * the truck has scrolled into view. It drives once; the observer disconnects after triggering.
 */
export function driveWhenVisible(truck) {
    if (!('IntersectionObserver' in window)) {
        truck.classList.add('is-driving');
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                truck.classList.add('is-driving');
                observer.disconnect();
            }
        },
        { threshold: 0.5 },
    );

    observer.observe(truck);
}
