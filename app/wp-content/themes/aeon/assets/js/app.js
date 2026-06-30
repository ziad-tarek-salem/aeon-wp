/* =================================================================
   AEON Digital Marketing — front-end interactions.
   All motion respects prefers-reduced-motion.
   ================================================================= */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var hasGSAP = typeof window.gsap !== 'undefined';
	var $ = function (sel, ctx) { return (ctx || document).querySelector(sel); };
	var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

	/* ---------- Smooth scroll (Lenis) ---------- */
	var lenis = null;
	function initSmoothScroll() {
		if (reduceMotion || typeof window.Lenis === 'undefined') return;
		lenis = new window.Lenis({ duration: 1.1, smoothWheel: true });
		function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
		requestAnimationFrame(raf);
		if (hasGSAP && window.ScrollTrigger) {
			lenis.on('scroll', window.ScrollTrigger.update);
		}
		// Anchor links go through Lenis.
		$$('a[href^="#"]').forEach(function (a) {
			var id = a.getAttribute('href');
			if (id.length < 2) return;
			a.addEventListener('click', function (e) {
				var target = document.getElementById(id.slice(1));
				if (target) { e.preventDefault(); lenis.scrollTo(target, { offset: -80 }); }
			});
		});
	}

	/* ---------- Header on scroll + progress bar ---------- */
	function initHeader() {
		var header = $('[data-header]');
		var bar = $('.aeon-progress span');
		function onScroll() {
			var y = window.scrollY || document.documentElement.scrollTop;
			if (header) header.classList.toggle('is-scrolled', y > 30);
			if (bar) {
				var h = document.documentElement.scrollHeight - window.innerHeight;
				bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
			}
		}
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* ---------- Mobile menu ---------- */
	function initMobileMenu() {
		var toggle = $('[data-nav-toggle]');
		var menu = $('[data-mobile-menu]');
		if (!toggle || !menu) return;
		function close() {
			menu.classList.remove('is-open');
			menu.setAttribute('aria-hidden', 'true');
			toggle.setAttribute('aria-expanded', 'false');
			document.body.style.overflow = '';
		}
		toggle.addEventListener('click', function () {
			var open = menu.classList.toggle('is-open');
			menu.setAttribute('aria-hidden', open ? 'false' : 'true');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.style.overflow = open ? 'hidden' : '';
		});
		$$('a', menu).forEach(function (a) { a.addEventListener('click', close); });
		document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
	}

	/* ---------- Reveal on scroll ---------- */
	function initReveal() {
		var els = $$('[data-reveal]');
		if (!els.length) return;
		if (reduceMotion || !('IntersectionObserver' in window)) {
			els.forEach(function (el) { el.classList.add('is-visible'); });
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
		els.forEach(function (el) { io.observe(el); });
	}

	/* ---------- Hero intro timeline ---------- */
	function initHero() {
		var hero = $('[data-hero]');
		if (!hero) return;
		var items = $$('[data-hero-el]', hero);
		if (reduceMotion || !hasGSAP) {
			items.forEach(function (el) { el.style.opacity = 1; });
			return;
		}
		window.gsap.set(items, { opacity: 0, y: 36 });
		window.gsap.to(items, { opacity: 1, y: 0, duration: 0.9, ease: 'power3.out', stagger: 0.12, delay: 0.35 });

		// Subtle parallax on orbs.
		if (window.ScrollTrigger) {
			$$('.hero__orb', hero).forEach(function (orb, i) {
				window.gsap.to(orb, {
					yPercent: i % 2 === 0 ? 30 : -24,
					ease: 'none',
					scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true }
				});
			});
		}
	}

	/* ---------- Count-up stats ---------- */
	function animateCount(el) {
		var target = parseFloat(el.getAttribute('data-count')) || 0;
		var suffix = el.getAttribute('data-suffix') || '';
		if (reduceMotion) { el.textContent = target + suffix; return; }
		var start = null, dur = 1800;
		function step(ts) {
			if (!start) start = ts;
			var p = Math.min((ts - start) / dur, 1);
			var eased = 1 - Math.pow(1 - p, 3);
			el.textContent = Math.round(target * eased) + suffix;
			if (p < 1) requestAnimationFrame(step);
		}
		requestAnimationFrame(step);
	}
	function initCounters() {
		var nums = $$('[data-count]');
		if (!nums.length) return;
		if (!('IntersectionObserver' in window)) { nums.forEach(animateCount); return; }
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) { animateCount(entry.target); io.unobserve(entry.target); }
			});
		}, { threshold: 0.6 });
		nums.forEach(function (n) { io.observe(n); });
	}

	/* ---------- Swiper (testimonials) ---------- */
	function initSwipers() {
		if (typeof window.Swiper === 'undefined') return;
		$$('[data-swiper="testimonials"]').forEach(function (el) {
			var slider = el.closest('.testimonials__slider') || el.parentNode;
			var count = el.querySelectorAll('.swiper-slide').length;
			// Loop needs enough slides to fill the centred view without gaps.
			var canLoop = count > 3;
			new window.Swiper(el, {
				slidesPerView: 'auto',
				centeredSlides: true,
				spaceBetween: 28,
				grabCursor: true,
				loop: canLoop,
				speed: 600,
				watchSlidesProgress: true,
				autoplay: reduceMotion ? false : { delay: 5500, disableOnInteraction: false },
				pagination: {
					el: slider.querySelector('.swiper-pagination'),
					clickable: true
				},
				navigation: {
					nextEl: slider.querySelector('.testimonials__nav--next'),
					prevEl: slider.querySelector('.testimonials__nav--prev')
				}
			});
		});
	}

	/* ---------- Portfolio filters ---------- */
	function initWorkFilters() {
		var wrap = $('[data-work-filters]');
		var grid = $('[data-work-grid]');
		if (!wrap || !grid) return;
		var cards = $$('.work-card', grid);
		$$('.work-filter', wrap).forEach(function (btn) {
			btn.addEventListener('click', function () {
				$$('.work-filter', wrap).forEach(function (b) { b.classList.remove('is-active'); });
				btn.classList.add('is-active');
				var f = btn.getAttribute('data-filter');
				cards.forEach(function (card) {
					var show = f === '*' || card.classList.contains(f);
					card.classList.toggle('is-hidden', !show);
				});
				if (lenis) lenis.resize();
			});
		});
	}

	/* ---------- Magnetic buttons ---------- */
	function initMagnetic() {
		if (reduceMotion || window.matchMedia('(pointer: coarse)').matches) return;
		$$('.magnetic').forEach(function (el) {
			el.addEventListener('mousemove', function (e) {
				var r = el.getBoundingClientRect();
				var x = e.clientX - r.left - r.width / 2;
				var y = e.clientY - r.top - r.height / 2;
				el.style.transform = 'translate(' + x * 0.18 + 'px,' + y * 0.28 + 'px)';
			});
			el.addEventListener('mouseleave', function () { el.style.transform = ''; });
		});
	}

	/* ---------- Contact form (AJAX) ---------- */
	function initContactForm() {
		var forms = $$('[data-contact-form]');
		if (!forms.length || typeof window.AEON === 'undefined') return;
		forms.forEach(function (form) {
			var status = $('[data-form-status]', form);
			var btn = form.querySelector('button[type="submit"]');
			var label = btn ? btn.querySelector('.btn__label') : null;
			var original = label ? label.textContent : '';
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				status.textContent = '';
				status.className = 'form-status';

				// Use form.elements: form.name would return the form's own name property.
				var name = form.elements['name'].value.trim();
				var email = form.elements['email'].value.trim();
				var message = form.elements['message'].value.trim();
				if (!name || !email || !message) {
					status.textContent = window.AEON.i18n.required;
					status.classList.add('is-error');
					return;
				}

				var data = new FormData(form);
				data.append('action', 'aeon_contact');
				data.append('nonce', window.AEON.nonce);

				if (btn) { btn.classList.add('is-loading'); }
				if (label) { label.textContent = window.AEON.i18n.sending; }

				fetch(window.AEON.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
					.then(function (r) { return r.json(); })
					.then(function (res) {
						if (res && res.success) {
							status.textContent = (res.data && res.data.message) || window.AEON.i18n.success;
							status.classList.add('is-success');
							form.reset();
						} else {
							status.textContent = (res && res.data && res.data.message) || window.AEON.i18n.error;
							status.classList.add('is-error');
						}
					})
					.catch(function () {
						status.textContent = window.AEON.i18n.error;
						status.classList.add('is-error');
					})
					.finally(function () {
						if (btn) { btn.classList.remove('is-loading'); }
						if (label) { label.textContent = original; }
					});
			});
		});
	}

	/* ---------- Boot ---------- */
	function boot() {
		if (hasGSAP && window.ScrollTrigger) { window.gsap.registerPlugin(window.ScrollTrigger); }
		initSmoothScroll();
		initHeader();
		initMobileMenu();
		initReveal();
		initHero();
		initCounters();
		initSwipers();
		initWorkFilters();
		initMagnetic();
		initContactForm();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
