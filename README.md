# Tailoring Record Management System

A simple school-project style PHP and MySQL app for recording tailoring customers, garments or alterations, measurements, prices, pickup dates, and order status.

## Requirements

- XAMPP with Apache, MySQL, and PHP 8 or newer
- A web browser

## Setup in XAMPP

1. Make sure this project folder is at `C:\xampp\htdocs\haha`.
2. Open XAMPP Control Panel and start **Apache** and **MySQL**.
3. Open `http://localhost/phpmyadmin`.
4. Select the **Import** tab and import `database/tailoring.sql`.
5. Visit `http://localhost/haha/` in your browser.

Log in with the demo account `admin` and password `admin123`.

The default MySQL settings are `root` with an empty password. If your XAMPP MySQL password is different, update `config/database.php`.

## Main features

- Dashboard with order counts
- Add new tailoring records
- Edit existing records
- Delete records
- Search by customer, phone, or garment
- Track Pending, Ongoing, and Completed orders
- Set order priority to High, Normal, or Low
- Print an individual order slip for reference or customer pickup
- Reports with date filtering, sales totals, status counts, and priority counts
- Admin dropdown with My Profile, Change Password, and Logout
- Login and logout protection for the records area

The form records the date received and expected pickup date for each order. If this project was already installed, import `database/tailoring.sql` again to migrate old statuses to the new three-status list.
