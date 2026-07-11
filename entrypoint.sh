#!/bin/bash

# Créer le fichier SQLite s'il n'existe pas
touch /var/www/html/database/database.sqlite
chmod 666 /var/www/html/database/database.sqlite

# Lancer les migrations et le seed automatiquement au démarrage
php artisan migrate --force
php artisan db:seed --class=SimpleSeeder --force
php artisan db:seed --force

# Lancer Apache en premier plan
exec apache2-foreground
