#!/bin/bash

# Créer le fichier SQLite s'il n'existe pas
touch /var/www/html/database/database.sqlite
chmod 666 /var/www/html/database/database.sqlite

# Lancer les migrations automatiquement au démarrage
php artisan migrate --force

# Lancer Apache en premier plan
exec apache2-foreground
