(function () {
    'use strict';

    const BATCH_SIZE = 50;
    const ESCAPE_MAP = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    };

    function titleCase(value) {
        return value
            .replace(/[-_]+/g, ' ')
            .replace(/\b\w/g, (char) => char.toUpperCase());
    }

    function escapeHtml(value) {
        return value.replace(/[&<>"']/g, (char) => ESCAPE_MAP[char] || char);
    }

    function buildUrl(endpoint) {
        try {
            return new URL(endpoint, window.location.origin);
        } catch (err) {
            const anchor = document.createElement('a');
            anchor.href = endpoint;
            return new URL(anchor.href);
        }
    }

    class FaIconAPI {
        constructor(endpoint) {
            this.endpoint = endpoint;
            this.types = null;
            this.securityToken = window.GravAdmin?.config?.security_token;
        }

        setEndpoint(endpoint) {
            if (endpoint && endpoint !== this.endpoint) {
                this.endpoint = endpoint;
                this.types = null;
            }
        }

        async getTypes() {
            if (this.types) {
                return this.types;
            }

            const url = buildUrl(this.endpoint);
            url.searchParams.set('mode', 'types');

            const response = await this.request(url);
            this.types = response.types || [];
            return this.types;
        }

        async getIcons(params) {
            const url = buildUrl(this.endpoint);
            url.searchParams.set('mode', 'icons');
            url.searchParams.set('type', params.type || 'regular');
            url.searchParams.set('offset', params.offset || 0);
            url.searchParams.set('limit', params.limit || BATCH_SIZE);

            if (params.search) {
                url.searchParams.set('q', params.search);
            }

            return this.request(url);
        }

        async request(url) {
            const options = {
                method: 'GET',
                credentials: 'same-origin',
                headers: {}
            };

            if (this.securityToken) {
                options.headers['X-Grav-Nonce'] = this.securityToken;
            }

            const response = await fetch(url.toString(), options);
            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            return response.json();
        }
    }

    class FaIconModal {
        constructor(api) {
            this.api = api;
            this.activeField = null;
            this.activeType = null;
            this.activeIcon = null;
            this.baseUrl = '';
            this.offset = 0;
            this.total = 0;
            this.loaded = 0;
            this.loading = false;
            this.searchQuery = '';
            this.typesLoaded = false;
            this.debounceHandle = null;
            this.icons = [];
            this.observer = null;
            this.pageSize = BATCH_SIZE;
            this._build();
        }

        static getInstance(endpoint) {
            if (!FaIconModal.instance) {
                FaIconModal.instance = new FaIconModal(new FaIconAPI(endpoint));
            } else {
                FaIconModal.instance.api.setEndpoint(endpoint);
            }

            return FaIconModal.instance;
        }

        _build() {
            this.element = document.createElement('div');
            this.element.className = 'faicon-modal';
            this.element.innerHTML = `
                <div class="faicon-modal__overlay" data-faicon-overlay></div>
                <div class="faicon-modal__dialog" role="dialog" aria-modal="true" aria-label="Select icon">
                    <div class="faicon-modal__header">
                        <h2>Select Icon</h2>
                        <button type="button" class="faicon-modal__close" aria-label="Close">&times;</button>
                    </div>
                    <div class="faicon-modal__controls">
                        <label class="faicon-modal__control">
                            <span>Style</span>
                            <select class="faicon-modal__select"></select>
                        </label>
                        <label class="faicon-modal__control faicon-modal__control--search">
                            <span>Search</span>
                            <input type="search" class="faicon-modal__search" placeholder="Search icons" autocomplete="off" />
                        </label>
                    </div>
                    <div class="faicon-modal__grid" data-faicon-grid></div>
                    <div class="faicon-modal__footer" data-faicon-footer></div>
                </div>`;

            this.overlay = this.element.querySelector('[data-faicon-overlay]');
            this.closeButton = this.element.querySelector('.faicon-modal__close');
            this.typeSelect = this.element.querySelector('.faicon-modal__select');
            this.searchInput = this.element.querySelector('.faicon-modal__search');
            this.grid = this.element.querySelector('[data-faicon-grid]');
            this.footer = this.element.querySelector('[data-faicon-footer]');

            this.status = document.createElement('div');
            this.status.className = 'faicon-modal__status';
            this.status.style.display = 'none';
            this.grid.appendChild(this.status);

            this.sentinel = document.createElement('div');
            this.sentinel.className = 'faicon-modal__sentinel';
            this.grid.appendChild(this.sentinel);

            this.closeButton.addEventListener('click', () => this.close());
            this.overlay.addEventListener('click', () => this.close());

            this.typeSelect.addEventListener('change', () => {
                this.activeType = this.typeSelect.value;
                this.loadIcons({ reset: true });
            });

            this.searchInput.addEventListener('input', () => {
                clearTimeout(this.debounceHandle);
                this.debounceHandle = setTimeout(() => {
                    this.searchQuery = this.searchInput.value.trim();
                    this.loadIcons({ reset: true });
                }, 250);
            });

            this._handleKeydown = this._handleKeydown.bind(this);
        }

        open(field) {
            this.activeField = field;
            this.baseUrl = field.baseUrl;
            this.api.setEndpoint(field.endpoint);

            const selection = field.getSelection();
            this.searchQuery = '';
            this.searchInput.value = '';

            this.ensureModalInDOM();

            this.fetchAndRender(selection)
                .catch((error) => {
                    console.error('Failed to load icons', error);
                    this.showStatus('Unable to load icons.', 'error');
                });
        }

        async fetchAndRender(selection) {
            this.showStatus('Loading icon catalog…', 'loading');

            if (!this.typesLoaded) {
                const types = await this.api.getTypes();
                this.populateTypes(types);
                this.typesLoaded = true;
            }

            this.activeType = this.resolveType(selection.type);
            this.typeSelect.value = this.activeType;
            this.activeIcon = selection.icon;

            this.loadIcons({ reset: true }).catch((error) => {
                console.error('Failed to load icons', error);
                this.showStatus('Unable to load icons.', 'error');
            });
        }

        ensureModalInDOM() {
            document.body.appendChild(this.element);
            requestAnimationFrame(() => {
                this.element.classList.add('is-open');
            });
            document.addEventListener('keydown', this._handleKeydown, true);
        }

        close() {
            this.element.classList.remove('is-open');
            document.removeEventListener('keydown', this._handleKeydown, true);
            setTimeout(() => {
                if (this.element.parentNode) {
                    this.element.parentNode.removeChild(this.element);
                }
            }, 150);

            this.detachObserver();
            this.activeField = null;
            this.activeIcon = null;
            this.icons = [];
        }

        _handleKeydown(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                this.close();
            }
        }

        resolveType(type) {
            const options = Array.from(this.typeSelect.options).map((option) => option.value);
            if (type && options.includes(type)) {
                return type;
            }

            return options.length ? options[0] : 'regular';
        }

        populateTypes(types) {
            this.typeSelect.innerHTML = '';
            types.forEach((type) => {
                const option = document.createElement('option');
                option.value = type.value;
                const label = type.count !== undefined ? `${titleCase(type.value)} (${type.count})` : titleCase(type.value);
                option.textContent = label;
                this.typeSelect.appendChild(option);
            });
        }

        async loadIcons({ reset = false } = {}) {
            if (this.loading) {
                return;
            }

            this.loading = true;

            if (reset) {
                this.detachObserver();
                this.grid.innerHTML = '';
                this.grid.appendChild(this.status);
                this.grid.appendChild(this.sentinel);
                this.icons = [];
                this.offset = 0;
                this.total = 0;
                this.loaded = 0;
                this.showStatus('Loading icons…', 'loading');
            } else {
                this.showStatus('Loading more icons…', 'loading');
            }

            try {
                const response = await this.api.getIcons({
                    type: this.activeType,
                    offset: this.offset,
                    limit: this.pageSize,
                    search: this.searchQuery
                });

                if (response.types && !this.typesLoaded) {
                    this.populateTypes(response.types);
                    this.typesLoaded = true;
                }

                this.activeType = response.type;
                this.typeSelect.value = this.activeType;

                this.total = response.total || 0;
                const icons = response.icons || [];

                if (reset) {
                    this.icons = icons;
                } else {
                    this.icons = this.icons.concat(icons);
                }

                this.renderIcons(icons, reset);

                this.offset = response.offset + icons.length;
                this.loaded = this.icons.length;

                this.updateFooter();

                if (this.loaded < this.total) {
                    this.attachObserver();
                    this.showStatus('', '');
                } else if (this.total === 0) {
                    this.showStatus('No icons found.', 'empty');
                } else {
                    this.showStatus('', '');
                }
            } catch (error) {
                console.error('Failed to fetch icons', error);
                this.showStatus('Unable to load icons.', 'error');
            } finally {
                this.loading = false;
            }
        }

        renderIcons(newIcons, reset) {
            if (reset) {
                this.grid.innerHTML = '';
                this.grid.appendChild(this.status);
                this.grid.appendChild(this.sentinel);
            }

            if (!newIcons.length && this.loaded === 0) {
                return;
            }

            const fragment = document.createDocumentFragment();
            const base = this.baseUrl.replace(/\/+$/, '');
            const selectedValue = this.activeField?.getValue();

            newIcons.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'faicon-modal__icon';
                button.dataset.value = item.value;
                button.dataset.icon = item.name;

                if (selectedValue && selectedValue === item.value) {
                    button.classList.add('is-selected');
                }

                const src = `${base}/${item.value}`;
                const label = escapeHtml(item.name);

                button.innerHTML = `
                    <span class="faicon-modal__icon-preview">
                        <img src="${src}" alt="${label}" loading="lazy" />
                    </span>
                    <span class="faicon-modal__icon-label">${label}</span>`;

                button.addEventListener('click', () => {
                    if (this.activeField) {
                        this.activeIcon = item.name;
                        this.activeField.setValue(item.value);
                    }
                    this.close();
                });

                fragment.appendChild(button);
            });

            this.grid.insertBefore(fragment, this.sentinel);
        }

        attachObserver() {
            if (this.observer) {
                return;
            }

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !this.loading && this.loaded < this.total) {
                        this.loadIcons();
                    }
                });
            }, {
                root: this.grid,
                threshold: 0.1
            });

            this.observer.observe(this.sentinel);
        }

        detachObserver() {
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }
        }

        updateFooter() {
            if (this.total === 0) {
                this.footer.textContent = '';
                return;
            }

            const displayed = Math.min(this.loaded, this.total);
            if (displayed >= this.total) {
                this.footer.textContent = `Showing ${displayed} of ${this.total}`;
            } else {
                this.footer.textContent = `Showing ${displayed} of ${this.total}. Scroll to load more.`;
            }
        }

        showStatus(message, state) {
            if (!this.status) {
                return;
            }

            this.status.textContent = message;
            this.status.className = 'faicon-modal__status';
            if (state) {
                this.status.classList.add(`faicon-modal__status--${state}`);
            }

            this.status.style.display = message ? 'block' : 'none';
        }
    }

    class FaIconField {
        constructor(container) {
            this.container = container;
            this.input = container.querySelector('[data-faicon-input]');
            this.preview = container.querySelector('[data-faicon-preview]');
            this.chooseButton = container.querySelector('[data-faicon-choose]');
            this.clearButton = container.querySelector('[data-faicon-clear]');
            this.placeholder = container.dataset.placeholder || 'No icon selected';
            this.defaultType = container.dataset.defaultType || 'regular';
            this.baseUrl = container.dataset.baseUrl || '';
            this.endpoint = container.dataset.endpoint || '';

            this.chooseButton?.addEventListener('click', () => this.openPicker());
            this.clearButton?.addEventListener('click', () => this.clear());
        }

        openPicker() {
            FaIconModal.getInstance(this.endpoint).open(this);
        }

        getSelection() {
            const value = this.getValue();

            if (!value) {
                return { type: this.defaultType, icon: '' };
            }

            const parts = value.split('/');
            const type = parts.shift() || this.defaultType;
            const iconWithExt = parts.join('/') || '';
            const icon = iconWithExt.replace(/\.svg$/i, '');

            return { type, icon };
        }

        getValue() {
            return (this.input?.value || '').trim();
        }

        setValue(value) {
            if (!this.input) {
                return;
            }

            this.input.value = value;
            const selection = this.getSelection();
            this.container.dataset.selectedType = selection.type;
            this.container.dataset.selectedIcon = selection.icon;
            this.updatePreview();
            this.triggerChange();
        }

        clear() {
            if (!this.input) {
                return;
            }

            this.input.value = '';
            this.container.dataset.selectedType = this.defaultType;
            this.container.dataset.selectedIcon = '';
            this.updatePreview();
            this.triggerChange();
        }

        updatePreview() {
            if (!this.preview) {
                return;
            }

            const value = this.getValue();

            if (!value) {
                this.preview.innerHTML = `<span class="faicon-field__placeholder">${escapeHtml(this.placeholder)}</span>`;
                if (this.clearButton) {
                    this.clearButton.disabled = true;
                }
                return;
            }

            const parts = value.split('/');
            const type = parts.shift() || this.defaultType;
            const iconWithExt = parts.join('/') || '';
            const icon = iconWithExt.replace(/\.svg$/i, '');
            const base = this.baseUrl.replace(/\/+$/, '');
            const src = `${base}/${value}`;

            this.preview.innerHTML = `
                <div class="faicon-field__preview-display">
                    <span class="faicon-field__preview-icon"><img src="${src}" alt="${escapeHtml(icon)}" loading="lazy" /></span>
                    <span class="faicon-field__preview-label">${escapeHtml(type)}/${escapeHtml(icon)}</span>
                </div>`;

            if (this.clearButton) {
                this.clearButton.disabled = false;
            }
        }

        triggerChange() {
            if (!this.input) {
                return;
            }

            const changeEvent = new Event('change', { bubbles: true });
            const inputEvent = new Event('input', { bubbles: true });
            this.input.dispatchEvent(changeEvent);
            this.input.dispatchEvent(inputEvent);
        }
    }

    function initialize() {
        const containers = document.querySelectorAll('[data-grav-faicon-field] .faicon-field__container');
        containers.forEach((container) => {
            const field = new FaIconField(container);
            field.updatePreview();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

})();
