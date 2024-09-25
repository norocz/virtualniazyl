#!/bin/bash

echo "Aktualizace Virtualni Azyl ...."
# Cesta k tvému projektu na serveru
PROJECT_DIR="/var/www/virtualniazyl"

# Přepnutí do pracovního adresáře projektu
cd $PROJECT_DIR || { echo "Chyba: Nelze přejít do adresáře projektu."; exit 1; }

# Vykonání git pull, aby se stáhly poslední změny
echo "Provádím git pull..."
git pull origin GitHub-V1 || { echo "Chyba: Git pull selhal."; exit 1; }

# Změna vlastníka složek temp a log na uživatele www-data
echo "Nastavuji vlastníka složek temp a log..."
chown -R www-data:www-data $PROJECT_DIR/vaz/temp || { echo "Chyba: Nastavení vlastníka složky temp selhalo."; exit 1; }
chown -R www-data:www-data $PROJECT_DIR/vaz/log || { echo "Chyba: Nastavení vlastníka složky log selhalo."; exit 1; }

# Nastavení správných oprávnění pro složky temp a log
echo "Nastavuji oprávnění složek temp a log..."
chmod -R 775 $PROJECT_DIR/vaz/temp || { echo "Chyba: Nastavení oprávnění složky temp selhalo."; exit 1; }
chmod -R 775 $PROJECT_DIR/vaz/log || { echo "Chyba: Nastavení oprávnění složky log selhalo."; exit 1; }

echo "Aktualizace aplikace dokončena."
