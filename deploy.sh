#!/usr/bin/env bash

# ==============================================================================
# Script Otomatisasi Deploy Multi-Website Laravel (Ubuntu 24.04 LTS)
# Projek : RZ Store (PHP 8.4 + Nginx)
# ==============================================================================

set -Eeuo pipefail

# Izinkan Composer berjalan di mode Root
export COMPOSER_ALLOW_SUPERUSER=1
export DEBIAN_FRONTEND=noninteractive

# Warna Output Terminal
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

# Error Handler Trap
catch_error() {
    local exit_code=$?
    local line_number=$1
    echo -e "\n${RED}==============================================================================${NC}"
    echo -e "${RED}[ERROR] Terjadi kesalahan pada baris ${line_number} (Exit Code: ${exit_code})!${NC}"
    echo -e "${RED}Deployment dihentikan untuk mencegah kerusakan konfigurasi.${NC}"
    echo -e "${RED}==============================================================================${NC}\n"
    exit "${exit_code}"
}
trap 'catch_error $LINENO' ERR

log_info() { echo -e "${CYAN}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
log_step() { echo -e "\n${BLUE}==>${NC} ${YELLOW}$1${NC}"; }

# ------------------------------------------------------------------------------
# 0. Verifikasi Hak Akses Root
# ------------------------------------------------------------------------------
if [[ $EUID -ne 0 ]]; then
   echo -e "${RED}[ERROR] Script ini harus dijalankan sebagai ROOT atau menggunakan 'sudo'.${NC}"
   exit 1
fi

clear
echo -e "${CYAN}"
cat << "EOF"
  ____ _____   ____ _____ ___  ____  _____ 
 |  _ \__  /  / ___|_   _/ _ \|  _ \| ____|
 | |_) |/ /   \___ \ | || | | | |_) |  _|  
 |  _ < / /_   ___) || || |_| |  _ <| |___ 
 |_| \_\____| |____/ |_| \___/|_| \_\_____|
                                           
 Auto-Deployer Script for Ubuntu 24.04 LTS
 Multi-Website Nginx Architecture (PHP 8.4)
EOF
echo -e "${NC}"

# ------------------------------------------------------------------------------
# 1. Konfigurasi Interaktif (Input Parameter)
# ------------------------------------------------------------------------------
log_step "Langkah 1: Konfigurasi Deployment"

DEFAULT_REPO="https://github.com/Khoiru-rozikin19/website_toko.git"
read -rp "Masukkan URL Git Repository [Default: ${DEFAULT_REPO}]: " GIT_REPO
GIT_REPO=${GIT_REPO:-$DEFAULT_REPO}

DEFAULT_FOLDER="rzstore"
read -rp "Masukkan nama folder di /var/www/ [Default: ${DEFAULT_FOLDER}]: " APP_DIR_NAME
APP_DIR_NAME=${APP_DIR_NAME:-$DEFAULT_FOLDER}
TARGET_PATH="/var/www/${APP_DIR_NAME}"

read -rp "Masukkan Domain / IP VPS (misal: rzstore.com atau IP VPS): " DOMAIN_NAME
if [[ -z "${DOMAIN_NAME}" ]]; then
    log_warn "Domain kosong, mendeteksi IP Publik VPS..."
    DOMAIN_NAME=$(curl -s -4 ifconfig.me || curl -s -4 icanhazip.com || echo "_")
    log_info "Menggunakan domain/IP: ${DOMAIN_NAME}"
fi

# ------------------------------------------------------------------------------
# 2. Pasang Repositori PPA & Install PHP 8.4
# ------------------------------------------------------------------------------
log_step "Langkah 2: Memasang PPA PHP 8.4 & Paket Sistem Ubuntu 24.04 LTS"

log_info "Memperbarui repositori OS dan dependensi dasar..."
apt-get update -y
apt-get install -y software-properties-common curl git unzip zip ufw lsb-release ca-certificates apt-transport-https

log_info "Menambahkan repositori resmi PHP 8.4 (ppa:ondrej/php)..."
add-apt-repository -y ppa:ondrej/php
apt-get update -y

log_info "Memasang Nginx Web Server..."
apt-get install -y nginx

log_info "Memasang PHP 8.4 & Seluruh Ekstensi Laravel..."
apt-get install -y php8.4 php8.4-fpm php8.4-cli php8.4-mbstring php8.4-xml php8.4-bcmath php8.4-curl php8.4-sqlite3 php8.4-mysql php8.4-zip php8.4-intl php8.4-gd php8.4-tokenizer php8.4-dom

# Set PHP 8.4 sebagai default CLI
update-alternatives --set php /usr/bin/php8.4 || true
systemctl restart php8.4-fpm || true
systemctl enable php8.4-fpm || true

PHP_FPM_SOCK="/var/run/php/php8.4-fpm.sock"
log_info "PHP Version: $(php -v | head -n 1)"
log_info "PHP-FPM Socket: ${PHP_FPM_SOCK}"

# Install Composer
if ! command -v composer &> /dev/null; then
    log_info "Memasang Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    chmod +x /usr/local/bin/composer
else
    log_info "Composer sudah terpasang ($(composer --version | head -n 1))."
fi

# Install Node.js 20.x & NPM
if ! command -v node &> /dev/null; then
    log_info "Memasang Node.js LTS (v20)..."
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y nodejs
else
    log_info "Node.js sudah terpasang ($(node -v))."
fi

log_success "Seluruh dependensi sistem & PHP 8.4 berhasil dipasang."

# ------------------------------------------------------------------------------
# 3. Clone / Update Repository Projek
# ------------------------------------------------------------------------------
log_step "Langkah 3: Menyiapkan Source Code Projek di ${TARGET_PATH}"

mkdir -p /var/www

if [[ -d "${TARGET_PATH}/.git" ]]; then
    log_warn "Direktori ${TARGET_PATH} sudah ada, melakukan git pull..."
    cd "${TARGET_PATH}"
    git config --global --add safe.directory "${TARGET_PATH}" || true
    git fetch --all
    git reset --hard origin/main || git reset --hard origin/master || true
    git pull origin main || git pull origin master || true
else
    log_info "Meng-clone repository ${GIT_REPO} ke ${TARGET_PATH}..."
    git clone "${GIT_REPO}" "${TARGET_PATH}"
    cd "${TARGET_PATH}"
    git config --global --add safe.directory "${TARGET_PATH}" || true
fi

# ------------------------------------------------------------------------------
# 4. Setup Laravel Environment, Dependencies & Database
# ------------------------------------------------------------------------------
log_step "Langkah 4: Konfigurasi Laravel & Build Aset"

# Copy file .env jika belum ada
if [[ ! -f ".env" ]]; then
    log_info "Membuat file .env dari .env.example..."
    if [[ -f ".env.example" ]]; then
        cp .env.example .env
    else
        cat > .env << 'ENVEOF'
APP_NAME="RZ Store"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120
ENVEOF
    fi
fi

# Update konfigurasi .env untuk production
sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env || true
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env || true
sed -i "s|^APP_URL=.*|APP_URL=http://${DOMAIN_NAME}|" .env || true
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|" .env || true

# Pastikan folder dan file SQLite siap sebelum composer post-install
mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch database/database.sqlite
chmod -R 777 storage bootstrap/cache database

log_info "Menjalankan Composer Install dengan PHP 8.4..."
php8.4 -d memory_limit=-1 /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

log_info "Generate APP_KEY..."
php8.4 artisan key:generate --force

log_info "Menjalankan migrasi database & seeder..."
php8.4 artisan migrate --force --seed

log_info "Mengompilasi aset Vite/Tailwind (NPM Build)..."
npm install --no-audit --no-fund
npm run build

log_info "Optimasi cache route, config, dan views..."
php8.4 artisan config:cache || true
php8.4 artisan route:cache || true
php8.4 artisan view:cache || true

# Pastikan direktori videos dan gambar ada
mkdir -p public/videos public/images

# Permission Ownership Akhir untuk www-data
log_info "Mengatur hak akses (ownership) www-data..."
chown -R www-data:www-data "${TARGET_PATH}"
chmod -R 775 "${TARGET_PATH}/storage" "${TARGET_PATH}/bootstrap/cache" "${TARGET_PATH}/database"
chmod 664 "${TARGET_PATH}/database/database.sqlite" || true

log_success "Aplikasi Laravel siap dijalankan."

# ------------------------------------------------------------------------------
# 5. Konfigurasi Nginx Server Block (Virtual Host)
# ------------------------------------------------------------------------------
log_step "Langkah 5: Konfigurasi Nginx Server Block (/etc/nginx/sites-available/${APP_DIR_NAME})"

NGINX_CONF="/etc/nginx/sites-available/${APP_DIR_NAME}"

cat > "${NGINX_CONF}" << EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN_NAME};
    root ${TARGET_PATH}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    # Ukuran upload media / video background
    client_max_body_size 100M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Aktifkan konfigurasi Nginx
ln -sf "${NGINX_CONF}" "/etc/nginx/sites-enabled/${APP_DIR_NAME}"

# Hapus default config jika nama domain diset khusus
if [[ -f "/etc/nginx/sites-enabled/default" && "${DOMAIN_NAME}" != "_" ]]; then
    rm -f /etc/nginx/sites-enabled/default
fi

log_info "Menguji sintaks konfigurasi Nginx..."
nginx -t

log_info "Mereload Nginx..."
systemctl reload nginx

# ------------------------------------------------------------------------------
# 6. Selesai
# ------------------------------------------------------------------------------
echo -e "\n${GREEN}==============================================================================${NC}"
echo -e "${GREEN}🎉 DEPLOYMENT BERHASIL SELESAI!${NC}"
echo -e "${GREEN}==============================================================================${NC}"
echo -e "Website kamu sekarang sudah aktif di: ${CYAN}http://${DOMAIN_NAME}${NC}"
echo -e "Folder Projek : ${TARGET_PATH}"
echo -e "Konfigurasi Nginx : ${NGINX_CONF}"
echo -e ""
echo -e "${YELLOW}Langkah Tambahan (Opsional):${NC}"
echo -e "Untuk memasang SSL Gratis (HTTPS), cukup jalankan perintah:"
echo -e "  ${CYAN}sudo apt install -y certbot python3-certbot-nginx && sudo certbot --nginx -d ${DOMAIN_NAME}${NC}"
echo -e "${GREEN}==============================================================================${NC}\n"
