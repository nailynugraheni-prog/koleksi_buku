@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">Wilayah Administrasi - Axios</h4>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Provinsi</label>
                    <select id="provinsi" class="form-select">
                        <option value="0">Pilih Provinsi</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kota</label>
                    <select id="kota" class="form-select" disabled>
                        <option value="0">Pilih Kota</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kecamatan</label>
                    <select id="kecamatan" class="form-select" disabled>
                        <option value="0">Pilih Kecamatan</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kelurahan</label>
                    <select id="kelurahan" class="form-select" disabled>
                        <option value="0">Pilih Kelurahan</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinsi = document.getElementById('provinsi');
    const kota = document.getElementById('kota');
    const kecamatan = document.getElementById('kecamatan');
    const kelurahan = document.getElementById('kelurahan');

    function resetSelect(select, placeholder, disabled = true) {
        select.innerHTML = `<option value="0">${placeholder}</option>`;
        select.disabled = disabled;
    }

    function fillSelect(select, items) {
        items.forEach(item => {
            select.insertAdjacentHTML(
                'beforeend',
                `<option value="${item.id}">${item.name}</option>`
            );
        });
    }

    async function loadProvinsi() {
        const res = await axios.get("{{ route('admin.wilayah.provinsi') }}");
        resetSelect(provinsi, 'Pilih Provinsi', false);
        fillSelect(provinsi, res.data);
    }

    provinsi.addEventListener('change', async function () {
        const provinsiId = this.value;

        resetSelect(kota, 'Pilih Kota');
        resetSelect(kecamatan, 'Pilih Kecamatan');
        resetSelect(kelurahan, 'Pilih Kelurahan');

        if (provinsiId == 0) return;

        const res = await axios.get(`/admin/wilayah/kota/${provinsiId}`);
        kota.disabled = false;
        fillSelect(kota, res.data);
    });

    kota.addEventListener('change', async function () {
        const kotaId = this.value;

        resetSelect(kecamatan, 'Pilih Kecamatan');
        resetSelect(kelurahan, 'Pilih Kelurahan');

        if (kotaId == 0) return;

        const res = await axios.get(`/admin/wilayah/kecamatan/${kotaId}`);
        kecamatan.disabled = false;
        fillSelect(kecamatan, res.data);
    });

    kecamatan.addEventListener('change', async function () {
        const kecamatanId = this.value;

        resetSelect(kelurahan, 'Pilih Kelurahan');

        if (kecamatanId == 0) return;

        const res = await axios.get(`/admin/wilayah/kelurahan/${kecamatanId}`);
        kelurahan.disabled = false;
        fillSelect(kelurahan, res.data);
    });

    loadProvinsi();
});
</script>
@endsection