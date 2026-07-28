# Authentication Setup Guide

## 1. Create the database in XAMPP
1. Start Apache and MySQL in XAMPP.
2. Open phpMyAdmin at http://localhost/phpmyadmin.
3. Create a database named `gpcms`.
4. Import the file `database/gpcms_auth_schema.sql`.

## 2. Verify the tables
After import, confirm these tables exist:
- `roles`
- `users`

## 3. Default login accounts
- Citizen: `citizen` / `Citizen@123`
- Gram Sevak: `gramsevak` / `Admin@123`
- Field Officer: `fieldofficer` / `Officer@123`
- Super Admin: `superadmin` / `Super@123`

## 4. Application flow
- Citizen portal login goes to `login.php` and then to `citizen_dashboard.php` after successful authentication.
- Official portal login goes to `official_login.php` and then to the matching official dashboard after successful authentication.
