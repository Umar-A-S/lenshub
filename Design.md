# 🎯 LensHub — AI Agent Frontend Rebuild Prompt

> **Panduan lengkap untuk AI Agent** dalam membangun ulang frontend aplikasi manajemen sewa kamera **LensHub** / **OwlCamera** agar 99% identik dengan desain referensi. Dokumen ini mencakup seluruh halaman, komponen, token desain, behaviour, dan aturan responsivitas.

---

## 📐 DESIGN SYSTEM & TOKEN

### Palet Warna Utama
```
--color-primary:       #2B4EFF   /* Biru utama navigasi & CTA */
--color-primary-dark:  #1A3ACC   /* Hover biru */
--color-accent-orange: #FF6B2B   /* Badge, notifikasi, ikon alert */
--color-accent-green:  #22C55E   /* Status aktif / selesai */
--color-accent-red:    #EF4444   /* Terlambat / denda */
--color-accent-yellow: #F59E0B   /* Hampir jatuh tempo */

/* Background */
--bg-sidebar:          #1E2A5E   /* Sidebar biru gelap navy */
--bg-page:             #F4F6FB   /* Background halaman utama */
--bg-card:             #FFFFFF   /* Card putih */
--bg-dark-section:     #1E2A5E   /* Section landing page gelap */

/* Teks */
--text-primary:        #111827
--text-secondary:      #6B7280
--text-on-dark:        #FFFFFF
--text-muted:          #9CA3AF

/* Border */
--border-default:      #E5E7EB
--border-radius-card:  12px
--border-radius-btn:   8px
--border-radius-badge: 999px
```

### Tipografi
```
Font Utama  : 'Plus Jakarta Sans', sans-serif  (judul, navigasi)
Font Angka  : 'DM Mono', monospace             (timer countdown, nominal Rp)
Font Body   : 'Inter', sans-serif              (teks tabel, deskripsi)

/* Scale */
--fs-hero    : 2.5rem   /* Heading halaman landing */
--fs-h1      : 1.5rem   /* Judul halaman dashboard */
--fs-h2      : 1.125rem /* Sub judul card */
--fs-body    : 0.875rem /* Teks tabel & form */
--fs-small   : 0.75rem  /* Label badge, meta info */
```

### Spasi & Layout
```
Sidebar width (desktop) : 220px  (fixed, tidak collapse)
Sidebar width (mobile)  : 0px   (overlay drawer)
Topbar height           : 60px
Content padding         : 24px
Card gap                : 16px
Stat card min-width     : 160px
```

---

## 🧱 STRUKTUR LAYOUT GLOBAL

### App Shell (semua halaman internal/dashboard)
```
┌─────────────────────────────────────────────────┐
│  SIDEBAR (fixed, 220px)  │  MAIN CONTENT AREA   │
│  - Logo LensHub          │  ┌─────────────────┐ │
│  - Navigation groups     │  │ TOP BAR         │ │
│  - User profile bottom   │  │ (judul + aksi)  │ │
│                          │  ├─────────────────┤ │
│                          │  │ CONTENT         │ │
│                          │  │ (scroll)        │ │
│                          │  └─────────────────┘ │
└─────────────────────────────────────────────────┘
```

### Sidebar
- Background: `--bg-sidebar` (#1E2A5E)
- Logo area: ikon bulat oranye + teks "LensHub" putih tebal + sub "Internal System" abu muda
- Navigasi dibagi 4 kelompok dengan label uppercase kecil abu:
  - **UTAMA**: Dashboard
  - **OPERASIONAL**: Manajemen Sewa, Transaksi, Inventory
  - **PELANGGAN**: Klien Database
  - **ANALITIK & SISTEM**: Laporan Keuangan, Denda & Penalti, Role Management, Pengaturan, Kembali ke Web
- Item aktif: background putih transparan 15%, teks putih, ikon berwarna
- Item non-aktif: teks abu muda #94A3B8, ikon abu
- User profile di bagian bawah sidebar: avatar bulat + nama + role kecil (contoh: "Anggeline / Owner – Full Access")

### Top Bar
- Background: `--bg-card` putih dengan shadow tipis bawah
- Kiri: Judul halaman (h1, bold) + tanggal hari ini (sub teks abu)
- Kanan: Tombol ikon notifikasi (bell) + Tombol `Export` (outline abu) + Tombol `+ Sewa Baru` (primary biru)

---

## 📄 HALAMAN 1 — LANDING PAGE (OwlCamera)

### Navbar Landing
- Background: putih, sticky top
- Kiri: logo "OwlCamera" teks biru bold
- Tengah: menu link → Dashboard, Fitur ▾, Produk ▾, Tentang
- Kanan: Tombol **"Start Free"** (biru solid, rounded)

### Hero Section
- Layout: 2 kolom (teks kiri, floating card kanan)
- Background: putih
- Heading: `"Sewa Kamera Profesional, Kapan Saja."` — biru gelap, font besar, baris 2
- Subtext: deskripsi singkat fitur (tracking sewa real-time hingga laporan keuangan)
- CTA buttons: `Mulai Perjalanan` (biru solid) + `Lihat Dashboard` (outline abu)
- **Floating UI card** (kanan hero):
  - Card putih, shadow medium, border-radius 16px
  - Menampilkan mini preview dashboard: total pendapatan (Rp 6.13), badge merah "100%", baris daftar sewa dengan avatar + status badge berwarna (Aktif / Terlambat / Selesai)
  - Sedikit miring (CSS `transform: rotate(-2deg)`) untuk kesan dinamis

### Section Fitur Unggulan
- Background: putih
- Heading tengah: `"Semua yang Anda Butuhkan dalam Satu Platform"`
- Sub heading: teks deskripsi singkat abu
- Grid 2×3 feature cards:
  - Setiap card: ikon berwarna (biru / orange / hijau) di dalam kotak bulat, judul bold, deskripsi kecil
  - Fitur: Smart Tracking Timer, Automatic Penalty, Klien Database, Dashboard Monitor, Role Management, Financial Report
  - Border: 1px `--border-default`, border-radius 12px, hover: shadow ringan

### Section 10 Alat Paling Sering Disewa
- Background: `--bg-dark-section` biru gelap (#1E2A5E)
- Heading tengah putih: `"10 Alat Paling Sering Disewa"`
- Grid 5 kolom × 2 baris, tiap item:
  - Foto kamera (gambar persegi, border-radius 8px, background gelap sedikit lebih terang)
  - Teks bawah: `Kamera` + `Type: EOS` (abu muda) + angka sewa (putih bold)
- Tidak ada tombol, murni showcase

### Footer Landing
- Background: biru gelap
- 4 kolom: Logo+deskripsi singkat | Fitur links | Dashboard links | Lainnya links
- Copyright bar bawah teks kecil abu

---

## 📄 HALAMAN 2 — DASHBOARD MONITOR

### Stat Cards Row (4 card)
Letakkan 4 card sejajar horizontal, responsive wrap ke 2×2 di tablet, 1 kolom di mobile.

| Card | Ikon | Label | Value | Sub |
|------|------|-------|-------|-----|
| Pendapatan Hari Ini | 💰 ikon dompet oranye | "Pendapatan Hari Ini" | `Rp 6,000,000` | "naik X% dari kemarin" |
| Sewa Aktif | 📷 ikon kamera biru | "Sewa Aktif" | `10` | "unit sedang disewa" |
| Denda Terkumpul | ⚠️ ikon merah | "Denda Terkumpul" | `Rp 500,000` | "dari X transaksi" |
| Stok Tersedia | 📦 ikon hijau | "Stok Tersedia" | `35 / 100` | "unit tersedia" |

Styling card:
- Background putih, border-radius 12px, padding 20px
- Ikon dalam kotak bulat warna pastel (oranye muda, biru muda, merah muda, hijau muda), ukuran 40×40px
- Nilai utama: font DM Mono, 1.75rem, bold, warna sesuai aksen
- Sub teks: 0.75rem, abu

### Chart Row (2 kolom: 60% + 40%)
**Kiri — Pendapatan 7 Hari (Bar Chart)**
- Judul: "Pendapatan 7 Hari" + tombol link "Laporan Penuh" (teks biru kecil)
- Bar chart sederhana, 7 batang, warna biru (#2B4EFF) dengan 1 batang aksen (hari ini lebih terang/highlight)
- Sumbu Y: label nominal Rp disingkat (Rp 50k, Rp 100k, dst)
- Sumbu X: label hari (Sen, Sel, Rab, …)
- Library: Chart.js atau Recharts

**Kanan — Distribusi Kategori (Donut Chart)**
- Judul: "Distribusi Kategori"
- Donut chart, 4 segmen warna: Kamera (#2B4EFF), Lensa (#22C55E), Lighting (#F59E0B), Lain-lain (#EF4444)
- Legend di bawah chart, horizontal, dot warna + label + persen

### Tabel Aktivitas Sewa Terkini
- Judul: "Aktivitas Sewa Terkini" + tombol kanan "Lihat Semua" (text link biru)
- Kolom: No/ID | Klien (nama + KTP) | Alat (nama kamera) | Mulai | Durasi | Total | Status | Aksi
- Baris: alternating background putih / abu sangat muda (#F9FAFB)
- Kolom **Status** badge pill:
  - `Aktif` → background hijau muda, teks hijau
  - `Terlambat` → background merah muda, teks merah
  - `Selesai` → background abu muda, teks abu gelap
  - `Hampir JT` → background kuning muda, teks kuning gelap
- Kolom **Aksi**: ikon titik tiga (···) abu + tombol kecil "lihat" outline
- Tabel responsive: di mobile scroll horizontal

---

## 📄 HALAMAN 3 — MANAJEMEN SEWA & TIMER

### Stat Bar (4 angka)
Row horizontal 4 stat mini:
- `15` Sewa Aktif (biru)
- `5` Hampir Jatuh Tempo (kuning/orange)
- `3` Terlambat (merah)
- `100` Selesai Bulan Ini (hijau)

Tiap item: angka besar bold + label kecil di bawah, tanpa card border (inline stat strip).

### Timer Cards Grid
Grid **3 kolom** (desktop), 2 kolom (tablet), 1 kolom (mobile).  
Tiap timer card:
```
┌──────────────────────────────────┐
│ Nama Klien (bold)     [badge AKSI]│
│ Nama Alat - #ID-0041             │
│                                  │
│        00:00:05                  │
│   (countdown, font DM Mono)      │
│                                  │
│ Mulai: 08.00  Selesai: 17.00     │
│ Total: Rp 150.000                │
└──────────────────────────────────┘
```
- Background card: putih, border-radius 12px
- Countdown `00:00:05`: font DM Mono, 2.5rem, warna biru (#2B4EFF) saat aktif, merah saat terlambat
- Badge aksi kanan atas: `AKTIF` (biru pill) / `TERLAMBAT` (merah pill)
- **Animasi**: angka countdown berubah setiap detik (JavaScript `setInterval`); saat terlambat, seluruh card berikan border kiri 4px merah + background merah muda sangat pucat

### Tabel Semua Sewa Aktif
- Judul: "Semua Sewa Aktif" + tombol kanan "+ Sewa Baru" (biru)
- Kolom: No | Klien (nama + badge KTP) | Alat | Mulai | Jatuh Tempo | Sisa Waktu | Denda | Status | Aksi
- Kolom **Sisa Waktu**: teks merah bold jika sudah lewat, hijau jika masih banyak
- Kolom **Denda**: tampilkan `Rp X` merah jika ada, `-` abu jika tidak
- Kolom **Aksi**: ikon eye + ikon edit (outline abu, ukuran 16px)

---

## 📄 HALAMAN 4 — RIWAYAT TRANSAKSI

### Stat Summary (4 angka)
Row horizontal:
- Total Transaksi Bulan ini: `250` (ikon biru)
- Total Pendapatan Bulan ini: `Rp 20,000,000` (ikon hijau)
- Sedang Berjalan: `50` (ikon oranye)
- Total Denda Bulan Ini: `Rp 2,100,000` (ikon merah)

### Filter Bar
- Input search placeholder "Cari transaksi, klien, alat…" (lebar penuh atau 50%)
- Dropdown `Semua Status` (Semua / Aktif / Selesai / Terlambat)
- Date picker `1/05/2026` (pilih bulan)
- Tombol `+ Transaksi Baru` (biru)

### Tabel Riwayat Transaksi
Kolom: No TRX | Klien (nama + KTP badge) | Alat | Durasi | Total Bayar | Denda | Status | Aksi

- Baris dengan badge status: `Transfer` (badge abu/outline) | `Aktif` (hijau) | `Selesai` (hijau gelap) | `Terlambat` (merah)
- Kolom **Aksi**: 2 tombol kecil → `lihat` (outline biru, xs) + `hapus` / `detail` (outline merah, xs)
- Tabel punya sticky header saat scroll

---

## 📄 HALAMAN 5 — INVENTORY

### Filter & Action Bar
- Input search placeholder "Cari alat…" (lebar 40%)
- Dropdown `Semua Status` (Tersedia / Disewa / Maintenance)
- Dropdown `Semua Kategori` (Kamera / Lensa / Lighting / Aksesoris)
- Tombol `+ Tambah Alat` (biru)

### Card Grid Inventory
Grid **3 kolom** (desktop), 2 kolom (tablet), 1 kolom (mobile).

Tiap item card:
```
┌────────────────────────────────┐
│  [foto kamera, 16:9, rounded]  │
│  [badge status pojok kanan: ●] │
├────────────────────────────────│
│  Sony Alpha A7 IV        ···   │
│  Kamera Mirrorless             │
│  ──────────────────────────    │
│  Rp 300,000/hari               │
│  Stok tersedia: 3              │
│  [Sewa]   [Detail]             │
└────────────────────────────────┘
```
- Badge status pojok foto: bulat kecil `● Tersedia` (hijau) / `● Disewa` (oranye) / `● Maintenance` (abu)
- Tombol `Sewa`: biru solid xs, `Detail`: outline abu xs
- **Card terakhir** khusus: tampilkan card kosong dengan ikon `+` besar dan teks "Tambah Alat Baru" — border dashed abu

---

## 📄 HALAMAN 6 — KLIEN DATABASE

### Stat Bar (4)
- Total Klien: `1500`
- Aktif Bulan Ini: `95`
- Klien VIP: `50`
- Pinjaman Terlambat: `25`

### Filter
- Search "Cari nama, KTP, nomor HP…"
- Dropdown `Semua Status`
- Tombol `+ Tambah Klien` (biru)

### Tabel Database Klien
Kolom: No | Nama | NIK | No HP | Alamat | Total Sewa | Terakhir Sewa | Status | Aksi

- Status badge: `Aktif` (hijau) / `Blacklist` (merah)
- Aksi: ikon edit (pensil) + ikon delete (tong sampah) — keduanya icon button outline abu, size 16px

---

## 📄 HALAMAN 7 — LAPORAN KEUANGAN

### Export & Filter Bar
- Tombol: `Export PDF` (icon pdf merah + teks) | `Export Excel` (icon excel hijau + teks)
- Dropdown bulan: `Mei 2026 ▾`

### Stat Row (4)
- Total Pendapatan: `Rp 100,000,000` (ikon hijau)
- Total Transaksi: `783`
- Total Alat: `50`
- Total Denda: `Rp 2,000,000`

### Bar Chart — Pendapatan per Kategori Alat
- Horizontal bar chart
- Kategori: Kamera, Lensa, Gimbal, Lighting, Aksesori, Lain-lain
- Bar warna biru (#2B4EFF), ukuran sesuai nilai
- Label nilai di ujung kanan bar

### Tabel Ringkasan Laporan Keuangan
- Judul: "Ringkasan Laporan Keuangan – Mei 2026" + tombol "Export" kanan
- Kolom: Periode | Transaksi | Pendapatan/Sewa | Denda | Total Order | Biaya Operasional | Net Profit
- **Baris total** di bawah: background biru muda, teks bold

---

## 📄 HALAMAN 8 — DENDA & PENALTI

### Stat Bar (4)
- Aktif Terlambat: `15`
- Denda Berjalan: `Rp 450,000`
- Total Denda Bulan Ini: `Rp 850,000`
- Total Selesai Dilunasi: `5`

### Section — Denda Aktif Saat Ini
Tabel:
Kolom: Klien | Alat | Keterlambatan | Tarif Denda/Jam | Total Denda | Status | Aksi

- Status: `Belum Lunas` (merah solid pill) | `Sudah Lunas` (hijau pill)
- Tombol aksi: `Tandai Lunas` (biru kecil) / `Lihat Detail`

### Section — Riwayat Denda Bulan Ini
Tabel lebih kecil:
Kolom: Tanggal | Klien | Alat | Keterlambatan | Total Denda | Status

---

## ⚙️ KOMPONEN GLOBAL REUSABLE

### Badge / Pill Status
```css
.badge {
  display: inline-flex; align-items: center;
  padding: 2px 10px;
  border-radius: 999px;
  font-size: 0.7rem; font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.03em;
}
.badge-aktif    { background: #DCFCE7; color: #16A34A; }
.badge-terlambat{ background: #FEE2E2; color: #DC2626; }
.badge-selesai  { background: #F1F5F9; color: #64748B; }
.badge-hampir   { background: #FEF9C3; color: #CA8A04; }
.badge-transfer { background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }
```

### Tombol
```css
/* Primary */
.btn-primary { background: #2B4EFF; color: #fff; padding: 8px 18px; border-radius: 8px; font-weight: 600; }
.btn-primary:hover { background: #1A3ACC; }

/* Outline */
.btn-outline { background: transparent; border: 1.5px solid #D1D5DB; color: #374151; padding: 8px 18px; border-radius: 8px; }
.btn-outline:hover { background: #F9FAFB; }

/* Icon button */
.btn-icon { background: none; border: none; color: #9CA3AF; padding: 6px; border-radius: 6px; cursor: pointer; }
.btn-icon:hover { background: #F3F4F6; color: #374151; }
```

### Input & Search Bar
```css
.input-search {
  width: 100%; max-width: 400px;
  padding: 9px 14px 9px 36px; /* kiri untuk ikon search */
  border: 1.5px solid #E5E7EB;
  border-radius: 8px;
  font-size: 0.875rem;
  background: #F9FAFB;
}
.input-search:focus { border-color: #2B4EFF; background: #fff; outline: none; box-shadow: 0 0 0 3px rgba(43,78,255,0.1); }
```

### Dropdown / Select
- Border 1px #E5E7EB, border-radius 8px, padding 8px 12px
- Chevron ikon custom kanan, background putih
- Opsi dengan hover background #EFF6FF

---

## 📱 RESPONSIVITAS

### Breakpoints
```
Mobile  : < 640px
Tablet  : 640px – 1024px
Desktop : > 1024px
```

### Aturan per Breakpoint

**Mobile (< 640px)**
- Sidebar: **hilang**, diganti hamburger menu di topbar → slide-in drawer dari kiri, overlay hitam transparan
- Topbar: hanya ikon burger + judul + bell + avatar (tanpa tombol Export / Sewa Baru → masuk ke FAB atau bottom sheet)
- Stat cards: 1 kolom, full width
- Chart row: 1 kolom, chart penuh lebar
- Timer cards grid: 1 kolom
- Inventory grid: 1 kolom (atau 2 kolom jika layar ≥ 400px)
- Tabel: scroll horizontal (`overflow-x: auto` wrapper), kolom non-esensial bisa disembunyikan (gunakan `hidden md:table-cell`)

**Tablet (640–1024px)**
- Sidebar: **collapse** menjadi icon-only (width 64px), hover expand, atau hamburger toggle
- Stat cards: 2 kolom × 2 baris
- Chart row: 1 kolom (chart full width, donut di bawah bar chart)
- Timer cards: 2 kolom
- Inventory grid: 2 kolom

**Desktop (> 1024px)**
- Sidebar: full 220px, selalu terlihat
- Semua layout seperti dijelaskan di atas

---

## 🚀 PERFORMA & BEST PRACTICE

### Ringan & Cepat
1. **Gunakan CSS Variables** untuk semua warna dan ukuran — tidak ada hardcode warna inline
2. **Lazy load gambar** — semua `<img>` tambahkan `loading="lazy"` dan `decoding="async"`
3. **Chart**: gunakan Chart.js (CDN) atau Recharts (React) — jangan render chart jika elemen belum visible (IntersectionObserver)
4. **Font**: load hanya weight yang dipakai (400, 600, 700). Gunakan `font-display: swap`
5. **Animasi**: gunakan `transform` dan `opacity` saja (GPU-accelerated), hindari animasi `width`/`height`/`top`/`left`
6. **Tabel besar**: gunakan virtual scroll jika baris > 100 (misalnya react-window)
7. **Gambar kamera**: gunakan WebP format, max 200KB per gambar
8. **Icon**: gunakan SVG inline atau icon library ringan (Lucide React / Heroicons) — tidak pakai FontAwesome CDN penuh

### Aksesibilitas Dasar
- Semua tombol punya `aria-label`
- Badge status punya `role="status"` atau `aria-label` deskriptif
- Fokus ring visible untuk navigasi keyboard
- Kontras warna memenuhi WCAG AA (minimal 4.5:1 untuk teks kecil)

---

## 🗂️ STRUKTUR FILE YANG DISARANKAN

```
src/
├── components/
│   ├── layout/
│   │   ├── Sidebar.jsx
│   │   ├── Topbar.jsx
│   │   └── AppShell.jsx
│   ├── ui/
│   │   ├── Badge.jsx
│   │   ├── Button.jsx
│   │   ├── StatCard.jsx
│   │   ├── DataTable.jsx
│   │   └── SearchInput.jsx
│   └── charts/
│       ├── BarChart7Days.jsx
│       └── DonutKategori.jsx
├── pages/
│   ├── Landing.jsx
│   ├── Dashboard.jsx
│   ├── ManajemenSewa.jsx
│   ├── Transaksi.jsx
│   ├── Inventory.jsx
│   ├── KlienDatabase.jsx
│   ├── LaporanKeuangan.jsx
│   └── DendaPenalti.jsx
└── styles/
    ├── variables.css   ← semua CSS custom properties
    └── global.css
```

---

## 🎨 DETAIL VISUAL PENTING (Jangan Terlewat)

1. **Sidebar logo**: lingkaran oranye solid #FF6B2B dengan ikon lensa/kamera putih di tengah, bukan gambar — buat SVG inline
2. **Countdown timer** di halaman Manajemen Sewa: format `HH:MM:SS`, update real-time, teks merah berkedip (`@keyframes blink`) saat sisa < 1 jam
3. **Chart bar highlight**: batang hari ini di chart pendapatan 7 hari lebih terang (gradient putih ke biru) vs batang hari lain (biru solid)
4. **Empty state**: saat tabel kosong, tampilkan ilustrasi SVG kecil + teks "Belum ada data" abu — jangan tampilkan tabel kosong tanpa feedback
5. **Loading skeleton**: saat data sedang dimuat, tampilkan skeleton shimmer (animasi gradient kiri-kanan) pada card dan baris tabel
6. **Hover tabel**: baris tabel saat di-hover background berubah ke #F0F4FF (biru sangat muda)
7. **Tombol "+ Sewa Baru"**: selalu ada di topbar kanan di semua halaman internal — ini CTA primer utama
8. **Card inventory "Tambah Alat Baru"**: border dashed 2px #D1D5DB, background #F9FAFB, ikon `+` ukuran 48px warna abu, teks "Tambah Alat Baru" abu gelap, hover border berubah biru dan background biru sangat muda

---

## ✅ CHECKLIST SEBELUM DEPLOY

- [ ] Semua halaman terdaftar di sidebar dan route berfungsi
- [ ] Sidebar aktif state sesuai halaman yang sedang dibuka
- [ ] Semua tabel bisa scroll horizontal di mobile
- [ ] Countdown timer berjalan tanpa memory leak (clearInterval saat unmount)
- [ ] Badge warna sesuai status (tidak ada badge statis)
- [ ] Chart render setelah data tersedia (tidak error saat data kosong)
- [ ] Font dimuat dari Google Fonts dengan preconnect
- [ ] Tidak ada `console.error` atau `console.warn` di production
- [ ] Semua gambar punya `alt` text deskriptif
- [ ] Halaman Landing responsif hingga 320px

---

*Dokumen ini dibuat berdasarkan analisis desain UI/UX LensHub & OwlCamera. Gunakan sebagai acuan tunggal dalam membangun frontend — seluruh detail warna, spasi, komponen, dan behaviour sudah didefinisikan di sini.*
