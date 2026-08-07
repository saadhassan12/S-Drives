#!/usr/bin/env bash
set -euo pipefail

# Fix 413 Request Entity Too Large for S-Drives uploads on Contabo (Nginx + PHP-FPM).
# Run on server as root:
#   bash /var/www/shedrives_backend/deploy/fix-413-upload.sh

APP_DIR="${APP_DIR:-/var/www/shedrives_backend}"
CLIENT_MAX_BODY_SIZE="${CLIENT_MAX_BODY_SIZE:-350M}"
UPLOAD_MAX_FILESIZE="${UPLOAD_MAX_FILESIZE:-55M}"
POST_MAX_SIZE="${POST_MAX_SIZE:-350M}"
MAX_IMAGE_UPLOAD_KB="${MAX_IMAGE_UPLOAD_KB:-51200}"

echo "==> Using app directory: ${APP_DIR}"

if [[ ! -d "${APP_DIR}/public" ]]; then
  echo "ERROR: ${APP_DIR}/public not found."
  exit 1
fi

echo "==> Writing ${APP_DIR}/public/.user.ini"
cat > "${APP_DIR}/public/.user.ini" <<EOF
upload_max_filesize = ${UPLOAD_MAX_FILESIZE}
post_max_size = ${POST_MAX_SIZE}
max_file_uploads = 20
EOF

echo "==> Ensuring MAX_IMAGE_UPLOAD_KB in ${APP_DIR}/.env"
if [[ -f "${APP_DIR}/.env" ]]; then
  if grep -q '^MAX_IMAGE_UPLOAD_KB=' "${APP_DIR}/.env"; then
    sed -i "s/^MAX_IMAGE_UPLOAD_KB=.*/MAX_IMAGE_UPLOAD_KB=${MAX_IMAGE_UPLOAD_KB}/" "${APP_DIR}/.env"
  else
    echo "MAX_IMAGE_UPLOAD_KB=${MAX_IMAGE_UPLOAD_KB}" >> "${APP_DIR}/.env"
  fi
  cd "${APP_DIR}"
  php artisan config:clear >/dev/null || true
  php artisan cache:clear >/dev/null || true
else
  echo "WARN: ${APP_DIR}/.env not found, skipping Laravel env update."
fi

echo "==> Updating PHP-FPM php.ini"
PHP_VERSION="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
PHP_INI="/etc/php/${PHP_VERSION}/fpm/php.ini"

if [[ ! -f "${PHP_INI}" ]]; then
  echo "ERROR: PHP ini not found at ${PHP_INI}"
  exit 1
fi

sed -i "s/^[;[:space:]]*upload_max_filesize = .*/upload_max_filesize = ${UPLOAD_MAX_FILESIZE}/" "${PHP_INI}"
sed -i "s/^[;[:space:]]*post_max_size = .*/post_max_size = ${POST_MAX_SIZE}/" "${PHP_INI}"

if ! grep -q '^upload_max_filesize' "${PHP_INI}"; then
  echo "upload_max_filesize = ${UPLOAD_MAX_FILESIZE}" >> "${PHP_INI}"
fi
if ! grep -q '^post_max_size' "${PHP_INI}"; then
  echo "post_max_size = ${POST_MAX_SIZE}" >> "${PHP_INI}"
fi

echo "==> Updating Nginx client_max_body_size"
NGINX_CONF=""
for candidate in /etc/nginx/sites-enabled/*; do
  if [[ -f "${candidate}" ]] && grep -q "${APP_DIR}/public" "${candidate}"; then
    NGINX_CONF="${candidate}"
    break
  fi
done

if [[ -z "${NGINX_CONF}" ]]; then
  for candidate in /etc/nginx/sites-enabled/*; do
    if [[ -f "${candidate}" ]] && grep -q "shedrives" "${candidate}"; then
      NGINX_CONF="${candidate}"
      break
    fi
  done
fi

if [[ -z "${NGINX_CONF}" ]]; then
  echo "ERROR: Could not find nginx site config for ${APP_DIR}."
  echo "Run: grep -r '${APP_DIR}/public' /etc/nginx/sites-enabled/"
  exit 1
fi

echo "    Found: ${NGINX_CONF}"

if grep -q 'client_max_body_size' "${NGINX_CONF}"; then
  sed -i "s/client_max_body_size[^;]*;/client_max_body_size ${CLIENT_MAX_BODY_SIZE};/" "${NGINX_CONF}"
else
  sed -i "/server {/a\\    client_max_body_size ${CLIENT_MAX_BODY_SIZE};" "${NGINX_CONF}"
fi

echo "==> Testing and reloading services"
nginx -t
systemctl reload nginx
systemctl restart "php${PHP_VERSION}-fpm"

echo
echo "Done. Current limits:"
grep 'client_max_body_size' "${NGINX_CONF}" || true
php -i | grep -E 'upload_max_filesize|post_max_size' | head -2 || true
echo
echo "Retry signup/upload now."
