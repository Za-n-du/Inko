/* INKO Melbourne - main.js: boot + carousels (Elementor containers wrapped for drag/arrows), location gallery, Follow-Us loop. */
(function () {
  'use strict';
  var INKO = window.INKO;
  if (!INKO || INKO.isEditor() || window.__ikInit) return;   // Elementor editor: do nothing so editing is never blocked
  var $ = INKO.$, $$ = INKO.$$, rm = window.matchMedia('(prefers-reduced-motion: reduce)');

  function boot() {
    var api = INKO.createSlides();
    if (!api) { INKO.initMenu(); return; }                     // ordinary page: burger menu only
    window.__ikInit = true;
    INKO.initNavigation(api);
    var slides = api.slides;
  /* ---------- carousels: wraps Elementor's .ik-car__tr container; items are Elementor containers ---------- */
  var carousels = [];
  var ARROW_L = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 4l-8 8 8 8"/></svg>';
  var ARROW_R = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4l8 8-8 8"/></svg>';
  function mkBtn(cls, label, svg) {
    var b = document.createElement('button');
    b.type = 'button'; b.className = cls; b.setAttribute('aria-label', label);
    b.innerHTML = '<span class="ik-arrow">' + svg + '</span>';
    return b;
  }
  function Carousel(el) {
    var tr = $('.ik-car__tr', el);
    var vp = document.createElement('div');
    vp.className = 'ik-car__vp'; vp.setAttribute('role', 'group'); vp.setAttribute('aria-roledescription', 'carousel');
    tr.parentNode.insertBefore(vp, tr); vp.appendChild(tr);
    var prev = mkBtn('ik-car__btn ik-car__btn--p', 'Previous', ARROW_L), next = mkBtn('ik-car__btn ik-car__btn--n', 'Next', ARROW_R);
    el.appendChild(prev); el.appendChild(next);
    var items = [].slice.call(tr.children), offs = [0], self = this, dragX = 0, startX = 0, startY = 0, down = false, moved = false;
    this.idx = 0; this.el = el; this.onchange = null; this.count = function () { return offs.length; };
    this.layout = function () {
      var maxX = Math.max(0, tr.scrollWidth - vp.clientWidth), seen = {};
      offs = [];
      items.forEach(function (it) { var o = Math.min(it.offsetLeft, maxX); if (!seen[o]) { seen[o] = 1; offs.push(o); } });
      if (!offs.length) offs = [0];
      self.idx = Math.min(self.idx, offs.length - 1);
      prev.hidden = next.hidden = offs.length < 2;   // nothing to scroll to: hide the arrows
      apply(false);
    };
    function apply(anim) { tr.classList.toggle('no-anim', !anim); tr.style.transform = 'translate3d(' + (-offs[self.idx]) + 'px,0,0)'; if (self.onchange) self.onchange(); }
    this.go = function (n) { var last = offs.length - 1; self.idx = n > last ? 0 : n < 0 ? last : n; apply(true); };
    prev.addEventListener('click', function () { self.go(self.idx - 1); });
    next.addEventListener('click', function () { self.go(self.idx + 1); });
    vp.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'mouse' && e.button !== 0) return;
      down = true; moved = false; startX = e.clientX; startY = e.clientY; dragX = 0;
    });
    vp.addEventListener('pointermove', function (e) {
      if (!down) return;
      var dx = e.clientX - startX, dy = e.clientY - startY;
      if (!moved && Math.abs(dx) > 8 && Math.abs(dx) > Math.abs(dy)) { moved = true; vp.classList.add('is-drag'); try { vp.setPointerCapture(e.pointerId); } catch (x) {} }
      if (moved) {
        dragX = dx;
        var edge = (self.idx === 0 && dx > 0) || (self.idx === offs.length - 1 && dx < 0);
        tr.classList.add('no-anim');
        tr.style.transform = 'translate3d(' + (-offs[self.idx] + dx * (edge ? 0.35 : 1)) + 'px,0,0)';
      }
    });
    function end() {
      if (!down) return;
      down = false; vp.classList.remove('is-drag');
      if (moved) { if (dragX < -50) self.go(self.idx + 1); else if (dragX > 50) self.go(self.idx - 1); else apply(true); }
    }
    vp.addEventListener('pointerup', end); vp.addEventListener('pointercancel', end);
    vp.addEventListener('click', function (e) { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
    vp.addEventListener('dragstart', function (e) { e.preventDefault(); });
    $$('img', el).forEach(function (im) { if (!im.complete) im.addEventListener('load', self.layout); });
    this.layout();
  }
  $$('.ik-car').forEach(function (el) {
    if (!$('.ik-car__tr', el)) return;
    var c = new Carousel(el); carousels.push(c);
    var s = el.closest('.ik-slide'); if (s) s.__car = c;
  });

  /* ---------- location gallery: Elementor image tiles, JS only swaps which tile is the big one ---------- */
  var gal = $('.s-loc__gal');
  if (gal) {
    var tiles = $$('.s-loc__tile', gal), cur = 0, M = tiles.length;
    var bp = document.createElement('button'), bn = document.createElement('button');
    bp.type = bn.type = 'button';
    bp.className = 'ik-arrow ik-arrow--p'; bn.className = 'ik-arrow ik-arrow--n';
    bp.setAttribute('aria-label', 'Previous photo'); bn.setAttribute('aria-label', 'Next photo');
    bp.innerHTML = ARROW_L; bn.innerHTML = ARROW_R;
    gal.appendChild(bp); gal.appendChild(bn);
    var stacked = window.matchMedia('(max-width:1180px)');
    var place = function () {
      var o = 0;
      tiles.forEach(function (t, i) {
        if (i === cur) { t.style.gridArea = stacked.matches ? '1 / 1 / 2 / 5' : '1 / 1 / 3 / 2'; t.setAttribute('aria-label', 'Current photo'); }
        else {
          t.style.gridArea = stacked.matches ? '2 / ' + (o + 1) + ' / 3 / ' + (o + 2) : (o === 0 ? '1 / 2 / 2 / 5' : '2 / ' + (o + 1) + ' / 3 / ' + (o + 2));
          o++; t.setAttribute('aria-label', 'Show photo ' + (i + 1));
        }
        t.classList.remove('is-fade');
      });
    };
    if (stacked.addEventListener) stacked.addEventListener('change', place);
    var show = function (n) {
      cur = (n + M) % M;
      if (rm.matches) return place();
      tiles.forEach(function (t) { t.classList.add('is-fade'); });
      setTimeout(place, 220);
    };
    tiles.forEach(function (t, i) { t.tabIndex = 0; t.setAttribute('role', 'button'); t.addEventListener('click', function () { if (i !== cur) show(i); }); t.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); if (i !== cur) show(i); } }); });
    bp.addEventListener('click', function () { show(cur - 1); });
    bn.addEventListener('click', function () { show(cur + 1); });
    var sx = 0;
    gal.addEventListener('pointerdown', function (e) { sx = e.clientX; });
    gal.addEventListener('pointerup', function (e) { var dx = e.clientX - sx; if (Math.abs(dx) > 50 && e.target.closest('.s-loc__tile') === tiles[cur]) show(cur + (dx < 0 ? 1 : -1)); });
    var ls = gal.closest('.ik-slide'); if (ls) ls.__car = { idx: 0, go: function (n) { show(cur + (n > 0 ? 1 : -1)); } };
    place();
  }


  /* ---------- Artists: rotate the four photos (centre + two behind), team panel from "Meet the team" ---------- */
  var stack = $('.s-art__stack');
  if (stack) {
    var ph = $$('.s-art__ph', stack), ci = 1, PM = ph.length;
    var paint = function () {
      ph.forEach(function (el, i) {
        var d = ((i - ci) % PM + PM) % PM;
        el.classList.remove('is-c', 'is-l', 'is-r', 'is-x');
        el.classList.add(d === 0 ? 'is-c' : d === 1 ? 'is-r' : d === PM - 1 ? 'is-l' : 'is-x');
      });
    };
    /* caption under the centre photo: the artist's details are read from the team cards (Elementor "ik-team"), the
       single source of the artist data; each photo container names its artist with an "ik-artist-<name>" CSS class */
    var slug = function (s) { return String(s).trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''); };
    var artists = {};
    $$('.ik-acard').forEach(function (c) {
      var n = $('.ik-acard__n', c);
      if (n) artists[slug(n.textContent)] = ['n', 'a', 'r', 'd'].map(function (k) { var e = $('.ik-acard__' + k, c); return e ? e.textContent.trim() : ''; });
    });
    var artistOf = function (el) { var m = el.className.match(/\bik-artist-([a-z0-9-]+)/); return m && artists[m[1]]; };
    var cap = null;
    if (ph.some(artistOf)) {
      cap = document.createElement('div'); cap.className = 's-art__cap'; cap.setAttribute('aria-live', 'polite'); stack.appendChild(cap);
    }
    var fill = function (a) {
      cap.textContent = '';
      if (!a) return;
      [['p', 'n', a[0]], ['p', 'a', a[1] + (a[1] && a[2] ? ' • ' : '') + a[2]], ['p', 'd', a[3]]].forEach(function (x) {
        if (!x[2]) return;
        var e = document.createElement(x[0]); e.className = 's-art__cap-' + x[1]; e.textContent = x[2]; cap.appendChild(e);
      });
    };
    /* caption layout follows the space the slide leaves for the photos (the tallest caption is reserved so the photos
       keep one size while rotating):
         below   - caption under the photos (default)
         compact - short but wide area: centre photo on the left, caption beside it
         overlay - very small area: caption over the foot of the photo area */
    var desk = window.matchMedia('(min-width:1181px)');
    var tallest = function () { var h = 0; ph.forEach(function (el) { var a = artistOf(el); if (a) { fill(a); h = Math.max(h, cap.offsetHeight); } }); return h; };
    var sizeCap = function () {
      if (!cap) return;
      stack.classList.remove('is-compact', 'is-overlay', 'is-onphoto'); stack.style.setProperty('--cap', '0px');
      var H = stack.clientHeight, Wd = stack.clientWidth, gap = 16;
      /* desktop: photos keep their full reference size and the caption sits on the foot of the centre photo */
      if (desk.matches) {
        stack.classList.add('is-onphoto'); stack.style.setProperty('--pw', Math.round(H * 0.88 * 0.75) + 'px');
        if (tallest() <= H * 0.88 * 0.36) { fill(artistOf(ph[ci])); return; }
        stack.classList.remove('is-onphoto');
      }
      var hMax = tallest();
      if (H - hMax - gap >= Math.max(150, H * 0.45)) {
        stack.style.setProperty('--cap', Math.ceil(hMax + gap) + 'px');
      } else {
        var pw = Math.round(Math.min(H * 0.92 * 0.75, Wd * 0.38)), cw = Math.min(Wd - pw - gap, 440);
        stack.classList.add('is-compact'); stack.style.setProperty('--pw', pw + 'px'); stack.style.setProperty('--capw', cw + 'px');
        stack.style.setProperty('--cx', Math.max(0, Math.round((Wd - pw - gap - cw) / 2)) + 'px');
        if (cw < 150 || tallest() > H) { stack.classList.remove('is-compact'); stack.classList.add('is-overlay'); }
      }
      fill(artistOf(ph[ci]));
      placeArrows();
    };
    /* overlay layout: the arrows flank the artist's name instead of covering the text */
    var placeArrows = function () {
      if (!cap || !stack.classList.contains('is-overlay')) return;
      var n = $('.s-art__cap-n', cap);
      if (n) stack.style.setProperty('--arrow-y', Math.round(cap.offsetTop + n.offsetTop + n.offsetHeight / 2) + 'px');
    };
    var capTimer;
    var showCap = function () {
      if (!cap) return;
      clearTimeout(capTimer);
      if (rm.matches) return fill(artistOf(ph[ci]));
      cap.classList.add('is-swap');
      capTimer = setTimeout(function () { fill(artistOf(ph[ci])); placeArrows(); cap.classList.remove('is-swap'); }, 220);
    };
    var rot = function (n) { ci = ((ci + n) % PM + PM) % PM; paint(); showCap(); };
    ['p', 'n'].forEach(function (k) {
      var b = document.createElement('button'); b.type = 'button'; b.className = 's-art__arrow s-art__arrow--' + k;
      b.setAttribute('aria-label', k === 'p' ? 'Previous artist photo' : 'Next artist photo');
      b.innerHTML = k === 'p' ? ARROW_L : ARROW_R; b.addEventListener('click', function () { rot(k === 'p' ? -1 : 1); }); stack.appendChild(b);
    });
    ph.forEach(function (el, i) { el.addEventListener('click', function () { if (el.classList.contains('is-l')) rot(-1); else if (el.classList.contains('is-r')) rot(1); }); });
    paint(); sizeCap();
    if (cap) {
      api.on('resize', sizeCap);
      window.addEventListener('load', sizeCap);
      if (document.fonts && document.fonts.ready) document.fonts.ready.then(sizeCap);
    }
    var artSlide = stack.closest('.ik-slide'); if (artSlide) artSlide.__car = { idx: 0, go: function (n) { rot(n > 0 ? 1 : -1); } };
  }
  var team = $('.ik-team');
  if (team) {
    var x = document.createElement('button'); x.type = 'button'; x.className = 'ik-team__x'; x.setAttribute('aria-label', 'Close team'); team.appendChild(x);
    var openTeam = function () { team.classList.add('is-open'); var c = team.querySelector('.ik-car__tr'); team.__lastFocus = document.activeElement; x.focus({ preventScroll: true }); carousels.forEach(function (cc) { cc.layout(); }); };
    var closeTeam = function () { team.classList.remove('is-open'); if (team.__lastFocus && team.__lastFocus.focus) team.__lastFocus.focus({ preventScroll: true }); };
    x.addEventListener('click', closeTeam);
    team.addEventListener('click', function (e) { if (e.target === team) closeTeam(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && team.classList.contains('is-open')) closeTeam(); });
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href="#ik-team"]'); if (a) { e.preventDefault(); e.stopPropagation(); openTeam(); }
    }, true);
    api.on('change', function () { if (team.classList.contains('is-open')) closeTeam(); });
  }

  /* ---------- pagination dots (mobile) for the reviews and promotions carousels ---------- */
  carousels.forEach(function (c) {
    var sl = c.el.closest('.s-rev, .s-promo'); if (!sl) return;
    var dots = document.createElement('div'); dots.className = 'ik-dots'; dots.setAttribute('aria-hidden', 'true'); c.el.appendChild(dots);
    c.onchange = function () {
      if (dots.children.length !== c.count()) { dots.innerHTML = ''; for (var i = 0; i < c.count(); i++) dots.appendChild(document.createElement('i')); }
      [].forEach.call(dots.children, function (d, i) { d.classList.toggle('is-on', i === c.idx); });
    };
    c.onchange();
  });

  api.on('resize', function () { carousels.forEach(function (c) { c.layout(); }); });
  api.on('hwheel', function (e) {
    var car = slides[api.active()] && slides[api.active()].__car;
    if (car) car.go(car.idx + (e.deltaX > 0 ? 1 : -1));
  });

  /* ---------- marquee (Follow Us): duplicates the Elementor group for a seamless loop ---------- */
  var mq = $('.ik-marq'), grp = mq && $('.ik-marq__grp', mq);
  if (grp) {
    var tr2 = document.createElement('div'); tr2.className = 'ik-marq__tr';
    grp.parentNode.insertBefore(tr2, grp); tr2.appendChild(grp);
    for (var k = 0; k < 2; k++) { var c2 = grp.cloneNode(true); c2.setAttribute('aria-hidden', 'true'); $$('a', c2).forEach(function (x) { x.tabIndex = -1; }); tr2.appendChild(c2); }
  }

  /* ---------- Ink Now headline streak mirrors the editable heading text ---------- */
  $$('.s-pay__title .elementor-heading-title').forEach(function (h) { h.setAttribute('data-text', h.textContent); });

  var hash = (location.hash || '').slice(1);
  if (hash) api.goToId(hash, true);
  window.addEventListener('load', function () {
    carousels.forEach(function (c) { c.layout(); });
    var idle = window.requestIdleCallback || function (f) { setTimeout(f, 1200); };
    idle(function () { $$('img[loading="lazy"]').forEach(function (im) { im.loading = 'eager'; }); });
  });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
})();
