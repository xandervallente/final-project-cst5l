# Inventory Management System
### CST5 Final Project Documentation

---

## Table of Contents
1. [Project Description](#project-description)
2. [Technologies Used](#technologies-used)
3. [Project Structure](#project-structure)
4. [Database Structure](#database-structure)
5. [Features](#features)
6. [Setup Instructions](#setup-instructions)
7. [Usage Guide](#usage-guide)
8. [Deployed Application](#deployed-application)
9. [GitHub Repository](#github-repository)
10. [Video Presentation](#video-presentation)

---

## Project Description

The **Inventory Management System** is a dynamic, database-driven web application built with PHP and MySQL. It allows users to register and log in to a secure account, then manage a product inventory through a clean and responsive interface.

The system supports full **CRUD operations** (Create, Read, Update, Delete) for products, along with search and filtering functionality. It is designed to be simple, functional, and easy to navigate — accessible through any standard web browser.

---

## Technologies Used

| Technology     | Purpose                                      |
|----------------|----------------------------------------------|
| PHP 8+         | Server-side scripting and application logic  |
| MySQL          | Relational database for storing data         |
| MySQLi         | PHP extension for database communication     |
| HTML5          | Page structure and markup                    |
| CSS3           | Styling, layout, and responsive design       |
| XAMPP          | Local development environment (Apache + MySQL)|
| phpMyAdmin     | Database management GUI                      |
| GitHub         | Version control and source code hosting      |

---

## Project Structure

```
/
├── index.php                   ← Login page (entry point)
├── logout.php                  ← Logout handler
├── inventory_system.sql        ← Database setup file
│
├── controllers/
│   ├── account.php             ← Login, register, logout logic
│   └── product.php             ← CRUD + search logic for products
│
├── models/
│   ├── account.php             ← Account data class (mirrors users table)
│   └── product.php             ← Product data class (mirrors products table)
│
├── views/
│   ├── dashboard.php           ← Dashboard page (after login)
│   ├── register.php            ← Registration page
│   ├── products.php            ← View all products with search & filter
│   ├── product-add.php         ← Add new product form
│   ├── product-edit.php        ← Edit existing product form
│   ├── product-delete.php      ← Delete product handler
│   └── partial/
│       ├── header.php          ← Shared navigation header
│       └── footer.php          ← Shared footer
│
└── public/
    ├── style.css               ← Main stylesheet (blue & black theme)
    └── database.config.php     ← Database connection credentials
```

The project follows an **MVC-inspired architecture**:
- **Models** — Plain PHP classes that mirror database tables
- **Controllers** — Classes that handle all database queries and business logic
- **Views** — PHP/HTML files that display content to the user

---

## Database Structure

### Database Name: `inventory_system`

---

### Table: `users`
Stores registered user accounts.

| Column       | Type           | Description                        |
|--------------|----------------|------------------------------------|
| id           | INT (PK, AI)   | Unique user ID                     |
| username     | VARCHAR(100)   | Unique username (not null)         |
| password     | VARCHAR(255)   | Bcrypt hashed password             |
| created_at   | TIMESTAMP      | Auto-set on account creation       |

---

### Table: `products`
Stores inventory product records.

| Column       | Type            | Description                       |
|--------------|-----------------|-----------------------------------|
| id           | INT (PK, AI)    | Unique product ID                 |
| name         | VARCHAR(150)    | Product name (not null)           |
| description  | TEXT            | Product description (not null)    |
| price        | DECIMAL(10,2)   | Product price (default 0.00)      |
| quantity     | INT(11)         | Stock quantity (default 0)        |
| created_at   | TIMESTAMP       | Auto-set on product creation      |

---

### Relationships
The `users` and `products` tables are independent. Each logged-in user can manage all products in the inventory (single-role system).

---

## Features

### Authentication
- **Register** — Create a new account with username and password validation
- **Login** — Secure login using `password_verify()` against bcrypt hashed passwords
- **Logout** — Destroys the session and redirects to the login page
- **Session protection** — All pages except login and register redirect unauthenticated users

### Product CRUD
- **View All Products** — Displays all products in a sortable table with stock status badges
- **Add Product** — Form to create a new product with full input validation
- **Edit Product** — Pre-filled form to update an existing product
- **Delete Product** — Removes a product with a confirmation prompt

### Search & Filtering
- **Keyword search** — Search products by name or description in real time
- **Stock filter** — Filter products by All / In Stock / Out of Stock
- **Result count** — Displays number of results and active search/filter terms
- **Clear button** — Resets search and filter back to default

### Input Validation (Server-side)
- All fields required — specific error messages per field
- Username: min 3 / max 100 characters, alphanumeric and underscores only
- Password: minimum 6 characters, confirm password must match
- Price: must be a valid number, cannot be negative
- Quantity: must be a whole number, cannot be negative
- Product name: max 150 characters

### UI/UX
- Fully responsive layout (mobile-friendly)
- Blue and black professional dark theme
- Error messages displayed as styled alert boxes with full list of issues
- Form inputs retain values on validation error
- Stock status badges (In Stock / Out of Stock) on product table
- Custom scrollbar styling

---

## Setup Instructions

### Requirements
- XAMPP (or any local server with PHP 8+ and MySQL)
- A web browser (Chrome, Firefox, Edge)

### Step-by-Step Setup

**1. Clone or download the project**
```
git clone https://github.com/kayljiyan/cst5.git
```
Place the project folder inside `C:/xampp/htdocs/`.

**2. Start XAMPP**
- Open XAMPP Control Panel
- Start **Apache** and **MySQL**

**3. Import the database**
- Open your browser and go to `http://localhost/phpmyadmin`
- Click **Import** in the top navigation
- Click **Choose File** and select `inventory_system.sql`
- Click **Go**

**4. Configure the database credentials** *(if needed)*

Open `public/database.config.php` and update if your credentials differ:
```php
$SERVER_NAME = "localhost";
$USERNAME    = "root";
$PASSWORD    = "";
$DB_NAME     = "inventory_system";
```

**5. Run the application**

Open your browser and go to:
```
http://localhost/cst5
```

**Default login credentials:**
| Field    | Value     |
|----------|-----------|
| Username | admin     |
| Password | admin123  |

---

## Usage Guide

1. Open the app in your browser — you will land on the **Login** page
2. Log in with the default credentials or **register** a new account
3. After login, you will be taken to the **Dashboard**
4. Click **View All Products** to see the inventory table
5. Use the **search bar** or **stock filter** to find specific products
6. Click **+ Add New Product** to create a new inventory item
7. Click **Edit** on any row to update a product's details
8. Click **Delete** on any row to remove a product (with confirmation)
9. Click **Logout** in the navbar to end your session

---

## Deployed Application

> **Link:** *(Insert your deployed application URL here)*

---

## GitHub Repository

> **Link:** https://github.com/kayljiyan/cst5

---

## Video Presentation

> **Link:** *(Insert your Google Drive video link here)*
>
> The video covers:
> - Purpose and overview of the application
> - walkthrough of all main features (Register, Login, Logout, Dashboard)
> - Demonstration of full CRUD operations on products
> - Search and filter functionality in action
> - Technologies used
> - Live demo of the deployed application
