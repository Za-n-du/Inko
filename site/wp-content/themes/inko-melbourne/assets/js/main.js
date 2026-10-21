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

  /* ---------- location: one featured photo on the slide + "View location" gallery dialog ----------
     The Elementor photo tiles (.s-loc__tile) stay the single source of the photos: the slide shows one of them large
     (the storefront, loc-03, when present), the dialog shows all of them. Address and directions link are read from
     the slide's own address card, so nothing is duplicated or invented. */
  var gal = $('.s-loc__gal');
  var locImgs = gal ? $$('.s-loc__tile img', gal) : [];
  if (locImgs.length && window.HTMLDialogElement) {
    var tiles = $$('.s-loc__tile', gal), LN = locImgs.length;
    var feat = 0; locImgs.forEach(function (im, i) { if (/loc-03\b/.test(im.currentSrc || im.src)) feat = i; });
    var lmk = function (tag, cls, txt) { var e = document.createElement(tag); if (cls) e.className = cls; if (txt != null) e.textContent = txt; return e; };
    var ltwo = function (i) { return ('0' + (i + 1)).slice(-2); };
    /* the slide: featured photo + button (the other tiles are hidden by CSS) */
    gal.classList.add('is-hero'); tiles[feat].classList.add('is-main');
    var locSlide = gal.closest('.ik-slide'); if (locSlide) locSlide.classList.add('has-loc-hero');
    tiles.forEach(function (t, i) { if (i !== feat) t.setAttribute('aria-hidden', 'true'); });
    var openBtn = lmk('button', 'ik-loc__open'); openBtn.type = 'button';
    openBtn.innerHTML = '<span>View location</span><small>' + LN + ' photos</small>' + ARROW_R;
    openBtn.setAttribute('aria-haspopup', 'dialog'); gal.appendChild(openBtn);
    tiles[feat].addEventListener('click', function () { lOpen(openBtn); });
    /* the dialog */
    var addrEl = $('#location .s-loc__addr'), mapA = $('#location .s-loc__card .ik-btn a');
    /* address card markup: <strong>studio name</strong><br>street, suburb */
    var addrName = addrEl && $('strong', addrEl);
    var addrLines = [addrName ? addrName.textContent.trim() : '', addrEl ? addrEl.textContent.replace(addrName ? addrName.textContent : '', '').replace(/\s+/g, ' ').trim() : ''];
    var ld = lmk('dialog', 'ik-artdlg ik-locdlg'); ld.setAttribute('aria-labelledby', 'ik-locdlg-t');
    ld.innerHTML =
      '<div class="ik-artdlg__box ik-locdlg__box">' +
        '<button type="button" class="ik-artdlg__x" aria-label="Close location photos"></button>' +
        '<div class="ik-locdlg__head">' +
          '<p class="ik-artdlg__eyebrow">Our location</p>' +
          '<h2 class="ik-locdlg__t" id="ik-locdlg-t"></h2>' +
          '<p class="ik-locdlg__addr"></p>' +
          '<div class="ik-locdlg__meta"><p class="ik-artdlg__num" aria-hidden="true"><b></b><span></span></p></div>' +
        '</div>' +
        '<div class="ik-locdlg__stage"></div>' +
        '<div class="ik-artdlg__bar">' +
          '<button type="button" class="ik-artdlg__btn ik-artdlg__btn--p" aria-label="Previous photo">' + ARROW_L + '</button>' +
          '<div class="ik-artdlg__tabs ik-locdlg__tabs" role="group" aria-label="Choose a photo"></div>' +
          '<button type="button" class="ik-artdlg__btn ik-artdlg__btn--n" aria-label="Next photo">' + ARROW_R + '</button>' +
        '</div>' +
        '<p class="ik-artdlg__live ik-sr" aria-live="polite"></p>' +
      '</div>';
    document.body.appendChild(ld);
    var lq = function (s) { return $(s, ld); };
    lq('.ik-locdlg__t').textContent = addrLines[0] || 'INKO Melbourne';
    lq('.ik-locdlg__addr').textContent = addrLines.slice(1).join(' ');
    if (mapA) {
      var dir = lmk('a', 'ik-locdlg__dir'); dir.href = mapA.href; dir.target = '_blank'; dir.rel = 'noopener';
      dir.innerHTML = '<span>' + (mapA.textContent.trim() || 'Directions') + '</span>' + ARROW_R;
      dir.setAttribute('aria-label', (mapA.textContent.trim() || 'Directions') + ' (Google Maps, opens in a new tab)');
      lq('.ik-locdlg__meta').appendChild(dir);
    }
    var stage = lq('.ik-locdlg__stage'), ltabs = lq('.ik-locdlg__tabs'), lBtns = [], lcur = feat, lOpener = null, lScrollY = 0, lT;
    locImgs.forEach(function (src, i) {
      /* whole photo (contain) over a blurred copy of itself; a short note replaces a photo that fails to load */
      var slot = lmk('figure', 'ik-locdlg__slot'), bg = src.cloneNode(false), im = src.cloneNode(false);
      [bg, im].forEach(function (x) { x.removeAttribute('loading'); x.removeAttribute('class'); x.sizes = '(max-width:700px) 96vw, 80vw'; x.draggable = false; });
      bg.alt = ''; bg.className = 'ik-artdlg__blur'; im.alt = src.alt || ('INKO Melbourne location, photo ' + (i + 1));
      im.addEventListener('error', function () { slot.classList.add('is-broken'); });
      slot.appendChild(bg); slot.appendChild(im); slot.appendChild(lmk('figcaption', 'ik-locdlg__err', 'This photo could not be loaded.'));
      stage.appendChild(slot);
      var b = lmk('button', 'ik-artdlg__tab ik-locdlg__tab'); b.type = 'button'; b.setAttribute('aria-label', 'Photo ' + (i + 1) + ' of ' + LN);
      var tim = src.cloneNode(false); tim.removeAttribute('class'); tim.alt = ''; tim.sizes = '160px'; tim.setAttribute('loading', 'lazy');
      b.appendChild(tim); b.appendChild(lmk('span', 'ik-artdlg__tnum', ltwo(i)));
      b.addEventListener('click', function () { lShow(i); }); ltabs.appendChild(b); lBtns.push(b);
    });
    var lFill = function () {
      lq('.ik-artdlg__num b').textContent = ltwo(lcur); lq('.ik-artdlg__num span').textContent = '/ ' + ltwo(LN - 1);
      [].forEach.call(stage.children, function (s, i) { s.classList.toggle('is-on', i === lcur); });
      lBtns.forEach(function (b, i) { if (i === lcur) b.setAttribute('aria-current', 'true'); else b.removeAttribute('aria-current'); });
      lq('.ik-artdlg__live').textContent = 'Photo ' + (lcur + 1) + ' of ' + LN;
    };
    var lShow = function (i) {
      var nx = ((i % LN) + LN) % LN; if (nx === lcur) return; lcur = nx;
      clearTimeout(lT); lFill();
      var on = lBtns[lcur]; if (on.scrollIntoView) on.scrollIntoView({ block: 'nearest', inline: 'nearest' });
    };
    var lOpen = function (opener) {
      if (ld.open) return;
      lOpener = opener || document.activeElement; lScrollY = window.scrollY; lcur = feat;
      lFill(); document.documentElement.classList.add('ik-dlg-open');
      ld.showModal(); lq('.ik-artdlg__x').focus({ preventScroll: true });
    };
    var lClose = function () { if (ld.open) ld.close(); };
    ld.addEventListener('close', function () {
      document.documentElement.classList.remove('ik-dlg-open');
      if (Math.abs(window.scrollY - lScrollY) > 1) window.scrollTo(0, lScrollY);
      if (lOpener && lOpener.focus) lOpener.focus({ preventScroll: true });
    });
    openBtn.addEventListener('click', function () { lOpen(openBtn); });
    lq('.ik-artdlg__x').addEventListener('click', lClose);
    lq('.ik-artdlg__btn--p').addEventListener('click', function () { lShow(lcur - 1); });
    lq('.ik-artdlg__btn--n').addEventListener('click', function () { lShow(lcur + 1); });
    var lDown = false;
    ld.addEventListener('pointerdown', function (e) { lDown = e.target === ld; });
    ld.addEventListener('click', function (e) { if (e.target === ld && lDown) lClose(); lDown = false; });
    ld.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') { e.preventDefault(); lShow(lcur + (e.key === 'ArrowRight' ? 1 : -1)); }
      else if (e.key === 'Home') { e.preventDefault(); lShow(0); } else if (e.key === 'End') { e.preventDefault(); lShow(LN - 1); }
      e.stopPropagation();
    });
    var lsx = null, lsy = 0;
    stage.addEventListener('touchstart', function (e) { if (e.touches.length === 1) { lsx = e.touches[0].clientX; lsy = e.touches[0].clientY; } }, { passive: true });
    stage.addEventListener('touchend', function (e) {
      var t = e.changedTouches && e.changedTouches[0]; if (lsx === null || !t) return;
      var dx = t.clientX - lsx, dy = t.clientY - lsy; lsx = null;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.2) lShow(lcur + (dx < 0 ? 1 : -1));
    }, { passive: true });
    api.on('change', function () { if (ld.open) lClose(); });
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
  /* ---------- "Meet the team": artist profile dialog (native <dialog>, opened from the hero's MEET THE TEAM button).
     One record per artist, built from the two existing sources so photo and details can never drift apart:
     the team cards in Elementor ("ik-team": name / AKA / role / bio, in their approved order) and the slide photo that
     carries the matching "ik-artist-<name>" class. The Elementor team panel itself stays hidden as the data source. ---------- */
  var team = $('.ik-team');
  var dlgList = [];
  if (team && stack && window.HTMLDialogElement) {
    $$('.ik-acard', team).forEach(function (c) {
      var n = $('.ik-acard__n', c), key = n && slug(n.textContent), im = key && $('.s-art__ph.ik-artist-' + key + ' img', stack);
      var t = function (k) { var e = $('.ik-acard__' + k, c); return e ? e.textContent.trim() : ''; };
      if (im) dlgList.push({ key: key, name: t('n'), aka: t('a').replace(/^aka\s+/i, ''), role: t('r'), bio: t('d'), img: im });
    });
  }
  if (dlgList.length) {
    var D = dlgList.length, dcur = 0, dOpener = null, dScrollY = 0, dT;
    var mk = function (tag, cls, txt) { var e = document.createElement(tag); if (cls) e.className = cls; if (txt != null) e.textContent = txt; return e; };
    var two = function (i) { return ('0' + (i + 1)).slice(-2); };
    var dlg = mk('dialog', 'ik-artdlg'); dlg.setAttribute('aria-labelledby', 'ik-artdlg-name');
    dlg.innerHTML =
      '<div class="ik-artdlg__box">' +
        '<button type="button" class="ik-artdlg__x" aria-label="Close artist profiles"></button>' +
        '<div class="ik-artdlg__main" data-ik-scroll>' +
          '<figure class="ik-artdlg__ph"></figure>' +
          '<div class="ik-artdlg__info">' +
            '<p class="ik-artdlg__eyebrow">Meet our artists</p>' +
            '<p class="ik-artdlg__num" aria-hidden="true"><b></b><span></span></p>' +
            '<h2 class="ik-artdlg__name" id="ik-artdlg-name"></h2>' +
            '<p class="ik-artdlg__aka">AKA <b></b></p>' +
            '<p class="ik-artdlg__role"></p>' +
            '<p class="ik-artdlg__bio"></p>' +
            '<a class="ik-artdlg__cta" href="#fine-line"><span>View work</span>' + ARROW_R + '</a>' +
          '</div>' +
        '</div>' +
        '<div class="ik-artdlg__bar">' +
          '<button type="button" class="ik-artdlg__btn ik-artdlg__btn--p" aria-label="Previous artist">' + ARROW_L + '</button>' +
          '<div class="ik-artdlg__tabs" role="group" aria-label="Choose an artist"></div>' +
          '<button type="button" class="ik-artdlg__btn ik-artdlg__btn--n" aria-label="Next artist">' + ARROW_R + '</button>' +
        '</div>' +
        '<p class="ik-artdlg__live ik-sr" aria-live="polite"></p>' +
      '</div>';
    document.body.appendChild(dlg);
    var q = function (s) { return $(s, dlg); };
    var fig = q('.ik-artdlg__ph'), tabs = q('.ik-artdlg__tabs'), mainEl = q('.ik-artdlg__main'), tabBtns = [];
    dlgList.forEach(function (a, i) {
      /* portrait: the whole photo is shown (contain) over a soft, blurred copy of itself, so nothing is cropped away */
      var bg = a.img.cloneNode(false), im = a.img.cloneNode(false);
      [bg, im].forEach(function (x) { x.removeAttribute('loading'); x.removeAttribute('class'); x.sizes = '(max-width:700px) 92vw, 46vw'; x.draggable = false; });
      bg.alt = ''; bg.className = 'ik-artdlg__blur'; im.alt = a.name + ' (' + a.aka + '), INKO Melbourne tattoo artist, at work';
      var slot = mk('div', 'ik-artdlg__slot'); slot.appendChild(bg); slot.appendChild(im); fig.appendChild(slot);
      var b = mk('button', 'ik-artdlg__tab'); b.type = 'button'; b.setAttribute('aria-label', a.name + ', AKA ' + a.aka);
      var tim = a.img.cloneNode(false); tim.removeAttribute('class'); tim.alt = ''; tim.sizes = '120px'; tim.setAttribute('loading', 'lazy');
      b.appendChild(tim); b.appendChild(mk('span', 'ik-artdlg__tnum', two(i))); b.appendChild(mk('span', 'ik-artdlg__tname', a.name));
      b.addEventListener('click', function () { dShow(i); }); tabs.appendChild(b); tabBtns.push(b);
    });
    var dFill = function () {
      var a = dlgList[dcur];
      q('.ik-artdlg__num b').textContent = two(dcur); q('.ik-artdlg__num span').textContent = '/ ' + two(D - 1);
      q('.ik-artdlg__name').textContent = a.name; q('.ik-artdlg__aka b').textContent = a.aka;
      q('.ik-artdlg__role').textContent = a.role; q('.ik-artdlg__bio').textContent = a.bio;
      q('.ik-artdlg__cta').setAttribute('aria-label', 'View our work (Fine-Line and Realism galleries)');
      dlg.setAttribute('data-artist', a.key);
      [].forEach.call(fig.children, function (s, i) { s.classList.toggle('is-on', i === dcur); });
      tabBtns.forEach(function (b, i) { if (i === dcur) b.setAttribute('aria-current', 'true'); else b.removeAttribute('aria-current'); });
      q('.ik-artdlg__live').textContent = 'Artist ' + (dcur + 1) + ' of ' + D + ': ' + a.name;
      mainEl.scrollTop = 0;
    };
    /* every field of the profile is written in one go (dFill), so a name is never shown with another artist's photo */
    var dShow = function (i) {
      var nx = ((i % D) + D) % D; if (nx === dcur) return; dcur = nx;
      clearTimeout(dT);
      if (rm.matches) return dFill();
      dlg.classList.add('is-swap');
      dT = setTimeout(function () { dFill(); dlg.classList.remove('is-swap'); }, 180);
    };
    var dOpen = function (opener) {
      if (dlg.open) return;
      dOpener = opener || document.activeElement; dScrollY = window.scrollY;
      dFill(); document.documentElement.classList.add('ik-dlg-open');
      dlg.showModal(); q('.ik-artdlg__x').focus({ preventScroll: true });
    };
    var dClose = function () { if (dlg.open) dlg.close(); };
    dlg.addEventListener('close', function () {
      document.documentElement.classList.remove('ik-dlg-open');
      if (dScrollY !== null && Math.abs(window.scrollY - dScrollY) > 1) window.scrollTo(0, dScrollY);
      if (dOpener && dOpener.focus) dOpener.focus({ preventScroll: true });
    });
    q('.ik-artdlg__x').addEventListener('click', dClose);
    q('.ik-artdlg__btn--p').addEventListener('click', function () { dShow(dcur - 1); });
    q('.ik-artdlg__btn--n').addEventListener('click', function () { dShow(dcur + 1); });
    /* backdrop click closes (a press that starts inside the box and ends outside it does not) */
    var downOnBackdrop = false;
    dlg.addEventListener('pointerdown', function (e) { downOnBackdrop = e.target === dlg; });
    dlg.addEventListener('click', function (e) { if (e.target === dlg && downOnBackdrop) dClose(); downOnBackdrop = false; });
    dlg.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') { e.preventDefault(); dShow(dcur + (e.key === 'ArrowRight' ? 1 : -1)); }
      else if (e.key === 'Home') { e.preventDefault(); dShow(0); } else if (e.key === 'End') { e.preventDefault(); dShow(D - 1); }
      e.stopPropagation();
    });
    /* swipe on the portrait */
    var dsx = null, dsy = 0;
    fig.addEventListener('touchstart', function (e) { if (e.touches.length === 1) { dsx = e.touches[0].clientX; dsy = e.touches[0].clientY; } }, { passive: true });
    fig.addEventListener('touchend', function (e) {
      var t = e.changedTouches && e.changedTouches[0]; if (dsx === null || !t) return;
      var dx = t.clientX - dsx, dy = t.clientY - dsy; dsx = null;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.2) dShow(dcur + (dx < 0 ? 1 : -1));
    }, { passive: true });
    /* "View work" closes the dialog and goes to the Fine-Line gallery (the link is handled by the slide navigation) */
    q('.ik-artdlg__cta').addEventListener('click', function () { dOpener = null; dScrollY = null; dClose(); });
    /* MEET THE TEAM (any link to #ik-team) opens the dialog instead of the old team panel */
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href="#ik-team"]'); if (a) { e.preventDefault(); e.stopPropagation(); dOpen(a); }
    }, true);
    api.on('change', function () { if (dlg.open) dClose(); });
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
