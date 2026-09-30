#!/usr/bin/env bash

# ==============================================================================
# Script Manajemen & Pembaruan Website RZ Store (Laravel 13 / PHP 8.4)
# Fungsi : Konfigurasi Website, Update dari GitHub, Terapkan Perubahan, SSL, Maintenance
# ==============================================================================

set -Eeuo pipefail

# Izinkan Composer berjalan jika dijalankan sebagai root
export COMPOSER_ALLOW_SUPERUSER=1
export DEBIAN_FRONTEND=noninteractive

# Warna Output Terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# Deteksi lokasi folder project saat ini
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${PROJECT_DIR}"

# Izinkan git beroperasi pada direktori proyek (mencegah error dubious ownership di Ubuntu)
if command -v git &>/dev/null; then
    git config --global --add safe.directory "${PROJECT_DIR}" 2>/dev/null || true
    git config --global --add safe.directory "*" 2>/dev/null || true
fi

log_info() { echo -e "${CYAN}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }
log_step() { echo -e "\n${BLUE}==>${NC} ${BOLD}$1${NC}"; }

# Periksa hak akses sudo/root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        log_error "Perintah ini memerlukan hak akses root. Silakan jalankan dengan: sudo ./manage.sh"
        exit 1
    fi
}

# ------------------------------------------------------------------------------
# 1. Terapkan Semua Perubahan ke Website (Rebuild, Migrate, Cache, Restart)
# ------------------------------------------------------------------------------
do_apply_changes() {
    log_step "Menerapkan Semua Perubahan ke Website (Rebuild & Activate)"
    
    log_info "Lokasi projek: ${PROJECT_DIR}"

    # 1. Pastikan file .env ada
    if [ ! -f ".env" ]; then
        log_warn "File .env belum ditemukan. Membuat dari .env.example..."
        if [ -f ".env.example" ]; then
            cp .env.example .env
            php artisan key:generate --force
        fi
    fi

    # 2. Composer Dependencies
    if [ -f "composer.json" ]; then
        log_info "Menginstal & mengoptimalkan dependensi PHP (Composer)..."
        composer install --no-dev --optimize-autoloader --no-interaction
        log_success "Composer dependensi berhasil dioptimalkan."
    fi

    # 3. NPM Build (Frontend Vite / CSS)
    if [ -f "package.json" ]; then
        log_info "Memeriksa Node.js & NPM..."
        if ! command -v npm &> /dev/null; then
            log_warn "NPM belum terpasang. Memasang Node.js LTS..."
            curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
            apt-get install -y nodejs
        fi
        log_info "Mengompilasi asset frontend terbaru (Vite build)..."
        npm install --silent
        npm run build
        log_success "Asset CSS & JavaScript berhasil dikompilasi ke public/build/."
    fi

    # 4. Database Migration
    log_info "Menjalankan migrasi database..."
    if grep -q "^DB_CONNECTION=sqlite" .env 2>/dev/null || ! grep -q "^DB_CONNECTION=" .env 2>/dev/null; then
        [ -f "database/database.sqlite" ] || touch database/database.sqlite
        chmod 664 database/database.sqlite 2>/dev/null || true
        chmod 775 database 2>/dev/null || true
    fi

    if ! php artisan migrate --force; then
        log_warn "Koneksi database saat ini gagal dihubungi."
        if grep -q "^DB_CONNECTION=mysql" .env 2>/dev/null; then
            log_info "Mengalihkan database ke SQLite secara otomatis..."
            sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|" .env
            sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=file|" .env
            sed -i "s|^CACHE_STORE=.*|CACHE_STORE=file|" .env
            sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|" .env
            touch database/database.sqlite
            chmod 664 database/database.sqlite 2>/dev/null || true
            chmod 775 database 2>/dev/null || true
            php artisan optimize:clear
            php artisan migrate --force
            log_success "Migrasi database SQLite berhasil diselesaikan!"
        fi
    else
        log_success "Migrasi tabel database selesai."
    fi

    # 5. Clear & Re-cache Optimization
    log_info "Membersihkan cache lama dan membuat cache baru untuk produksi..."
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    log_success "Cache konfigurasi, route, dan blade view berhasil dibuat."

    # 6. Fix Permissions
    fix_permissions_internal

    # 7. Restart / Reload Web Server & PHP-FPM
    if command -v systemctl &> /dev/null; then
        log_info "Memuat ulang layanan PHP-FPM 8.4 dan Nginx..."
        systemctl reload php8.4-fpm 2>/dev/null || systemctl restart php8.4-fpm 2>/dev/null || true
        systemctl reload nginx 2>/dev/null || systemctl restart nginx 2>/dev/null || true
    fi

    # Tampilkan info commit terakhir
    local latest_commit=""
    if command -v git &>/dev/null && [ -d ".git" ]; then
        latest_commit=$(git log -1 --pretty=format:"%h - %s (%cr) <%an>" 2>/dev/null || echo "")
    fi

    echo -e "\n${GREEN}==============================================================================${NC}"
    echo -e "${GREEN}🎉 SEMUA PERUBAHAN BERHASIL DITERAPKAN KE WEBSITE LIVE!${NC}"
    if [[ -n "$latest_commit" ]]; then
        echo -e "${CYAN}Commit Aktif : ${YELLOW}${latest_commit}${NC}"
    fi
    echo -e "${GREEN}==============================================================================${NC}\n"
}

# ------------------------------------------------------------------------------
# 2. Update Repository dari GitHub (Git Pull + Terapkan Perubahan)
# ------------------------------------------------------------------------------
do_update() {
    log_step "Memulai Pembaruan Website dari GitHub (Git Pull)"
    
    log_info "Lokasi projek: ${PROJECT_DIR}"
    
    # 1. Pastikan safe.directory diizinkan untuk menghindari error dubious ownership
    git config --global --add safe.directory "${PROJECT_DIR}" 2>/dev/null || true
    git config --global --add safe.directory "*" 2>/dev/null || true

    # 2. Git Pull
    log_info "Mengambil update terbaru dari GitHub..."
    git fetch --all
    git reset --hard origin/main || git pull origin main
    log_success "Kode terbaru dari GitHub berhasil diunduh."

    # 3. Terapkan seluruh perubahan (Composer, NPM, Migrate, Cache, Permissions)
    do_apply_changes
}

# ------------------------------------------------------------------------------
# 3. Konfigurasi File Environment (.env)
# ------------------------------------------------------------------------------
do_configure_env() {
    log_step "Konfigurasi Nilai .env Website"

    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env
            php artisan key:generate --force
            log_success "File .env berhasil dibuat dari template .env.example."
        else
            touch .env
        fi
    fi

    echo -e "\nPilih konfigurasi yang ingin diubah:"
    echo -e "1) Domain / APP_URL (Saat ini: $(grep '^APP_URL=' .env 2>/dev/null || echo 'Belum diset'))"
    echo -e "2) Mode Produksi / Debug (APP_ENV & APP_DEBUG)"
    echo -e "3) Konfigurasi Database (SQLite / MySQL)"
    echo -e "4) Generate Ulang APP_KEY"
    echo -e "5) Edit manual file .env dengan nano"
    echo -e "0) Kembali ke Menu Utama"
    read -rp "Pilihan Anda [0-5]: " env_choice

    case "$env_choice" in
        1)
            read -rp "Masukkan URL Domain lengkap (misal: https://rzstore.com atau http://IP_VPS): " new_url
            if [[ -n "$new_url" ]]; then
                sed -i "s|^APP_URL=.*|APP_URL=${new_url}|" .env
                log_success "APP_URL diubah menjadi: ${new_url}"
                php artisan config:cache
            fi
            ;;
        2)
            echo -e "Pilih mode: 1) Production (Debug OFF) | 2) Development (Debug ON)"
            read -rp "Pilihan [1/2]: " mode_choice
            if [[ "$mode_choice" == "1" ]]; then
                sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
                sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
                log_success "Mode diubah menjadi: Production (APP_DEBUG=false)"
            else
                sed -i "s|^APP_ENV=.*|APP_ENV=local|" .env
                sed -i "s|^APP_DEBUG=.*|APP_DEBUG=true|" .env
                log_success "Mode diubah menjadi: Development (APP_DEBUG=true)"
            fi
            php artisan config:cache
            ;;
        3)
            echo -e "Pilih tipe database: 1) SQLite (Disarankan) | 2) MySQL / MariaDB"
            read -rp "Pilihan [1/2]: " db_choice
            if [[ "$db_choice" == "1" ]]; then
                sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|" .env
                touch database/database.sqlite
                log_success "Database diatur menggunakan SQLite (database/database.sqlite)."
            else
                sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" .env
                read -rp "DB Host [127.0.0.1]: " db_host; db_host=${db_host:-127.0.0.1}
                read -rp "DB Port [3306]: " db_port; db_port=${db_port:-3306}
                read -rp "DB Database [rzstore]: " db_name; db_name=${db_name:-rzstore}
                read -rp "DB Username [root]: " db_user; db_user=${db_user:-root}
                read -rp "DB Password: " db_pass

                sed -i "s|^DB_HOST=.*|DB_HOST=${db_host}|" .env
                sed -i "s|^DB_PORT=.*|DB_PORT=${db_port}|" .env
                sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${db_name}|" .env
                sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${db_user}|" .env
                sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${db_pass}|" .env
                log_success "Konfigurasi MySQL berhasil disimpan ke .env."
            fi
            php artisan config:cache
            ;;
        4)
            php artisan key:generate --force
            php artisan config:cache
            log_success "APP_KEY baru berhasil dibuat."
            ;;
        5)
            nano .env
            php artisan config:cache
            log_success "File .env telah disimpan dan cache diperbarui."
            ;;
        *)
            log_info "Kembali ke menu..."
            ;;
    esac
}

# ------------------------------------------------------------------------------
# 4. Perbaiki Hak Akses Folder & File (Storage, Cache, Database)
# ------------------------------------------------------------------------------
fix_permissions_internal() {
    log_info "Mengatur izin dan hak akses folder Laravel (www-data)..."
    if id "www-data" &>/dev/null; then
        chown -R www-data:www-data "${PROJECT_DIR}"
    fi
    chmod -R 775 "${PROJECT_DIR}/storage" "${PROJECT_DIR}/bootstrap/cache"
    if [ -f "${PROJECT_DIR}/database/database.sqlite" ]; then
        chmod 664 "${PROJECT_DIR}/database/database.sqlite"
        chmod 775 "${PROJECT_DIR}/database"
    fi
    log_success "Hak akses file & folder berhasil diperbaiki."
}

do_fix_permissions() {
    check_root
    log_step "Memperbaiki Hak Akses Direktori"
    fix_permissions_internal
}

# ------------------------------------------------------------------------------
# 5. Bersihkan dan Optimalkan Cache
# ------------------------------------------------------------------------------
do_optimize_cache() {
    log_step "Membersihkan & Mengoptimalkan Cache Laravel"
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    log_success "Semua cache berhasil dioptimalkan."
}

# ------------------------------------------------------------------------------
# 6. Pasang SSL Gratis Let's Encrypt (Certbot)
# ------------------------------------------------------------------------------
do_setup_ssl() {
    check_root
    log_step "Pemasangan SSL Gratis Let's Encrypt (HTTPS)"

    read -rp "Masukkan nama domain (misal: rzstore.com atau sub.domain.com): " ssl_domain
    if [[ -z "$ssl_domain" ]]; then
        log_error "Nama domain tidak boleh kosong!"
        return
    fi

    log_info "Memeriksa dan memasang Certbot Nginx..."
    apt-get update -y
    apt-get install -y certbot python3-certbot-nginx

    log_info "Membuat sertifikat SSL untuk domain: ${ssl_domain}..."
    certbot --nginx -d "${ssl_domain}" --non-interactive --agree-tos --register-unsafely-without-email || certbot --nginx -d "${ssl_domain}"

    # Update APP_URL di .env menjadi https
    if [ -f ".env" ]; then
        sed -i "s|^APP_URL=.*|APP_URL=https://${ssl_domain}|" .env
        php artisan config:cache
    fi

    log_success "SSL HTTPS berhasil dipasang untuk domain https://${ssl_domain}!"
}

# ------------------------------------------------------------------------------
# 7. Buat Akun Admin Baru / Reset Password
# ------------------------------------------------------------------------------
do_manage_admin() {
    log_step "Manajemen Akun Admin"

    echo -e "1) Buat Akun Admin Baru"
    echo -e "2) Reset Password User / Admin"
    echo -e "0) Batal"
    read -rp "Pilihan [0-2]: " admin_choice

    case "$admin_choice" in
        1)
            read -rp "Nama Admin: " admin_name
            read -rp "Email Admin: " admin_email
            read -rsp "Password Admin: " admin_pass
            echo ""
            if [[ -n "$admin_name" && -n "$admin_email" && -n "$admin_pass" ]]; then
                php artisan tinker --execute "
                \$u = \App\Models\User::firstOrNew(['email' => '${admin_email}']);
                \$u->name = '${admin_name}';
                \$u->password = \Illuminate\Support\Facades\Hash::make('${admin_pass}');
                \$u->role = 'admin';
                \$u->save();
                echo 'User Admin berhasil dibuat/diperbarui! ID: ' . \$u->id . PHP_EOL;
                "
                log_success "Akun Admin '${admin_email}' berhasil dibuat dengan role 'admin'."
            else
                log_error "Semua field harus diisi!"
            fi
            ;;
        2)
            read -rp "Masukkan Email Akun yang akan direset: " reset_email
            read -rsp "Masukkan Password Baru: " new_pass
            echo ""
            if [[ -n "$reset_email" && -n "$new_pass" ]]; then
                php artisan tinker --execute "
                \$u = \App\Models\User::where('email', '${reset_email}')->first();
                if (\$u) {
                    \$u->password = \Illuminate\Support\Facades\Hash::make('${new_pass}');
                    \$u->save();
                    echo 'Password berhasil diubah untuk ' . \$u->name . PHP_EOL;
                } else {
                    echo 'User tidak ditemukan!' . PHP_EOL;
                }
                "
                log_success "Password untuk '${reset_email}' telah diperbarui."
            fi
            ;;
        *)
            log_info "Batal."
            ;;
    esac
}

# ------------------------------------------------------------------------------
# 8. Status Sistem & Layanan
# ------------------------------------------------------------------------------
do_system_status() {
    log_step "Status Sistem & Layanan Website"
    echo -e "${BOLD}--- Status Layanan ---${NC}"
    systemctl status php8.4-fpm --no-pager 2>/dev/null | head -n 3 || echo "PHP 8.4-FPM: Tidak terdeteksi"
    systemctl status nginx --no-pager 2>/dev/null | head -n 3 || echo "Nginx: Tidak terdeteksi"

    echo -e "\n${BOLD}--- Penggunaan Disk & RAM ---${NC}"
    df -h / | awk 'NR==1 || NR==2'
    free -h

    echo -e "\n${BOLD}--- Konfigurasi Laravel (.env) ---${NC}"
    echo "APP_NAME  : $(grep '^APP_NAME=' .env 2>/dev/null || echo '-')"
    echo "APP_ENV   : $(grep '^APP_ENV=' .env 2>/dev/null || echo '-')"
    echo "APP_URL   : $(grep '^APP_URL=' .env 2>/dev/null || echo '-')"
    echo "DB_TYPE   : $(grep '^DB_CONNECTION=' .env 2>/dev/null || echo '-')"
}

# ------------------------------------------------------------------------------
# Menu Utama Interaktif
# ------------------------------------------------------------------------------
show_menu() {
    clear
    echo -e "${CYAN}${BOLD}"
    cat << "EOF"
  ____ _____   ____ _____ ___  ____  _____ 
 |  _ \__  /  / ___|_   _/ _ \|  _ \| ____|
 | |_) |/ /   \___ \ | || | | | |_) |  _|  
 |  _ < / /_   ___) || || |_| |  _ <| |___ 
 |_| \_\____| |____/ |_| \___/|_| \_\_____|
                                           
 Tool Manajemen & Update Website RZ Store
 Lokasi: $(pwd)
EOF
    echo -e "${NC}"
    echo -e "${BOLD}PILIH MENU OPERASI:${NC}"
    echo -e " ${GREEN}1)${NC} 🚀 ${BOLD}Terapkan Perubahan ke Website${NC} (Composer, Vite Build, Migrate, Cache, Reload)"
    echo -e " ${GREEN}2)${NC} 🔄 ${BOLD}Update dari GitHub & Terapkan${NC} (Git Pull + Terapkan Perubahan)"
    echo -e " ${GREEN}3)${NC} ⚙️  ${BOLD}Konfigurasi .env Website${NC} (Domain, DB, Mode Debug, Key)"
    echo -e " ${GREEN}4)${NC} 🧹 ${BOLD}Bersihkan & Optimalkan Cache${NC} (optimize:clear & cache)"
    echo -e " ${GREEN}5)${NC} 🛡️  ${BOLD}Perbaiki Hak Akses Folder / Permissions${NC} (storage & cache)"
    echo -e " ${GREEN}6)${NC} 🔒 ${BOLD}Pasang SSL Let's Encrypt (HTTPS)${NC} (Certbot)"
    echo -e " ${GREEN}7)${NC} 👥 ${BOLD}Manajemen Akun Admin${NC} (Buat Baru / Reset Password)"
    echo -e " ${GREEN}8)${NC} 📊 ${BOLD}Status Server & Layanan${NC} (Nginx, PHP-FPM, Disk, RAM)"
    echo -e " ${RED}0)${NC} ❌ Keluar"
    echo ""
    read -rp "Masukkan nomor pilihan [0-8]: " menu_choice

    case "$menu_choice" in
        1) do_apply_changes ;;
        2) do_update ;;
        3) do_configure_env ;;
        4) do_optimize_cache ;;
        5) do_fix_permissions ;;
        6) do_setup_ssl ;;
        7) do_manage_admin ;;
        8) do_system_status ;;
        0) echo -e "${CYAN}Sampai jumpa!${NC}"; exit 0 ;;
        *) log_error "Pilihan tidak valid."; sleep 1 ;;
    esac

    echo ""
    read -rp "Tekan [ENTER] untuk kembali ke menu utama..."
    show_menu
}

# ------------------------------------------------------------------------------
# CLI Arguments Support
# ------------------------------------------------------------------------------
if [[ $# -gt 0 ]]; then
    case "$1" in
        apply|deploy|rebuild)
            do_apply_changes
            ;;
        update|pull)
            do_update
            ;;
        config|env)
            do_configure_env
            ;;
        cache|optimize)
            do_optimize_cache
            ;;
        permissions|perms|fix)
            do_fix_permissions
            ;;
        ssl|https)
            do_setup_ssl
            ;;
        admin)
            do_manage_admin
            ;;
        status)
            do_system_status
            ;;
        help|--help|-h)
            echo "Penggunaan: ./manage.sh [command]"
            echo "Commands:"
            echo "  apply        : Terapkan semua perubahan (Composer, Vite Build, Migrate, Cache, Reload Nginx/FPM)"
            echo "  update       : Tarik kode dari GitHub (git pull) lalu terapkan perubahan"
            echo "  config       : Konfigurasi variabel .env (Domain, DB, Debug)"
            echo "  cache        : Bersihkan dan generate ulang cache Laravel"
            echo "  permissions  : Perbaiki kepemilikan folder storage & cache"
            echo "  ssl          : Pasang sertifikat SSL Let's Encrypt"
            echo "  admin        : Buat admin atau reset password"
            echo "  status       : Tampilkan status server & resource"
            echo "  (tanpa arg)  : Membuka menu interaktif"
            ;;
        *)
            log_error "Perintah '$1' tidak dikenal. Jalankan './manage.sh --help' untuk melihat daftar perintah."
            exit 1
            ;;
    esac
else
    show_menu
fi
