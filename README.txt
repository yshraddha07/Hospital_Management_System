HOSPITAL MANAGEMENT SYSTEM - QUICK SETUP

1. Install XAMPP.
2. Start Apache and MySQL from XAMPP Control Panel.
3. Copy the whole Hospital_Management_System folder into:
   C:\xampp\htdocs\
4. Open phpMyAdmin:
   http://localhost/phpmyadmin
5. Click Import and select database.sql from this project folder.
6. Open:
   http://localhost/Hospital_Management_System/
7. Login:
   Username: admin
   Password: admin123

If your MySQL password is not blank, change it in config.php.

Important:
- PHP files cannot run using only VS Code's Live Server.
- Run this project through Apache/XAMPP.
- Keep all PHP files in the same project folder.
The PHP files were syntax-checked with PHP lint before packaging.
