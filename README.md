# ifs24023 - PABWE 2026 Praktikum 7 Studi Kasus: Stock Barang (Laravel 13 & Livewire)

Aplikasi Manajemen Stok Barang (**StockPro**) modern dengan arsitektur **Clean Architecture & Feature-First Approach** sesuai standar industri, dibangun menggunakan **Laravel 13**, **Livewire 4**, basis data **MySQL**, upload/export spreadsheet via **PhpOffice/PhpSpreadsheet**, integrasi kecerdasan buatan (**OpenAI AI Content Generation**), rate limiting (1000 per detik), proteksi kepemilikan middleware, force HTTPS, dan **100% Test Coverage**.

---

## 🚀 Fitur Utama

1. **Clean Architecture & Feature-First Approach**:
   - Modul diorganisir per feature/domain (`App\Features\Auth`, `App\Features\Stock`, `App\Features\Admin`, `App\Features\Ai`, `App\Features\Profile`).
   - Menerapkan Dependency Inversion Principle (DIP) dengan repository interface dan client contracts.
   - Pemisahan lapisan yang jelas antara Actions, Repositories, Services, DTOs, Policies, dan UI (Livewire).

2. **Autentikasi & Autorisasi Role-Based**:
   - **Login Fleksibel**: Pengguna dapat masuk menggunakan **Username** atau **Email** (input `id="login-username"`, `id="login-password"`, `id="login-submit"`).
   - **Role `admin`**:
     - Mengakses halaman Manajemen Pengguna (`/admin/users`).
     - Melihat daftar seluruh pengguna beserta username, email, dan jumlah item stok barang mereka.
     - Melihat detail stok barang pengguna lain dalam mode **Read-Only** (`/admin/users/{user}/stocks`).
     - Admin **tidak dapat** memodifikasi/menghapus stok barang pengguna lain.
   - **Role `member`**:
     - User yang mendaftar melalui form registrasi (dengan nama, username unik, email, password) otomatis memiliki role `member`.
     - Hanya dapat mengelola (CRUD), mengubah kuantitas (+/-), mengekspor/mengimpor, serta generate AI untuk stok barang miliknya sendiri (`/stocks`).
   - **Middleware & Policy**:
     - `EnsureRole`: Membatasi rute khusus admin.
     - `EnsureStockOwnership` (`owns.stock`): Memastikan member tidak dapat mengakses atau memodifikasi stok barang milik orang lain.
     - `StockPolicy`: Memvalidasi otorisasi di tingkat model.

3. **Spreadsheet Excel Import & Export**:
   - Menggunakan library aman dari kerentanan: **`phpoffice/phpspreadsheet`**.
   - **Export**: Menghasilkan file `.xlsx` dengan styling profesional (header berwarna, border, text wrap, auto size).
   - **Import**: Mengunggah file `.xlsx`, `.xls`, `.csv` untuk melakukan bulk insert stok barang secara aman dengan validasi format data, kuantitas, batas stok minimum, harga, dan status otomatis.

4. **AI Stock Content Generator (OpenAI)**:
   - Dikonfigurasi secara dinamis melalui `.env`:
     - `OPENAI_ENDPOINT`
     - `OPENAI_KEY`
     - `OPENAI_MODEL`
   - Kemampuan generate multiple item stok barang secara otomatis berdasarkan prompt/permintaan pengguna.
   - Output diformat dan divalidasi ke dalam DTO `GeneratedStockItem`.
   - Pengguna dapat melihat preview hasil generate AI, memilih item yang diinginkan, dan menyimpannya langsung ke basis data.

5. **Rate Limiting & Keamanan**:
   - Skrip pembatas akses: **1000 request per detik** via `RateLimiter::for('1000-per-second', ...)`.
   - **Force HTTPS**: Dikonfigurasi melalui `FORCE_HTTPS=true` di `.env` atau otomatis aktif pada environment `production`.
   - Bebas dari kerentanan keamanan (`composer audit` clean 0 advisories).

6. **100% Test Coverage**:
   - Seluruh classes, methods, dan baris kode pada direktori `app/` memiliki **100% coverage**.
   - Unit test dan feature test komprehensif menguji seluruh alur autentikasi, model, repository, policy, middleware, service AI (mocked), dan upload/download spreadsheet.
