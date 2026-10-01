/**
 * Click or tap the logo to roll the mark off to the right, then replay its entrance.
 *
 * Setting data-logo-state="exit" swaps the entrance animations for the roll-out (see
 * app.css). Removing it when the roll-out ends re-applies the entrance animations,
 * which restarts them from the beginning. If the entrance was skipped on load
 * (data-logo-intro="skip"), that's cleared too so the replay still rolls back in.
 */
export function enableLogoReplay(logo) {
    const mark = logo.querySelector('.logo-roll-in');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!mark) return;

    logo.addEventListener('click', () => {
        if (reducedMotion.matches || logo.dataset.logoState === 'exit') return;

        delete logo.dataset.logoIntro;
        logo.dataset.logoState = 'exit';
    });

    // Animation events from the bin, lid, and lines bubble up here too, so match by name.
    mark.addEventListener('animationend', (event) => {
        if (event.animationName === 'logo-roll-out') {
            delete logo.dataset.logoState;
        }
    });
}
