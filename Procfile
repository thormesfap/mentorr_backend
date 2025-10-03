web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:work --timeout=1800 --tries=3
