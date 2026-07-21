# GARDENA-AI

GARDENA-AI adalah sistem monitoring nutrisi hidroponik skala mikro berbasis Internet of Things (IoT) yang memanfaatkan ESP32, sensor pH, sensor TDS, dan sensor suhu DS18B20. Sistem ini mengirimkan data sensor secara real-time ke aplikasi berbasis Laravel melalui REST API sehingga pengguna dapat memantau kondisi larutan nutrisi dan memperoleh rekomendasi untuk menjaga kualitas nutrisi tanaman.

---

# Fitur

- Monitoring nilai pH secara real-time.
- Monitoring nilai TDS (Total Dissolved Solids).
- Monitoring suhu larutan nutrisi.
- Dashboard monitoring berbasis web.
- Penyimpanan data sensor ke database MySQL.
- Komunikasi data menggunakan REST API.

---

# Teknologi yang Digunakan

## Backend
- Laravel 12
- PHP 8.3 / 8.2
- MySQL

## Frontend
- HTML
- CSS
- Bootstrap
- JavaScript

## IoT
- ESP32
- Arduino IDE
- Sensor pH
- Sensor TDS
- DS18B20

---

# Persyaratan Sistem

Pastikan perangkat telah terinstal:

- PHP 8.2
- Composer
- Node.js
- MySQL
- Arduino IDE

---

# Instalasi Backend

Clone repository

```bash
git clone https://github.com/chrstnestg/IF4PD13-GARDENA-AI
```

Masuk ke folder project

```bash
cd GARDENA-AI
```

Install dependency

```bash
composer install
```

Copy file environment

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Konfigurasi database pada file `.env`

```env
DB_DATABASE=gardena
DB_USERNAME=root
DB_PASSWORD=
```

Migrasi database

```bash
php artisan migrate
```

Jalankan server

```bash
php artisan serve
```

---

# Setup ESP32

Buka file program pada Arduino IDE.

Ubah konfigurasi jaringan Wi-Fi.

```cpp
const char* ssid = "Nama_WiFi";
const char* password = "Password_WiFi";
```

Atur alamat REST API.

```cpp
const char* serverURL = "http://alamat-server/api/sensor";
```

Upload program ke ESP32 menggunakan Arduino IDE.

---

#  Wiring Perangkat

| Sensor | ESP32 |
|---------|--------|
| Sensor pH | GPIO34 |
| Sensor TDS | GPIO35 |
| DS18B20 DATA | GPIO4 |
| VCC | VIN / 3.3V (sesuai modul) |
| GND | GND |

---

# REST API

## Endpoint

```
POST /api/sensor
```

## Header

```
Content-Type: application/json
```

## Request Body

```json
{
    "id_device":3,
    "suhu":27.50,
    "ph":6.80,
    "ec_tds":920
}
```

## Contoh Pengiriman Data ESP32

```cpp
HTTPClient http;
http.begin(serverURL);
http.addHeader("Content-Type","application/json");

String jsonData = "{\"id_device\":3"
                  ",\"suhu\":" + String(suhu,2)
                  + ",\"ph\":" + String(ph,2)
                  + ",\"ec_tds\":" + String(tds,2) + "}";

http.POST(jsonData);
```

---

# Cara Menjalankan Sistem

1. Jalankan MySQL.
2. Jalankan Laravel menggunakan `php artisan serve`.
3. Hubungkan ESP32 ke jaringan Wi-Fi.
4. Upload program ESP32.
5. Pastikan data sensor berhasil dikirim ke REST API.
6. Buka dashboard monitoring pada browser.

---

# Tim Pengembang

- **Irene Kristi Syari Rani Samosir**
  - IoT Developer
  - UI/UX Designer
  - Frontend Developer
  - Backend Developer

- **Christine Thalia Elisabeth Sitanggang**
  - AI Engineer
  - UI/UX Designer
  - Frontend Developer
  - Backend Developer


---

# Lisensi

Project ini dikembangkan sebagai Project Based Learning (PBL) Program Studi Teknik Informatika, Politeknik Negeri Batam.