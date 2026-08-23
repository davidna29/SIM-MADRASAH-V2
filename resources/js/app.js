import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.directive('pin-animate', (el) => {
    el.addEventListener('click', () => {
        const pin = el.querySelector('[data-pin]');
        if (pin) {
            pin.classList.remove('scale-0');
            pin.classList.add('scale-100');
            setTimeout(() => pin.classList.add('pin-popped'), 240);
        }
    });
});

document.addEventListener('alpine:init', () => {
    Alpine.store('toasts', {
        items: [],
        push(message, type = 'success') {
            const id = Date.now();
            this.items.push({ id, message, type });
            setTimeout(() => {
                this.items = this.items.filter((t) => t.id !== id);
            }, 4200);
        },
        remove(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    });
});

Alpine.start();
