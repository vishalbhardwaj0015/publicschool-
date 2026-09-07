#!/usr/bin/env bash
set -e

# Render web services expect your app to listen on $PORT (default 10000).
PORT="${PORT:-80}"

sed -i "s/^[ \t]*Listen[ \t]*[0-9]*$/Listen ${PORT}/" /etc/apache2/ports.conf

exec apache2-foreground "$@"