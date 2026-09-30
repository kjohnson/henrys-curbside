/**
 * Formats input as a US phone number, "(423) 555-0123", without a country code.
 * Separators only appear once the digit after them is typed, so backspace works naturally.
 */
export function formatUsPhone(value) {
    // Drop a leading country code "1" (area codes never start with 1), then cap at 10 digits.
    const digits = value.replace(/\D/g, '').replace(/^1/, '').slice(0, 10);

    if (digits.length === 0) return '';
    if (digits.length <= 3) return `(${digits}`;
    if (digits.length <= 6) return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;

    return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
}

function digitsBefore(value, position) {
    return value.slice(0, position).replace(/\D/g, '').length;
}

function positionAfterDigits(value, count) {
    if (count === 0) return value.indexOf('(') === 0 ? 1 : 0;

    let seen = 0;
    for (let i = 0; i < value.length; i++) {
        if (/\d/.test(value[i]) && ++seen === count) return i + 1;
    }

    return value.length;
}

export function applyUsPhoneMask(input) {
    const format = () => {
        const raw = input.value;
        const hadLeadingOne = /^\D*1/.test(raw);
        let caretDigits = digitsBefore(raw, input.selectionStart ?? raw.length);
        if (hadLeadingOne && caretDigits > 0) caretDigits--;

        const formatted = formatUsPhone(raw);
        if (formatted === raw) return;

        input.value = formatted;
        if (document.activeElement === input) {
            const caret = positionAfterDigits(formatted, caretDigits);
            input.setSelectionRange(caret, caret);
        }
    };

    input.addEventListener('input', format);
    format(); // Tidy values restored by old() or browser autofill.
}
