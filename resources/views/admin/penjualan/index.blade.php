@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-4">
    <h3>Form Kasir (Admin)</h3>

    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label for="kode">Kode barang :</label>
            <select id="kode" class="form-select">
                <option value="">-- Pilih kode barang --</option>
                @foreach($barangs as $b)
                    <option value="{{ $b->id_barang }}" data-nama="{{ $b->nama }}" data-harga="{{ $b->harga }}">
                        {{ $b->id_barang }} — {{ $b->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="nama">Nama barang :</label>
            <input id="nama" class="form-control" readonly>
        </div>

        <div class="col-md-2">
            <label for="harga">Harga :</label>
            <input id="harga" class="form-control" readonly>
        </div>

        <div class="col-md-2">
            <label for="jumlah">Jumlah :</label>
            <input id="jumlah" class="form-control" type="number" value="1" min="1">
        </div>

        {{-- dibuat lebih lebar biar pas, tidak mepet --}}
        <div class="col-md-2 d-grid">
            <button id="tambahBtn" type="button" class="btn btn-success" disabled>
                Tambahkan
            </button>
        </div>
    </div>

    <hr>

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="cartTable">
            <thead>
                <tr>
                    <th>Kode</th><th>Nama</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                    <td id="totalCell">0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex justify-content-end">
        <button id="bayarBtn" type="button" class="btn btn-primary">
            <span class="spinner-border spinner-border-sm me-2 d-none" id="bayarSpinner" role="status" aria-hidden="true"></span>
            <span id="bayarText">Bayar</span>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

    const kode = document.getElementById('kode');
    const nama = document.getElementById('nama');
    const harga = document.getElementById('harga');
    const jumlah = document.getElementById('jumlah');
    const tambahBtn = document.getElementById('tambahBtn');
    const cartTableBody = document.querySelector('#cartTable tbody');
    const totalCell = document.getElementById('totalCell');
    const bayarBtn = document.getElementById('bayarBtn');
    const bayarSpinner = document.getElementById('bayarSpinner');
    const bayarText = document.getElementById('bayarText');

    let foundBarang = null;
    let cart = [];
    let isLoading = false;

    function updateTambahBtn() {
        tambahBtn.disabled = !foundBarang || Number(jumlah.value) <= 0;
    }

    function setBayarLoading(status) {
        isLoading = status;
        bayarBtn.disabled = status;
        bayarSpinner.classList.toggle('d-none', !status);
        bayarText.textContent = status ? 'Memproses...' : 'Bayar';
    }

    kode.addEventListener('change', function () {
        const val = kode.value;
        if (!val) {
            foundBarang = null;
            nama.value = '';
            harga.value = '';
        } else {
            const opt = kode.selectedOptions[0];
            foundBarang = {
                id_barang: val,
                nama: opt.dataset.nama,
                harga: Number(opt.dataset.harga)
            };
            nama.value = foundBarang.nama;
            harga.value = foundBarang.harga;
            jumlah.value = 1;
        }
        updateTambahBtn();
    });

    jumlah.addEventListener('input', updateTambahBtn);

    function renderCart() {
        cartTableBody.innerHTML = '';
        let total = 0;

        cart.forEach((it, idx) => {
            total += Number(it.subtotal);
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>${it.id_barang}</td>
                <td>${it.nama}</td>
                <td>${it.harga}</td>
                <td>
                    <input type="number" class="form-control qty-input" data-idx="${idx}" value="${it.jumlah}" min="1" style="width:90px">
                </td>
                <td class="subtotal-cell">${it.subtotal}</td>
                <td><button type="button" class="btn btn-sm btn-danger remove-btn" data-idx="${idx}">Hapus</button></td>
            `;
            cartTableBody.appendChild(tr);
        });

        totalCell.innerText = total;

        document.querySelectorAll('.qty-input').forEach(inp => {
            inp.addEventListener('change', function () {
                const i = Number(this.dataset.idx);
                const newQty = Math.max(1, Number(this.value));
                cart[i].jumlah = newQty;
                cart[i].subtotal = Number(cart[i].harga) * newQty;
                renderCart();
            });
        });

        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const i = Number(this.dataset.idx);
                cart.splice(i, 1);
                renderCart();
            });
        });
    }

    tambahBtn.addEventListener('click', function () {
        if (!foundBarang) return;

        const kodeVal = foundBarang.id_barang;
        const existing = cart.find(it => it.id_barang === kodeVal);
        const qty = Math.max(1, Number(jumlah.value));

        if (existing) {
            existing.jumlah += qty;
            existing.subtotal = existing.jumlah * existing.harga;
        } else {
            cart.push({
                id_barang: kodeVal,
                nama: foundBarang.nama,
                harga: Number(foundBarang.harga),
                jumlah: qty,
                subtotal: qty * Number(foundBarang.harga)
            });
        }

        kode.value = '';
        nama.value = '';
        harga.value = '';
        jumlah.value = 1;
        foundBarang = null;

        updateTambahBtn();
        renderCart();
        kode.focus();
    });

    bayarBtn.addEventListener('click', function () {
        if (isLoading) return;

        if (cart.length === 0) {
            Swal.fire('Kosong', 'Cart masih kosong', 'warning');
            return;
        }

        const total = cart.reduce((s, it) => s + Number(it.subtotal), 0);

        setBayarLoading(true);

        axios.post('/admin/api/penjualan', {
            cart: cart,
            total: total
        })
        .then(res => {
            if (res.data.success) {
                Swal.fire('Berhasil', 'Transaksi tersimpan (ID: ' + res.data.id_penjualan + ')', 'success');
                cart = [];
                renderCart();
            } else {
                Swal.fire('Gagal', 'Tidak bisa menyimpan transaksi', 'error');
            }
        })
        .catch(err => {
            const msg = err.response && err.response.data && err.response.data.message
                ? err.response.data.message
                : 'Server error';
            Swal.fire('Error', msg, 'error');
        })
        .finally(() => {
            setBayarLoading(false);
        });
    });

    renderCart();
});
</script>
@endsection