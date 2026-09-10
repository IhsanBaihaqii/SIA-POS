<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
requireLogin();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Buat Transaksi Baru</h2>
    <a href="<?= url('pages/transaksi/index.php') ?>" class="text-blue-600 hover:text-blue-800">
        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<form id="formTransaksi" action="<?= url('pages/transaksi/simpan.php') ?>" method="POST" class="space-y-6">

    <!-- Info Transaksi -->
    <div class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No Transaksi</label>
            <input type="text" name="no_transaksi" id="no_transaksi" readonly
                   value="TRX-<?= date('Ymd-His') ?>"
                   class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
            <input type="text" id="pelangganSearch" placeholder="Ketik nama pelanggan..." autocomplete="off"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="hidden" name="pelanggan_id" id="pelangganId">
            <div id="pelangganResults" class="absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tanpa pelanggan (umum).</p>
        </div>
    </div>

    <!-- Pilih Barang -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-800 mb-4"><i class="fa-solid fa-boxes-packing mr-2"></i> Pilih Barang</h3>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-6 relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                <input type="text" id="barangSearch" placeholder="Ketik nama / kode barang..." autocomplete="off"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="hidden" id="barangId">
                <div id="barangResults" class="absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                <input type="number" id="barangHarga" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                <input type="number" id="barangJumlah" value="1" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="md:col-span-2">
                <button type="button" id="btnTambah" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    <i class="fa-solid fa-plus mr-1"></i> Tambah
                </button>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Stok tersedia: <span id="infoStok" class="font-semibold">-</span></p>
    </div>

    <!-- Keranjang -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody id="keranjangBody" class="bg-white divide-y divide-gray-200">
                <tr id="keranjangKosong">
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Keranjang masih kosong.</td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
                    <td class="px-4 py-3 text-right font-bold text-lg" id="totalHarga">Rp 0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Keterangan & Aksi -->
    <div class="bg-white rounded-lg shadow p-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
        <textarea name="keterangan" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                  placeholder="Catatan tambahan (opsional)"></textarea>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg">
                <i class="fa-solid fa-save mr-2"></i> Simpan Transaksi
            </button>
            <a href="<?= url('pages/transaksi/index.php') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">Batal</a>
        </div>
    </div>
    <!-- Hidden input untuk data keranjang -->
<div id="hiddenItems"></div>
</form>

<script>
// ================== Utility ==================
function formatRupiah(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

// ================== Autocomplete Pelanggan ==================
const pelangganSearch = document.getElementById('pelangganSearch');
const pelangganResults = document.getElementById('pelangganResults');
const pelangganId = document.getElementById('pelangganId');

let pelangganTimer;
pelangganSearch.addEventListener('input', () => {
    clearTimeout(pelangganTimer);
    const q = pelangganSearch.value.trim();
    if (q.length < 1) { pelangganResults.classList.add('hidden'); pelangganId.value = ''; return; }
    pelangganTimer = setTimeout(() => {
        fetch('<?= url('ajax/search_pelanggan.php') ?>?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                pelangganResults.innerHTML = '';
                if (data.length === 0) {
                    pelangganResults.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Tidak ditemukan</div>';
                } else {
                    data.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer';
                        div.innerHTML = `<div class="font-medium">${p.nama}</div><div class="text-xs text-gray-500">${p.no_hp ?? ''}</div>`;
                        div.addEventListener('click', () => {
                            pelangganSearch.value = p.nama;
                            pelangganId.value = p.id;
                            pelangganResults.classList.add('hidden');
                        });
                        pelangganResults.appendChild(div);
                    });
                }
                pelangganResults.classList.remove('hidden');
            });
    }, 250);
});

// ================== Autocomplete Barang ==================
const barangSearch = document.getElementById('barangSearch');
const barangResults = document.getElementById('barangResults');
const barangId = document.getElementById('barangId');
const barangHarga = document.getElementById('barangHarga');
const barangJumlah = document.getElementById('barangJumlah');
const infoStok = document.getElementById('infoStok');

let barangTimer;
let currentBarang = null;

barangSearch.addEventListener('input', () => {
    clearTimeout(barangTimer);
    const q = barangSearch.value.trim();
    if (q.length < 1) { barangResults.classList.add('hidden'); barangId.value = ''; currentBarang = null; return; }
    barangTimer = setTimeout(() => {
        fetch('<?= url('ajax/search_barang.php') ?>?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                barangResults.innerHTML = '';
                if (data.length === 0) {
                    barangResults.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Tidak ditemukan</div>';
                } else {
                    data.forEach(b => {
                        const div = document.createElement('div');
                        div.className = 'px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer';
                        div.innerHTML = `<div class="font-medium">${b.nama}</div>
                                         <div class="text-xs text-gray-500">${b.kode} • Stok: ${b.stok} ${b.satuan ?? ''} • ${formatRupiah(b.harga_jual)}</div>`;
                        div.addEventListener('click', () => {
                            barangSearch.value = b.nama;
                            barangId.value = b.id;
                            barangHarga.value = b.harga_jual;
                            currentBarang = b;
                            infoStok.textContent = b.stok + ' ' + (b.satuan ?? '');
                            barangResults.classList.add('hidden');
                        });
                        barangResults.appendChild(div);
                    });
                }
                barangResults.classList.remove('hidden');
            });
    }, 250);
});

// ================== Keranjang ==================
let keranjang = []; // {id, kode, nama, harga_jual, harga_beli, jumlah, satuan, stok}

const btnTambah = document.getElementById('btnTambah');
btnTambah.addEventListener('click', () => {
    if (!currentBarang) { alert('Pilih barang terlebih dahulu.'); return; }
    const jumlah = parseInt(barangJumlah.value);
    if (jumlah < 1) { alert('Jumlah minimal 1.'); return; }
    if (jumlah > currentBarang.stok) { alert('Jumlah melebihi stok tersedia (' + currentBarang.stok + ').'); return; }

    const existing = keranjang.find(k => k.id === currentBarang.id);
    if (existing) {
        if (existing.jumlah + jumlah > currentBarang.stok) {
            alert('Total jumlah melebihi stok tersedia.');
            return;
        }
        existing.jumlah += jumlah;
    } else {
        keranjang.push({
            id: currentBarang.id,
            kode: currentBarang.kode,
            nama: currentBarang.nama,
            harga_jual: parseFloat(currentBarang.harga_jual),
            harga_beli: parseFloat(currentBarang.harga_beli),
            satuan: currentBarang.satuan,
            stok: currentBarang.stok,
            jumlah: jumlah
        });
    }
    renderKeranjang();
    // reset input barang
    barangSearch.value = '';
    barangId.value = '';
    barangHarga.value = '';
    barangJumlah.value = 1;
    infoStok.textContent = '-';
    currentBarang = null;
});

function renderKeranjang() {
    const tbody = document.getElementById('keranjangBody');
    const totalEl = document.getElementById('totalHarga');
    tbody.innerHTML = '';
    let total = 0;

    if (keranjang.length === 0) {
        tbody.innerHTML = '<tr id="keranjangKosong"><td colspan="5" class="px-4 py-4 text-center text-gray-500">Keranjang masih kosong.</td></tr>';
        totalEl.textContent = 'Rp 0';
        document.getElementById('hiddenItems').innerHTML = '';
        return;
    }

    keranjang.forEach((item, idx) => {
        const subtotal = item.harga_jual * item.jumlah;
        total += subtotal;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-4 py-3">
                <div class="font-medium">${item.nama}</div>
                <div class="text-xs text-gray-500">${item.kode}</div>
            </td>
            <td class="px-4 py-3 text-right">${formatRupiah(item.harga_jual)}</td>
            <td class="px-4 py-3 text-center">
                <input type="number" min="1" max="${item.stok}" value="${item.jumlah}"
                       data-idx="${idx}" class="ubahJumlah w-20 px-2 py-1 border border-gray-300 rounded text-center">
            </td>
            <td class="px-4 py-3 text-right font-semibold">${formatRupiah(subtotal)}</td>
            <td class="px-4 py-3 text-center">
                <button type="button" data-idx="${idx}" class="hapusItem text-red-600 hover:text-red-800">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    totalEl.textContent = formatRupiah(total);

    // update hidden inputs
    const hidden = document.getElementById('hiddenItems');
    hidden.innerHTML = '';
    keranjang.forEach((item, idx) => {
        hidden.innerHTML += `
            <input type="hidden" name="items[${idx}][barang_id]" value="${item.id}">
            <input type="hidden" name="items[${idx}][jumlah]" value="${item.jumlah}">
            <input type="hidden" name="items[${idx}][harga_jual]" value="${item.harga_jual}">
            <input type="hidden" name="items[${idx}][harga_beli]" value="${item.harga_beli}">
        `;
    });

    // event listener ubah jumlah
    document.querySelectorAll('.ubahJumlah').forEach(inp => {
        inp.addEventListener('change', (e) => {
            const idx = parseInt(e.target.dataset.idx);
            const val = parseInt(e.target.value);
            if (val < 1) { e.target.value = 1; keranjang[idx].jumlah = 1; }
            else if (val > keranjang[idx].stok) { alert('Melebihi stok.'); e.target.value = keranjang[idx].stok; keranjang[idx].jumlah = keranjang[idx].stok; }
            else { keranjang[idx].jumlah = val; }
            renderKeranjang();
        });
    });

    // event listener hapus
    document.querySelectorAll('.hapusItem').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const idx = parseInt(e.currentTarget.dataset.idx);
            keranjang.splice(idx, 1);
            renderKeranjang();
        });
    });
}

// Validasi sebelum submit
document.getElementById('formTransaksi').addEventListener('submit', (e) => {
    if (keranjang.length === 0) {
        e.preventDefault();
        alert('Keranjang masih kosong.');
    }
});
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>