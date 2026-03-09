@extends('layouts.app')

@section('title', 'Form Barang - DataTables')


@section('content')
  <div class="card p-4 shadow-sm">
    <h4 class="mb-4">Form Input Barang (DataTables)</h4>

    <form id="barangForm" novalidate>
      @csrf

      <div class="row mb-3 align-items-center">
        <label for="id_barang" class="col-md-3 col-form-label form-label">ID barang :</label>
        <div class="col-md-9">
          <input id="id_barang" class="form-control" type="text" placeholder="Isi ID barang (kosong = auto)">
          <div id="errId" class="error" role="alert" aria-live="polite" style="display:none;color:#c92a2a">ID tidak valid atau sudah dipakai</div>
        </div>
      </div>

      <div class="row mb-3 align-items-center">
        <label for="nama" class="col-md-3 col-form-label form-label">
          Nama barang :
        </label>
        <div class="col-md-9">
          <input id="nama" class="form-control" type="text" placeholder="Masukkan nama barang" required>
          <div id="errNama" class="error" role="alert" aria-live="polite" style="display:none;color:#c92a2a">Nama barang wajib diisi</div>
        </div>
      </div>

      <div class="row mb-3 align-items-center">
        <label for="harga" class="col-md-3 col-form-label form-label">
          Harga barang :
        </label>
        <div class="col-md-9">
          <input id="harga" class="form-control" type="number" placeholder="Masukkan harga (angka)" required min="0" step="any">
          <div id="errHarga" class="error" role="alert" aria-live="polite" style="display:none;color:#c92a2a">Harga harus berupa angka dan tidak boleh kurang dari nol</div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-3"></div>
        <div class="col-md-9 d-flex justify-content-end">
          <button id="submitBtn" class="btn btn-primary" type="submit">submit</button>
        </div>
      </div>
    </form>

    <table id="tabelBarang" class="display table table-striped" style="width:100%">
      <thead>
        <tr>
          <th>ID barang</th>
          <th>Nama</th>
          <th>Harga</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div class="mt-3">
      <strong>Catatan:</strong> DataTables hanya untuk tampilan (sorting/search/pagination). Data tetap di client.
    </div>
  </div>

  <!-- Modal Edit/Delete untuk DataTables (ID editable) -->
  <div class="modal fade" id="dtEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content p-3">
        <div class="modal-header border-0">
          <h5 class="modal-title">Ubah / Hapus Barang (DataTables)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="dtModalForm" novalidate>
            <div class="mb-3 row align-items-center">
              <label class="col-md-3 col-form-label">ID barang :</label>
              <div class="col-md-9">
                <!-- editable ID di modal -->
                <input id="dt_modal_id" class="form-control" type="text">
                <div id="dt_modal_errId" class="error" style="display:none;color:#c92a2a">ID tidak valid atau sudah dipakai</div>
              </div>
            </div>

            <div class="mb-3 row align-items-center">
              <label class="col-md-3 col-form-label">Nama barang :</label>
              <div class="col-md-9">
                <input id="dt_modal_nama" class="form-control" type="text" required>
                <div id="dt_modal_errNama" class="error" style="display:none;color:#c92a2a">Nama wajib diisi</div>
              </div>
            </div>

            <div class="mb-3 row align-items-center">
              <label class="col-md-3 col-form-label">Harga barang :</label>
              <div class="col-md-9">
                <input id="dt_modal_harga" class="form-control" type="number" required min="0" step="any">
                <div id="dt_modal_errHarga" class="error" style="display:none;color:#c92a2a">Harga harus angka ≥ 0</div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer border-0">
          <button id="dt_modal_delete" type="button" class="btn btn-danger">Hapus</button>
          <button id="dt_modal_update" type="button" class="btn btn-success">Ubah</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('script-page')
  <!-- jQuery + DataTables JS -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <script>
    $(document).ready(function () {
      // Inisialisasi DataTable
      const table = $('#tabelBarang').DataTable({
        columns: [
          { title: "ID barang", width: "110px", className: "dt-center" },
          { title: "Nama" },
          { title: "Harga", className: "dt-right" }
        ],
        data: []
      });

      // Elemen DOM
      const idEl = $('#id_barang');
      const namaEl = $('#nama');
      const hargaEl = $('#harga');
      const errId = $('#errId');
      const errNama = $('#errNama');
      const errHarga = $('#errHarga');
      const submitBtn = $('#submitBtn');

      // modal elements (DataTables) — ID editable now
      const dtModal = new bootstrap.Modal($('#dtEditModal')[0]);
      const dt_modal_id = $('#dt_modal_id');
      const dt_modal_nama = $('#dt_modal_nama');
      const dt_modal_harga = $('#dt_modal_harga');
      const dt_modal_errId = $('#dt_modal_errId');
      const dt_modal_errNama = $('#dt_modal_errNama');
      const dt_modal_errHarga = $('#dt_modal_errHarga');
      const dt_modal_delete = $('#dt_modal_delete');
      const dt_modal_update = $('#dt_modal_update');

      // nextId hanya dipakai saat auto-generate
      let nextId = 1;
      let currentDtRow = null; // object DataTables row

      function showError(elInput, elError, msg) {
        elError.text(msg).show();
        elInput.addClass('is-invalid');
      }

      function hideError(elInput, elError) {
        elError.hide();
        elInput.removeClass('is-invalid');
      }

      // cek apakah ID sudah ada di tabel (cek kolom pertama setiap baris)
      function isIdDuplicate(idVal) {
        const data = table.rows().data().toArray();
        for (let i = 0; i < data.length; i++) {
          if (String(data[i][0]) === String(idVal)) return true;
        }
        return false;
      }

      // cek duplikat kecuali baris yang sedang diedit
      function isIdDuplicateDtExcept(idVal, excludeRow) {
        if (!idVal) return false;
        const nodes = table.rows().nodes().toArray();
        for (let i = 0; i < nodes.length; i++) {
          const node = nodes[i];
          if (excludeRow && node === excludeRow.node()) continue;
          const txt = $(node).find('td').eq(0).text().trim();
          if (String(txt) === String(idVal)) return true;
        }
        return false;
      }

      function validate() {
        let ok = true;

        // ID: optional — kalau diisi, harus valid dan unik
        const idVal = idEl.val().trim();
        if (idVal !== '') {
          const re = /^[A-Za-z0-9_-]+$/;
          if (!re.test(idVal)) {
            showError(idEl, errId, 'ID hanya boleh huruf, angka, underscore atau tanda -');
            ok = false;
          } else if (isIdDuplicate(idVal)) {
            showError(idEl, errId, 'ID sudah dipakai, gunakan ID lain');
            ok = false;
          } else {
            hideError(idEl, errId);
          }
        } else {
          while (isIdDuplicate(nextId)) nextId++;
          hideError(idEl, errId);
        }

        // Nama (required)
        const namaVal = namaEl.val().trim();
        if (!namaVal) {
          showError(namaEl, errNama, 'Nama barang wajib diisi');
          ok = false;
        } else {
          hideError(namaEl, errNama);
        }

        // Harga (required)
        const hargaVal = hargaEl.val();
        if (hargaVal === '' || isNaN(Number(hargaVal)) || Number(hargaVal) < 0) {
          showError(hargaEl, errHarga, 'Harga harus berupa angka dan tidak boleh kurang dari nol');
          ok = false;
        } else {
          hideError(hargaEl, errHarga);
        }

        return ok;
      }

      function clearInputs() {
        idEl.val('');
        namaEl.val('');
        hargaEl.val('');
        hideError(idEl, errId);
        hideError(namaEl, errNama);
        hideError(hargaEl, errHarga);
        namaEl.focus();
      }

      // real time validation saat input berubah
      idEl.on('input', function () {
        const v = $(this).val().trim();
        if (v === '') {
          hideError(idEl, errId);
          return;
        }
        const re = /^[A-Za-z0-9_-]+$/;
        if (!re.test(v)) {
          showError(idEl, errId, 'ID hanya boleh huruf, angka, underscore atau tanda -');
        } else if (isIdDuplicate(v)) {
          showError(idEl, errId, 'ID sudah dipakai, gunakan ID lain');
        } else {
          hideError(idEl, errId);
        }
      });

      namaEl.on('input', function () {
        if ($(this).val().trim()) hideError($(this), errNama);
      });

      hargaEl.on('input', function () {
        const v = $(this).val();
        if (v !== '' && !isNaN(Number(v)) && Number(v) >= 0) hideError($(this), errHarga);
      });

      $('#barangForm').on('submit', function (e) {
        e.preventDefault();
        submitBtn.prop('disabled', true);

        if (!validate()) {
          // fokus ke field pertama yang invalid
          if (idEl.hasClass('is-invalid')) idEl.focus();
          else if (namaEl.hasClass('is-invalid')) namaEl.focus();
          else if (hargaEl.hasClass('is-invalid')) hargaEl.focus();

          submitBtn.prop('disabled', false);
          return;
        }

        // gunakan ID user jika ada, kalau kosong auto-generate
        let idVal = idEl.val().trim();
        if (idVal === '') {
          while (isIdDuplicate(nextId)) nextId++;
          idVal = String(nextId++);
        }

        const nama = namaEl.val().trim();
        const harga = Number(hargaEl.val());

        table.row.add([
          $('<div>').text(idVal).html(),
          $('<div>').text(nama).html(),
          harga.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 })
        ]).draw(false);

        clearInputs();
        submitBtn.prop('disabled', false);
      });

      // pointer on hover (css)
      $('#tabelBarang').on('mouseenter', 'tbody tr', function () { $(this).css('cursor', 'pointer'); });

      // klik row DataTables -> buka modal
      $('#tabelBarang tbody').on('click', 'tr', function () {
        const row = table.row(this);
        if (!row.node()) return;

        currentDtRow = row;

        const data = row.data(); // array [id, nama, hargaStr]

        // decode HTML-escaped content (important)
        const idVal = $('<div>').html(data[0]).text().trim();
        const namaVal = $('<div>').html(data[1]).text().trim();

        // harga mungkin string terformat "1.000" -> bersihkan
        let hargaVal = String(data[2]).replace(/\./g,'').replace(/,/g,'.').trim();

        dt_modal_id.val(idVal);
        dt_modal_nama.val(namaVal);
        dt_modal_harga.val(hargaVal);

        dt_modal_errId.hide();
        dt_modal_errNama.hide();
        dt_modal_errHarga.hide();
        dt_modal_id.removeClass('is-invalid');
        dt_modal_nama.removeClass('is-invalid');
        dt_modal_harga.removeClass('is-invalid');

        dtModal.show();
      });

      function validateDtModal() {
        let ok = true;
        const idVal = dt_modal_id.val().trim();
        const re = /^[A-Za-z0-9_-]+$/;

        if (!idVal) {
          dt_modal_errId.text('ID tidak boleh kosong saat mengubah.');
          dt_modal_errId.show();
          dt_modal_id.addClass('is-invalid');
          ok = false;
        } else if (!re.test(idVal)) {
          dt_modal_errId.text('ID hanya boleh huruf, angka, underscore atau tanda -');
          dt_modal_errId.show();
          dt_modal_id.addClass('is-invalid');
          ok = false;
        } else if (isIdDuplicateDtExcept(idVal, currentDtRow)) {
          dt_modal_errId.text('ID sudah dipakai di row lain');
          dt_modal_errId.show();
          dt_modal_id.addClass('is-invalid');
          ok = false;
        } else {
          dt_modal_errId.hide();
          dt_modal_id.removeClass('is-invalid');
        }

        if (!dt_modal_nama.val().trim()) {
          dt_modal_errNama.show();
          dt_modal_nama.addClass('is-invalid');
          ok = false;
        } else {
          dt_modal_errNama.hide();
          dt_modal_nama.removeClass('is-invalid');
        }

        if (dt_modal_harga.val() === '' || isNaN(Number(dt_modal_harga.val())) || Number(dt_modal_harga.val()) < 0) {
          dt_modal_errHarga.show();
          dt_modal_harga.addClass('is-invalid');
          ok = false;
        } else {
          dt_modal_errHarga.hide();
          dt_modal_harga.removeClass('is-invalid');
        }

        return ok;
      }

      // delete row
      dt_modal_delete.on('click', function () {
        if (!currentDtRow) { dtModal.hide(); return; }
        currentDtRow.remove().draw(false);
        currentDtRow = null;
        dtModal.hide();
      });

      // update row (termasuk ID)
      dt_modal_update.on('click', function () {
        if (!currentDtRow) { dtModal.hide(); return; }
        if (!validateDtModal()) return;

        const nama = dt_modal_nama.val().trim();
        const harga = Number(dt_modal_harga.val());
        const idVal = dt_modal_id.val().trim();

        currentDtRow.data([
          $('<div>').text(idVal).html(),
          $('<div>').text(nama).html(),
          harga.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 })
        ]).draw(false);

        currentDtRow = null;
        dtModal.hide();
      });

      // modal id realtime validation (DataTables)
      dt_modal_id.on('input', function () {
        const v = $(this).val().trim();
        const re = /^[A-Za-z0-9_-]+$/;
        if (!v) {
          dt_modal_errId.hide();
          $(this).removeClass('is-invalid');
          return;
        }
        if (!re.test(v)) {
          dt_modal_errId.text('ID hanya boleh huruf, angka, underscore atau tanda -').show();
          $(this).addClass('is-invalid');
        } else if (isIdDuplicateDtExcept(v, currentDtRow)) {
          dt_modal_errId.text('ID sudah dipakai di row lain').show();
          $(this).addClass('is-invalid');
        } else {
          dt_modal_errId.hide();
          $(this).removeClass('is-invalid');
        }
      });

      // modal inputs realtime hide error
      dt_modal_nama.on('input', function () {
        if ($(this).val().trim()) { dt_modal_errNama.hide(); $(this).removeClass('is-invalid'); }
      });
      dt_modal_harga.on('input', function () {
        const v = $(this).val();
        if (v !== '' && !isNaN(Number(v)) && Number(v) >= 0) { dt_modal_errHarga.hide(); $(this).removeClass('is-invalid'); }
      });

    });
  </script>
@endpush