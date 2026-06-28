/* ─────────────────────────────────────────────────────────────────────────
   Creative Trees Group — motion system
   Smooth scroll (Lenis) · refined scroll reveals (blur-in) · drifting canvas
   character-field · text scramble (code → letters) · magnetic buttons · cursor.
   Stable data-attribute API. Degrades gracefully; respects reduced-motion.
   ───────────────────────────────────────────────────────────────────────── */
import './echo';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';
import focus from '@alpinejs/focus';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';

const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const coarse = window.matchMedia('(pointer: coarse)').matches;

/* ── Alpine ─────────────────────────────────────────────────────────────── */
Alpine.plugin(collapse);
Alpine.plugin(intersect);
Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();

/* ── Smooth scroll + GSAP wiring ────────────────────────────────────────── */
gsap.registerPlugin(ScrollTrigger);
let lenis = null;

function initSmoothScroll() {
    if (reduce || coarse) return;
    lenis = new Lenis({
        duration: 1.15,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
    });
    document.documentElement.classList.add('lenis', 'lenis-smooth');
    window.lenis = lenis;
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href');
            if (id.length < 2) return;
            const target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            lenis.scrollTo(target, { offset: -80 });
            target.setAttribute("tabindex", "-1");
            target.focus({ preventScroll: true });
        });
    });
}

/* ── Scroll reveals + stagger (blur-in) ─────────────────────────────────── */
function initReveals() {
    if (reduce) return;

    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        gsap.to(el, {
            opacity: 1,
            y: 0,
            filter: 'blur(0px)',
            duration: 1.1,
            ease: 'expo.out',
            delay: parseFloat(el.dataset.revealDelay || 0),
            scrollTrigger: { trigger: el, start: 'top 90%', once: true },
        });
    });

    gsap.utils.toArray('[data-stagger]').forEach((group) => {
        const items = group.querySelectorAll('[data-stagger-item]');
        gsap.set(items, { opacity: 0, y: 24, filter: 'blur(6px)' });
        gsap.to(items, {
            opacity: 1,
            y: 0,
            filter: 'blur(0px)',
            duration: 0.95,
            ease: 'expo.out',
            stagger: 0.075,
            scrollTrigger: { trigger: group, start: 'top 84%', once: true },
        });
    });

    gsap.utils.toArray('[data-parallax]').forEach((el) => {
        const speed = parseFloat(el.dataset.parallax || 0.15);
        gsap.to(el, {
            yPercent: -speed * 100,
            ease: 'none',
            scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });
}

/* ── Text scramble / decode (code → letters) ────────────────────────────── */
const GLYPHS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/<>#*+=';
const codeify = (text, glyphs = GLYPHS) => text.replace(/\S/g, () => glyphs[(Math.random() * glyphs.length) | 0]);

function scramble(el, finalText, duration = 900, glyphs = GLYPHS) {
    const start = performance.now();
    const step = (now) => {
        const p = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - p, 3);
        const revealed = Math.floor(eased * finalText.length);
        let out = '';
        for (let i = 0; i < finalText.length; i++) {
            const ch = finalText[i];
            if (ch === ' ' || ch === '\n') { out += ch; continue; }
            out += i < revealed ? ch : glyphs[(Math.random() * glyphs.length) | 0];
        }
        el.textContent = out;
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = finalText;
    };
    requestAnimationFrame(step);
}

function initScramble() {
    if (reduce) return; // reduced motion: leave the real text as-is

    document.querySelectorAll('[data-scramble]').forEach((el) => {
        const finalText = el.textContent;
        const dur = parseInt(el.dataset.scrambleDuration || 850);
        const delay = parseInt(el.dataset.scrambleDelay || 0);
        const glyphs = el.hasAttribute('data-scramble-binary') ? '01' : GLYPHS;

        // Show code immediately (no flash of the real text), then resolve on enter.
        el.textContent = codeify(finalText, glyphs);

        ScrollTrigger.create({
            trigger: el,
            start: 'top 95%',
            once: true,
            onEnter: () => {
                if (delay > 0) setTimeout(() => scramble(el, finalText, dur, glyphs), delay);
                else scramble(el, finalText, dur, glyphs);
            },
        });
    });
}

/* ── Magnetic buttons ───────────────────────────────────────────────────── */
function initMagnetic() {
    if (reduce || coarse) return;
    document.querySelectorAll('[data-magnetic]').forEach((el) => {
        const strength = parseFloat(el.dataset.magnetic || 0.3);
        el.addEventListener('mousemove', (e) => {
            const r = el.getBoundingClientRect();
            gsap.to(el, {
                x: (e.clientX - (r.left + r.width / 2)) * strength,
                y: (e.clientY - (r.top + r.height / 2)) * strength,
                duration: 0.6,
                ease: 'power3.out',
            });
        });
        el.addEventListener('mouseleave', () => {
            gsap.to(el, { x: 0, y: 0, duration: 0.7, ease: 'elastic.out(1, 0.45)' });
        });
    });
}

/* ── Character-field signature (drifts DOWN, fast, alive, soft) ──────────── */
const FIELD_GLYPHS = '01<>/[]{}()=+*#%·—|:;.!?\\';

function initCharField() {
    document.querySelectorAll('[data-charfield]').forEach((host) => {
        const canvas = document.createElement('canvas');
        canvas.setAttribute('aria-hidden', 'true');
        host.replaceChildren(canvas);
        const ctx = canvas.getContext('2d');
        const buffer = document.createElement('canvas');
        const bctx = buffer.getContext('2d');

        const colW = 10, lineH = 18, fontSize = 13;
        let w = 0, h = 0, cols = 0, rows = 0;

        const rnd = () => FIELD_GLYPHS[(Math.random() * FIELD_GLYPHS.length) | 0];
        const alpha = () => 0.05 + Math.random() * 0.075;

        function drawCell(cx, cy) {
            bctx.clearRect(cx * colW, cy * lineH, colW, lineH);
            bctx.fillStyle = `rgba(10, 10, 10, ${alpha()})`;
            bctx.fillText(rnd(), cx * colW, cy * lineH);
        }

        function renderBuffer() {
            bctx.clearRect(0, 0, w, h);
            bctx.font = `${fontSize}px ui-monospace, 'SFMono-Regular', monospace`;
            bctx.textBaseline = 'top';
            for (let y = 0; y < rows; y++) {
                for (let x = 0; x < cols; x++) drawCell(x, y);
            }
        }

        function layout() {
            const r = host.getBoundingClientRect();
            w = Math.max(1, Math.round(r.width));
            h = Math.max(1, Math.round(r.height));
            cols = Math.ceil(w / colW) + 1;
            rows = Math.ceil(h / lineH) + 1;
            canvas.width = w;
            canvas.height = h;
            buffer.width = w;
            buffer.height = h;
            renderBuffer();
        }

        layout();
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(layout, 200);
        });

        const blit = (offset) => {
            ctx.clearRect(0, 0, w, h);
            ctx.drawImage(buffer, 0, offset);
            ctx.drawImage(buffer, 0, offset - h);
        };

        if (reduce) { blit(0); return; }

        let raf = null, offset = 0, frame = 0, visible = true;
        const speed = 1.4; // px per frame → faster downward drift

        const draw = () => {
            offset += speed;
            if (offset >= h) offset -= h;
            blit(offset);
            if ((frame++ % 4) === 0) {
                for (let i = 0; i < 7; i++) drawCell((Math.random() * cols) | 0, (Math.random() * rows) | 0);
            }
            raf = requestAnimationFrame(draw);
        };
        const play = () => { if (!raf && visible) raf = requestAnimationFrame(draw); };
        const stop = () => { if (raf) { cancelAnimationFrame(raf); raf = null; } };

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(([entry]) => {
                visible = entry.isIntersecting;
                visible ? play() : stop();
            }).observe(host);
        } else {
            play();
        }
    });
}

/* ── Custom cursor (desktop) ────────────────────────────────────────────── */
function initCursor() {
    if (reduce || coarse) return;
    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    document.body.appendChild(dot);
    document.documentElement.classList.add('has-cursor');

    let x = innerWidth / 2, y = innerHeight / 2, cx = x, cy = y;
    addEventListener('mousemove', (e) => { x = e.clientX; y = e.clientY; });
    const render = () => {
        cx += (x - cx) * 0.22;
        cy += (y - cy) * 0.22;
        dot.style.transform = `translate(${cx}px, ${cy}px)`;
        requestAnimationFrame(render);
    };
    render();

    const grow = () => dot.classList.add('is-grow');
    const shrink = () => dot.classList.remove('is-grow');
    document.querySelectorAll('a, button, [data-magnetic], [data-cursor-grow]').forEach((el) => {
        el.addEventListener('mouseenter', grow);
        el.addEventListener('mouseleave', shrink);
    });
}

/* ── Boot ───────────────────────────────────────────────────────────────── */
let bootOk = false;

function safe(fn) {
    try { fn(); } catch (e) { console.warn('[ctg motion]', fn.name, e); }
}

function boot() {
    document.documentElement.classList.add('is-ready');
    safe(initSmoothScroll);
    safe(initReveals);
    safe(initScramble);
    safe(initCounters);
    safe(initCarousels);
    safe(initPricingCarousel);
    safe(initBinaryText);
    safe(initMagnetic);
    safe(initCharField);
    safe(initCursor);
    try {
        ScrollTrigger.refresh();
        bootOk = true;
    } catch (e) {
        bootOk = false;
    }

    setTimeout(() => {
        document.querySelectorAll('[data-reveal], [data-stagger-item]').forEach((el) => {
            const r = el.getBoundingClientRect();
            const inView = r.top < window.innerHeight && r.bottom > 0;
            if ((inView || !bootOk) && getComputedStyle(el).opacity === '0') {
                gsap.set(el, { opacity: 1, y: 0, filter: 'blur(0px)' });
            }
        });
    }, 1500);
}

if (document.readyState !== 'loading') boot();
else document.addEventListener('DOMContentLoaded', boot);

/* ── Animated number counters (stat bands) ──────────────────────────────── */
function initCounters() {
    if (reduce) return;
    document.querySelectorAll('[data-count]').forEach((el) => {
        const finalText = el.textContent.trim();
        const m = finalText.match(/^(\D*)(\d[\d,]*(?:\.\d+)?)(.*)$/s);
        if (!m) return;
        const prefix = m[1];
        const numStr = m[2].replace(/,/g, '');
        const suffix = m[3];
        const target = parseFloat(numStr);
        const decimals = (numStr.split('.')[1] || '').length;
        const grouped = m[2].includes(',');
        const fmt = (n) => {
            let s = decimals ? n.toFixed(decimals) : String(Math.round(n));
            if (grouped) s = Number(s).toLocaleString('en-US');
            return prefix + s + suffix;
        };
        el.textContent = fmt(0);
        ScrollTrigger.create({
            trigger: el,
            start: 'top 92%',
            once: true,
            onEnter: () => {
                const start = performance.now();
                const dur = 1700;
                const tick = (now) => {
                    const p = Math.min(1, (now - start) / dur);
                    const eased = 1 - Math.pow(1 - p, 4);
                    el.textContent = fmt(target * eased);
                    if (p < 1) requestAnimationFrame(tick);
                    else el.textContent = finalText;
                };
                requestAnimationFrame(tick);
            },
        });
    });
}

/* ── Testimonials carousel (Swiper — autoplay, loop, responsive) ─────────── */
function initCarousels() {
    document.querySelectorAll('.testimonials-swiper').forEach((el) => {
        const slides = el.querySelectorAll('.swiper-slide').length;
        new Swiper(el, {
            modules: [Autoplay, Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 24,
            grabCursor: true,
            speed: 750,
            loop: slides > 3,
            autoplay: reduce ? false : {
                delay: 5500,
                disableOnInteraction: true,
                pauseOnMouseEnter: true,
            },
            navigation: { prevEl: '.t-prev', nextEl: '.t-next' },
            pagination: { el: '.t-pagination', clickable: true },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
        });
    });
}

/* ── Binary wordmark — fill letters with a tiling field of 0/1 ───────────── */
function initBinaryText() {
    const els = document.querySelectorAll('[data-binary-text]');
    if (!els.length) return;

    const size = 160, cw = 8, ch = 10;
    const tile = document.createElement('canvas');
    tile.width = size;
    tile.height = size;
    const tctx = tile.getContext('2d');
    tctx.font = '9px ui-monospace, "SFMono-Regular", monospace';
    tctx.textBaseline = 'top';
    tctx.fillStyle = '#3f3f3f';
    for (let y = 0; y < size; y += ch) {
        for (let x = 0; x < size; x += cw) {
            tctx.fillText(Math.random() < 0.5 ? '0' : '1', x, y);
        }
    }
    const url = tile.toDataURL();

    els.forEach((el) => {
        el.style.backgroundImage = `url(${url})`;
        el.classList.add('is-binary');
    });
}

/* ── Pricing tiers carousel (Swiper — manual, scales to many tiers) ──────── */
function initPricingCarousel() {
    document.querySelectorAll('.pricing-swiper').forEach((el) => {
        const slides = el.querySelectorAll('.swiper-slide').length;
        new Swiper(el, {
            modules: [Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 24,
            grabCursor: true,
            speed: 600,
            loop: slides > 3,
            navigation: { prevEl: '.p-prev', nextEl: '.p-next' },
            pagination: { el: '.p-pagination', clickable: true },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
        });
    });
}
