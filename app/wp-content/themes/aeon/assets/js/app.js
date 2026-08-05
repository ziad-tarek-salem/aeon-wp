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
	}

	/* ---------- Section links ---------- */
	/**
	 * The one-page nav scrolls without ever writing to the address bar: whichever
	 * section you jump to, the URL stays the plain page URL with no "#section"
	 * fragment. The hrefs stay real URLs so the menu still works with JS off and
	 * "open in new tab" still lands on the right section.
	 *
	 * Kept out of initSmoothScroll() on purpose — that one bails when Lenis is
	 * missing or reduced motion is on, and the browser's own hash jump would then
	 * put the fragment straight back into the URL.
	 */
	// --header-h is the theme's own figure for the bar, already used for the hero
	// and page-banner offsets. Measuring the element instead would read its tall
	// unscrolled height (76px) even though it has shrunk to ~64px by the time the
	// scroll lands, leaving a visible gap above the section.
	function sectionOffset() {
		var raw = getComputedStyle(document.documentElement).getPropertyValue('--header-h');
		var h = parseInt(raw, 10);
		if (isNaN(h)) {
			var header = $('[data-header]');
			h = header ? header.offsetHeight : 64;
		}
		return -(h + 12);
	}

	function sectionTop(target) {
		var top = window.scrollY || document.documentElement.scrollTop;
		return Math.max(0, target.getBoundingClientRect().top + top + sectionOffset());
	}

	function scrollToSection(target) {
		if (lenis) {
			lenis.scrollTo(target, { offset: sectionOffset() });
			return;
		}
		var y = sectionTop(target);
		if (reduceMotion) window.scrollTo(0, y);
		else window.scrollTo({ top: y, behavior: 'smooth' });
	}

	/**
	 * Land on a section with no animation, for a hash arriving from another page.
	 *
	 * Lenis owns the scroll position whenever it is running, so the jump goes
	 * through it; scrolling the window directly here would only be undone on its
	 * next tick. The native branch matters under reduced motion, where there is no
	 * Lenis and the browser's own fragment jump lands the section under the fixed
	 * header — clearing the hash then leaves nothing to correct it.
	 */
	function jumpToSection(target) {
		if (lenis) {
			lenis.scrollTo(target, { offset: sectionOffset(), immediate: true });
			return;
		}
		window.scrollTo(0, sectionTop(target));
	}

	function stripHash() {
		if (!window.history || !history.replaceState) return;
		history.replaceState(null, '', window.location.pathname + window.location.search);
	}

	function initSectionLinks() {
		// Both bare "#id" links and the one-page menu's full URLs
		// ("https://site/#contact") when they point at a section of the page we
		// are already on.
		$$('a[href*="#"]').forEach(function (a) {
			var url;
			try { url = new URL(a.href, window.location.href); } catch (err) { return; }
			if (url.hash.length < 2) return;
			if (url.host !== window.location.host || url.pathname !== window.location.pathname) return;
			a.addEventListener('click', function (e) {
				// Leave modified clicks to the browser so new-tab/new-window work.
				if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
				var target = document.getElementById(decodeURIComponent(url.hash.slice(1)));
				if (!target) return;
				e.preventDefault();
				scrollToSection(target);
			});
		});

		// A hash arriving from elsewhere (/services/#service-web, or the menu of
		// another page) still lands on its section — then the fragment is cleared
		// so the bar reads as a plain URL from the first moment.
		if (window.location.hash.length > 1) {
			var landing = document.getElementById(window.location.hash.slice(1));
			if (landing) {
				setTimeout(function () { jumpToSection(landing); stripHash(); }, 60);
			} else {
				stripHash();
			}
		}
	}

	/* ---------- Active nav item (scroll spy) ---------- */
	/**
	 * The menu item for whichever section is on screen carries the same
	 * `current-menu-item` class WordPress prints server-side, so the underline in
	 * main.css already covers it and no second "active" style has to exist.
	 *
	 * Pairs are read off the rendered menus rather than a list kept in here: any
	 * item whose href points at a section of the page we are already on. That is
	 * the test initSectionLinks() uses for the smooth scroll, so the highlight and
	 * the scrolling can never disagree about what the menu covers. A section
	 * commented out of front-page.php drops out on its own — no element, no pair —
	 * and on the Services page, where those one-page hrefs resolve to a different
	 * path, nothing matches and the spy stays out of the way entirely.
	 *
	 * Nothing here writes to history or location: the address bar is left exactly
	 * as initSectionLinks() leaves it, section or no section.
	 */
	var CURRENT_CLASS = 'current-menu-item';

	// A click sends the page gliding past every section in between, and following
	// that with the highlight would read as flicker. So the clicked item is lit
	// straight away and the spy holds still until the glide is over — a shade
	// longer than Lenis' 1.1s, and cut short the moment a real scroll comes in.
	var SPY_HOLD_MS = 1200;

	function initScrollSpy() {
		var pairs = [];
		var sections = [];

		$$('.site-nav__menu a[href*="#"], .mobile-menu__list a[href*="#"]').forEach(function (a) {
			var url;
			try { url = new URL(a.href, window.location.href); } catch (err) { return; }
			if (url.hash.length < 2) return;
			if (url.host !== window.location.host || url.pathname !== window.location.pathname) return;
			var section = document.getElementById(decodeURIComponent(url.hash.slice(1)));
			if (!section) return;
			pairs.push({ link: a, item: a.closest('li') || a, section: section });
			if (sections.indexOf(section) === -1) sections.push(section);
		});
		if (!sections.length) return;

		// Document order, not measured tops: the page is still laying itself out at
		// boot, and the sections are siblings in <main>, so source order is the
		// order they are scrolled through.
		sections.sort(function (a, b) {
			return (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING) ? -1 : 1;
		});

		var active = null;
		var holdUntil = 0;
		var ticking = false;

		function setActive(section) {
			if (section === active) return;
			active = section;
			pairs.forEach(function (p) {
				var on = p.section === section;
				p.item.classList.toggle(CURRENT_CLASS, on);
				// "true" rather than "page": every item leads to the page we are on,
				// so what is current is the item within the set, not the page. This
				// also clears the aria-current WordPress prints on the Home item once
				// the reader has scrolled past the hero.
				if (on) p.link.setAttribute('aria-current', 'true');
				else p.link.removeAttribute('aria-current');
			});
		}

		function currentSection() {
			var y = window.scrollY || document.documentElement.scrollTop;

			// A closing section shorter than the viewport can never reach the line
			// below, so the foot of the page counts as being inside it.
			if (y + window.innerHeight >= document.documentElement.scrollHeight - 2) {
				return sections[sections.length - 1];
			}

			// sectionOffset() is negative, so this is the y just below the bar —
			// the very line a clicked section is parked on. Reusing it is what makes
			// the highlight flip exactly as a section settles into place.
			var line = y - sectionOffset();
			var found = sections[0];
			for (var i = 0; i < sections.length; i++) {
				if (sections[i].getBoundingClientRect().top + y > line) break;
				found = sections[i];
			}
			return found;
		}

		function update() {
			if (ticking) return;
			ticking = true;
			requestAnimationFrame(function () {
				ticking = false;
				if (Date.now() < holdUntil) return;
				setActive(currentSection());
			});
		}

		pairs.forEach(function (p) {
			p.link.addEventListener('click', function (e) {
				// Modified clicks open a new tab and leave this page where it is, so
				// the highlight must stay put too — same bail as initSectionLinks().
				if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
				setActive(p.section);
				holdUntil = Date.now() + SPY_HOLD_MS;
			});
		});

		// Scrolling by hand outranks the hold: whatever the click was heading for,
		// the reader is now driving.
		['wheel', 'touchstart', 'keydown'].forEach(function (type) {
			window.addEventListener(type, function () { holdUntil = 0; }, { passive: true });
		});

		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		// Late images and fonts move the sections under the line; re-read once the
		// page has finished settling.
		window.addEventListener('load', update);
		setActive(currentSection());
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

	/* ---------- Welcome popup ---------- */
	// The site's only popup: logo and one button, opening by itself the first time
	// the home page is reached. Nothing opens it on click; the floating WhatsApp
	// button is a plain link that goes straight to the chat.
	//
	// Inner pages never print the markup (footer.php gates it on is_front_page),
	// so this simply finds no modal there and returns.
	//
	// Its button carries the same bare wa.me href as the floating one and is left
	// untouched by JS, so both land on WhatsApp's own chat page for the number.
	//
	// Delay is long enough for the entrance transition to run from the hidden
	// state instead of flashing in on top of first paint.
	var WELCOME_DELAY = 300;

	// Once per visit, not once per home page load: leaving for the Services page
	// and coming back should not be greeted a second time. sessionStorage is what
	// matches that — it survives navigation but dies with the tab, unlike
	// localStorage, which would still be there weeks later.
	//
	// A refresh is the deliberate exception. Reloading is how someone asks for the
	// page fresh, so the flag is dropped and the greeting returns; the Navigation
	// Timing entry is what tells a reload apart from ordinary navigation. Back and
	// forward count as navigation, so they stay quiet.
	var WELCOME_SEEN = 'aeonWelcomeSeen';

	function welcomeIsReload() {
		try {
			var entry = performance.getEntriesByType('navigation')[0];
			if (entry && entry.type) return 'reload' === entry.type;
			// Navigation Timing Level 1, for browsers without the entry above.
			return !!(performance.navigation && 1 === performance.navigation.type);
		} catch (err) {
			return false;
		}
	}

	function welcomeAlreadySeen() {
		try {
			if (welcomeIsReload()) {
				sessionStorage.removeItem(WELCOME_SEEN);
				return false;
			}
			return '1' === sessionStorage.getItem(WELCOME_SEEN);
		} catch (err) {
			// Storage blocked (private mode, cookies off). Greeting on every load is
			// the friendlier failure of the two.
			return false;
		}
	}

	function markWelcomeSeen() {
		try { sessionStorage.setItem(WELCOME_SEEN, '1'); } catch (err) {}
	}

	function initWelcome() {
		var modal = $('[data-welcome-modal]');
		if (!modal) return;
		// Left as the markup authored it — hidden, and out of the a11y tree.
		if (welcomeAlreadySeen()) return;
		var card = $('.welcome-card', modal);
		var link = $('[data-welcome-link]', modal);
		if (!card || !link) return;

		function isOpen() { return modal.classList.contains('is-open'); }

		function open() {
			markWelcomeSeen();
			modal.hidden = false;
			// Flush styles synchronously so the transition runs from the hidden
			// state. A rAF callback would do the same, but it is throttled in
			// background tabs — which would leave the card open yet invisible.
			void modal.offsetWidth;
			modal.classList.add('is-open');

			// Hold the page still. Lenis owns the scroll when it is running; the
			// body class covers reduced-motion and no-library cases. Pad for the
			// vanished scrollbar so the layout does not jump sideways.
			var bar = window.innerWidth - document.documentElement.clientWidth;
			if (bar > 0) document.body.style.paddingRight = bar + 'px';
			document.body.classList.add('welcome-lock');
			if (lenis) lenis.stop();

			link.focus();
		}

		function close() {
			if (!isOpen()) return;
			modal.classList.remove('is-open');
			document.body.classList.remove('welcome-lock');
			document.body.style.paddingRight = '';
			if (lenis) lenis.start();
			// Keep it in the DOM until the fade finishes, then take it back out
			// of the accessibility tree.
			setTimeout(function () { if (!isOpen()) modal.hidden = true; }, 500);
			// The popup opened itself, so there is no element worth restoring
			// focus to — hand it back to the document and let the page take over.
			if (document.activeElement && modal.contains(document.activeElement)) {
				document.activeElement.blur();
			}
		}

		// Backdrop and close button share the hook, so a click outside the card
		// dismisses it the way a modal should.
		$$('[data-welcome-close]', modal).forEach(function (el) {
			el.addEventListener('click', close);
		});

		// Tapping through to WhatsApp should leave a clean page behind.
		link.addEventListener('click', close);

		document.addEventListener('keydown', function (e) {
			if (!isOpen()) return;
			if (e.key === 'Escape') { close(); return; }
			if (e.key !== 'Tab') return;
			// Focus trap: the dialog is modal, so Tab must not reach the page.
			var items = $$('a[href], button:not([disabled])', card).filter(function (el) {
				return el.offsetWidth || el.offsetHeight || el.getClientRects().length;
			});
			if (!items.length) return;
			var first = items[0];
			var last = items[items.length - 1];
			if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
		});

		setTimeout(open, WELCOME_DELAY);
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
		// Tap the scrim to dismiss. Guarded by closest() so taps that land inside
		// the panel — including on its padding — don't bubble up as a close.
		menu.addEventListener('click', function (e) {
			if (!e.target.closest('[data-menu-panel]')) close();
		});
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

	/* ---------- Portfolio showcase (filter + lightbox) ---------- */
	function initShowcase() {
		var section = $('#showcase');
		if (!section) return;
		var filters = $$('.showcase__filter', section);
		var items = $$('.showcase-item', section);

		filters.forEach(function (btn) {
			btn.addEventListener('click', function () {
				filters.forEach(function (b) { b.classList.remove('is-active'); });
				btn.classList.add('is-active');
				var f = btn.getAttribute('data-filter');
				items.forEach(function (item) {
					var show = f === 'all' || item.getAttribute('data-cat') === f;
					item.classList.toggle('is-hidden', !show);
				});
				if (lenis) lenis.resize();
			});
		});

		var lb = $('[data-showcase-lightbox]', section);
		if (!lb) return;
		var lbImg = $('[data-lb-img]', lb);
		var lbCap = $('[data-lb-caption]', lb);
		var current = -1;

		function visible() { return items.filter(function (it) { return !it.classList.contains('is-hidden'); }); }
		function render(item) {
			lbImg.src = item.getAttribute('data-full');
			lbImg.alt = item.getAttribute('data-caption') || '';
			lbCap.textContent = item.getAttribute('data-caption') || '';
		}
		function open(item) {
			current = visible().indexOf(item);
			render(item);
			lb.hidden = false;
			document.body.style.overflow = 'hidden';
		}
		function close() { lb.hidden = true; document.body.style.overflow = ''; }
		function step(dir) {
			var vis = visible();
			if (!vis.length) return;
			current = (current + dir + vis.length) % vis.length;
			render(vis[current]);
		}

		items.forEach(function (item) { item.addEventListener('click', function () { open(item); }); });
		$('[data-lb-close]', lb).addEventListener('click', close);
		$('[data-lb-prev]', lb).addEventListener('click', function () { step(-1); });
		$('[data-lb-next]', lb).addEventListener('click', function () { step(1); });
		lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
		document.addEventListener('keydown', function (e) {
			if (lb.hidden) return;
			if (e.key === 'Escape') { close(); }
			else if (e.key === 'ArrowRight') { step(1); }
			else if (e.key === 'ArrowLeft') { step(-1); }
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

	/* ---------- Contact form → the visitor's own mail app ---------- */
	// Submitting composes the inquiry and hands it to whatever client the device
	// registers for mailto:, so the site itself never sends mail — no SMTP, no
	// relay, no DNS records. The AJAX POST still fires so the lead is recorded in
	// wp-admin even when the mail app never opens, or opens and is never sent.
	function initContactForm() {
		var forms = $$('[data-contact-form]');
		if (!forms.length || typeof window.AEON === 'undefined') return;

		function val(form, field) {
			return form.elements[field] ? form.elements[field].value.trim() : '';
		}

		// Blank optional fields are dropped rather than printed empty, so an
		// inquiry with no service picked does not arrive with a dangling label.
		function buildBody(rows, messageLabel, message) {
			var lines = [];
			rows.forEach(function (row) {
				if (row.value) { lines.push(row.label + ': ' + row.value); }
			});
			// mailto: expects CRLF between lines.
			return lines.join('\r\n') + '\r\n\r\n' + messageLabel + ':\r\n' + message;
		}

		forms.forEach(function (form) {
			var status = $('[data-form-status]', form);
			var done = $('[data-form-done]', form);
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				status.textContent = '';
				status.className = 'form-status';
				// Clear last time's tick: a submission that now fails validation
				// must not leave a success mark sitting under the error.
				if (done) { done.classList.remove('is-visible'); }

				// Use form.elements: form.name would return the form's own name property.
				var name = val(form, 'name');
				var message = val(form, 'message');
				var service = val(form, 'service');

				// Honeypot before anything else: bots get silence, not a mail app.
				if (val(form, 'website')) { return; }

				if (!name || !message) {
					status.textContent = window.AEON.i18n.required;
					status.classList.add('is-error');
					return;
				}

				var t = window.AEON.i18n;
				var subject = service ? t.mailSubject.replace('%s', service) : t.mailSubjectGeneral;
				var body = buildBody([
					{ label: t.labelName, value: name },
					{ label: t.labelService, value: service }
				], t.labelMessage, message);

				// Record the lead, but do not await it: the mailto: handoff has to
				// happen inside the user gesture or Safari blocks the navigation.
				var data = new FormData(form);
				data.append('action', 'aeon_contact');
				data.append('nonce', window.AEON.nonce);
				fetch(window.AEON.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
					.catch(function () { /* the mail app is the delivery path; a lost record is not worth alarming anyone over */ });

				window.location.href = 'mailto:' + window.AEON.leadEmail +
					'?subject=' + encodeURIComponent(subject) +
					'&body=' + encodeURIComponent(body);

				// Deliberately not calling form.reset(): a device with no mailto:
				// handler shows nothing at all, and clearing the fields would throw
				// away everything the visitor just typed.

				/* --- Post-submit confirmation message: disabled on request ---
				 * Uncomment the two lines below to bring it back. Nothing it needs
				 * was removed: `mailOpened` and `mailFallback` are still localized
				 * in inc/enqueue.php, their strings still live in inc/i18n.php, and
				 * `leadEmail` is still there because the mailto: above uses it.
				 *
				 * The <p data-form-status> element stays in contact.php on purpose.
				 * The required-fields branch above still writes its error into it,
				 * and taking it out would leave `status` null and throw on the first
				 * line of this handler. Its min-height also reserves the space, so
				 * leaving it empty costs no layout shift.
				 */
				// status.textContent = t.mailOpened + ' ' + t.mailFallback + ' ' + window.AEON.leadEmail;
				// status.classList.add('is-success');

				// The tick stands in for that message. Dropping the class and
				// re-adding it either side of a forced reflow is what makes the
				// pop-in replay on a second submission — toggling it twice in the
				// same tick would otherwise go unnoticed and the mark would just
				// sit there.
				if (done) {
					done.classList.remove('is-visible');
					void done.offsetWidth;
					done.classList.add('is-visible');
				}
			});
		});
	}

	// Reel strip on the Services page: starting one reel stops whichever was
	// already running, so a visitor clicking down the row never ends up with two
	// soundtracks at once. Listening on the document (capture, since `play` does
	// not bubble) covers every player without a listener each.
	function initReels() {
		var reels = $$('.svc-work__shot--video video');
		if (!reels.length) { return; }

		document.addEventListener('play', function (e) {
			if (!e.target || 'VIDEO' !== e.target.tagName) { return; }
			reels.forEach(function (other) {
				if (other !== e.target && !other.paused) { other.pause(); }
			});
		}, true);
	}

	/* ---------- Boot ---------- */
	function boot() {
		if (hasGSAP && window.ScrollTrigger) { window.gsap.registerPlugin(window.ScrollTrigger); }
		initSmoothScroll();
		initSectionLinks();
		initScrollSpy();
		initHeader();
		initWelcome();
		initMobileMenu();
		initReveal();
		initHero();
		initCounters();
		initSwipers();
		initWorkFilters();
		initShowcase();
		initReels();
		initMagnetic();
		initContactForm();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
