@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4">Wilayah Administrasi - Ajax</h4>

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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {
    const provinsi = $('#provinsi');
    const kota = $('#kota');
    const kecamatan = $('#kecamatan');
    const kelurahan = $('#kelurahan');

    function resetSelect(select, placeholder, disable = true) {
        select.html(`<option value="0">${placeholder}</option>`);
        select.prop('disabled', disable);
    }

    function fillSelect(select, items) {
        items.forEach(item => {
            select.append(`<option value="${item.id}">${item.name}</option>`);
        });
    }

    function loadProvinsi() {
        $.ajax({
            url: "{{ route('admin.wilayah.provinsi') }}",
            type: "GET",
            success: function (res) {
                resetSelect(provinsi, 'Pilih Provinsi', false);
                fillSelect(provinsi, res);
            }
        });
    }

    provinsi.on('change', function () {
        const provinsiId = $(this).val();

        resetSelect(kota, 'Pilih Kota');
        resetSelect(kecamatan, 'Pilih Kecamatan');
        resetSelect(kelurahan, 'Pilih Kelurahan');

        if (provinsiId == 0) return;

        $.ajax({
            url: `/admin/wilayah/kota/${provinsiId}`,
            type: "GET",
            success: function (res) {
                kota.prop('disabled', false);
                fillSelect(kota, res);
            }
        });
    });

    kota.on('change', function () {
        const kotaId = $(this).val();

        resetSelect(kecamatan, 'Pilih Kecamatan');
        resetSelect(kelurahan, 'Pilih Kelurahan');

        if (kotaId == 0) return;

        $.ajax({
            url: `/admin/wilayah/kecamatan/${kotaId}`,
            type: "GET",
            success: function (res) {
                kecamatan.prop('disabled', false);
                fillSelect(kecamatan, res);
            }
        });
    });

    kecamatan.on('change', function () {
        const kecamatanId = $(this).val();

        resetSelect(kelurahan, 'Pilih Kelurahan');

        if (kecamatanId == 0) return;

        $.ajax({
            url: `/admin/wilayah/kelurahan/${kecamatanId}`,
            type: "GET",
            success: function (res) {
                kelurahan.prop('disabled', false);
                fillSelect(kelurahan, res);
            }
        });
    });

    loadProvinsi();
});
</script>
@endsection