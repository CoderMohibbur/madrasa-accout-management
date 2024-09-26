import './bootstrap';

import Alpine from 'alpinejs';
import 'flowbite';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const toastSuccess = document.getElementById('toast-success');
    const toastError = document.getElementById('toast-error');

    if (toastSuccess) {
        setTimeout(() => {
            toastSuccess.remove();
        }, 5000); // Dismiss after 5 seconds
    }

    if (toastError) {
        setTimeout(() => {
            toastError.remove();
        }, 5000); // Dismiss after 5 seconds
    }
});
