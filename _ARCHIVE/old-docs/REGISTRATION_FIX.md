# Culture Radar - Registration Fix Guide

## Problem
You're unable to create an account when testing the registration form.

## Solutions Applied

### 1. Fixed API Endpoint (`api/auth.php`)
- Updated to handle both `confirmPassword` and `password_confirm` field names
- Added support for user preferences during registration
- Improved error handling

### 2. Fixed Auth Class (`classes/Auth.php`)
- Made password confirmation validation optional
- Added preferences handling during registration
- Fixed validation logic

### 3. Fixed Database Connection (`includes/db.php`)
- Added automatic database creation if it doesn't exist
- Auto-creates required tables on first connection
- Better error handling and recovery

### 4. Created Setup Files
- `.env` file with default configuration
- `database/setup-complete.sql` for manual database setup
- `test-registration.php` for testing the registration system

## Setup Instructions

### Step 1: Configure Database

#### For MAMP Users:
1. Open MAMP and start servers
2. Default settings:
   - Host: localhost
   - Port: 8889 (MySQL)
   - User: root
   - Password: root

#### For XAMPP Users:
1. Open XAMPP Control Panel
2. Start Apache and MySQL
3. Default settings:
   - Host: localhost
   - Port: 3306 (MySQL)
   - User: root
   - Password: (empty)

#### For WAMP Users:
1. Start WAMP server
2. Default settings:
   - Host: localhost
   - Port: 3306 (MySQL)
   - User: root
   - Password: (empty)

### Step 2: Create Database

#### Option A: Using PHPMyAdmin
1. Open PHPMyAdmin (http://localhost:8888/phpMyAdmin for MAMP)
2. Click "New" to create a database
3. Name: `culture_radar`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Click "Import" tab
7. Choose file: `database/setup-complete.sql`
8. Click "Go"

#### Option B: Automatic Creation
The application will automatically create the database and tables when you first access it.

### Step 3: Configure Environment

1. Check the `.env` file in the root directory
2. Update database credentials if needed:
```env
# For MAMP
DB_HOST=localhost
DB_PORT=8889
DB_NAME=culture_radar
DB_USER=root
DB_PASS=root

# For XAMPP/WAMP (uncomment these)
#DB_HOST=localhost
#DB_PORT=3306
#DB_NAME=culture_radar
#DB_USER=root
#DB_PASS=
```

### Step 4: Test Registration

1. Open your browser
2. Go to: `http://localhost:8888/register.php` (adjust port as needed)
3. Fill in the registration form:
   - First Name: Test
   - Last Name: User
   - Email: test@example.com
   - Password: TestPass123
   - Confirm Password: TestPass123
   - City: Paris
   - Select some preferences
   - Check the terms checkbox

4. Click "Créer mon compte"

## Troubleshooting

### Error: "Database connection failed"
- Check that MAMP/XAMPP/WAMP is running
- Verify MySQL service is started
- Check database credentials in `.env`

### Error: "Email already exists"
- The email is already registered
- Try a different email address

### Error: "Passwords don't match"
- Make sure both password fields have the same value
- Password must be at least 8 characters

### Registration form doesn't submit
1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify the API endpoint is accessible: 
   - Try: `http://localhost:8888/api/auth.php?action=check`
   - Should return JSON response

### White page or PHP errors
1. Check PHP is enabled in your web server
2. Verify file permissions (should be readable)
3. Check PHP error log

## Testing Tools

### Test Registration System
Run: `http://localhost:8888/test-registration.php`

This will:
- Check all required files
- Test database connection
- Verify tables exist
- Attempt a test registration

### Manual API Test
You can test the API directly using curl or a tool like Postman:

```bash
curl -X POST http://localhost:8888/api/auth.php?action=register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "TestPass123",
    "confirmPassword": "TestPass123",
    "location": "Paris",
    "preferences": ["musique", "theatre"],
    "newsletter": false,
    "terms": true
  }'
```

## Files Modified

1. `/api/auth.php` - Fixed field name handling
2. `/classes/Auth.php` - Fixed validation logic
3. `/includes/db.php` - Added auto-creation
4. Created `/database/setup-complete.sql`
5. Created `/.env` with defaults
6. Created `/test-registration.php` for testing

## Still Having Issues?

1. Clear browser cache and cookies
2. Try a different browser
3. Check the browser console for errors
4. Check PHP error logs:
   - MAMP: `/Applications/MAMP/logs/php_error.log`
   - XAMPP: `/xampp/php/logs/php_error_log`
   - WAMP: `/wamp64/logs/php_error.log`

## Contact Support

If you're still experiencing issues:
1. Run the test script: `/test-registration.php`
2. Copy the output
3. Check browser console for errors
4. Note your server setup (MAMP/XAMPP/WAMP version)

The registration should now work properly!