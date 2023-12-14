START=composer update && php artisan key:generate && php -S 0.0.0.0:80 -t public
MAIN=routes/api.php
MEMORY=1024
VERSION=recommended
DISPLAY_NAME=Hotel Faker
SUBDOMAIN=hotel-faker-api
