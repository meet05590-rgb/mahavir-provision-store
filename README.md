# Mahavir Provision Store — Galaxy Glass
A beginner-friendly PHP + MySQL grocery store starter with a responsive Galaxy Glass-inspired interface.

## Included in this starter
- MySQL-backed product catalogue and categories
- Customer registration and login with password hashing
- First-admin setup page
- Admin dashboard, product add/delete, customer list, order list and sales summary
- Cart and wishlist saved in browser localStorage
- Checkout validates stock and creates orders in a database transaction
- PWA manifest, offline fallback page and static asset caching

## Requirements
- PHP 8.1+ with PDO MySQL
- MySQL / MariaDB
- HTTPS for production PWA installation (localhost is treated as secure by modern browsers)

## Local installation (Windows + XAMPP)
1. Download/clone this repository into `C:\xampp\htdocs\mahavir-provision-store`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin at `http://localhost/phpmyadmin` and import `database.sql`.
4. Check database credentials in `config.php` (default XAMPP root with blank password).
5. Open `http://localhost/mahavir-provision-store/setup.php`, create the first admin with a strong password, then DELETE `setup.php`.
6. Visit `http://localhost/mahavir-provision-store/`. Admin: `/admin/index.php`.

## Use on an Android phone
- For phone-only use, deploy PHP and MySQL to a PHP-compatible hosting provider with HTTPS, then open the site URL in Chrome and choose **Add to Home screen**.
- For local testing with a laptop, connect the phone and laptop to the same Wi-Fi, run XAMPP, and open the laptop's LAN IP and project folder in the phone browser. Allow Apache through the laptop firewall only on a trusted private network.
- GitHub Pages cannot run PHP or MySQL. GitHub stores the source; it is not the PHP server.

## Offline limits
The service worker caches static CSS/JavaScript and an offline fallback. The cart and wishlist are stored locally in the browser. PHP login, live product/database updates, checkout, and order submission require an internet connection to the server. This starter does not yet queue offline orders or process online payments.

## Before production
- Use HTTPS, a non-default database password and environment variables.
- Remove setup.php after creating the admin.
- Add rate limiting, password reset, audit logs, database backups, image upload validation, full edit/category management, GST/invoice logic, payment gateway integration and stronger role permissions before real business use.
