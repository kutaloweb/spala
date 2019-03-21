# Spala

<p align="center">
<a href="https://packagist.org/packages/spala/spala"><img src="https://poser.pugx.org/spala/spala/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/spala/spala"><img src="https://poser.pugx.org/spala/spala/license.svg" alt="License"></a>
</p>

SPA Content Management System based on [Laravel 5.8](https://laravel.com), [Vue 2.5](https://vuejs.org), [Bootstrap 4](https://getbootstrap.com/) and [Monster Admin Template](https://wrappixel.com/demos/admin-templates/monster-admin/Documentation/document.html)

![Screen](https://user-images.githubusercontent.com/17153568/53791966-80bcd980-3edf-11e9-9425-12fd7a789ad7.png "Dashboard")

## Getting Started

These instructions will get you a copy of the project up and running on your local Linux or Mac OS X machine

### Installing

Move to your web projects directory and clone the application using Git

```
cd /var/www/html
git clone https://github.com/kutaloweb/spala
```

Move to application directory

```
cd spala
```

Install the application dependencies

```
composer install
npm install
```

Create the environment configuration file and edit it with your favorite IDE

```
cp .env.example .env
```

Set your application key

```
php artisan key:generate
```

Generate your JSON Web Token key

```
php artisan jwt:secret
```

Run database migrations

```
php artisan migrate
```

Execute the NPM script

```
npm run dev
```

Change the group ownership of the storage and cache directories and grant them all permissions (for Mac type `_www` instead of `www-data`)

```
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R 777 storage public/uploads public/images bootstrap/cache
```

Install the application (create default roles, permissions, etc.)

```
php artisan install
```

By default server-side rendering is used for the SEO purposes but if you need your own prerender server you can use [prerender.io](https://prerender.io/)

## First steps

Go to the page `/register`. The first registered user will get the admin role

If you need enable login and give other permissions to user role on the page `/configuration/permission/assign`

## Contributing

As an open project, I welcome contributions from everybody. Please, feel free to fork the repository and submit pull requests

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Premium Support

Want help with implementation or new features? Start a conversation with me: kutalo84@gmail.com
