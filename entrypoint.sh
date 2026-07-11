#!/bin/bash

# Créer le fichier SQLite s'il n'existe pas
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chmod 666 /var/www/html/database/database.sqlite

# Lancer les migrations
php artisan migrate --force

# Lancer le seeder principal (qui est maintenant robuste)
php artisan db:seed --force

# Lancer Apache en premier plan
exec apache2-foreground
