// public/js/form-global.js
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    // cari semua tombol yang mengontrol submit form di luar form
    const buttons = document.querySelectorAll('.js-external-submit');

    buttons.forEach(btn => {
      // target form: ambil dari data-target (contoh: data-target="#createBarangForm")
      const targetSelector = btn.dataset.target || btn.getAttribute('data-target') || btn.getAttribute('form');
      if (!targetSelector) return;

      const form = document.querySelector(targetSelector);
      if (!form) return;

      // cari atau buat elemen alert untuk menampilkan daftar field yang invalid
      let formAlert = form.querySelector('.form-alert') || form.querySelector('#formAlert');
      if (!formAlert) {
        formAlert = document.createElement('div');
        formAlert.className = 'alert alert-warning form-alert d-none';
        formAlert.setAttribute('role', 'alert');
        formAlert.setAttribute('aria-live', 'polite');
        // letakkan di atas form (atau pertama pada form body)
        if (form.firstChild) form.insertBefore(formAlert, form.firstChild);
        else form.appendChild(formAlert);
      }

      // cari elemen spinner & text di dalam tombol (opsional)
      let btnSpinner = btn.querySelector('.btn-spinner');
      let btnText = btn.querySelector('.btn-text');

      // jika tidak ada spinner, kita bisa buat satu tapi sembunyikan dulu
      if (!btnSpinner) {
        btnSpinner = document.createElement('span');
        btnSpinner.className = 'spinner-border spinner-border-sm me-2 btn-spinner d-none';
        btnSpinner.setAttribute('role', 'status');
        btnSpinner.setAttribute('aria-hidden', 'true');
        // place spinner before text if text exists, else append
        if (btnText) btn.insertBefore(btnSpinner, btnText);
        else btn.appendChild(btnSpinner);
      }

      if (!btnText) {
        // jika tidak ada span.btn-text, anggap teks tombol saat ini sebagai btnText
        btnText = document.createElement('span');
        btnText.className = 'btn-text';
        btnText.textContent = btn.textContent.trim();
        btn.textContent = '';
        btn.appendChild(btnText);
      }

      let submitting = false;

      function showInvalidList() {
        const invalids = Array.from(form.querySelectorAll(':invalid'));
        if (invalids.length === 0) {
          formAlert.classList.add('d-none');
          formAlert.innerHTML = '';
          return;
        }

        const lines = invalids.map(el => {
          const id = el.id || el.name || 'field';
          const labelEl = form.querySelector(`label[for="${id}"]`);
          const labelText = labelEl ? labelEl.textContent.trim() : (el.name || id);
          return `<li>${labelText}</li>`;
        });

        formAlert.innerHTML = `<strong>Silakan lengkapi field berikut:</strong><ul class="mb-0">${lines.join('')}</ul>`;
        formAlert.classList.remove('d-none');

        // fokus ke field pertama invalid
        try { invalids[0].focus(); } catch (e) { /* ignore */ }
      }

      function setButtonBusy(isBusy) {
        if (isBusy) {
          submitting = true;
          btn.setAttribute('disabled', 'disabled');
          btn.setAttribute('aria-busy', 'true');
          btnSpinner.classList.remove('d-none');
          // ubah teks (jika perlu)
          if (btn.dataset.busyText) btnText.textContent = btn.dataset.busyText;
        } else {
          submitting = false;
          btn.removeAttribute('disabled');
          btn.setAttribute('aria-busy', 'false');
          btnSpinner.classList.add('d-none');
          if (btn.dataset.defaultText) btnText.textContent = btn.dataset.defaultText;
        }
      }

      btn.addEventListener('click', function (e) {
        if (submitting) return;

        // sembunyikan pesan lama
        formAlert.classList.add('d-none');
        formAlert.innerHTML = '';

        // cek validitas HTML5
        if (!form.checkValidity()) {
          form.reportValidity();
          showInvalidList();
          return;
        }

        // valid -> disable & spinner -> submit form
        setButtonBusy(true);

        // gunakan requestSubmit bila tersedia (lebih "user-like")
        if (typeof form.requestSubmit === 'function') {
          form.requestSubmit();
        } else {
          form.submit();
        }

        // jangan re-enable; asumsi halaman akan reload/redirect
      });

      // sembunyikan alert ketika semua valid kembali
      form.addEventListener('input', function () {
        if (!formAlert.classList.contains('d-none') && form.checkValidity()) {
          formAlert.classList.add('d-none');
          formAlert.innerHTML = '';
        }
      });
    });
  });
})();