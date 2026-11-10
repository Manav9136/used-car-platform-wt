# Used Car Buy & Sell Platform — Setup Guide

## Admin Login
- **Username:** `admin`
- **Password:** `admin123`

---

## How to Run (XAMPP)

### Step 1 — Copy the project
Copy the `used-car-platform` folder to:
```
C:\xampp\htdocs\used-car-platform      (Windows)
/Applications/XAMPP/htdocs/used-car-platform  (Mac)
```

### Step 2 — Import the database
1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **Import**
3. Choose the file `database.sql`
4. Click **Go**

### Step 3 — Open the website
- **User Site:** `http://localhost/used-car-platform/`
- **Admin Panel:** `http://localhost/used-car-platform/admin/login.php`

---

## Manual Testing Checklist

| # | Test | Expected Result |
|---|------|----------------|
| 1 | Register new user | Account created, redirected to login |
| 2 | Login with wrong password | Error message shown |
| 3 | Login with correct details | Session started, redirected to home |
| 4 | Browse cars without login | Cars visible, seller info hidden |
| 5 | Submit a car (logged in) | Car saved as Pending |
| 6 | Edit pending car | Details updated |
| 7 | Delete pending car | Listing removed |
| 8 | Admin login | Redirected to dashboard |
| 9 | Admin approves car | Car visible on public site |
| 10 | Admin rejects car | Car hidden from public site |
| 11 | Admin deletes user | User and listings removed |
| 12 | Logout | Session destroyed |

---

## Project File Structure
```
used-car-platform/
├── index.php          # Home page with hero + recent cars
├── register.php       # User registration
├── login.php          # User login
├── logout.php         # User logout
├── cars.php           # Browse + search + filter cars
├── car-details.php    # Full car info page
├── sell-car.php       # Submit a car for sale
├── my-cars.php        # User's own listings
├── edit-car.php       # Edit pending listing
├── config.php         # PDO database connection
├── style.css          # Main CSS (black & white premium theme)
├── database.sql       # MySQL database schema + admin record
├── uploads/           # Car images
├── includes/
│   ├── header.php     # Shared navbar + HTML head
│   ├── footer.php     # Shared footer + Bootstrap JS
│   ├── auth.php       # User session guard
│   └── admin-auth.php # Admin session guard
└── admin/
    ├── login.php       # Admin login
    ├── dashboard.php   # Stats dashboard
    ├── manage-cars.php # Approve / reject listings
    ├── view-car.php    # Full car detail for admin
    ├── manage-users.php# Search / delete users
    └── logout.php      # Admin logout
```
