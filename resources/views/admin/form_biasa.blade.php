@extends('layouts.app')

@section('title', 'Form Barang - Tabel Biasa')

@section('content')
<div class="card p-4 shadow-sm">
  <h4 class="mb-4">Form Input Barang (Tabel Biasa)</h4>

  <form id="barangForm" novalidate>
    @csrf

    <!-- ID (opsional) -->
    <div class="row mb-3 align-items-center">
      <label for="id_barang" class="col-md-3 col-form-label">ID barang :</label>
      <div class="col-md-9">
        <input id="id_barang" class="form-control" type="text" placeholder="Isi ID barang (kosong = auto)">
        <div id="errId" class="error" style="display:none;color:#c92a2a">ID tidak valid atau sudah dipakai.</div>
      </div>
    </div>

    <!-- Nama (required) -->
    <div class="row mb-3 align-items-center">
      <label for="nama" class="col-md-3 col-form-label">Nama barang : <span style="color:#c92a2a">*</span></label>
      <div class="col-md-9">
        <input id="nama" class="form-control" type="text" placeholder="Masukkan nama barang" required>
        <div id="errNama" class="error" style="display:none;color:#c92a2a">Nama barang wajib diisi.</div>
      </div>
    </div>

    <!-- Harga (required) -->
    <div class="row mb-3 align-items-center">
      <label for="harga" class="col-md-3 col-form-label">Harga barang : <span style="color:#c92a2a">*</span></label>
      <div class="col-md-9">
        <input id="harga" class="form-control" type="number" placeholder="Masukkan harga (angka)" required min="0" step="any">
        <div id="errHarga" class="error" style="display:none;color:#c92a2a">Harga barang wajib diisi (angka ≥ 0).</div>
      </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3"></div>
        <div class="col-md-9 d-flex justify-content-end">
          <button id="submitBtn" class="btn btn-primary" type="submit">submit</button>
        </div>
      </div>
  </form>

  <table id="tabelBarang" class="mt-4 table table-striped table-bordered">
    <thead>
      <tr>
        <th style="width:130px">ID barang</th>
        <th>Nama</th>
        <th>Harga</th>
      </tr>
    </thead>
    <tbody>
      <!-- rows akan ditambahkan di sini -->
    </tbody>
  </table>

  <div class="mt-3">
    <strong>Ketentuan:</strong>
    <ol>
      <li>ID boleh dikosongkan (auto-generate) atau diisi manual (alfanumerik, underscore, dash).</li>
      <li>Nama barang dan harga barang adalah <em>required</em>.</li>
      <li>Ketika disubmit:
        <ol type="i">
          <li>Jalankan validasi required + ID.</li>
          <li>Input dikosongkan setelah submit.</li>
          <li>Nama & harga ditambahkan ke row baru pada table. ID barang berurutan (1,2,3...) jika auto.</li>
          <li>Data tidak disimpan ke Database (hanya di browser memory).</li>
        </ol>
      </li>
    </ol>
  </div>
</div>

<!-- Modal Edit / Delete -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title">Ubah / Hapus Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="modalForm" novalidate>
          <div class="mb-3 row align-items-center">
            <label class="col-md-3 col-form-label">ID barang :</label>
            <div class="col-md-9">
              <input id="modal_id" class="form-control" type="text" readonly>
            </div>
          </div>

          <div class="mb-3 row align-items-center">
            <label class="col-md-3 col-form-label">Nama barang :</label>
            <div class="col-md-9">
              <input id="modal_nama" class="form-control" type="text" required>
              <div id="modal_errNama" class="error" style="display:none;color:#c92a2a">Nama wajib diisi</div>
            </div>
          </div>

          <div class="mb-3 row align-items-center">
            <label class="col-md-3 col-form-label">Harga barang :</label>
            <div class="col-md-9">
              <input id="modal_harga" class="form-control" type="number" required min="0" step="any">
              <div id="modal_errHarga" class="error" style="display:none;color:#c92a2a">Harga harus angka ≥ 0</div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer border-0">
        <button id="modalDelete" type="button" class="btn btn-danger">Hapus</button>
        <button id="modalUpdate" type="button" class="btn btn-success">Ubah</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('script-page')
<script>
(function () {
  const form = document.getElementById('barangForm');
  const idEl = document.getElementById('id_barang');
  const namaEl = document.getElementById('nama');
  const hargaEl = document.getElementById('harga');

  const errId = document.getElementById('errId');
  const errNama = document.getElementById('errNama');
  const errHarga = document.getElementById('errHarga');

  const tbody = document.querySelector('#tabelBarang tbody');
  const submitBtn = document.getElementById('submitBtn');

  // modal elements
  const modalEl = document.getElementById('editModal');
  const bsModal = new bootstrap.Modal(modalEl);
  const modal_id = document.getElementById('modal_id');
  const modal_nama = document.getElementById('modal_nama');
  const modal_harga = document.getElementById('modal_harga');
  const modal_errNama = document.getElementById('modal_errNama');
  const modal_errHarga = document.getElementById('modal_errHarga');
  const modalDelete = document.getElementById('modalDelete');
  const modalUpdate = document.getElementById('modalUpdate');

  let nextId = 1;
  let currentRow = null; // TR yang sedang diedit

  // pointer on hover (CSS injection)
  const style = document.createElement('style');
  style.innerHTML = `
    #tabelBarang tbody tr:hover { cursor: pointer; }
    .is-invalid { border-color: #dc3545; }
  `;
  document.head.appendChild(style);

  function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
  }

  function formatNumber(n) {
    return Number(n).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
  }

  function isIdDuplicate(idVal) {
    if (idVal === '') return false;
    const rows = tbody.querySelectorAll('tr');
    for (let i = 0; i < rows.length; i++) {
      const cell = rows[i].querySelector('td');
      if (!cell) continue;
      if (String(cell.textContent).trim() === String(idVal).trim()) return true;
    }
    return false;
  }

  function showIdError(msg) {
    errId.textContent = msg;
    errId.style.display = 'block';
    idEl.classList.add('is-invalid');
  }

  function hideIdError() {
    errId.style.display = 'none';
    idEl.classList.remove('is-invalid');
  }

  function validate() {
    let ok = true;

    // ID: optional. jika diisi, cek pola & unik
    const idVal = idEl.value.trim();
    if (idVal !== '') {
      const re = /^[A-Za-z0-9_-]+$/;
      if (!re.test(idVal)) {
        showIdError('ID hanya boleh huruf, angka, underscore atau tanda -');
        ok = false;
      } else if (isIdDuplicate(idVal)) {
        showIdError('ID sudah dipakai, gunakan ID lain');
        ok = false;
      } else {
        hideIdError();
      }
    } else {
      while (isIdDuplicate(nextId)) nextId++;
      hideIdError();
    }

    // Nama
    if (!namaEl.value.trim()) {
      errNama.style.display = 'block';
      namaEl.classList.add('is-invalid');
      ok = false;
    } else {
      errNama.style.display = 'none';
      namaEl.classList.remove('is-invalid');
    }

    // Harga
    const hargaVal = hargaEl.value;
    if (hargaVal === '' || isNaN(Number(hargaVal)) || Number(hargaVal) < 0) {
      errHarga.style.display = 'block';
      hargaEl.classList.add('is-invalid');
      ok = false;
    } else {
      errHarga.style.display = 'none';
      hargaEl.classList.remove('is-invalid');
    }

    return ok;
  }

  function clearInputs() {
    idEl.value = '';
    namaEl.value = '';
    hargaEl.value = '';
    hideIdError();
    errNama.style.display = 'none';
    errHarga.style.display = 'none';
    namaEl.focus();
  }

  // realtime validation
  idEl.addEventListener('input', function () {
    const v = idEl.value.trim();
    if (v === '') { hideIdError(); return; }
    const re = /^[A-Za-z0-9_-]+$/;
    if (!re.test(v)) showIdError('ID hanya boleh huruf, angka, underscore atau tanda -');
    else if (isIdDuplicate(v)) showIdError('ID sudah dipakai, gunakan ID lain');
    else hideIdError();
  });

  namaEl.addEventListener('input', function () {
    if (this.value.trim()) { errNama.style.display = 'none'; this.classList.remove('is-invalid'); }
  });

  hargaEl.addEventListener('input', function () {
    const v = this.value;
    if (v !== '' && !isNaN(Number(v)) && Number(v) >= 0) { errHarga.style.display = 'none'; this.classList.remove('is-invalid'); }
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    submitBtn.disabled = true;

    if (!validate()) {
      if (idEl.classList.contains('is-invalid')) idEl.focus();
      else if (namaEl.classList.contains('is-invalid')) namaEl.focus();
      else if (hargaEl.classList.contains('is-invalid')) hargaEl.focus();
      submitBtn.disabled = false;
      return;
    }

    let idVal = idEl.value.trim();
    if (idVal === '') {
      while (isIdDuplicate(nextId)) nextId++;
      idVal = String(nextId++);
    }

    const nama = namaEl.value.trim();
    const harga = Number(hargaEl.value);

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="text-align:center">${escapeHtml(idVal)}</td>
      <td>${escapeHtml(nama)}</td>
      <td style="text-align:right">${formatNumber(harga)}</td>
    `;
    tbody.appendChild(tr);

    clearInputs();
    submitBtn.disabled = false;
  });

  // klik row -> buka modal dan isi fields
  tbody.addEventListener('click', function (ev) {
    let tr = ev.target.closest('tr');
    if (!tr) return;
    currentRow = tr;

    const cells = tr.querySelectorAll('td');
    const idVal = cells[0].textContent.trim();
    const namaVal = cells[1].textContent.trim();
    const hargaVal = cells[2].textContent.trim().replace(/\./g,'').replace(/,/g,'.'); // ambil angka kasar

    modal_id.value = idVal;
    modal_nama.value = namaVal;
    modal_harga.value = hargaVal;

    modal_errNama.style.display = 'none';
    modal_errHarga.style.display = 'none';
    modal_nama.classList.remove('is-invalid');
    modal_harga.classList.remove('is-invalid');

    bsModal.show();
  });

  function validateModal() {
    let ok = true;
    if (!modal_nama.value.trim()) {
      modal_errNama.style.display = 'block';
      modal_nama.classList.add('is-invalid');
      ok = false;
    } else {
      modal_errNama.style.display = 'none';
      modal_nama.classList.remove('is-invalid');
    }

    if (modal_harga.value === '' || isNaN(Number(modal_harga.value)) || Number(modal_harga.value) < 0) {
      modal_errHarga.style.display = 'block';
      modal_harga.classList.add('is-invalid');
      ok = false;
    } else {
      modal_errHarga.style.display = 'none';
      modal_harga.classList.remove('is-invalid');
    }

    return ok;
  }

  // Hapus row
  modalDelete.addEventListener('click', function () {
    if (!currentRow) { bsModal.hide(); return; }
    currentRow.remove();
    currentRow = null;
    bsModal.hide();
  });

  // Ubah row
  modalUpdate.addEventListener('click', function () {
    if (!currentRow) { bsModal.hide(); return; }
    if (!validateModal()) return;

    const nama = modal_nama.value.trim();
    const harga = Number(modal_harga.value);

    const cells = currentRow.querySelectorAll('td');
    cells[1].textContent = nama;
    cells[2].textContent = formatNumber(harga);

    currentRow = null;
    bsModal.hide();
  });

  // input modal realtime hide error
  modal_nama.addEventListener('input', function () {
    if (this.value.trim()) { modal_errNama.style.display = 'none'; this.classList.remove('is-invalid'); }
  });
  modal_harga.addEventListener('input', function () {
    const v = this.value;
    if (v !== '' && !isNaN(Number(v)) && Number(v) >= 0) { modal_errHarga.style.display = 'none'; this.classList.remove('is-invalid'); }
  });
})();
</script>
@endpush