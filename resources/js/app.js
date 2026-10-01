import { enableLogoReplay } from './logo-replay';
import { applyUsPhoneMask } from './phone-mask';

document.querySelectorAll('input[data-mask="us-phone"]').forEach(applyUsPhoneMask);
document.querySelectorAll('[data-logo-replay]').forEach(enableLogoReplay);
