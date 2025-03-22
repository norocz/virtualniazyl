#!/bin/bash

ONION_DIR="/var/lib/tor/hidden_service"
BACKUP_DIR="/root/tor_backup"

# Ověření, že záloha existuje
if [ -f "$BACKUP_DIR/hs_ed25519_secret_key" ]; then
    echo "Obnovuji onion privátní klíč..."

    # Kopírování souborů do Tor adresáře
    cp "$BACKUP_DIR/hs_ed25519_secret_key" "$ONION_DIR/"
    cp "$BACKUP_DIR/hs_ed25519_public_key" "$ONION_DIR/"

    # Nastavení správných práv
    chown -R debian-tor:debian-tor "$ONION_DIR"
    chmod 700 "$ONION_DIR"
    chmod 600 "$ONION_DIR/hs_ed25519_secret_key"

    echo "Obnova dokončena."
else
    echo "Záložní soubory nebyly nalezeny!"
fi

# Restart Toru pro načtení klíče
systemctl restart tor
