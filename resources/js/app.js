/**
 * Public-site Alpine components.
 *
 * Alpine is provided globally by Livewire (Flux); we only register extra
 * `Alpine.data()` components here. Vite bundles this file via the site layout.
 */

document.addEventListener('alpine:init', () => {
    /**
     * teamCarousel — the chapter-page "Wie zijn wij" rider carousel.
     *
     * Keeps the prev/next nav (start/end/scrollable/page/update) and layers on a
     * "flick-through" feel for mouse users: grab-drag the row, release to fling
     * with momentum, then settle onto the nearest rider with a (no-overshoot)
     * spring. The card nearest the centre lifts into focus as you scroll, and on
     * hover each character leans toward the cursor.
     *
     * Touch keeps its native momentum + scroll-snap (we never hijack it), and
     * everything physical is suppressed under prefers-reduced-motion.
     */
    window.Alpine.data('teamCarousel', () => ({
        start: true,
        end: false,
        scrollable: true,
        dragging: false,
        animating: false,
        reduce: window.matchMedia('(prefers-reduced-motion: reduce)').matches,

        init() {
            this._raf = null;
            this.$nextTick(() => this.update());
            this.armReveal();
        },

        // --- one-shot staggered entrance: cards drift in from the right --------
        // Hide the row up front, then reveal it the first time it scrolls into
        // view so the stagger plays where it can be seen. Skipped entirely under
        // reduced motion — there the cards are simply present from the start.
        armReveal() {
            const track = this.$refs.track;
            if (this.reduce || !track || !('IntersectionObserver' in window)) {
                return;
            }
            track.classList.add('is-revealable');
            const io = new IntersectionObserver((entries, obs) => {
                for (const entry of entries) {
                    if (entry.isIntersecting) {
                        track.classList.remove('is-revealable');
                        track.classList.add('is-revealed');
                        obs.disconnect();
                    }
                }
            }, { threshold: 0.18 });
            io.observe(track);
        },

        // --- prev/next nav (unchanged behaviour) -------------------------------
        page(dir) {
            cancelAnimationFrame(this._raf);
            this.animating = false;
            const t = this.$refs.track;
            const card = t.querySelector('.chapter-team__card');
            if (!card) {
                return;
            }
            const step = card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0);
            t.scrollBy({ left: dir * step, behavior: this.reduce ? 'auto' : 'smooth' });
        },

        update() {
            const t = this.$refs.track;
            const max = t.scrollWidth - t.clientWidth;
            const card = t.querySelector('.chapter-team__card');
            const step = card ? card.offsetWidth + parseFloat(getComputedStyle(t).columnGap || 0) : 0;
            this.scrollable = max > 1;
            // half-a-card tolerance both ends — the full-bleed track's snap leaves a
            // few-dozen px of lead-in scroll at rest, so an exact 0 / max never fires
            this.start = step === 0 || t.scrollLeft <= step / 2;
            this.end = step > 0 && max - t.scrollLeft <= step / 2;
            this.focusPass();
        },

        // --- centre focus: lift the card nearest the viewport centre -----------
        focusPass() {
            const t = this.$refs.track;
            if (!t) {
                return;
            }
            const mid = t.scrollLeft + t.clientWidth / 2;
            for (const card of t.children) {
                const c = card.offsetLeft + card.offsetWidth / 2;
                const proximity = Math.max(0, 1 - Math.abs(c - mid) / card.offsetWidth);
                card.style.setProperty('--focus', this.reduce ? '0' : proximity.toFixed(3));
            }
        },

        // --- grab-drag → fling → spring (mouse/pen only) -----------------------
        onDown(e) {
            if (this.reduce || (e.pointerType !== 'mouse' && e.pointerType !== 'pen')) {
                return; // touch keeps native momentum + snap
            }
            if (e.pointerType === 'mouse' && e.button !== 0) {
                return;
            }
            cancelAnimationFrame(this._raf);
            this.dragging = true;
            this.animating = true;
            this._startX = e.clientX;
            this._startScroll = this.$refs.track.scrollLeft;
            this._lastX = e.clientX;
            this._lastT = e.timeStamp;
            this._v = 0;
            this.$refs.track.setPointerCapture?.(e.pointerId);
        },

        onMove(e) {
            if (!this.dragging) {
                return;
            }
            const t = this.$refs.track;
            t.scrollLeft = this._startScroll - (e.clientX - this._startX);
            const dt = e.timeStamp - this._lastT || 16;
            this._v = (e.clientX - this._lastX) / dt; // px/ms, +ve = pointer moving right
            this._lastX = e.clientX;
            this._lastT = e.timeStamp;
        },

        onUp() {
            if (!this.dragging) {
                return;
            }
            this.dragging = false;
            this.fling(-this._v * 16); // px/ms → px/frame of scrollLeft delta
        },

        fling(v0) {
            const t = this.$refs.track;
            const max = t.scrollWidth - t.clientWidth;
            let v = v0;
            const tick = () => {
                v *= 0.94; // exponential decay = ease-out, no bounce
                t.scrollLeft += v;
                this.update();
                if (Math.abs(v) > 0.4 && t.scrollLeft > 0 && t.scrollLeft < max) {
                    this._raf = requestAnimationFrame(tick);
                } else {
                    this.snap();
                }
            };
            this._raf = requestAnimationFrame(tick);
        },

        snap() {
            const t = this.$refs.track;
            const padLeft = parseFloat(getComputedStyle(t).paddingLeft || 0);
            const max = t.scrollWidth - t.clientWidth;
            let target = null;
            let best = Infinity;
            for (const card of t.children) {
                const tgt = card.offsetLeft - padLeft;
                const d = Math.abs(tgt - t.scrollLeft);
                if (d < best) {
                    best = d;
                    target = tgt;
                }
            }
            this.springTo(target == null ? t.scrollLeft : Math.max(0, Math.min(target, max)));
        },

        springTo(target) {
            const t = this.$refs.track;
            const tick = () => {
                const diff = target - t.scrollLeft;
                t.scrollLeft += diff * 0.18; // critically-damped approach
                this.update();
                if (Math.abs(diff) > 0.5) {
                    this._raf = requestAnimationFrame(tick);
                } else {
                    t.scrollLeft = target;
                    this.animating = false;
                    this.update();
                }
            };
            this._raf = requestAnimationFrame(tick);
        },

        // --- magnetic lean: the character tips toward the cursor --------------
        lean(e) {
            if (this.reduce || this.dragging || e.pointerType !== 'mouse') {
                return;
            }
            const card = e.currentTarget;
            const img = card.querySelector('.chapter-team__photo img');
            if (!img) {
                return;
            }
            const r = card.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width - 0.5;
            const py = (e.clientY - r.top) / r.height - 0.5;
            img.style.setProperty('--lean-x', (px * 10).toFixed(2) + 'px');
            img.style.setProperty('--lean-y', (py * 8).toFixed(2) + 'px');
            img.style.setProperty('--lean-r', (px * 5).toFixed(2) + 'deg');
        },

        leaveLean(e) {
            const img = e.currentTarget.querySelector('.chapter-team__photo img');
            if (!img) {
                return;
            }
            img.style.setProperty('--lean-x', '0px');
            img.style.setProperty('--lean-y', '0px');
            img.style.setProperty('--lean-r', '0deg');
        },
    }));
});
