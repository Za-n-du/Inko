/* INKO Melbourne - quotation form: client-side validation and submission to /api/quote.
   The server (api/quote.js) repeats every check; success is shown only when it confirms the email was accepted. */
(function () {
  'use strict';
  var form = document.getElementById('quote-form');
  if (!form) return;

  var MAX_BYTES = 4 * 1024 * 1024;
  var EXTS = ['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif', 'pdf'];
  var EMAIL_RE = /^[^\s@<>()",;:]+@[^\s@<>()",;:]+\.[^\s@<>()",;:]{2,}$/;
  var PHONE_RE = /^[+()0-9 .-]{6,25}$/;

  var $ = function (id) { return document.getElementById(id); };
  var fields = {
    firstName: { el: $('q-first'), err: $('q-first-err') },
    lastName: { el: $('q-last'), err: $('q-last-err') },
    email: { el: $('q-email'), err: $('q-email-err') },
    phone: { el: $('q-phone'), err: $('q-phone-err') },
    details: { el: $('q-details'), err: $('q-details-err') },
    file: { el: $('q-file'), err: $('q-file-err') }
  };
  var fileBox = form.querySelector('.q-file'), fileName = $('q-file-name'), fileClear = $('q-file-clear');
  var status = $('q-status'), done = $('q-done'), send = form.querySelector('.q-send'), sendTxt = form.querySelector('.q-send__txt');
  var sending = false, submissionId = null;

  var checks = {
    firstName: function (v) { return !v ? 'Please enter your first name.' : ''; },
    lastName: function (v) { return !v ? 'Please enter your last name.' : ''; },
    email: function (v) { return !v ? 'Please enter your email address.' : !EMAIL_RE.test(v) ? 'Please enter a valid email address, e.g. name@example.com.' : ''; },
    phone: function (v) { return v && !PHONE_RE.test(v) ? 'Please enter a valid phone number (digits, spaces, + and brackets only).' : ''; },
    details: function (v) { return !v ? 'Please describe the size, placement and color preference.' : v.length < 10 ? 'Please add a little more detail (at least 10 characters).' : ''; },
    file: function () {
      var f = fields.file.el.files[0];
      if (!f) return '';
      var ext = (f.name.split('.').pop() || '').toLowerCase();
      if (EXTS.indexOf(ext) < 0) return 'This file type is not supported. Please upload a JPG, PNG, WEBP, HEIC or PDF file.';
      if (f.size > MAX_BYTES) return 'This file is ' + (f.size / 1048576).toFixed(1) + ' MB. Please upload a file under 4 MB.';
      if (f.size === 0) return 'This file is empty. Please choose another file.';
      return '';
    }
  };

  var showError = function (key, msg) {
    var f = fields[key];
    f.err.textContent = msg || '';
    if (key === 'file') fileBox.classList.toggle('is-invalid', !!msg);
    if (msg) f.el.setAttribute('aria-invalid', 'true'); else f.el.removeAttribute('aria-invalid');
  };
  var check = function (key) {
    var msg = checks[key](key === 'file' ? null : fields[key].el.value.trim());
    showError(key, msg);
    return !msg;
  };

  // validate a field when the visitor leaves it, and clear its error as soon as it is fixed
  Object.keys(fields).forEach(function (key) {
    if (key === 'file') return;
    var el = fields[key].el;
    el.addEventListener('blur', function () { if (el.value.trim() || el.hasAttribute('aria-invalid')) check(key); });
    el.addEventListener('input', function () { if (el.hasAttribute('aria-invalid')) check(key); });
  });

  fields.file.el.addEventListener('change', function () {
    var f = fields.file.el.files[0];
    fileName.textContent = f ? f.name : 'No file chosen';
    fileClear.hidden = !f;
    check('file');
  });
  fileClear.addEventListener('click', function () {
    fields.file.el.value = '';
    fileName.textContent = 'No file chosen';
    fileClear.hidden = true;
    showError('file', '');
    fields.file.el.focus();
  });

  var setSending = function (on) {
    sending = on;
    send.disabled = on;
    send.setAttribute('aria-busy', on ? 'true' : 'false');
    sendTxt.textContent = on ? 'Sending…' : 'Send';
  };
  var showStatus = function (msg) {
    status.textContent = msg;
    status.hidden = !msg;
  };
  var newId = function () {
    if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
    return Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 12);
  };
  // a changed form is a new request; an unchanged retry reuses the id so the studio never gets it twice
  form.addEventListener('input', function () { if (!sending) submissionId = null; });
  form.addEventListener('change', function () { if (!sending) submissionId = null; });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (sending) return;
    showStatus('');

    var firstBad = null;
    Object.keys(fields).forEach(function (key) { if (!check(key) && !firstBad) firstBad = fields[key].el; });
    if (firstBad) {
      showStatus('Please check the highlighted fields.');
      (firstBad === fields.file.el ? form.querySelector('.q-file__btn') : firstBad).scrollIntoView({ block: 'center', behavior: 'smooth' });
      firstBad.focus({ preventScroll: true });
      return;
    }

    if (!submissionId) submissionId = newId();
    var data = new FormData(form);
    data.set('submissionId', submissionId);
    setSending(true);

    fetch(form.action, { method: 'POST', body: data, headers: { Accept: 'application/json' } })
      .then(function (res) {
        return res.json().catch(function () { return {}; }).then(function (body) { return { res: res, body: body }; });
      })
      .then(function (r) {
        if (r.res.ok && r.body.ok === true) {
          form.hidden = true;
          done.hidden = false;
          done.focus();
          done.scrollIntoView({ block: 'center', behavior: 'smooth' });
          return;
        }
        var errs = r.body.fields || {};
        Object.keys(errs).forEach(function (key) { if (fields[key]) showError(key, errs[key]); });
        showStatus((r.body.error || 'Something went wrong while sending your request.') + ' You can also email us at inko.melbourne@gmail.com.');
        setSending(false);
      })
      .catch(function () {
        showStatus('We could not reach the server. Please check your connection and try again, or email us at inko.melbourne@gmail.com.');
        setSending(false);
      });
  });
})();
