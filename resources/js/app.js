import { applyUsPhoneMask } from './phone-mask';

document.querySelectorAll('input[data-mask="us-phone"]').forEach(applyUsPhoneMask);
