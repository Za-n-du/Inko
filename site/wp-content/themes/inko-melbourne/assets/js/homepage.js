/* INKO Melbourne vertically scrolling homepage interactions. */
(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return [].slice.call((c || document).querySelectorAll(s)); };
  var header = $('.ik-header.is-home');
  var menuApi = window.INKO && window.INKO.initMenu ? window.INKO.initMenu() : null;
  if (menuApi) $$('.ik-menu a').forEach(function (a) { a.addEventListener('click', menuApi.closeMenu); });

  function onScroll() {
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 24);
  }
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  var links = $$('.ik-nav a, .ik-menu__a a');
  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var id = entry.target.id;
        links.forEach(function (a) {
          if (a.hash === '#' + id) a.setAttribute('aria-current', 'true');
          else a.removeAttribute('aria-current');
        });
      });
    }, { rootMargin: '-32% 0px -55% 0px', threshold: 0 });
    $$('.inko-home > section[id], .inko-home > footer[id]').forEach(function (section) { observer.observe(section); });
  }

  $$('[data-carousel]').forEach(function (track) {
    var name = track.getAttribute('data-carousel');
    var prev = $('[data-carousel-prev="' + name + '"]'), next = $('[data-carousel-next="' + name + '"]');
    var step = function () { return Math.max(240, Math.round(track.clientWidth * .78)); };
    if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
    var down = false, start = 0, origin = 0;
    track.addEventListener('pointerdown', function (e) {
      if (e.pointerType !== 'mouse' || e.button !== 0) return;
      down = true; start = e.clientX; origin = track.scrollLeft; track.classList.add('is-dragging');
      try { track.setPointerCapture(e.pointerId); } catch (x) {}
    });
    track.addEventListener('pointermove', function (e) { if (down) track.scrollLeft = origin - (e.clientX - start); });
    function release() { down = false; track.classList.remove('is-dragging'); }
    track.addEventListener('pointerup', release); track.addEventListener('pointercancel', release);
  });

  if (window.INKOHomeConfig && window.INKOHomeConfig.wordpress) {
    var form = $('#quote-form');
    if (form) form.action = window.INKOHomeConfig.quoteUrl;
  }
})();
