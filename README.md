# LaporBanjir (BPBD Kabupaten Bandung)

Aplikasi pelaporan banjir: warga mengirim laporan + foto bukti, petugas mengelola laporan setelah login.
Laravel + Blade, autentikasi **manual** (tanpa Breeze), database MySQL `db_laporbanjir`.

## Menjalankan
```
composer install
copy .env.example .env      (Linux/Mac: cp .env.example .env)
php artisan key:generate
# buat database db_laporbanjir di phpMyAdmin, cek DB_* di .env
php artisan migrate
php artisan storage:link
php artisan db:seed --class=PetugasSeeder
php artisan serve
```
Buka http://127.0.0.1:8000. Login petugas: `admin@bpbd.bandungkab.go.id` / `Admin12345`.

## Halaman
| Route | Nama | Akses | Keterangan |
|---|---|---|---|
| GET /lapor | laporan.create | Warga | Form pelaporan + foto |
| POST /lapor | laporan.store | Warga | Simpan laporan (status Menunggu) |
| GET /laporan | laporan.index | Petugas (auth) | Daftar + thumbnail + pagination |
| GET /laporan/{id} | laporan.show | Petugas (auth) | Detail laporan |
| GET /laporan/{id}/edit | laporan.edit | Petugas (auth) | Edit, ganti foto, ubah status |
| PUT /laporan/{id} | laporan.update | Petugas (auth) | Simpan perubahan |
| DELETE /laporan/{id} | laporan.destroy | Petugas (auth) | Hapus laporan + foto |

Status penanganan: Menunggu, Diproses, Selesai.
Tingkat genangan: < 30 cm Waspada, 30-70 cm Siaga, > 70 cm Awas.

## Tes
```
php artisan test
```
