document.addEventListener('alpine:init', () => {
    Alpine.data('guidelinesFilter', () => ({
        selectedTags: [],
        searchQuery: '',

        init() {
            // Check URL for any existing tag filters and apply them
            const urlParams = new URLSearchParams(window.location.search);
            const tagParam = urlParams.get('tags');
            const searchParam = urlParams.get('q');
            if (tagParam) {
                this.selectedTags = tagParam.split(',').filter(t => t);
            }
            if (searchParam) {
                this.searchQuery = searchParam;
            }
            if (tagParam || searchParam) {
                this.$nextTick(() => this.filterCards());
            }
        },

        onSearchInput() {
            this.updateURL();
            this.filterCards();
        },

        toggleTag(tag) {
            const index = this.selectedTags.indexOf(tag);
            if (index > -1) {
                this.selectedTags.splice(index, 1);
            } else {
                this.selectedTags.push(tag);
            }
            this.updateURL();
            this.filterCards();
        },

        isSelected(tag) {
            return this.selectedTags.includes(tag);
        },

        clearFilters() {
            this.selectedTags = [];
            this.searchQuery = '';
            this.updateURL();
            this.filterCards();
        },

        hasFilters() {
            return this.selectedTags.length > 0 || this.searchQuery.length > 0;
        },

        updateURL() {
            const url = new URL(window.location);
            if (this.selectedTags.length > 0) {
                url.searchParams.set('tags', this.selectedTags.join(','));
            } else {
                url.searchParams.delete('tags');
            }
            if (this.searchQuery.length > 0) {
                url.searchParams.set('q', this.searchQuery);
            } else {
                url.searchParams.delete('q');
            }
            window.history.replaceState({}, '', url);
        },

        filterCards() {
            const cards = document.querySelectorAll('[data-guideline-card]');
            let visibleEven = [];
            let visibleOdd = [];
            let visibleIndex = 0;
            const searchLower = this.searchQuery.toLowerCase().trim();

            cards.forEach((card) => {
                const cardTags = JSON.parse(card.dataset.tags || '[]');
                const cardTitle = (card.dataset.title || '').toLowerCase();

                // Check tag filter: show if no tags selected OR card has ALL selected tags
                const matchesTags = this.selectedTags.length === 0 ||
                    this.selectedTags.every(tag => cardTags.includes(tag));

                // Check search filter: show if no search OR title contains search query
                const matchesSearch = searchLower.length === 0 ||
                    cardTitle.includes(searchLower);

                const shouldShow = matchesTags && matchesSearch;

                if (shouldShow) {
                    card.classList.remove('hidden');
                    // Track which column this card should go in
                    if (visibleIndex % 2 === 0) {
                        visibleEven.push(card);
                    } else {
                        visibleOdd.push(card);
                    }
                    visibleIndex++;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Redistribute cards between columns for balanced layout
            this.redistributeCards(visibleEven, visibleOdd);
        },

        redistributeCards(evenCards, oddCards) {
            const col2 = document.getElementById('blog-col-2');
            const col3 = document.getElementById('blog-col-3');

            if (!col2 || !col3) return;

            // Move cards to appropriate columns
            evenCards.forEach(card => {
                if (card.parentElement !== col2) {
                    col2.appendChild(card);
                }
            });

            oddCards.forEach(card => {
                if (card.parentElement !== col3) {
                    col3.appendChild(card);
                }
            });
        }
    }));
});
