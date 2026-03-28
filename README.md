<div align="center">

```
 _____
|  _  |___  ___ _ _ _ __
| |_| /_ // _ | | | '_ \
|  _  \_ \  __/ |_| | | |
|_| |_|___/\___|\__,_| |_|
```

# Azaxm Portfolio CMS

**Website portfolio personal dengan sistem CMS yang aman, dibangun menggunakan PHP native + MySQL.**

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Termux](https://img.shields.io/badge/Termux-Ready-black?style=flat-square)

</div>

---

## ✨ Fitur Utama

- **6 Halaman Publik** — Home, About, Skills, Contact + Admin Panel
- **Hamburger Menu** — slide dari kanan, animasi slowmo, semua device
- **Dark Theme Modern** — `#0a0a0a` background, gradient indigo/violet
- **Admin CMS** — kelola profile, skills, social links, pesan, settings
- **Keamanan Tinggi** — CSRF, XSS, SQL Injection protection, rate limiting
- **Responsive** — mobile-first, tampil sempurna di semua ukuran layar
- **Animasi Slowmo** — scroll reveal, progress bar, counter, typed text
- **Support Termux** — bisa jalan di Android tanpa VPS

---

## 📁 Struktur Folder

```
azaxm-portfolio/
├── assets/
│   ├── css/
│   │   ├── style.css          # Style utama (dark theme)
│   │   └── admin.css          # Style admin panel
│   ├── js/
│   │   ├── main.js            # Animasi & interaksi frontend
│   │   ├── admin.js           # JS admin panel
│   │   └── hamburger.js       # Hamburger menu
│   ├── images/
│   │   └── default-avatar.svg
│   └── uploads/               # Upload foto profil
│       └── .htaccess          # Block PHP execution di uploads
├── config/
│   ├── config.php             # Konfigurasi global
│   ├── database.php           # Koneksi PDO
│   └── security.php           # Fungsi keamanan
├── includes/
│   ├── header.php             # HTML head + CDN
│   ├── navbar.php             # Hamburger + side navigation
│   ├── footer.php             # Footer template
│   ├── functions.php          # Helper functions
│   └── session.php            # Session management
├── admin/
│   ├── includes/
│   │   ├── auth.php           # Cek login admin
│   │   ├── admin-header.php   # Header admin panel
│   │   └── admin-footer.php   # Footer admin panel
│   ├── index.php              # Redirect ke login/dashboard
│   ├── login.php              # Halaman login
│   ├── logout.php             # Proses logout
│   ├── dashboard.php          # Dashboard & statistik
│   ├── profile.php            # Edit profile & avatar
│   ├── skills.php             # CRUD skills
│   ├── social.php             # CRUD social links
│   ├── messages.php           # Inbox pesan kontak
│   └── settings.php          # Settings & ganti password
├── public/
│   ├── index.php              # Halaman Home
│   ├── about.php              # Halaman About
│   ├── skills.php             # Halaman Skills
│   └── contact.php            # Halaman Contact
├── .htaccess                  # Konfigurasi Apache
├── database.sql               # Skema + data default
├── generate_password.php      # Helper generate hash (hapus setelah pakai)
├── LICENSE                    # MIT License
└── README.md                  # Dokumentasi ini
```

---

## 🚀 Instalasi

### Prasyarat

| Kebutuhan | Versi |
|-----------|-------|
| PHP | 7.4+ (recommended 8.0+) |
| MySQL / MariaDB | 5.7+ |
| Web Server | Apache / PHP built-in server |

---

### 🟢 Cara 1: Install di Termux (Android)

#### Step 1 — Install Termux dari F-Droid
> ⚠️ **Wajib dari F-Droid**, bukan Play Store!
> Download: https://f-droid.org

#### Step 2 — Update & Install Package
```bash
pkg update && pkg upgrade -y
pkg install php mariadb apache2 nano zip unzip -y
```

#### Step 3 — Setup Storage
```bash
termux-setup-storage
```
Klik **Allow** saat muncul popup izin.

#### Step 4 — Clone / Copy Project
```bash
# Kalau clone dari GitHub:
pkg install git -y
git clone https://github.com/azaxm/azaxm-portfolio.git

# Atau copy dari storage HP:
cp -r ~/storage/documents/azaxm-portfolio ~/
```

#### Step 5 — Setup Database
```bash
# Start MariaDB
mysqld_safe &
sleep 5

# Buat database
mysql -u root -e "CREATE DATABASE azaxm_portfolio;"

# Import schema
mysql -u root azaxm_portfolio < ~/azaxm-portfolio/database.sql
```

#### Step 6 — Konfigurasi
```bash
nano ~/azaxm-portfolio/config/database.php
```
Pastikan:
```php
define('DB_HOST', '127.0.0.1');  // Termux WAJIB pakai 127.0.0.1
define('DB_NAME', 'azaxm_portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### Step 7 — Generate Password Admin
```bash
php ~/azaxm-portfolio/generate_password.php "PasswordKamu@123"
```
Copy hash yang muncul, lalu:
```bash
mysql -u root azaxm_portfolio -e 'UPDATE users SET password="HASH_DISINI", failed_attempts=0 WHERE username="admin";'
```

> ⚠️ Gunakan **single quote** di luar dan **double quote** di dalam agar tanda `$` tidak terbaca sebagai variabel bash!

#### Step 8 — Jalankan Server
```bash
cd ~/azaxm-portfolio
php -S 0.0.0.0:9000
```

Buka di browser HP:
```
http://localhost:9000/public/
```

Admin panel:
```
http://localhost:9000/admin/login.php
```

#### Auto-Start dengan Termux:Boot
Install **Termux:Boot** dari F-Droid, lalu:
```bash
mkdir -p ~/.termux/boot
cat > ~/.termux/boot/start-server.sh << 'EOF'
#!/data/data/com.termux/files/usr/bin/bash
mysqld_safe &
sleep 5
cd ~/azaxm-portfolio
php -S 0.0.0.0:9000 &
EOF
chmod +x ~/.termux/boot/start-server.sh
```

---

### 🔵 Cara 2: Install di XAMPP / Laragon (Windows/Linux)

#### Step 1 — Copy Project
```
XAMPP   → C:/xampp/htdocs/azaxm-portfolio/
Laragon → C:/laragon/www/azaxm-portfolio/
```

#### Step 2 — Import Database
Buka phpMyAdmin → Buat database `azaxm_portfolio` → Import `database.sql`

#### Step 3 — Konfigurasi
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'azaxm_portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### Step 4 — Generate Password
Buka browser: `http://localhost/azaxm-portfolio/generate_password.php`

#### Step 5 — Akses
```
http://localhost/azaxm-portfolio/public/
http://localhost/azaxm-portfolio/admin/
```

---

### 🟠 Cara 3: Deploy ke Shared Hosting (cPanel)

#### Step 1 — Upload File
Upload semua file ke `public_html/` via File Manager atau FTP.

#### Step 2 — Buat Database
cPanel → MySQL Databases → Buat database & user → Import `database.sql`

#### Step 3 — Konfigurasi
Edit `config/database.php` sesuai kredensial database dari cPanel.

Edit `config/config.php`:
```php
// Ubah APP_ENV ke production
define('APP_ENV', 'production');
```

#### Step 4 — Permission
```bash
chmod 755 assets/uploads/
chmod 755 assets/uploads/avatars/
```

#### Step 5 — Generate Password
Buka: `https://domain-kamu.com/generate_password.php`

> ⚠️ **HAPUS** `generate_password.php` setelah selesai!

---

## 🔑 Login Admin Default

| Field | Value |
|-------|-------|
| URL | `/admin/login.php` |
| Username | `admin` |
| Password | `Admin@123` |

> **Segera ganti password** setelah pertama login via Admin → Settings!

---

## 🔒 Keamanan

| Fitur | Implementasi |
|-------|-------------|
| SQL Injection | PDO Prepared Statements |
| XSS | `htmlspecialchars()` semua output |
| CSRF | Token per-session di setiap form |
| Brute Force | Lock 15 menit setelah 5 gagal |
| Session Hijacking | Validasi user-agent + IP |
| Session Timeout | 30 menit inaktif auto logout |
| File Upload | Validasi MIME + ekstensi + rename acak |
| Path Traversal | `.htaccess` block PHP di `uploads/` |
| Security Headers | CSP, X-Frame-Options, X-XSS-Protection |
| Honeypot | Hidden field di form login & kontak |

---

## ⚙️ Kustomisasi

### Ganti Warna Tema
Edit variabel di `assets/css/style.css`:
```css
:root {
    --primary:   #6366f1;  /* warna utama */
    --secondary: #8b5cf6;  /* warna sekunder */
    --bg:        #0a0a0a;  /* background */
}
```

### Tambah Google Maps
Admin Panel → Settings → Google Maps Embed URL
1. Buka Google Maps → cari lokasi → Share → Embed a map
2. Copy URL dari `src="..."`
3. Paste di field Settings

---

## 🛠️ Troubleshooting

| Masalah | Solusi |
|---------|--------|
| CSS tidak load | Pastikan `BASE_URL` benar di `config/config.php` |
| MySQL error di Termux | Ganti `localhost` → `127.0.0.1` di `database.php` |
| Session error / redirect loop | Set `APP_ENV` ke `production` |
| Port sudah dipakai | `pkill -f "php -S"` lalu coba port lain |
| Permission denied upload | `chmod 755 assets/uploads/` |
| Password `$` terpotong di bash | Pakai single quote di luar command mysql |
| Deprecated warnings PHP 8.5 | Sudah diperbaiki di versi ini |

---

## 📝 Changelog

### v1.1.0
- Fix: `DB_CHARSET` undefined error
- Fix: `PDO::MYSQL_ATTR_INIT_COMMAND` deprecated (PHP 8.5)
- Fix: Session error setelah login admin
- Fix: `headers_sent()` check di security headers
- Fix: `redirect()` function dipindah ke `session.php`
- Fix: BASE_URL auto-detect untuk semua environment
- Add: Support Termux (127.0.0.1 sebagai default DB_HOST)
- Add: Try-catch di semua database query

### v1.0.0
- Initial release

---

## 🤝 Kontribusi

Pull request sangat welcome! Untuk perubahan besar, buka issue dulu.

1. Fork repo ini
2. Buat branch: `git checkout -b fitur-baru`
3. Commit: `git commit -m 'Tambah fitur baru'`
4. Push: `git push origin fitur-baru`
5. Buat Pull Request

---

## 📄 License

MIT License — lihat file [LICENSE](LICENSE) untuk detail lengkap.

---

<div align="center">

Dibuat dengan ❤️ oleh **Azaxm**

[Telegram](https://t.me/azaxm) · [GitHub](https://github.com/azaxm) · [Instagram](https://instagram.com/azaxm)

</div>
