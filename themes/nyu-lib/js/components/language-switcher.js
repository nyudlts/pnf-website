document.addEventListener('alpine:init', () => {
    Alpine.data('languageSwitcher', (initial = {}) => ({
        open: false,
        current: initial.current || {},
        options: initial.options || [],

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => this.focusFirstOption());
            }
        },

        close() {
            this.open = false;
        },

        focusFirstOption() {
            const first = this.$refs.optionsList?.querySelector('[data-option]');
            if (first) {
                first.focus();
            }
        },

        focusLastOption() {
            const options = this.$refs.optionsList?.querySelectorAll('[data-option]');
            if (options && options.length) {
                options[options.length - 1].focus();
            }
        },

        onTriggerKeydown(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                this.toggle();
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (!this.open) {
                    this.open = true;
                }
                this.$nextTick(() => this.focusFirstOption());
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (!this.open) {
                    this.open = true;
                }
                this.$nextTick(() => this.focusLastOption());
            } else if (event.key === 'Escape') {
                this.close();
            }
        },

        onOptionKeydown(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                this.close();
                this.$refs.trigger?.focus();
            } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                const direction = event.key === 'ArrowDown' ? 'nextElementSibling' : 'previousElementSibling';
                let element = event.target[direction];
                while (element && !element.hasAttribute('data-option')) {
                    element = element[direction];
                }
                if (element) {
                    element.focus();
                }
            }
        }
    }));
});
