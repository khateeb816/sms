# Laravel Dashtreme Admin Dashboard

This is a Laravel implementation of the Dashtreme Admin Dashboard theme.

## Features

- Modern and responsive admin dashboard
- Authentication (Login, Register, Password Reset)
- Dashboard with charts and statistics
- UI components (Icons, Forms, Tables)
- Calendar integration
- User profile management
- Theme customization

## Installation

1. Clone the repository
```bash
git clone <repository-url>
```

2. Navigate to the project directory
```bash
cd laravel-dashtreme
```

3. Install dependencies
```bash
composer install
```

4. Copy the environment file
```bash
cp .env.example .env
```

5. Generate application key
```bash
php artisan key:generate
```

6. Configure your database in the .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_dashtreme
DB_USERNAME=root
DB_PASSWORD=
```

7. Run migrations
```bash
php artisan migrate
```

8. Start the development server
```bash
php artisan serve
```

## Usage

1. Access the application at `http://localhost:8000`
2. Register a new account or login with existing credentials
3. Explore the dashboard and various features

## Credits

- [Laravel](https://laravel.com/)
- [Dashtreme Admin Dashboard](https://codervent.com/dashtreme/)
