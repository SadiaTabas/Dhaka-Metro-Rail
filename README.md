 # Metro Rail Ticket Management System

A comprehensive web-based Metro Rail Ticket Management System designed to simplify and automate the ticketing process for urban metro transportation services. The system provides dedicated interfaces for Passengers, Administrators, and Employees, ensuring efficient ticket booking, route management, payment processing, and customer support.

## Key Features

### Common Features (All Users)

* Secure User Registration and Login
* Logout Functionality
* Password Recovery (Forgot Password)
* Change Password Option
* Profile Management (View and Update Personal Information)

---

## Passenger Module

### Dashboard

* View active bookings and ticket information
* Access recent travel activities

### Ticket Booking

* Search available routes and stations
* Book metro tickets online
* Update booking information when required

### Journey History

* View complete travel history
* Delete previous travel records

### Payment Records

* View transaction history
* Track payment logs and booking payments

---

## Admin Module

### System Dashboard

* Monitor total users
* Track overall revenue
* View system statistics and operational status

### Fare and Route Management

* Manage metro stations and routes
* Maintain distance information
* Configure fare structures
* View and delete route-related records

### Employee Management

* Add new employees
* View employee information
* Delete employee accounts
* Manage staff operations

---

## Employee Module

### Ticket Management

* Confirm pending ticket bookings
* Process ticket cancellation requests

### Passenger Support

* Handle customer queries
* Resolve booking-related issues
* Assist passengers with ticket management

### Payment Verification

* Verify payment status
* Monitor transaction records
* Ensure payment security and accuracy

---

## Technology Stack

| Technology             | Description                     |
| ---------------------- | ------------------------------- |
| Frontend               | HTML5, CSS3 (Responsive Design) |
| Client-Side Validation | JavaScript                      |
| Backend                | PHP                             |
| Database               | MySQL                           |
| Server Environment     | XAMPP / WAMP (Apache Server)    |

---

## Database Architecture

The system uses a relational database structure that efficiently manages and connects passengers, employees, ticket bookings, payments, routes, and administrative operations.

---

## Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/mehedisrabon/Metro_Ticket_Management_System.git
```

### 2. Set Up the Database

1. Install XAMPP or WAMP.
2. Start **Apache** and **MySQL** services.
3. Open **phpMyAdmin**.
4. Create a new database named:

```sql
mydb
```

5. Import the `mydb.sql` file located in the project directory.

### 3. Configure Database Connection

1. Open the `config.php` file (or the database connection file used in your project).
2. Update the following credentials:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "mydb";
```

### 4. Run the Project

1. Move the project folder to your server directory:

   * XAMPP: `htdocs`
   * WAMP: `www`

2. Start Apache and MySQL.

3. Open your browser and navigate to:

```text
http://localhost/metro-rail-management/
```

---

## Project Objective

The Metro Rail Ticket Management System aims to provide a reliable, user-friendly, and efficient digital platform for metro transportation services by reducing manual processes, improving ticket management, enhancing passenger experience, and ensuring smooth administrative operations.
