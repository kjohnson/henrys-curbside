/**
 * Shows the rest of the address (line 2, city, state, ZIP) once the visitor starts typing
 * the first line. The fields are hidden by CSS only when JavaScript is running.
 */
export function enableAddressReveal(fieldset) {
    const street = fieldset.querySelector('[name="street"]');
    const details = fieldset.querySelector('[data-address-details]');

    if (!street || !details) return;

    const reveal = () => {
        if (street.value.trim() !== '') details.classList.add('is-revealed');
    };

    street.addEventListener('input', reveal);
    reveal(); // A value restored by the browser (e.g. going back) counts too.
}
