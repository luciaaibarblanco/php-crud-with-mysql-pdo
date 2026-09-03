# PHP 8+ CRUD using MySQL PDO and AJAX

A lightweight, modern single-page CRUD web application built with modern **PHP 8+**, **MySQL PDO**, and vanilla **JavaScript (AJAX / Fetch API)**.

## Features

- **PHP 8+ Modern Features**: Utilizes OOP principles, interface-backed repository patterns, type safety, constructor property promotion, and PHP 8.4 property hooks.
- **AJAX & Async Operations**: Full asynchronous CRUD operations using JavaScript's native `fetch()` API and `async/await`.
- **Prepared Statements**: Secure database operations preventing SQL injection via PDO prepared statements.
- **JSON RESTful-style Endpoints**: API responses formatted cleanly as JSON.
- **XSS Protection**: HTML special character sanitization on model attributes (`htmlspecialchars`).

---

## Project Structure

```text
├── index.html        # Single Page Application frontend (HTML5 / JS Fetch API)
├── api.php           # Backend REST API endpoint, database configuration, and models
└── README.md         # Documentation
```

---

## Database Setup

Run the following SQL commands in your MySQL server to set up the database schema and insert initial test data:

```sql
CREATE DATABASE IF NOT EXISTS `biblioteca_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

USE `biblioteca_db`;

CREATE TABLE IF NOT EXISTS `libros` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `titulo` VARCHAR(100) NOT NULL,
    `autor` VARCHAR(100) NOT NULL,
    `precio` DECIMAL(8, 2) NOT NULL
);

INSERT INTO `libros` (`titulo`, `autor`, `precio`) VALUES
('Asado Argentino', 'Lucía Aibar Blanco', 120.30),
('El Precio Justo', 'Lita', 49.90),
('PHP 123', 'Michael Jordan', 22.33);
```

---

## Environment Variables

The backend relies on system environment variables for MySQL database connections. Configure these variables before starting your Web Server or PHP CLI server:

| Environment Variable | Description                                      |
| :------------------- | :----------------------------------------------- |
| `MYAPP_MYSQL_HOST`   | Database host (e.g., `127.0.0.1` or `localhost`) |
| `MYAPP_MYSQL_DB`     | Database name (`biblioteca_db`)                  |
| `MYAPP_MYSQL_USER`   | MySQL Username                                   |
| `MYAPP_MYSQL_PASS`   | MySQL Password                                   |

### Example Setup (Linux/macOS)

```bash
export MYAPP_MYSQL_HOST="127.0.0.1"
export MYAPP_MYSQL_DB="biblioteca_db"
export MYAPP_MYSQL_USER="root"
export MYAPP_MYSQL_PASS="your_password"
```

---

## Getting Started

1. **Clone or download the repository**.
2. **Setup the Database** using the SQL script above.
3. **Set Environment Variables** in your web server environment (Apache, Nginx) or terminal session.
4. **Run with built-in PHP development server**:

```bash
php -S localhost:8000
```

5. Open `http://localhost:8000` in your browser.

---

## API Endpoints (`api.php`)

All requests are dispatched to `api.php` via `POST` using `application/x-www-form-urlencoded`.

| Action (`accion`) | Expected Parameters               | Description                                                       |
| :---------------- | :-------------------------------- | :---------------------------------------------------------------- |
| `getAll`          | _None_                            | Retrieves all books formatted as HTML table rows (`<tr>...</tr>`) |
| `getById`         | `id`                              | Fetches details for a specific book by ID                         |
| `add`             | `titulo`, `autor`, `precio`       | Inserts a new book record                                         |
| `update`          | `id`, `titulo`, `autor`, `precio` | Updates existing book information                                 |
| `delete`          | `id`                              | Deletes a book record                                             |

---

## Author

**Lucía Aibar Blanco**

- **Website**: [https://www.luciaaibarblanco.com](https://www.luciaaibarblanco.com)
- **Email**: luciaaibarblanco@gmail.com
