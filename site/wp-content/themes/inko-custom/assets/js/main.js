/**
 * INKO Melbourne Custom — front-end behaviour (no dependencies).
 *
 * Every feature checks for its markup first and degrades gracefully:
 * without JS the page is a normal vertical document with everything visible.
 */
(function () {
	'use strict';

	var doc = document.documentElement;
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');

	function $(sel, ctx) { return (ctx || document).querySelector(sel); }
	function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }
	function clamp(v, min, max) { return Math.min(max, Math.max(min, v)); }

	/* ------------------------------------------------------------------
	 * Page loaded → hero entrance
	 * ---------------------------------------------------------------- */
	function initEntrance() {
		var done = false;
		function go() {
			if (done) { return; }
			done = true;
			requestAnimationFrame(function () { doc.classList.add('is-loaded'); });
		}
		var hero = $('.hero-slide.is-active img') || $('.hero__img');
		if (hero && !hero.complete) {
			hero.addEventListener('load', go, { once: true });
			hero.addEventListener('error', go, { once: true });
			setTimeout(go, 1200);
		} else {
			go();
		}
	}

	/* ------------------------------------------------------------------
	 * Header state
	 * ---------------------------------------------------------------- */
	function initHeader() {
		var header = $('#site-header');
		if (!header) { return; }
		var last = null;
		function update() {
			var scrolled = window.scrollY > 40;
			if (scrolled !== last) {
				header.classList.toggle('is-scrolled', scrolled);
				last = scrolled;
			}
		}
		window.addEventListener('scroll', update, { passive: true });
		update();
	}

	/* ------------------------------------------------------------------
	 * Overlay menu (focus-trapped dialog pattern)
	 * ---------------------------------------------------------------- */
	function initMenu() {
		var toggle = $('.menu-toggle');
		var overlay = $('#menu-overlay');
		if (!toggle || !overlay) { return; }

		$$('.menu-overlay__nav li', overlay).forEach(function (li, i) { li.style.setProperty('--n', i); });

		function focusables() {
			return $$('a[href], button:not([disabled])', overlay).concat([toggle]);
		}
		function open() {
			overlay.hidden = false;
			requestAnimationFrame(function () { overlay.classList.add('is-open'); });
			toggle.setAttribute('aria-expanded', 'true');
			document.body.classList.add('menu-open');
			document.documentElement.classList.add('menu-open'); // lock background scroll
			var first = $('a', overlay);
			if (first) { setTimeout(function () { first.focus(); }, 60); }
		}
		function close(returnFocus) {
			overlay.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('menu-open');
			document.documentElement.classList.remove('menu-open');
			setTimeout(function () { if (!overlay.classList.contains('is-open')) { overlay.hidden = true; } }, 460);
			if (returnFocus) { toggle.focus(); }
		}
		toggle.addEventListener('click', function () {
			if (toggle.getAttribute('aria-expanded') === 'true') { close(true); } else { open(); }
		});
		overlay.addEventListener('click', function (e) {
			if (e.target.closest('a')) { close(false); }
		});
		document.addEventListener('keydown', function (e) {
			if (toggle.getAttribute('aria-expanded') !== 'true') { return; }
			if (e.key === 'Escape') { close(true); return; }
			if (e.key === 'Tab') {
				var f = focusables();
				var i = f.indexOf(document.activeElement);
				if (e.shiftKey && (i <= 0)) { e.preventDefault(); f[f.length - 1].focus(); }
				else if (!e.shiftKey && i === f.length - 1) { e.preventDefault(); f[0].focus(); }
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Hero image slider (Slide 01)
	 * ---------------------------------------------------------------- */
	function initHeroSlider() {
		var root = $('[data-hero-slider]');
		if (!root) { return; }
		var hero = root.closest('.slide') || root;
		var slides = $$('[data-hero-slide]', root);
		var dots = $$('[data-hero-dot]', hero);
		if (slides.length < 2) { return; }

		var index = 0;
		var timer = null;
		var stopped = reduceMotion.matches; // no autoplay for reduced-motion users
		var paused = false;
		var visible = true;
		var DELAY = 7000;

		function go(i) {
			index = (i + slides.length) % slides.length;
			slides.forEach(function (s, n) {
				var on = n === index;
				s.classList.toggle('is-active', on);
				if (on) { s.removeAttribute('aria-hidden'); } else { s.setAttribute('aria-hidden', 'true'); }
			});
			dots.forEach(function (d, n) {
				d.classList.toggle('is-active', n === index);
				if (n === index) { d.setAttribute('aria-current', 'true'); } else { d.removeAttribute('aria-current'); }
			});
		}
		function schedule() {
			clearTimeout(timer);
			if (stopped || paused || !visible || document.hidden) { return; }
			timer = setTimeout(function () { go(index + 1); schedule(); }, DELAY);
		}
		function user(i) { stopped = true; clearTimeout(timer); go(i); }

		var prev = $('[data-hero-prev]', hero);
		var next = $('[data-hero-next]', hero);
		if (prev) { prev.addEventListener('click', function () { user(index - 1); }); }
		if (next) { next.addEventListener('click', function () { user(index + 1); }); }
		dots.forEach(function (d) {
			d.addEventListener('click', function () { user(parseInt(d.getAttribute('data-hero-dot'), 10) || 0); });
		});

		// Keyboard: arrow keys while focus is inside the hero controls.
		hero.addEventListener('keydown', function (e) {
			if (!e.target.closest('[data-hero-prev], [data-hero-next], [data-hero-dot]')) { return; }
			if (e.key === 'ArrowLeft') { e.preventDefault(); user(index - 1); }
			if (e.key === 'ArrowRight') { e.preventDefault(); user(index + 1); }
		});

		// Touch swipe (horizontal only, so vertical page scrolling is unaffected).
		var sx = null;
		var sy = null;
		hero.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
		hero.addEventListener('touchend', function (e) {
			if (sx === null) { return; }
			var dx = e.changedTouches[0].clientX - sx;
			var dy = e.changedTouches[0].clientY - sy;
			if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) { user(index + (dx < 0 ? 1 : -1)); }
			sx = sy = null;
		}, { passive: true });

		// Pause on hover / keyboard focus, when the tab is hidden or the hero is off screen.
		hero.addEventListener('mouseenter', function () { paused = true; schedule(); });
		hero.addEventListener('mouseleave', function () { paused = false; schedule(); });
		hero.addEventListener('focusin', function () { paused = true; schedule(); });
		hero.addEventListener('focusout', function () { paused = false; schedule(); });
		document.addEventListener('visibilitychange', schedule);
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) { visible = en[0].isIntersecting; schedule(); }, { threshold: 0.3 }).observe(hero);
		}

		go(0);
		schedule();
	}

	/* ------------------------------------------------------------------
	 * Layered stack
	 *  - slides taller than the viewport pin at their bottom edge (--stick)
	 *  - the slide being covered dims/recedes (--cover 0→1)
	 *  - anchor links + focus use the slides' natural (unstuck) offsets
	 * ---------------------------------------------------------------- */
	var stack = { slides: [], starts: [], heights: [] };

	function measureStack() {
		var main = $('.stack');
		if (!main) { return; }
		var vh = window.innerHeight;
		var top = main.getBoundingClientRect().top + window.scrollY;
		stack.starts = [];
		stack.heights = [];
		stack.slides.forEach(function (s) {
			var h = s.offsetHeight;
			stack.starts.push(top);
			stack.heights.push(h);
			s.style.setProperty('--stick', Math.min(0, vh - h) + 'px');
			top += h;
		});
	}

	function slideStart(id) {
		for (var i = 0; i < stack.slides.length; i++) {
			if (stack.slides[i].id === id) { return stack.starts[i]; }
		}
		return null;
	}

	function scrollToSlide(id, smooth) {
		var y = slideStart(id);
		if (y === null) { return false; }
		window.scrollTo({ top: y, behavior: smooth && !reduceMotion.matches ? 'smooth' : 'instant' });
		return true;
	}

	function initStack() {
		var main = $('.stack');
		if (!main) { return; }
		stack.slides = $$('.stack > .slide');
		if (!stack.slides.length) { return; }

		measureStack();
		if ('ResizeObserver' in window) {
			var ro = new ResizeObserver(function () { measureStack(); updateCover(); });
			stack.slides.forEach(function (s) { ro.observe(s); });
		}
		window.addEventListener('resize', function () { measureStack(); updateCover(); }, { passive: true });
		window.addEventListener('load', measureStack);

		// Cover progress.
		var ticking = false;
		function updateCover() {
			ticking = false;
			var vh = window.innerHeight;
			for (var i = 0; i < stack.slides.length - 1; i++) {
				var nextTop = stack.slides[i + 1].getBoundingClientRect().top;
				var p = clamp((vh - nextTop) / vh, 0, 1);
				var s = stack.slides[i];
				var prev = s._cover || 0;
				if (Math.abs(prev - p) > 0.004 || (p === 0 && prev !== 0) || (p === 1 && prev !== 1)) {
					s.style.setProperty('--cover', p.toFixed(3));
					s._cover = p;
				}
			}
		}
		window.addEventListener('scroll', function () {
			if (!ticking) { ticking = true; requestAnimationFrame(updateCover); }
		}, { passive: true });
		updateCover();

		// In-page anchors → natural slide offsets (sticky elements report stuck positions).
		document.addEventListener('click', function (e) {
			var a = e.target.closest('a[href*="#"]');
			if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) { return; }
			var url = new URL(a.href, location.href);
			if (url.pathname !== location.pathname || url.origin !== location.origin || !url.hash) { return; }
			var id = decodeURIComponent(url.hash.slice(1));
			var target = document.getElementById(id);
			if (!target) { return; }
			var slide = target.closest('.stack > .slide');
			if (!slide) { return; }
			e.preventDefault();
			var y = slideStart(slide.id);
			if (slide !== target) {
				y += target.getBoundingClientRect().top - slide.getBoundingClientRect().top - 90;
			}
			window.scrollTo({ top: Math.max(0, y), behavior: reduceMotion.matches ? 'instant' : 'smooth' });
			if (history.pushState) { history.pushState(null, '', '#' + id); }
			// Move focus for keyboard / screen-reader users without a second jump.
			var focusTarget = slide.querySelector('h1, h2') || slide;
			if (!focusTarget.hasAttribute('tabindex')) { focusTarget.setAttribute('tabindex', '-1'); }
			// Focus once the smooth scroll has finished; flag it so the focusin
			// handler below doesn't treat it as keyboard navigation.
			var focused = false;
			var doFocus = function () {
				if (focused) { return; }
				focused = true;
				stack.programmaticFocus = true;
				focusTarget.focus({ preventScroll: true });
				stack.programmaticFocus = false;
			};
			if (reduceMotion.matches) { doFocus(); }
			else {
				if ('onscrollend' in window) { window.addEventListener('scrollend', doFocus, { once: true }); }
				setTimeout(doFocus, 1600);
			}
		});

		// Initial hash (e.g. after the quote form redirect) and back/forward.
		function fromHash(smooth) {
			if (location.hash.length > 1) {
				var id = decodeURIComponent(location.hash.slice(1));
				var el = document.getElementById(id);
				var slide = el && el.closest('.stack > .slide');
				if (slide) { scrollToSlide(slide.id, smooth); }
			}
		}
		window.addEventListener('load', function () { measureStack(); fromHash(false); });
		window.addEventListener('popstate', function () { fromHash(true); });

		// Keyboard focus inside a covered slide: bring that slide back into view.
		document.addEventListener('focusin', function (e) {
			var el = e.target;
			var slide = el.closest && el.closest('.stack > .slide');
			if (!slide || el === slide || stack.programmaticFocus) { return; }
			var r = el.getBoundingClientRect();
			var x = clamp(r.left + r.width / 2, 1, window.innerWidth - 1);
			var y = clamp(r.top + Math.min(r.height / 2, 20), 1, window.innerHeight - 1);
			var hit = document.elementFromPoint(x, y);
			if (hit && (slide.contains(hit) || hit.closest('.site-header, .slide-rail'))) { return; }
			var i = stack.slides.indexOf(slide);
			var start = stack.starts[i];
			var within = r.top - slide.getBoundingClientRect().top;
			var maxY = start + Math.max(0, stack.heights[i] - window.innerHeight);
			window.scrollTo({ top: clamp(start + within - window.innerHeight * 0.3, start, maxY), behavior: 'instant' });
		});
	}

	/* ------------------------------------------------------------------
	 * Active section → slide rail + header nav
	 * ---------------------------------------------------------------- */
	function initActiveSection() {
		var slides = $$('.stack > .slide');
		if (!slides.length || !('IntersectionObserver' in window)) { return; }
		var rail = $('.slide-rail');
		var lightSlides = ['pay-later'];
		var groups = { finelineshowcase: 'finelineshowcase', 'realism-showcase': 'finelineshowcase', about: 'home', quote: 'contact', 'pay-later': 'contact' };

		function setActive(id) {
			$$('[data-rail]').forEach(function (a) {
				var on = a.getAttribute('data-rail') === id;
				a.classList.toggle('is-active', on);
				if (on) { a.setAttribute('aria-current', 'true'); } else { a.removeAttribute('aria-current'); }
			});
			$$('[data-menu]').forEach(function (a) {
				var on = a.getAttribute('data-menu') === id;
				a.classList.toggle('is-active', on);
				if (on) { a.setAttribute('aria-current', 'location'); } else { a.removeAttribute('aria-current'); }
			});
			var navId = groups[id] || id;
			$$('.site-nav a[data-nav]').forEach(function (a) {
				a.classList.toggle('is-active', a.getAttribute('data-nav') === navId);
			});
			if (rail) {
				rail.classList.toggle('on-light', lightSlides.indexOf(id) !== -1);
				rail.classList.toggle('is-hidden', id === 'home'); // keeps clear of the hero slider arrow
			}
		}

		// Slides are sticky, so a pinned slide never "re-enters" the band when the
		// page scrolls back up. Track every slide inside the band and treat the
		// last one in document order (the top card of the stack) as active.
		var inBand = {};
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) { inBand[en.target.id] = en.isIntersecting; });
			for (var i = slides.length - 1; i >= 0; i--) {
				if (inBand[slides[i].id]) { setActive(slides[i].id); break; }
			}
		}, { rootMargin: '-55% 0px -44% 0px', threshold: 0 });
		slides.forEach(function (s) { io.observe(s); });
	}

	/* ------------------------------------------------------------------
	 * Scroll reveals
	 * ---------------------------------------------------------------- */
	function initReveals() {
		var els = $$('[data-reveal], [data-words]');
		if (!els.length) { return; }
		if (reduceMotion.matches || !('IntersectionObserver' in window)) {
			els.forEach(function (el) { el.classList.add('is-in'); });
			return;
		}
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) {
					en.target.classList.add('is-in');
					io.unobserve(en.target);
				}
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
		els.forEach(function (el) { io.observe(el); });
	}

	/* ------------------------------------------------------------------
	 * Subtle parallax ([data-depth] ghost type, [data-parallax] images)
	 * ---------------------------------------------------------------- */
	function initParallax() {
		var els = $$('[data-depth], [data-parallax]');
		if (!els.length || reduceMotion.matches) { return; }
		var ticking = false;
		function update() {
			ticking = false;
			if (window.innerWidth < 768) {
				els.forEach(function (el) { el.style.removeProperty('--py'); });
				return;
			}
			var vh = window.innerHeight;
			els.forEach(function (el) {
				var r = el.getBoundingClientRect();
				if (r.bottom < -200 || r.top > vh + 200) { return; }
				var f = parseFloat(el.getAttribute('data-depth') || el.getAttribute('data-parallax')) || 0;
				var center = r.top + r.height / 2 - vh / 2;
				var y = clamp(center * -f, -120, 120);
				el.style.setProperty('--py', y.toFixed(1) + 'px');
			});
		}
		window.addEventListener('scroll', function () {
			if (!ticking) { ticking = true; requestAnimationFrame(update); }
		}, { passive: true });
		window.addEventListener('resize', update, { passive: true });
		update();
	}

	/* ------------------------------------------------------------------
	 * Hero 3D tilt (fine pointers only)
	 * ---------------------------------------------------------------- */
	function initTilt() {
		var hero = $('.slide--hero');
		var frame = $('.hero__frame');
		var visual = $('[data-tilt]');
		if (!hero || !frame || !visual || reduceMotion.matches || !finePointer.matches) { return; }
		var img = $('.hero__img', frame);
		var raf = null;
		var tx = 0;
		var ty = 0;
		function apply() {
			raf = null;
			frame.style.setProperty('--rx', (ty * -5).toFixed(2) + 'deg');
			frame.style.setProperty('--ry', (tx * 7).toFixed(2) + 'deg');
			if (img) {
				img.style.setProperty('--ix', (tx * -14).toFixed(1) + 'px');
				img.style.setProperty('--iy', (ty * -14).toFixed(1) + 'px');
			}
			visual.style.setProperty('--rx2', (tx * 18).toFixed(1) + 'px');
			visual.style.setProperty('--ry2', (ty * 18).toFixed(1) + 'px');
		}
		hero.addEventListener('pointermove', function (e) {
			var r = hero.getBoundingClientRect();
			tx = clamp((e.clientX - r.left) / r.width - 0.5, -0.5, 0.5);
			ty = clamp((e.clientY - r.top) / r.height - 0.5, -0.5, 0.5);
			if (!raf) { raf = requestAnimationFrame(apply); }
		});
		hero.addEventListener('pointerleave', function () {
			tx = 0; ty = 0;
			if (!raf) { raf = requestAnimationFrame(apply); }
		});
	}

	/* ------------------------------------------------------------------
	 * Horizontal carousels: buttons, progress, mouse drag
	 * ---------------------------------------------------------------- */
	function initCarousels() {
		$$('[data-carousel-prev], [data-carousel-next]').forEach(function (btn) {
			var id = btn.getAttribute('data-carousel-prev') || btn.getAttribute('data-carousel-next');
			var track = document.getElementById(id);
			if (!track) { return; }
			var dir = btn.hasAttribute('data-carousel-prev') ? -1 : 1;
			btn.addEventListener('click', function () {
				track.scrollBy({ left: dir * track.clientWidth * 0.8, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
			});
		});

		$$('[data-carousel-bar]').forEach(function (bar) {
			var track = document.getElementById(bar.getAttribute('data-carousel-bar'));
			if (!track) { return; }
			var prev = $('[data-carousel-prev="' + track.id + '"]');
			var next = $('[data-carousel-next="' + track.id + '"]');
			function update() {
				var max = track.scrollWidth - track.clientWidth;
				var ratio = track.scrollWidth ? track.clientWidth / track.scrollWidth : 1;
				bar.style.width = (ratio * 100).toFixed(1) + '%';
				bar.style.left = (max > 0 ? (track.scrollLeft / track.scrollWidth) * 100 : 0).toFixed(1) + '%';
				if (prev) { prev.disabled = track.scrollLeft <= 2; }
				if (next) { next.disabled = track.scrollLeft >= max - 2; }
			}
			track.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update, { passive: true });
			update();
		});

		// Mouse drag-to-scroll (touch already scrolls natively).
		$$('[data-drag], .promo-track').forEach(function (track) {
			var down = false;
			var startX = 0;
			var startLeft = 0;
			var moved = false;
			track.addEventListener('pointerdown', function (e) {
				if (e.pointerType !== 'mouse' || e.button !== 0) { return; }
				down = true; moved = false;
				startX = e.clientX; startLeft = track.scrollLeft;
			});
			window.addEventListener('pointermove', function (e) {
				if (!down) { return; }
				var dx = e.clientX - startX;
				if (!moved && Math.abs(dx) > 6) { moved = true; track.classList.add('is-dragging'); }
				if (moved) { track.scrollLeft = startLeft - dx; }
			});
			window.addEventListener('pointerup', function () {
				if (!down) { return; }
				down = false;
				setTimeout(function () { track.classList.remove('is-dragging'); }, 0);
			});
			track.addEventListener('click', function (e) {
				if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; }
			}, true);
			track.addEventListener('dragstart', function (e) { e.preventDefault(); });
		});
	}

	/* ------------------------------------------------------------------
	 * Lightbox (native <dialog>)
	 * ---------------------------------------------------------------- */
	function initLightbox() {
		var dialog = $('#lightbox');
		if (!dialog || typeof dialog.showModal !== 'function') { return; }
		var img = $('.lightbox__img', dialog);
		var cap = $('.lightbox__caption', dialog);
		var items = [];
		var index = 0;
		var opener = null;

		function show(i) {
			index = (i + items.length) % items.length;
			var a = items[index];
			var alt = a.getAttribute('data-caption') || '';
			img.src = a.href;
			img.alt = alt;
			cap.textContent = alt + (items.length > 1 ? '  (' + (index + 1) + ' / ' + items.length + ')' : '');
		}

		document.addEventListener('click', function (e) {
			var a = e.target.closest('a[data-lightbox]');
			if (!a || e.defaultPrevented) { return; }
			e.preventDefault();
			var group = a.getAttribute('data-lightbox');
			items = $$('a[data-lightbox="' + group + '"]');
			opener = a;
			dialog.classList.toggle('is-single', items.length < 2);
			show(items.indexOf(a));
			dialog.showModal();
			document.body.classList.add('menu-open');
		});

		$('.lightbox__prev', dialog).addEventListener('click', function () { show(index - 1); });
		$('.lightbox__next', dialog).addEventListener('click', function () { show(index + 1); });
		$('.lightbox__close', dialog).addEventListener('click', function () { dialog.close(); });
		dialog.addEventListener('click', function (e) { if (e.target === dialog) { dialog.close(); } });
		dialog.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { show(index - 1); }
			if (e.key === 'ArrowRight') { show(index + 1); }
		});
		dialog.addEventListener('close', function () {
			document.body.classList.remove('menu-open');
			img.removeAttribute('src');
			if (opener) { opener.focus({ preventScroll: true }); }
		});

		// Swipe on touch.
		var sx = null;
		dialog.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
		dialog.addEventListener('touchend', function (e) {
			if (sx === null) { return; }
			var dx = e.changedTouches[0].clientX - sx;
			if (Math.abs(dx) > 50 && items.length > 1) { show(index + (dx < 0 ? 1 : -1)); }
			sx = null;
		});
	}

	/* ------------------------------------------------------------------
	 * Quote form: file name, client-side validation, status focus
	 * ---------------------------------------------------------------- */
	function initQuoteForm() {
		var form = $('.quote-form');
		if (!form) { return; }
		var file = $('#q-file', form);
		var label = $('[data-file-name]', form);
		var status = $('.quote-form__status', form);
		var defaultLabel = label ? label.textContent : '';
		var allowed = /\.(jpe?g|png|webp|heic|pdf)$/i;

		if (file && label) {
			file.addEventListener('change', function () {
				label.textContent = file.files && file.files[0] ? file.files[0].name : defaultLabel;
			});
		}

		function say(msg, type) {
			status.className = 'quote-form__status is-' + type;
			status.textContent = msg;
		}

		form.addEventListener('submit', function (e) {
			var invalid = [];
			$$('[required]', form).forEach(function (f) {
				var min = parseInt(f.getAttribute('minlength'), 10) || 0;
				var bad = !f.value.trim() || f.value.trim().length < min || (f.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.value.trim()));
				f.setAttribute('aria-invalid', bad ? 'true' : 'false');
				if (bad) { invalid.push(f); }
			});
			if (invalid.length) {
				e.preventDefault();
				say('Please fill in your first name, last name, a valid email and the details of your tattoo.', 'error');
				invalid[0].focus();
				return;
			}
			if (file && file.files && file.files[0]) {
				var f0 = file.files[0];
				var max = parseInt(file.getAttribute('data-max'), 10) || 4194304;
				if (f0.size > max || !allowed.test(f0.name)) {
					e.preventDefault();
					say('The file could not be accepted. Please attach a JPG, PNG, WEBP, HEIC or PDF up to 4 MB.', 'error');
					file.focus();
					return;
				}
			}
			var btn = $('button[type="submit"]', form);
			if (btn) { btn.disabled = true; btn.setAttribute('aria-busy', 'true'); }

			// Static site (Vercel): post to the serverless function and show its answer in place.
			if (!form.hasAttribute('data-async') || !window.fetch || !window.FormData) { return; }
			e.preventDefault();
			var data = new FormData(form);
			data.append('submissionId', submissionId);
			say('Sending…', 'info');
			fetch(form.action, { method: 'POST', body: data, headers: { Accept: 'application/json' } })
				.then(function (r) { return r.json().catch(function () { return { ok: false }; }); })
				.then(function (res) {
					if (res && res.ok) {
						form.reset();
						if (label) { label.textContent = defaultLabel; }
						submissionId = newId();
						say('Thank you — your request has been sent. We’ll get back to you with a personalised quote.', 'ok');
						return;
					}
					var fields = (res && res.fields) || {};
					var map = { firstName: 'q-first', lastName: 'q-last', email: 'q-email', phone: 'q-phone', details: 'q-details', file: 'q-file' };
					Object.keys(fields).forEach(function (k) { var el = map[k] && document.getElementById(map[k]); if (el) { el.setAttribute('aria-invalid', 'true'); } });
					var msgs = Object.keys(fields).map(function (k) { return fields[k]; });
					say((res && res.error ? res.error : 'Sorry, your request could not be sent. Please email or call us instead.') + (msgs.length ? ' ' + msgs.join(' ') : ''), 'error');
				})
				.catch(function () { say('Sorry, your request could not be sent. Please check your connection, or email or call us instead.', 'error'); })
				.then(function () {
					if (btn) { btn.disabled = false; btn.removeAttribute('aria-busy'); }
					status.focus({ preventScroll: true });
				});
		});

		// One id per submission attempt: the server ignores accidental double sends.
		function newId() { return 'q-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10); }
		var submissionId = newId();

		// "Book This Artist": preselect the artist in the form.
		var artistSelect = $('[data-artist-select]', form);
		$$('[data-book-artist]').forEach(function (a) {
			a.addEventListener('click', function () {
				if (artistSelect) { artistSelect.value = a.getAttribute('data-book-artist'); }
			});
		});

		$$('input, textarea', form).forEach(function (f) {
			f.addEventListener('input', function () { if (f.getAttribute('aria-invalid') === 'true' && f.value.trim()) { f.setAttribute('aria-invalid', 'false'); } });
		});

		if (status && status.textContent.trim()) {
			window.addEventListener('load', function () { setTimeout(function () { status.focus({ preventScroll: true }); }, 50); });
		}
	}

	function boot() {
		initEntrance();
		initHeader();
		initMenu();
		initHeroSlider();
		initStack();
		initActiveSection();
		initReveals();
		initParallax();
		initTilt();
		initCarousels();
		initLightbox();
		initQuoteForm();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
