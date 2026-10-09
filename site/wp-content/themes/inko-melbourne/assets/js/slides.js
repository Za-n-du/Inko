/* INKO Melbourne - slides.js: stacked full-screen slide takeover (vanilla JS).
   Exposes INKO.createSlides(). One slide per wheel gesture / swipe / key press; eased, never skips, never jitters. */
(function () {
  'use strict';
  var INKO = (window.INKO = window.INKO || {});
  var clamp = function (v, a, b) { return v < a ? a : v > b ? b : v; };
  var ease = function (t) { return 0.5 - 0.5 * Math.cos(Math.PI * t); };
  INKO.clamp = clamp;
  INKO.$ = function (s, c) { return (c || document).querySelector(s); };
  INKO.$$ = function (s, c) { return [].slice.call((c || document).querySelectorAll(s)); };
  INKO.locked = function () { return !!document.querySelector('.ik-team.is-open'); };
  INKO.isEditor = function () {
    var b = document.body;
    return !b || b.classList.contains('elementor-editor-active') || b.classList.contains('elementor-editor-preview') || /[?&]elementor-preview=/.test(location.search);
  };

  INKO.createSlides = function () {
    var slides = INKO.$$('.ik-slide');
    if (!slides.length) return null;
    var N = slides.length, ids = slides.map(function (s) { return s.id; });
    var track = INKO.$('.ik-track');
    if (!track) { track = document.createElement('div'); track.className = 'ik-track'; track.setAttribute('aria-hidden', 'true'); document.body.appendChild(track); }
    var rm = window.matchMedia('(prefers-reduced-motion: reduce)');
    var menu = INKO.$('.ik-menu');
    var W = window.innerWidth, H = window.innerHeight;
    var p = 0, active = -1, lastY = window.scrollY, dir = 1, raf = 0, tw = 0, settleT, cache = [], listeners = { change: [], progress: [], resize: [], hwheel: [] };
    var emit = function (k, a) { listeners[k].forEach(function (f) { f(a); }); };

    slides.forEach(function (s, i) {
      s.style.zIndex = i + 1;
      s.setAttribute('role', 'region');
      s.setAttribute('aria-label', 'Section ' + (i + 1) + ' of ' + N);
    });
    document.documentElement.style.scrollBehavior = 'auto';

    function measure() {
      var w = window.innerWidth, h = window.innerHeight;
      if (w !== W || Math.abs(h - H) > 140 || !track.style.height) { W = w; H = h; }
      track.style.height = ((N - 1) * H + h) + 'px';
    }

    function render() {
      raf = 0;
      p = clamp(window.scrollY / H, 0, N - 1);
      var r = rm.matches;
      for (var i = 0; i < N; i++) {
        var s = slides[i];
        var t = i === 0 ? 1 : clamp(p - (i - 1), 0, 1);
        var n = i < N - 1 ? clamp(p - i, 0, 1) : 0;
        if (r) { t = t >= 0.5 ? 1 : 0; n = n >= 0.5 ? 1 : 0; }
        var te = r ? t : ease(t), ne = r ? n : ease(n);
        var y = ((1 - te) * 100).toFixed(3), sc = (1 - 0.05 * ne).toFixed(4), dim = (0.6 * ne).toFixed(3);
        var hidden = (i > 0 && te === 0) || ne >= 1;
        var key = y + '|' + sc + '|' + dim + '|' + hidden;
        if (cache[i] === key) continue;
        cache[i] = key;
        s.style.transform = 'translate3d(0,' + y + '%,0) scale(' + sc + ')';
        s.style.setProperty('--dim', dim);
        s.style.visibility = hidden ? 'hidden' : 'visible';
      }
      emit('progress', p);
      var a = Math.round(p);
      if (a !== active) {
        active = a;
        slides.forEach(function (s, i) {
          if (i === a) { s.removeAttribute('inert'); s.removeAttribute('aria-hidden'); }
          else { s.setAttribute('inert', ''); s.setAttribute('aria-hidden', 'true'); }
        });
        emit('change', a);
      }
    }

    function stopTween() { if (tw) { cancelAnimationFrame(tw); tw = 0; } }
    /* one eased move to slide i (~0.6-0.7s for a single slide, a little longer for jumps) */
    function goTo(i, instant, dur) {
      i = clamp(Math.round(i), 0, N - 1);
      var to = i * H, from = window.scrollY, d = Math.abs(to - from);
      stopTween();
      if (rm.matches || instant || d < 2) { window.scrollTo(0, to); return; }
      dur = dur || clamp(620 + (d / H - 1) * 140, 620, 1000);
      var t0 = performance.now();
      (function step(now) {
        var k = clamp((now - t0) / dur, 0, 1);
        window.scrollTo(0, from + (to - from) * (k < 0.5 ? 4 * k * k * k : 1 - Math.pow(-2 * k + 2, 3) / 2));
        if (k < 1) tw = requestAnimationFrame(step); else { tw = 0; cool = performance.now() + 150; }
      })(t0);
    }
    function stepBy(d) { if (!tw && !INKO.locked()) goTo(active + d); }

    /* wheel + trackpad: exactly one slide per gesture. Inertia tails are ignored; a clearly stronger fresh swipe is a new gesture. */
    var lastWheel = 0, used = false, prevAbs = 0, cool = 0, lastH = 0;
    window.addEventListener('wheel', function (e) {
      if (e.ctrlKey) return;
      e.preventDefault();
      if ((menu && menu.classList.contains('is-open')) || INKO.locked()) return;
      var now = performance.now();
      if (Math.abs(e.deltaX) > Math.abs(e.deltaY) * 1.4) {                 // sideways trackpad swipe: drives the carousel on the visible slide
        if (now - lastH > 420) { lastH = now; emit('hwheel', e); }
        return;
      }
      var gap = now - lastWheel, dy = e.deltaMode === 1 ? e.deltaY * 16 : e.deltaY, ad = Math.abs(dy);
      lastWheel = now;
      if (gap >= 130) used = false;
      else if (used && !tw && now >= cool && ad >= 40 && ad > prevAbs + 30) used = false;
      prevAbs = ad;
      if (used || tw || now < cool || ad < 2) return;
      used = true; stepBy(dy > 0 ? 1 : -1);
    }, { passive: false });

    /* touch: the slide follows the finger, then settles on the next/previous slide only */
    var ts = null;
    window.addEventListener('touchstart', function (e) {
      if (e.touches.length !== 1) { ts = null; return; }
      // menu / team panel open: keep the page where it is (native scrolling would otherwise move the slides underneath)
      if ((menu && menu.classList.contains('is-open')) || INKO.locked()) { ts = { lock: true }; return; }
      stopTween();
      var t = e.touches[0];
      ts = { x: t.clientX, y: t.clientY, t: performance.now(), base: window.scrollY, idx: Math.round(window.scrollY / H), mode: null };
    }, { passive: true });
    window.addEventListener('touchmove', function (e) {
      if (!ts) return;
      if (ts.lock) { if (e.cancelable) e.preventDefault(); return; }
      var t = e.touches[0], dx = t.clientX - ts.x, dy = ts.y - t.clientY;
      if (!ts.mode && (Math.abs(dx) > 8 || Math.abs(dy) > 8)) ts.mode = Math.abs(dy) >= Math.abs(dx) ? 'v' : 'h';
      if (ts.mode === 'v') {
        if (e.cancelable) e.preventDefault();
        window.scrollTo(0, clamp(ts.base + dy, Math.max(0, ts.base - H), Math.min((N - 1) * H, ts.base + H)));
      }
    }, { passive: false });
    window.addEventListener('touchend', function (e) {
      if (!ts) return;
      var m = ts; ts = null;
      if (m.mode !== 'v') return;
      var t = (e.changedTouches && e.changedTouches[0]) || { clientY: m.y }, dy = m.y - t.clientY, v = dy / Math.max(1, performance.now() - m.t), next = m.idx;
      if (Math.abs(dy) > H * 0.16 || Math.abs(v) > 0.4) next = m.idx + (dy > 0 ? 1 : -1);
      goTo(next, false, 480);
    }, { passive: true });
    window.addEventListener('touchcancel', function () { if (ts) { var m = ts; ts = null; if (!m.lock) goTo(m.idx, false, 400); } }, { passive: true });

    /* safety net for any other scroll source (scrollbar drag, find-in-page): settle on the nearest slide */
    function settle() {
      if (ts || tw) { clearTimeout(settleT); settleT = setTimeout(settle, 200); return; }
      var f = p - Math.floor(p);
      if (f < 0.004 || f > 0.996) return;
      goTo(dir >= 0 ? (f > 0.12 ? Math.ceil(p) : Math.floor(p)) : (f < 0.88 ? Math.floor(p) : Math.ceil(p)));
    }
    window.addEventListener('scroll', function () {
      var y = window.scrollY;
      if (y !== lastY) dir = y > lastY ? 1 : -1;
      lastY = y;
      if (!raf) raf = requestAnimationFrame(render);
      clearTimeout(settleT); settleT = setTimeout(settle, 140);
    }, { passive: true });
    window.addEventListener('resize', function () {
      var oldH = H; measure();
      if (oldH !== H) window.scrollTo(0, Math.round(active < 0 ? 0 : active) * H);
      cache = []; render(); emit('resize');
    });
    if (rm.addEventListener) rm.addEventListener('change', function () { cache = []; render(); });

    measure(); render();
    return {
      slides: slides, ids: ids, N: N,
      active: function () { return active; },
      goTo: goTo, stepBy: stepBy,
      goToId: function (id, instant) { var i = ids.indexOf(id); if (i < 0) return false; goTo(i, instant); return true; },
      on: function (k, f) { listeners[k].push(f); if (k === 'change' && active > -1) f(active); }
    };
  };
})();
