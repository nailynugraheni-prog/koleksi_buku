@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-center">Select Kota</h3>

    <!-- Poin E.i: Card Pertama (Select Biasa) -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white"><strong>Select</strong></div>
        <div class="card-body">
            <!-- Poin B: Input Text untuk nambah opsi -->
            <div class="form-group mb-3">
                <label>Input Kota:</label>
                <div class="input-group">
                    <input type="text" id="inputNative" class="form-control" placeholder="Ketik nama kota lalu klik tambah...">
                    <button class="btn btn-success" type="button" id="btnTambahNative">Tambahkan</button>
                </div>
            </div>

            <!-- Poin A: Element Input Select -->
            <div class="form-group mb-3">
                <label>Select Kota:</label>
                <select id="selectNative" class="form-control">
                    <option value="" disabled selected>-- Pilih kota yang sudah ditambah --</option>
                </select>
            </div>

            <!-- Poin C: Kota Terpilih -->
            <div class="mt-2">
                <strong>Kota Terpilih!</strong> <span id="displayNative" class="badge badge-primary text-wrap" style="font-size: 1rem;">-</span>
            </div>
        </div>
    </div>

    <!-- Poin E.ii: Card Kedua (Select2) -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><strong>select 2</strong></div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label>Input Kota:</label>
                <div class="input-group">
                    <input type="text" id="inputSelect2" class="form-control" placeholder="Ketik nama kota lalu klik tambah...">
                    <button class="btn btn-info" type="button" id="btnTambahSelect2">Tambahkan</button>
                </div>
            </div>

            <div class="form-group mb-3">
                <label>Select Kota (Select2):</label>
                <select id="select2" class="form-control" style="width: 100%">
                    <option value="" disabled selected>-- Pilih kota yang sudah ditambah --</option>
                </select>
            </div>

            <div class="mt-2">
                <strong>Kota Terpilih!</strong> <span id="displaySelect2" class="badge badge-info text-wrap" style="font-size: 1rem;">-</span>
            </div>
        </div>
    </div>
</div>

<!-- Load Library -->
<link href="https://cdn.jsdelivr.net" rel="stylesheet" />
@endsection

@push('scripts')
<script src="https://code.jquery.com"></script>
<script src="https://cdn.jsdelivr.net"></script>

<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#select2').select2({
        placeholder: "-- Pilih kota yang sudah ditambah --"
    });

    // FUNGSI UNTUK CARD 1 (NATIVE)
    $('#btnTambahNative').on('click', function() {
        let namaKota = $('#inputNative').val().trim();
        if (namaKota !== "") {
            // Langsung tambah ke element select
            $('#selectNative').append(`<option value="${namaKota}">${namaKota}</option>`);
            $('#inputNative').val('').focus(); // Kosongkan input
        }
    });

    // Update tampilan Kota Terpilih Card 1
    $('#selectNative').on('change', function() {
        $('#displayNative').text($(this).val());
    });


    // FUNGSI UNTUK CARD 2 (SELECT2)
    $('#btnTambahSelect2').on('click', function() {
        let namaKota = $('#inputSelect2').val().trim();
        if (namaKota !== "") {
            // Buat opsi baru
            let newOption = new Option(namaKota, namaKota, false, false);
            // Tambah ke select2 dan trigger update
            $('#select2').append(newOption).trigger('change');
            $('#inputSelect2').val('').focus(); // Kosongkan input
        }
    });

    // Update tampilan Kota Terpilih Card 2
    $('#select2').on('change', function() {
        $('#displaySelect2').text($(this).val());
    });

    // Shortcut: Tekan Enter untuk menambah
    $('#inputNative, #inputSelect2').keypress(function (e) {
        if (e.which == 13) {
            $(this).next('button').click();
        }
    });
});
</script>
@endpush
