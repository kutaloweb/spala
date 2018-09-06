# Spala

Content Management System based on [Laravel 5.6](https://laravel.com), [Vue 2.5](https://vuejs.org), [Bootstrap 4](https://getbootstrap.com/) and [Monster Admin Template](https://wrappixel.com/demos/admin-templates/monster-admin/Documentation/document.html).

## Getting Started

These instructions will get you a copy of the project up and running on your local linux machine.

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
npm instal
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

Change the group ownership of the storage and cache directories and grant them all permissions

```
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R 777 storage public/uploads public/images bootstrap/cache
```

Install the application (create default roles, permissions, etc.)

```
php artisan install
```

If you need own prerender server for SEO purposes

```
git clone https://github.com/prerender/prerender.git
cd prerender
npm install
npm install -g forever
forever start server.js
```

## Contributing

As an open project, I welcome contributions from everybody. Please, feel free to fork the repository and submit pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Premium Support

Want help with implementation or new features? Start a conversation with me: kutalo84@gmail.com