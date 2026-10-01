import { enableAddressReveal } from './address-reveal';
import { enableLogoReplay } from './logo-replay';
import { applyUsPhoneMask } from './phone-mask';
import { driveWhenVisible } from './truck';

document.querySelectorAll('input[data-mask="us-phone"]').forEach(applyUsPhoneMask);
document.querySelectorAll('[data-logo-replay]').forEach(enableLogoReplay);
document.querySelectorAll('[data-drive-when-visible]').forEach(driveWhenVisible);
document.querySelectorAll('[data-address-reveal]').forEach(enableAddressReveal);
