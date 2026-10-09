/* INKO Melbourne - navigation.js: mobile menu (every page), header nav highlight, slide counter, scroll cue, keyboard (slide pages). */
(function () {
  'use strict';
  var INKO = (window.INKO = window.INKO || {});
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return [].slice.call((c || document).querySelectorAll(s)); };

  /* which nav item (by slide id) is highlighted for each slide; unknown/new slides inherit the previous one */
  var GROUP = { home: 'home', specialities: 'fine-line', 'fine-line': 'fine-line', realism: 'fine-line', artists: 'artists', 'follow-us': 'fine-line',
    location: 'location', reviews: 'location', 'pay-later': 'location', promotions: 'fine-line', quote: 'quote', footer: 'quote' };

  /* burger + full-screen menu: used on slide pages AND ordinary pages */
  INKO.initMenu = function () {
    var menu = $('.ik-menu'), burger = $('.ik-burger');
    function openMenu() {
      menu.classList.add('is-open'); burger.setAttribute('aria-expanded', 'true'); burger.setAttribute('aria-label', 'Close menu');
      document.documentElement.style.overflow = 'hidden';
      var f = $('a', menu); if (f) f.focus({ preventScroll: true });
    }
    function closeMenu() {
      if (!menu || !menu.classList.contains('is-open')) return;
      menu.classList.remove('is-open'); burger.setAttribute('aria-expanded', 'false'); burger.setAttribute('aria-label', 'Open menu');
      document.documentElement.style.overflow = '';
    }
    if (burger && menu) burger.addEventListener('click', function () { menu.classList.contains('is-open') ? closeMenu() : openMenu(); });
    document.addEventListener('keydown', function (e) {
      if (!menu || !menu.classList.contains('is-open')) return;
      if (e.key === 'Escape') { closeMenu(); burger.focus(); }
      if (e.key === 'Tab') {
        var f = [burger].concat($$('a', menu)), i = f.indexOf(document.activeElement);
        if (e.shiftKey && i <= 0) { e.preventDefault(); f[f.length - 1].focus(); }
        else if (!e.shiftKey && i === f.length - 1) { e.preventDefault(); f[0].focus(); }
      }
    });
    return { closeMenu: closeMenu };
  };

  INKO.initNavigation = function (api) {
    var header, menu, cue, mini, countEl;
    /* one header only: drop any extra copies injected by older templates/plugins */
    ['.ik-header', '.ik-menu', '.ik-label', '.ik-rail', '.ik-cue', '.ik-count:not(.ik-count--mini)', '.ik-count--mini'].forEach(function (sel) {
      var all = $$(sel); var keep = all.filter(function (e) { return !e.closest('.elementor'); })[0] || all[0];
      all.forEach(function (e) { if (e !== keep) e.parentNode.removeChild(e); });
    });
    header = $('.ik-header'); menu = $('.ik-menu'); cue = $('.ik-cue'); mini = $('.ik-count--mini'); countEl = $('.ik-count:not(.ik-count--mini)');
    var logoBox = header && $('.ik-logo--box', header), logoPlain = header && $('.ik-logo--plain', header);
    var wide = window.matchMedia('(min-width:1181px)');
    var N = api.N, ids = api.ids, navId = [];
    ids.forEach(function (id, i) { navId[i] = GROUP[id] || (i ? navId[i - 1] : id); });
    var closeMenu = INKO.initMenu().closeMenu;

    var counts = [];
    if (countEl) {
      api.slides.forEach(function (s, i) {
        var li = document.createElement('li'), b = document.createElement('button');
        b.type = 'button'; b.textContent = ('0' + (i + 1)).slice(-2);
        b.setAttribute('data-go', i); b.setAttribute('aria-label', 'Go to section ' + (i + 1));
        li.appendChild(b); countEl.appendChild(li); counts.push(b);
      });
    }

    api.on('progress', function (p) {
      if (header) {
        var hero = p < 0.5, boxOn = hero && wide.matches;
        header.classList.toggle('is-hero', hero);
        if (logoBox) logoBox.hidden = !boxOn;
        if (logoPlain) logoPlain.hidden = boxOn;
      }
    });
    api.on('change', function (a) {
      var target = navId[a];
      document.body.setAttribute('data-ik-slide', ids[a]);
      $$('.ik-nav__a a, .ik-menu__a a').forEach(function (l) {
        var h = (l.getAttribute('href') || '').split('#')[1];
        if (h === target) l.setAttribute('aria-current', 'true'); else l.removeAttribute('aria-current');
      });
      counts.forEach(function (b, i) { if (i === a) b.setAttribute('aria-current', 'true'); else b.removeAttribute('aria-current'); });
      if (cue) {
        var end = a === N - 1;
        cue.classList.toggle('is-end', end);
        cue.setAttribute('aria-label', end ? 'Back to top' : 'Next section');
        var lab = $('span', cue); if (lab) lab.textContent = end ? 'Top' : 'Scroll';
      }
    });

    /* links: "#slide-id" or "/#slide-id" scroll to the slide; [data-go] = slide number or "next" */
    document.addEventListener('click', function (e) {
      var g = e.target.closest('[data-go]');
      if (g) {
        e.preventDefault(); closeMenu();
        var v = g.getAttribute('data-go');
        api.goTo(v === 'next' ? (api.active() >= N - 1 ? 0 : api.active() + 1) : +v);
        return;
      }
      var a = e.target.closest('a[href*="#"]');
      if (!a) return;
      if (a.getAttribute('href') === '#') { e.preventDefault(); return; }
      var u; try { u = new URL(a.href, location.href); } catch (x) { return; }
      if (u.origin !== location.origin || u.pathname.replace(/\/$/, '') !== location.pathname.replace(/\/$/, '') || !u.hash) return;
      var i = ids.indexOf(u.hash.slice(1));
      if (i > -1) { e.preventDefault(); closeMenu(); api.goTo(i); }
    });

    document.addEventListener('keydown', function (e) {
      if (e.altKey || e.ctrlKey || e.metaKey) return;
      var t = e.target, tag = t && t.tagName;
      if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (t && t.isContentEditable)) return;
      if ((menu && menu.classList.contains('is-open')) || INKO.locked()) return;
      var k = e.key;
      if (k === 'ArrowDown' || k === 'PageDown' || (k === ' ' && !e.shiftKey)) {
        if (tag === 'BUTTON' && k === ' ') return;
        e.preventDefault(); api.stepBy(1);
      } else if (k === 'ArrowUp' || k === 'PageUp' || (k === ' ' && e.shiftKey)) { e.preventDefault(); api.stepBy(-1); }
      else if (k === 'Home') { e.preventDefault(); api.goTo(0); }
      else if (k === 'End') { e.preventDefault(); api.goTo(N - 1); }
      else if (k === 'ArrowLeft' || k === 'ArrowRight') {
        var car = api.slides[api.active()] && api.slides[api.active()].__car;
        if (car) { e.preventDefault(); car.go(car.idx + (k === 'ArrowRight' ? 1 : -1)); }
      }
    });
  };
})();
