/**
 * Rolls the logo's mark off to the right, then replays its entrance: on click or tap, and
 * automatically on load when the logo has data-logo-intro="replay" (the step right after
 * the form is submitted).
 *
 * Setting data-logo-state="exit" swaps the entrance animations for the roll-out (see
 * app.css). Removing it when the roll-out ends re-applies the entrance animations,
 * which restarts them from the beginning. Any data-logo-intro (which holds the logo at
 * rest on load) is cleared too so the replay still rolls back in.
 */
export function enableLogoReplay(logo) {
    const mark = logo.querySelector('.logo-roll-in');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!mark) return;

    const replay = () => {
        if (reducedMotion.matches || logo.dataset.logoState === 'exit') return;

        delete logo.dataset.logoIntro;
        logo.dataset.logoState = 'exit';
    };

    logo.addEventListener('click', replay);

    // Animation events from the bin, lid, and lines bubble up here too, so match by name.
    mark.addEventListener('animationend', (event) => {
        if (event.animationName === 'logo-roll-out') {
            delete logo.dataset.logoState;
        }
    });

    if (logo.dataset.logoIntro === 'replay') replay();
}
