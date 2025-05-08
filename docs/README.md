# BookHaven - Online Bookstore Application

BookHaven is a modern, feature-rich online bookstore application built with PHP and MySQL. It provides a seamless book shopping experience with advanced features for both customers and administrators.

## Features

- 📚 Extensive book catalog with search and filtering
- 🛒 Shopping cart with real-time updates
- 👤 User authentication and profile management
- 💳 Secure checkout process
- ⭐ Book reviews and ratings
- 📱 Responsive design for all devices
- 🔐 Admin dashboard for store management

## Tech Stack

- Frontend: HTML5, CSS3, JavaScript
- Backend: PHP 8.0+
- Database: MySQL 8.0+
- Additional: PDO, JSON, AJAX

## Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx)
- Composer (for dependencies)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/bookhaven.git
   ```

2. Create MySQL database:
   ```sql
   CREATE DATABASE bookapp;
   ```

3. Configure database connection:
   - Open `backend/config/database.php`
   - Update database credentials if needed

4. Import database schema:
   ```bash
   mysql -u root -p bookapp < database/schema.sql
   ```

5. Start the development server:
   ```bash
   php -S localhost:8000
   ```

6. Access the application:
   - Website: http://localhost:8000
   - Admin: http://localhost:8000/admin (credentials below)

## Default Admin Credentials

- Email: admin@example.com
- Password: admin123

## Project Structure

```
bookhaven/
├── admin/              # Admin dashboard files
├── assets/            # Static assets (CSS, JS, images)
├── backend/           # PHP backend files
│   ├── api/          # API endpoints
│   ├── config/       # Configuration files
│   └── functions/    # Helper functions
├── docs/             # Documentation
├── frontend/         # Frontend components
└── vendor/           # Dependencies
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.