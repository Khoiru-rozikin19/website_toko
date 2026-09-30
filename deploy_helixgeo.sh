#!/usr/bin/env bash

# ==============================================================================
# Script Otomatisasi Deploy Multi-Website: HELIXGEO (PHP Native + MySQL)
# Target OS: Ubuntu 24.04 LTS (Nginx + PHP 8.4 + MariaDB)
# ==============================================================================

set -Eeuo pipefail

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

log_info() { echo -e "${CYAN}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }
log_step() { echo -e "\n${BLUE}==>${NC} ${BOLD}$1${NC}"; }

# Error Handler
catch_error() {
    local exit_code=$?
    local line_number=$1
    echo -e "\n${RED}==============================================================================${NC}"
    echo -e "${RED}[ERROR] Terjadi kesalahan pada baris ${line_number} (Exit Code: ${exit_code})!${NC}"
    echo -e "${RED}==============================================================================${NC}\n"
    exit "${exit_code}"
}
trap 'catch_error $LINENO' ERR

# Verifikasi Root
if [[ $EUID -ne 0 ]]; then
   log_error "Script ini harus dijalankan sebagai ROOT atau menggunakan 'sudo ./deploy_helixgeo.sh'."
   exit 1
fi

clear
echo -e "${CYAN}${BOLD}"
cat << "EOF"
  _   _ _____ _     _____  ______ _____ _____ 
 | | | | ____| |   |_ _\ \/ / ___| ____/ _ \ 
 | |_| |  _| | |    | | \  / |  _|  _|| | | |
 |  _  | |___| |___ | | /  \ |_| | |__| |_| |
 |_| |_|_____|_____|___/_/\_\____|_____\___/ 
                                              
 Auto-Deployer Script for HELIXGEO (PHP + MySQL)
 Multi-Website Nginx Architecture (Ubuntu 24.04 LTS)
EOF
echo -e "${NC}"

# ------------------------------------------------------------------------------
# 1. Konfigurasi Deployment
# ------------------------------------------------------------------------------
log_step "Langkah 1: Konfigurasi Target & Domain HELIXGEO"

TARGET_DIR="/var/www/helixgeo"
CURRENT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo -e "Pilih metode akses website HELIXGEO di VPS:"
echo -e "1) Menggunakan Domain / Subdomain tersendiri (misal: helix.domain.com atau domain2.com)"
echo -e "2) Menggunakan Port Terpisah (misal: http://IP_VPS:8080) - Cocok jika belum ada domain kedua"
read -rp "Pilihan Anda [1/2]: " access_mode

DOMAIN_NAME=""
SERVER_PORT=80

if [[ "$access_mode" == "1" ]]; then
    read -rp "Masukkan nama Domain/Subdomain untuk HELIXGEO: " DOMAIN_NAME
    while [[ -z "$DOMAIN_NAME" ]]; do
        read -rp "Domain tidak boleh kosong. Masukkan nama domain: " DOMAIN_NAME
    done
    SERVER_PORT=80
else
    read -rp "Masukkan Port HTTP [Default: 8080]: " custom_port
    SERVER_PORT=${custom_port:-8080}
    DOMAIN_NAME="_"
    log_info "HELIXGEO akan diakses via Port: ${SERVER_PORT}"
fi

# ------------------------------------------------------------------------------
# 2. Siapkan MariaDB / MySQL Server
# ------------------------------------------------------------------------------
log_step "Langkah 2: Menyiapkan MariaDB Database Server"

if ! command -v mariadb &>/dev/null && ! command -v mysql &>/dev/null; then
    log_info "Memasang MariaDB Server..."
    apt-get update -y
    apt-get install -y mariadb-server mariadb-client
    systemctl start mariadb
    systemctl enable mariadb
    log_success "MariaDB Server berhasil dipasang."
else
    log_info "MariaDB/MySQL sudah terpasang di sistem."
fi

# Pastikan MySQL berjalan
systemctl start mariadb 2>/dev/null || systemctl start mysql 2>/dev/null || true

DB_NAME="helixgeo_db"
DB_USER="helixgeo_user"
DB_PASS="HelixGeoPass_$(openssl rand -hex 4)"

log_info "Membuat database '${DB_NAME}' dan user MySQL..."
mariadb -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || \
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

mariadb -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';" 2>/dev/null || \
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"

mariadb -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null || \
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;"

log_success "Database '${DB_NAME}' dan user '${DB_USER}' siap."

# ------------------------------------------------------------------------------
# 3. Salin / Pindahkan File HELIXGEO ke /var/www/helixgeo
# ------------------------------------------------------------------------------
log_step "Langkah 3: Menyiapkan Direktori Website /var/www/helixgeo"

mkdir -p "${TARGET_DIR}"

if [[ "${CURRENT_DIR}" != "${TARGET_DIR}" ]]; then
    log_info "Menyalin file dari ${CURRENT_DIR} ke ${TARGET_DIR}..."
    cp -ru "${CURRENT_DIR}/." "${TARGET_DIR}/" 2>/dev/null || true
fi

# ------------------------------------------------------------------------------
# 4. Import SQL Schema (HELIXGEO.sql)
# ------------------------------------------------------------------------------
log_step "Langkah 4: Mengimpor Database HELIXGEO.sql"

if [ -f "${TARGET_DIR}/HELIXGEO.sql" ]; then
    log_info "Mengimpor tabel dari HELIXGEO.sql ke database '${DB_NAME}'..."
    mariadb "${DB_NAME}" < "${TARGET_DIR}/HELIXGEO.sql" 2>/dev/null || \
    mysql "${DB_NAME}" < "${TARGET_DIR}/HELIXGEO.sql"
    log_success "Database schema berhasil diimpor!"
else
    log_warn "File HELIXGEO.sql tidak ditemukan di ${TARGET_DIR}, lewati impor SQL."
fi

# ------------------------------------------------------------------------------
# 5. Perbarui config.php dengan Database VPS
# ------------------------------------------------------------------------------
log_step "Langkah 5: Memperbarui Koneksi Database (config.php)"

cat > "${TARGET_DIR}/config.php" << EOF
<?php

\$conn = new mysqli(
    "localhost",
    "${DB_USER}",
    "${DB_PASS}",
    "${DB_NAME}"
);

if (\$conn->connect_error) {
    die("Database Error: " . \$conn->connect_error);
}
EOF

log_success "File config.php telah disesuaikan dengan database lokal VPS."

# ------------------------------------------------------------------------------
# 6. Konfigurasi Nginx Server Block untuk HELIXGEO
# ------------------------------------------------------------------------------
log_step "Langkah 6: Membuat Konfigurasi Virtual Host Nginx"

NGINX_CONF="/etc/nginx/sites-available/helixgeo"

cat > "${NGINX_CONF}" << EOF
server {
    listen ${SERVER_PORT};
    listen [::]:${SERVER_PORT};

    server_name ${DOMAIN_NAME};
    root ${TARGET_DIR};

    index index.php index.html index.htm;
    charset utf-8;

    client_max_body_size 64M;

    # Logging
    access_log /var/log/nginx/helixgeo_access.log;
    error_log /var/log/nginx/helixgeo_error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # PHP-FPM Handler (PHP 8.4)
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files (.git, .env, .sql, etc.)
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ \.sql$ {
        deny all;
    }
}
EOF

# Aktifkan konfigurasi Nginx
ln -sf "${NGINX_CONF}" /etc/nginx/sites-enabled/helixgeo

# Buka firewall port jika port kustom digunakan
if [[ "${SERVER_PORT}" != "80" && "${SERVER_PORT}" != "443" ]]; then
    if command -v ufw &>/dev/null; then
        log_info "Membuka port ${SERVER_PORT} di firewall UFW..."
        ufw allow "${SERVER_PORT}/tcp" || true
    fi
fi

# ------------------------------------------------------------------------------
# 7. Atur Hak Akses & Permission Folder
# ------------------------------------------------------------------------------
log_step "Langkah 7: Mengatur Hak Akses Direktori (www-data)"

mkdir -p "${TARGET_DIR}/uploads" "${TARGET_DIR}/admin"
chown -R www-data:www-data "${TARGET_DIR}"
chmod -R 755 "${TARGET_DIR}"
chmod -R 775 "${TARGET_DIR}/uploads"

# ------------------------------------------------------------------------------
# 8. Uji & Restart Nginx
# ------------------------------------------------------------------------------
log_step "Langkah 8: Memuat Ulang Nginx"

nginx -t
systemctl reload nginx
systemctl reload php8.4-fpm || systemctl restart php8.4-fpm || true

# ------------------------------------------------------------------------------
# 9. Opsional: SSL HTTPS (Let's Encrypt) jika menggunakan domain
# ------------------------------------------------------------------------------
if [[ "$access_mode" == "1" && "$DOMAIN_NAME" != "_" ]]; then
    echo -e "\nApakah Anda ingin memasang SSL Gratis (HTTPS / Let's Encrypt) untuk domain ${DOMAIN_NAME} sekarang?"
    read -rp "Pasang SSL? [y/N]: " setup_ssl
    if [[ "$setup_ssl" =~ ^[Yy]$ ]]; then
        log_info "Memasang SSL Let's Encrypt..."
        apt-get install -y certbot python3-certbot-nginx
        certbot --nginx -d "${DOMAIN_NAME}" --non-interactive --agree-tos --register-unsafely-without-email || certbot --nginx -d "${DOMAIN_NAME}"
        log_success "SSL HTTPS aktif untuk https://${DOMAIN_NAME}!"
    fi
fi

# ------------------------------------------------------------------------------
# Selesai
# ------------------------------------------------------------------------------
VPS_IP=$(curl -s -4 ifconfig.me || curl -s -4 icanhazip.com || echo "IP_VPS")

echo -e "\n${GREEN}==============================================================================${NC}"
echo -e "${GREEN}🎉 PROJEK HELIXGEO BERHASIL DI-DEPLOY DI VPS!${NC}"
echo -e "${GREEN}==============================================================================${NC}"
if [[ "$access_mode" == "1" && "$DOMAIN_NAME" != "_" ]]; then
    echo -e "🌐 URL Website    : ${CYAN}http://${DOMAIN_NAME}${NC} (atau https://${DOMAIN_NAME})"
else
    echo -e "🌐 URL Website    : ${CYAN}http://${VPS_IP}:${SERVER_PORT}${NC}"
fi
echo -e "📁 Folder Project  : ${YELLOW}${TARGET_DIR}${NC}"
echo -e "🗄️  Database MySQL  : ${YELLOW}${DB_NAME}${NC} (User: ${DB_USER})"
echo -e "🔑 Password DB    : ${YELLOW}${DB_PASS}${NC}"
echo -e "⚙️  Nginx Config   : ${YELLOW}${NGINX_CONF}${NC}"
echo -e "${GREEN}==============================================================================${NC}\n"
