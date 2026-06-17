// BarberApp — main.js
// Helpers globales Alpine / SweetAlert2

// Confirmar acción destructiva (usar en formularios con data-confirm)
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', async (e) => {
            e.preventDefault();
            const msg = el.dataset.confirm || '¿Estás seguro?';
            const result = await Swal.fire({
                title: '¿Confirmar?',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
            });
            if (result.isConfirmed) {
                if (el.tagName === 'A') window.location.href = el.href;
                else if (el.closest('form')) el.closest('form').submit();
            }
        });
    });

});

// Toast rápido accesible desde PHP via window.toast(...)
window.toast = (icon, title, message = '') => {
    Swal.fire({
        icon, title, text: message || undefined,
        timer: 3500, timerProgressBar: true,
        showConfirmButton: false,
        toast: true, position: 'top-end',
    });
};
