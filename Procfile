web: vendor/bin/heroku-php-apache2 public/
reverb: php artisan reverb:start --host=mindlink-backend-747580ed3b68.herokuapp.com --port=8080
worker: php artisan queue:work --timeout=1800 --tries=3
