## About Laravel

### downloade project by ssh
git clone git@github.com:MohamedA1998/example_app.git
### after ----
cd example_app
### after ----
composer install
### after ----
cp .env.example .env
### after ----
php artisan key:generate
### after ----
php artisan migrate
### after ----
php artisan migrate:refresh --seed