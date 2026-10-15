# API GeoJSON Data Permohonan Yogyakarta

Aplikasi Laravel yang menyediakan autentikasi berbasis token dan endpoint untuk mengambil data permohonan dalam format GeoJSON. Data spasial dibaca dari tabel `Contoh_Permohonan`; aplikasi ini tidak menyediakan antarmuka peta.

## Fitur

- Registrasi dan login pengguna melalui API.
- Token akses menggunakan Laravel Sanctum.
- Informasi pengguna yang sedang terautentikasi.
- Data permohonan sebagai GeoJSON `FeatureCollection`.
- Pencabutan token saat logout.

## Teknologi dan prasyarat

- PHP 8.3 atau lebih baru.
- Composer.
- Node.js dan npm untuk membangun aset frontend.
- SQLite untuk pengembangan autentikasi secara lokal (konfigurasi bawaan).
- PostgreSQL dengan ekstensi PostGIS untuk endpoint data GeoJSON.

## Instalasi dan menjalankan aplikasi

Jalankan perintah berikut dari direktori proyek. Contoh menggunakan PowerShell:

```powershell
composer install
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

Migrasi menyiapkan tabel Laravel seperti `users` dan `personal_access_tokens`. Seeder membuat akun lokal untuk pengujian:

| Email           | Kata sandi |
| --------------- | ---------- |
| `ma@yusfan.com` | `rahasia`  |

Akun tersebut hanya untuk pengembangan. Jangan gunakan kata sandi contoh ini di lingkungan produksi.

Untuk menjalankan server pengembangan, Vite, dan worker antrean:

```powershell
composer run dev
```

Aplikasi tersedia di `http://127.0.0.1:8000`. Untuk pengujian tanpa proses Vite atau worker, jalankan `php artisan serve` saja. Jika aset frontend berubah, jalankan `npm run dev` di terminal lain atau bangun ulang dengan `npm run build`.

## Menyiapkan sumber data GeoJSON

Konfigurasi bawaan `.env.example` menggunakan SQLite. Konfigurasi itu cukup untuk tabel autentikasi dan migrasi Laravel, tetapi tidak menyediakan tabel spasial yang dibutuhkan `/api/geojson-data`.

Untuk menggunakan endpoint GeoJSON:

1. Siapkan server PostgreSQL dengan PostGIS aktif dan tabel `Contoh_Permohonan` tersedia.
2. Pastikan tabel tersebut memiliki kolom `geom`, `pemohon`, `tgl_surat`, `keterangan`, dan `wadmpr`. Kolom `geom` harus dapat diproses oleh fungsi PostGIS `ST_AsGeoJSON`.
3. Atur koneksi database di `.env`, misalnya:

    ```dotenv
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nama_database
    DB_USERNAME=nama_pengguna
    DB_PASSWORD=password_database
    ```

4. Jalankan migrasi pada database yang dikonfigurasi dengan `php artisan migrate --seed`.

Tabel `Contoh_Permohonan` dan datanya merupakan sumber data eksternal: migrasi dalam proyek ini tidak membuat atau mengisi tabel tersebut. Jangan menaruh kredensial database sungguhan di README atau repositori.

## Panduan API

Semua endpoint menggunakan prefiks `/api`. Kirim dan terima JSON dengan header `Accept: application/json`; permintaan dengan body juga harus mengirim `Content-Type: application/json`.

### 1. Registrasi

`POST /api/register`

Contoh body:

```json
{
    "name": "Pengguna",
    "email": "pengguna@example.com",
    "password": "password-minimal-8",
    "password_confirmation": "password-minimal-8"
}
```

Respons berhasil berstatus `201` dan berisi `access_token`. Token juga dapat diperoleh dengan login berikutnya.

### 2. Login

`POST /api/login`

```json
{
    "email": "bisnis_ma@yusfan.com",
    "password": "rahasia"
}
```

Simpan nilai `access_token` dari respons. Kredensial harus diganti dengan akun yang Anda miliki jika tidak menggunakan seeder lokal.

### 3. Mengakses endpoint yang dilindungi

Sertakan token sebagai bearer token pada header `Authorization`:

```http
Authorization: Bearer <access_token>
```

| Metode | Endpoint            | Kegunaan                                                       |
| ------ | ------------------- | -------------------------------------------------------------- |
| `GET`  | `/api/user`         | Mengambil data pengguna yang sedang login.                     |
| `GET`  | `/api/geojson-data` | Mengambil data permohonan sebagai GeoJSON `FeatureCollection`. |
| `POST` | `/api/logout`       | Mencabut token yang digunakan pada permintaan ini.             |

Contoh permintaan di PowerShell setelah login:

```powershell
$login = Invoke-RestMethod `
  -Uri "http://127.0.0.1:8000/api/login" `
  -Method Post `
  -ContentType "application/json" `
  -Headers @{ Accept = "application/json" } `
  -Body (@{ email = "ma@yusfan.com"; password = "rahasia" } | ConvertTo-Json)

$headers = @{
  Accept = "application/json"
  Authorization = "Bearer $($login.access_token)"
}

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/geojson-data" -Headers $headers
```

GeoJSON mengembalikan properti `Pemohon`, `Tgl_Surat`, `Keterangan`, dan `WADMPR` untuk setiap fitur. Endpoint ini memerlukan token yang valid serta koneksi ke tabel spasial yang sudah disiapkan.

## Pengujian

Jalankan pengujian aplikasi dengan:

```powershell
php artisan test
```

## Catatan keamanan

- Jangan unggah `.env` atau membagikan token akses.
- Gunakan `APP_DEBUG=false`, kredensial kuat, dan konfigurasi database yang aman di produksi.
- Kata sandi contoh dari seeder hanya untuk lingkungan lokal.
