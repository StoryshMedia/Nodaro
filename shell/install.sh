#!/bin/sh
PRIVATE_KEY_PATH="../config/jwt/private.pem"
PUBLIC_KEY_PATH="../config/jwt/public.pem"
if [ ! -f "$PRIVATE_KEY_PATH" ] || [ ! -f "$PUBLIC_KEY_PATH" ]; then
    echo "Mindestens eine der JWT-Schlüsseldateien fehlt. Generiere neue..."
    php bin/console lexik:jwt:generate-keypair
  # Hier kannst du z. B. openssl verwenden, um neue Schlüssel zu erzeugen
else
  echo "Beide JWT-Schlüsseldateien existieren bereits."
fi

# generate encryptionkey for database field encryption
VAR_NAME="APP_ENCRYPTION_KEY"
VAR_VALUE=$(openssl rand -hex 32)
current_value=$(grep "^$VAR_NAME=" .env | cut -d'=' -f2-)

if [ -z "$current_value" ]; then
  # Wenn der Key fehlt oder leer ist, setze oder ersetze ihn
  if grep -q "^$VAR_NAME=" .env; then
    sed -i "s/^$VAR_NAME=.*/$VAR_NAME=$VAR_VALUE/" .env
  else
    echo "$VAR_NAME=$VAR_VALUE" >> .env
  fi
else
  echo "$VAR_NAME hat bereits einen Wert. Keine Änderung vorgenommen."
fi

php -d memory_limit=-1 bin/console nodaro:install:database
