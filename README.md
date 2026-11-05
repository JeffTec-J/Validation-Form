# Simple Student Registration Form

## Files
- `index.php` - form and PHP validation
- `db.php` - database connection
- `css/style.css` - page design
- `database/student_registration.sql` - database and table

## Run with XAMPP
1. Copy this folder into `C:\xampp\htdocs`.
2. Start Apache and MySQL.
3. Open phpMyAdmin.
4. Import `database/student_registration.sql`.
5. Visit `http://localhost/simple-student-registration/`.

If MySQL uses a different port, update the connection settings in `db.php`.

## Example formats used
- Phone: 9 digits starting with 6, e.g. 677123456.
- Matriculation: SE followed by 7 digits, e.g. SE2026001.
- Password: at least 8 characters.
- Date of birth cannot be in the future.

Change these examples if your institution uses other formats.
