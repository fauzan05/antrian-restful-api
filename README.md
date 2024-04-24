# Aplikasi Antrian (RESTful API)

## Background
Berawal dari sebuah rasa penasaran dan mencoba untuk membuat aplikasi antrian, karena sebelumnya bekerja di sebuah perusahaan kecil yang membuat mesin antrian. Namun kali ini, saya membuat sebuah aplikasi yang full di web, tidak ada campur tangan mikrokontroller.

## Requirements

- PHP 8.2
- Docker Compose
- Laravel 10
- Livewire 3
- MariaDB 11.4.1

## Installation

Jika menggunakan database yang running di docker container, maka jalankan perintah 
```
docker compose start
```
pada root directory untuk membuat database MariaDB di sebuah kontainer dan di expose port-nya.
Jika tidak ingin menggunakan docker container, tinggal gunakan XAMPP/MAMP atau MariaDB langsung di komputer dengan konfigurasi port yang 
ada di file .env .

<br>

Kemudian, jalankan perintah berikut di root directory agar menjalankan server localhost pada port 8000 dan host localhost.
```
php artisan serve --port=8000 --host=localhost
```


<br>

Setelah Antrian RESTful API-nya dijalankan, maka API sudah bisa di consume. Kemudian buka file yang ada di folder "antrian-consume-api" dan jalankan perintah berikut pada root directory-nya :

```
php artisan serve --port=8001 --host=localhost
```

Perintah diatas untuk menjalankan server di port 8001 dan host localhost. Setelah itu jalankan perintah berikut di root directory-nya juga :

```
php artisan websockets:serve
```
Perintah diatas untuk menjalankan laravel Echo agar bisa bertukar data secara real-time seperti Websocket dalam 1 lingkup aplikasi.
Kemudian jalankan juga perintah berikut :

```
php artisan queue:work
```
Perintah diatas untuk menjalankan Queue, agar nomor antriannya diproses secara tidak langsung. Sebenarnya fitur Queue yang digunakan pada aplikasi "antrian consume api" ini hanyalah percobaan saya dalam menggunakan fitur di laravel yaitu laravel Queue. Dalam aplikasi tersebut harus dijalankan perintah tersebut agar saat pemanggilan nomor dapat diproses.

Setelah semuanya dijalankan, tinggal buka di browser http://localhost:8001/ maka akan secara otomatis diarahkan ke halaman home dari aplikasinya. Dan sekarang tinggal buat akun admin menggunakan endpoint api register. Untuk lebih lengkapnya cek pada folder "antrian-restful-api" dan cari folder "docs", kemudian buka file api-specs.json untuk melihat semua endpoint-nya.

<br>

## Specification
Aplikasi ini memiliki spesifikasi dan fitur sebagai berikut :
- Admin tidak memiliki tingkatan, jadi jika membuat lebih dari 1 akun dengan role admin maka tidak akan berpengaruh apakah admin yang satunya posisinya lebih tinggi dari admin yang lain.
- Pengunjung mendapatkan 2 nomor antrian. Yang pertama adalah nomor antrian pendaftaran, dan yang kedua adalah nomor antrian poli.
- Operator yang bertugas di loket yang melayani layanan poli tidak akan mendapatkan nomor antrian terbaru hingga nomor antrian di layanan registrasi berhasil dipanggil.
- Ketika operator menekan button call, button tersebut akan ter-disabled dan setelah itu akan ter-enabled lagi secara otomatis setelah audio antrian berhasil diputar hingga tuntas.
- 1 layanan bisa digunakan di lebih dari 1 loket. Namun 1 loket hanya bisa melayani 1 layanan saja.
- Adanya jadwal jam operasional, yang dimana ketika diaktifkan maka setiap request antrian baru akan selalu di validasi apakah jam sekarang sudah melewati batas yang ditentukan (jam buka/tutup). Jika iya maka akan menampilkan informasi bahwa sudah tutup/belum dibuka.

<br>

Aplikasi ini dibagi menjadi 2 bagian, yaitu API dan Front-end. Untuk API-nya sendiri ada di folder "antrian-restful-api", dan front-endnya ada di folder "antrian-consume-api". Keduanya harus dijalankan bersama-sama agar dapat berjalan dengan baik. Untuk melihat endpoint API-nya, masuk ke folder "antrian-restful-api" dan buka folder "docs". Disitu ada file api-specs.json yang akan membantu dalam memahami response dan request yang dibutuhkan.