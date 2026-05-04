@extends('layouts.app')
@section('title', 'Scan Barcode Label')
@section('content')
<div class="container py-3">
    <h1 class="mb-3">Scan Barcode Label</h1>


    <audio id="scanSound" src="{{ asset('sounds/beep.mp3') }}" preload="auto"></audio>


    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div id="reader" style="width:100%;"></div>


                    <div class="mt-3 d-flex gap-2">
                        <button id="btnReset" class="btn btn-secondary" type="button" disabled>Scan Ulang</button>
                    </div>


                    <div id="status" class="mt-3 text-muted">
                        Scanner akan aktif otomatis. Arahkan barcode ke kamera.
                    </div>
                </div>
            </div>
        </div>


        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Hasil Scan</h5>


                    <div class="mb-2">
                        <strong>ID Barang</strong>
                        <div id="hasil-id">-</div>
                    </div>


                    <div class="mb-2">
                        <strong>Nama Barang</strong>
                        <div id="hasil-nama">-</div>
                    </div>


                    <div class="mb-2">
                        <strong>Harga Barang</strong>
                        <div id="hasil-harga">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('script-page')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    const statusEl = document.getElementById('status');
    const hasilId = document.getElementById('hasil-id');
    const hasilNama = document.getElementById('hasil-nama');
    const hasilHarga = document.getElementById('hasil-harga');
    const btnReset = document.getElementById('btnReset');
    const readerEl = document.getElementById('reader');
    const scanSound = document.getElementById('scanSound');


    let html5QrcodeScanner = null;
    let locked = false;
    let audioUnlocked = false;


    async function unlockAudio() {
        if (audioUnlocked) return;


        try {
            scanSound.muted = true;
            await scanSound.play();
            scanSound.pause();
            scanSound.currentTime = 0;
            scanSound.muted = false;
            audioUnlocked = true;
        } catch (e) {
            console.warn('Audio belum bisa dibuka:', e);
        }
    }


    function beep() {
        scanSound.currentTime = 0;
        scanSound.play().catch(err => {
            console.warn('Gagal memutar suara:', err);
        });
    }


    function setStatus(text, type = 'muted') {
        statusEl.classList.remove('text-muted', 'text-success', 'text-danger');
        statusEl.classList.add(`text-${type}`);
        statusEl.textContent = text;
    }


    async function fetchBarang(barcodeValue) {
        const url = `{{ route('admin.barang.scanFind', ['id' => '__ID__']) }}`.replace(
            '__ID__',
            encodeURIComponent(barcodeValue)
        );


        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });


        const raw = await response.text();


        let data;
        try {
            data = JSON.parse(raw);
        } catch (e) {
            console.error('Response mentah:', raw);
            throw new Error('Server mengirim HTML, bukan JSON. Cek route / login / error 404.');
        }


        if (!response.ok) {
            throw new Error(data.message || 'Barang tidak ditemukan');
        }


        hasilId.textContent = data.data.id_barang;
        hasilNama.textContent = data.data.nama;
        hasilHarga.textContent = 'Rp ' + Number(data.data.harga).toLocaleString('id-ID');
    }


    async function onScanSuccess(decodedText, decodedResult) {
        if (locked) return;
        locked = true;


        try {
            await unlockAudio();
            beep();


            setStatus('Barcode berhasil dibaca. Scanner berhenti.', 'success');


            if (html5QrcodeScanner) {
                await html5QrcodeScanner.clear();
            }


            await fetchBarang(decodedText.trim());
            btnReset.disabled = false;
        } catch (e) {
            setStatus(e.message, 'danger');
            locked = false;
        }
    }


    function onScanFailure() {
        
    }


    function initScanner() {
        locked = false;
        setStatus('Scanner aktif. Arahkan barcode ke kamera...', 'muted');


        html5QrcodeScanner = new Html5QrcodeScanner(
            'reader',
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128],
                rememberLastUsedCamera: true
            },
            false
        );


        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }


    async function resetScanner() {
        try {
            if (html5QrcodeScanner) {
                await html5QrcodeScanner.clear();
            }
        } catch (e) {}


        readerEl.innerHTML = '';
        hasilId.textContent = '-';
        hasilNama.textContent = '-';
        hasilHarga.textContent = '-';
        btnReset.disabled = true;
        initScanner();
    }


    btnReset.addEventListener('click', async function () {
        await unlockAudio();
        resetScanner();
    });


    document.addEventListener('pointerdown', unlockAudio, { once: true });
    document.addEventListener('keydown', unlockAudio, { once: true });


    document.addEventListener('DOMContentLoaded', function () {
        initScanner();
    });
</script>
@endpush
