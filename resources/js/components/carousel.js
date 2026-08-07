document.addEventListener('alpine:init', () => {

    Alpine.data('carousel', () => ({

        current: 1,
        transitionEnabled: true,
        autoplayInterval: null,

        slides: [
            {
                src: covers[0]['cover']['image_id'],
                title: banners[0]['title'],
                subtitle: banners[0]['body'],
                game: banners[0]['game_name']
            },
            {
                src: covers[1]['cover']['image_id'],
                title: banners[1]['title'],
                subtitle: banners[1]['body'],
                game: banners[1]['game_name']
            },
            {
                src: covers[2]['cover']['image_id'],
                title: banners[2]['title'],
                subtitle: banners[2]['body'],
                game: banners[2]['game_name']
            },
            {
                src: covers[3]['cover']['image_id'],
                title: banners[3]['title'],
                subtitle: banners[3]['body'],
                game: banners[3]['game_name']
            },
            {
                src: covers[4]['cover']['image_id'],
                title: banners[4]['title'],
                subtitle: banners[4]['body'],
                game: banners[4]['game_name']
            },
            {
                src: covers[5]['cover']['image_id'],
                title: banners[5]['title'],
                subtitle: banners[5]['body'],
                game: banners[5]['game_name']
            }
        ],

        get extendedSlides() {
            return [
                this.slides[this.slides.length - 1],
                ...this.slides,
                this.slides[0]
            ];
        },

        truncate(text, limit = 280) {
            return text.length > limit
                ? text.slice(0, limit) + '...'
                : text;
        },

        init() {
            this.startAutoplay();
        },

        startAutoplay() {
            this.autoplayInterval = setInterval(() => this.next(), 8000);
        },

        stopAutoplay() {
            clearInterval(this.autoplayInterval);
        },

        restartAutoplay() {
            this.stopAutoplay();
            this.startAutoplay();
        },

        next() {
            this.current++;
        },

        previous() {
            this.current--;
        },

        handleTransitionEnd() {
            if (this.current === this.extendedSlides.length - 1) {
                this.transitionEnabled = false;
                this.current = 1;

                this.$nextTick(() => {
                    requestAnimationFrame(() => {
                        this.transitionEnabled = true;
                    });
                });
            }

            if (this.current === 0) {
                this.transitionEnabled = false;
                this.current = this.slides.length;

                this.$nextTick(() => {
                    requestAnimationFrame(() => {
                        this.transitionEnabled = true;
                    });
                });
            }
        }

    }));

});