web: vendor/bin/heroku-php-apache2 public/
release: heroku config:set DB_URL=$DATABASE_URL
reverb: php artisan reverb:start --host=0.0.0.0 --port=8080
