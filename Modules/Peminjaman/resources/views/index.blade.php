@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content')
    <div class="card">
        <h2>Data Peminjaman Buku</h2>

        {{-- Pesan sukses AJAX (tanpa refresh halaman) --}}
        <div id="pesan" class="alert success"></div>

        <div class="toolbar">
            <span class="sub" style="margin:0">Semua tambah, ubah, dan hapus berjalan lewat AJAX.</span>
            <button type="button" class="btn" id="btnTambah">+ Tambah Peminjaman</button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th class="center">Tanggal Pinjam</th>
                        <th class="center">Tanggal Kembali</th>
                        <th class="center">Status</th>
                        <th class="center">Bukti</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabelPeminjaman">
                    <tr><td colspan="8" class="kosong">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal form tambah / ubah --}}
    <div class="modal" id="modal">
        <div class="modal-content">
            <h2 id="judulModal">Tambah Peminjaman</h2>

            <form id="formPeminjaman" enctype="multipart/form-data" novalidate>
                <label for="nama_peminjam">Nama Peminjam</label>
                <input type="text" id="nama_peminjam" name="nama_peminjam">
                <div class="field-error" data-error="nama_peminjam"></div>

                <label for="buku_id">Buku</label>
                <select id="buku_id" name="buku_id"></select>
                <div class="field-error" data-error="buku_id"></div>

                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam">
                <div class="field-error" data-error="tanggal_pinjam"></div>

                <label for="tanggal_kembali">Tanggal Kembali</label>
                <input type="date" id="tanggal_kembali" name="tanggal_kembali">
                <div class="field-error" data-error="tanggal_kembali"></div>

                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Dipinjam">Dipinjam</option>
                    <option value="Dikembalikan">Dikembalikan</option>
                </select>
                <div class="field-error" data-error="status"></div>

                <label for="bukti">Foto / Berkas Bukti Peminjaman</label>
                <input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png,.pdf">
                <div class="hint">Format jpg, png, atau pdf. Maksimal 2 MB.</div>
                <div class="hint" id="buktiSaatIni"></div>
                <div class="field-error" data-error="bukti"></div>

                <div class="form-actions">
                    <button type="submit" class="btn" id="btnSimpan">Simpan</button>
                    <button type="button" class="btn abu" id="btnBatal">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const URL_DASAR = @json(route('peminjaman.index'));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    const modal = document.getElementById('modal');
    const form = document.getElementById('formPeminjaman');
    const tabel = document.getElementById('tabelPeminjaman');
    const pesan = document.getElementById('pesan');
    const selectBuku = document.getElementById('buku_id');
    const btnSimpan = document.getElementById('btnSimpan');

    let idEdit = null;
    let timerPesan = null;

    // Mencegah XSS saat menyusun HTML dari data JSON
    function esc(teks) {
        const div = document.createElement('div');
        div.textContent = teks ?? '';
        return div.innerHTML;
    }

    function tampilPesan(teks) {
        pesan.textContent = teks;
        clearTimeout(timerPesan);
        timerPesan = setTimeout(() => { pesan.textContent = ''; }, 4000);
    }

    // ---------- Error 422 di bawah setiap input ----------
    function bersihkanError() {
        form.querySelectorAll('[data-error]').forEach(el => { el.textContent = ''; });
        form.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
    }

    function tampilkanError(errors) {
        Object.entries(errors).forEach(([field, daftar]) => {
            const kotak = form.querySelector('[data-error="' + field + '"]');
            const input = form.elements[field];
            if (kotak) kotak.textContent = daftar[0];
            if (input) input.classList.add('invalid');
        });
    }

    // ---------- Render tabel & pilihan buku dari JSON ----------
    function renderTabel(data) {
        if (data.length === 0) {
            tabel.innerHTML = '<tr><td colspan="8" class="kosong">Belum ada data peminjaman.</td></tr>';
            return;
        }

        tabel.innerHTML = data.map((p, i) => {
            const statusKelas = p.status === 'Dipinjam' ? 'status-dipinjam' : 'status-dikembalikan';
            const bukti = p.bukti_url
                ? '<a href="' + esc(p.bukti_url) + '" target="_blank" rel="noopener">Lihat berkas</a>'
                : '-';

            return '<tr>'
                + '<td>' + (i + 1) + '</td>'
                + '<td>' + esc(p.nama_peminjam) + '</td>'
                + '<td>' + esc(p.buku ? p.buku.judul : '-') + '</td>'
                + '<td class="center">' + esc(p.tanggal_pinjam) + '</td>'
                + '<td class="center">' + esc(p.tanggal_kembali || '-') + '</td>'
                + '<td class="center"><span class="badge ' + statusKelas + '">' + esc(p.status) + '</span></td>'
                + '<td class="center">' + bukti + '</td>'
                + '<td><div class="aksi">'
                +   '<button type="button" class="btn edit" data-aksi="edit" data-id="' + p.id + '">Edit</button>'
                +   '<button type="button" class="btn hapus" data-aksi="hapus" data-id="' + p.id + '">Hapus</button>'
                + '</div></td>'
                + '</tr>';
        }).join('');
    }

    function renderBuku(buku) {
        const terpilih = selectBuku.value;
        selectBuku.innerHTML = '<option value="">-- Pilih Buku --</option>' + buku.map(b =>
            '<option value="' + b.id + '">' + esc(b.judul) + ' (Stok: ' + b.stok + ')</option>'
        ).join('');
        selectBuku.value = terpilih;
    }

    // Ambil ulang data lewat AJAX (GET) -> stok pada pilihan buku ikut segar
    async function muatData() {
        const res = await fetch(URL_DASAR, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const hasil = await res.json();
        renderTabel(hasil.data);
        renderBuku(hasil.buku);
    }

    // ---------- Modal ----------
    function bukaModal(judul) {
        document.getElementById('judulModal').textContent = judul;
        bersihkanError();
        modal.classList.add('open');
    }

    function tutupModal() {
        modal.classList.remove('open');
    }

    document.getElementById('btnTambah').addEventListener('click', () => {
        idEdit = null;
        form.reset();
        document.getElementById('buktiSaatIni').textContent = '';
        bukaModal('Tambah Peminjaman');
    });

    document.getElementById('btnBatal').addEventListener('click', tutupModal);
    modal.addEventListener('click', e => { if (e.target === modal) tutupModal(); });

    // ---------- Edit & Hapus (delegasi klik pada tabel) ----------
    tabel.addEventListener('click', e => {
        const tombol = e.target.closest('button[data-aksi]');
        if (!tombol) return;

        if (tombol.dataset.aksi === 'edit') editData(tombol.dataset.id);
        if (tombol.dataset.aksi === 'hapus') hapusData(tombol.dataset.id);
    });

    async function editData(id) {
        const res = await fetch(URL_DASAR + '/' + id, { headers: { 'Accept': 'application/json' } });

        if (!res.ok) {
            tampilPesan('Gagal mengambil data peminjaman.');
            return;
        }

        const item = (await res.json()).data;
        idEdit = id;
        form.reset();

        form.nama_peminjam.value = item.nama_peminjam;
        form.buku_id.value = item.buku_id;
        form.tanggal_pinjam.value = item.tanggal_pinjam;
        form.tanggal_kembali.value = item.tanggal_kembali || '';
        form.status.value = item.status;

        document.getElementById('buktiSaatIni').innerHTML = item.bukti_url
            ? 'Berkas saat ini: <a href="' + esc(item.bukti_url) + '" target="_blank" rel="noopener">Lihat berkas</a>'
              + ' (pilih berkas baru untuk menggantinya)'
            : '';

        bukaModal('Edit Peminjaman');
    }

    async function hapusData(id) {
        if (!confirm('Yakin ingin menghapus peminjaman ini?')) return;

        const data = new FormData();
        data.append('_token', CSRF);
        data.append('_method', 'DELETE');

        const res = await fetch(URL_DASAR + '/' + id, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        });
        const hasil = await res.json();

        if (!res.ok) {
            tampilPesan(hasil.message || 'Gagal menghapus data.');
            return;
        }

        tampilPesan(hasil.message);
        await muatData();
    }

    // ---------- Simpan (tambah / ubah) ----------
    form.addEventListener('submit', async e => {
        e.preventDefault();
        bersihkanError();

        const data = new FormData(form);
        data.append('_token', CSRF);

        let url = URL_DASAR;
        if (idEdit) {
            url += '/' + idEdit;
            data.append('_method', 'PUT'); // multipart hanya terbaca PHP lewat POST
        }

        btnSimpan.disabled = true;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: data
            });
            const hasil = await res.json();

            if (res.status === 422) {          // gagal validasi -> error di bawah input
                tampilkanError(hasil.errors || {});
                return;
            }

            if (!res.ok) {
                tampilkanError({ bukti: [hasil.message || 'Terjadi kesalahan pada server.'] });
                return;
            }

            tutupModal();
            tampilPesan(hasil.message);
            await muatData();
        } catch (err) {
            tampilkanError({ nama_peminjam: ['Gagal menghubungi server.'] });
        } finally {
            btnSimpan.disabled = false;
        }
    });

    muatData();
</script>
@endpush
