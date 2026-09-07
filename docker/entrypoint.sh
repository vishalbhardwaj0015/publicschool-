#!/usr/bin/env bash
set -e

# Render web services expect your app to listen on $PORT (default 10000).
PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on ${PORT}"

# Write a clean listen config so ports.conf never has leftover/duplicate Listen lines.
printf 'Listen %s\n' "${PORT}" > /etc/apache2/listen_render.conf

# Ensure apache2.conf includes our generated listen config.
if ! grep -q "listen_render.conf" /etc/apache2/apache2.conf; then
  echo "Include listen_render.conf" >> /etc/apache2/apache2.conf
fi

cat /etc/apache2/listen_render.conf

# Export the env vars the official apache2-foreground script expects.
export APACHE_CONFDIR="${APACHE_CONFDIR:-/etc/apache2}"
export APACHE_ENVVARS="${APACHE_ENVVARS:-/etc/apache2/envvars}"
export APACHE_RUN_USER="${APACHE_RUN_USER:-www-data}"
export APACHE_RUN_GROUP="${APACHE_RUN_GROUP:-www-data}"
export APACHE_RUN_DIR="${APACHE_RUN_DIR:-/var/run/apache2}"
export APACHE_PID_FILE="${APACHE_PID_FILE:-${APACHE_RUN_DIR}/apache2.pid}"
export APACHE_LOG_DIR="${APACHE_LOG_DIR:-/var/log/apache2}"

mkdir -p "${APACHE_RUN_DIR}" "${APACHE_LOG_DIR}"
chown www-data:www-data "${APACHE_RUN_DIR}" "${APACHE_LOG_DIR}" || true

echo "==> Starting Apache"
exec apache2-foreground