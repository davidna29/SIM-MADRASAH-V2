import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

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
    // Theme store — terang/gelap
    Alpine.store('theme', {
        mode: localStorage.getItem('sim-theme')
            || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),

        toggle() {
            this.mode = this.mode === 'dark' ? 'light' : 'dark';
            this.apply();
            localStorage.setItem('sim-theme', this.mode);
        },

        apply() {
            document.documentElement.classList.toggle('dark', this.mode === 'dark');
        },

        init() {
            this.apply();
            matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('sim-theme')) {
                    this.mode = e.matches ? 'dark' : 'light';
                    this.apply();
                }
            });
        },
    });

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

    Alpine.data('sortableTable', (options = {}) => ({
        init() {
            const el = this.$root.querySelector('tbody');
            if (!el || this.$root.querySelector('[data-sortable-inited]')) {
                return;
            }
            this.$root.querySelector('[data-sortable-inited]')?.remove();
            el.setAttribute('data-sortable-inited', '1');

            Sortable.create(el, {
                handle: options.handle || '[data-drag-handle]',
                animation: 200,
                ghostClass: 'opacity-40',
                onEnd: () => {
                    const order = [...el.querySelectorAll('tr[data-id]')].map((tr) => tr.dataset.id);
                    this.$root.dispatchEvent(new CustomEvent('reorder', { detail: { order } }));
                },
            });
        },
    }));
});

Alpine.start();
