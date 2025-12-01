# Server Setup Guide - InfinityFree Deployment

This document provides step-by-step instructions for deploying the Ashesi Campus Resource Locator (ACRL) on InfinityFree hosting.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [InfinityFree Account Setup](#infinityfree-account-setup)
3. [Database Configuration](#database-configuration)
4. [File Upload](#file-upload)
5. [Configuration Changes](#configuration-changes)
6. [Post-Deployment Verification](#post-deployment-verification)
7. [Troubleshooting](#troubleshooting)
8. [Important Notes](#important-notes)

---

## Prerequisites

Before beginning deployment, ensure you have:

- A registered InfinityFree account
- FTP client software (FileZilla recommended)
- All project files from the repository
- Basic understanding of FTP file transfers

---

## InfinityFree Account Setup

### Step 1: Create an Account

1. Navigate to [InfinityFree](https://www.infinityfree.com/)
2. Click "Sign Up" and complete the registration process
3. Verify your email address

### Step 2: Create a Hosting Account

1. Log in to your InfinityFree client area
2. Click "Create Account" under Free Hosting
3. Choose a subdomain (e.g., `acrl.infinityfreeapp.com`) or connect your own domain
4. Select a label for your account
5. Complete the CAPTCHA and create the account
6. Wait for the account to be activated (usually within a few minutes)

### Step 3: Retrieve FTP Credentials

1. Go to your hosting account control panel
2. Navigate to "FTP Details" or "Account Details"
3. Note the following information:
   - FTP Hostname (e.g., `ftpupload.net`)
   - FTP Username
   - FTP Password
   - Website URL

---

## Database Configuration

### Important: SQLite vs MySQL

The application currently uses SQLite (`mockDatabase.db`). InfinityFree supports MySQL databases. You have two options:

#### Option A: Continue Using SQLite (Recommended for this project)

1. The SQLite database file (`setup/mockDatabase.db`) will be uploaded with the project
2. Ensure the `setup/` directory has write permissions (chmod 755 or 777)
3. Ensure the database file has write permissions (chmod 666)

#### Option B: Migrate to MySQL

If you prefer MySQL:

1. In the InfinityFree control panel, go to "MySQL Databases"
2. Create a new database and note:
   - Database Name
   - Database Username
   - Database Password
   - Database Hostname (usually `sqlXXX.infinityfree.com`)
3. Modify `backend/dbConnector.php` to use MySQL instead of SQLite

---

## File Upload

### Using FileZilla FTP Client

#### Step 1: Connect to Server

1. Open FileZilla
2. Enter your FTP credentials:
   - Host: Your FTP hostname
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21
3. Click "Quickconnect"

#### Step 2: Navigate to Web Root

1. In the remote site panel, navigate to `htdocs/` directory
2. This is your web root directory

#### Step 3: Upload Files

Upload the following directories and files to `htdocs/`:

```
htdocs/
├── backend/
│   ├── add_booking.php
│   ├── addType.php
│   ├── cancel_booking.php
│   ├── check_availability.php
│   ├── create_booking.php
│   ├── dbConnector.php
│   ├── fetch_bookings.php
│   ├── fetch_resources.php
│   ├── getTypes.php
│   ├── loginSignupPreprocessor.php
│   └── resourceAllocator.php
├── frontend/
│   ├── about.php
│   ├── available_sessions.php
│   ├── bookings.php
│   ├── home.php
│   ├── login_signup.php
│   ├── pageflow.php
│   ├── resourceAllocator.php
│   ├── resourceLocator.php
│   ├── software_architecture.php
│   ├── css/
│   │   └── style.css
│   ├── images/
│   │   └── [all image files]
│   └── js/
│       ├── available_sessions.js
│       ├── bookings.js
│       ├── main.js
│       ├── map.js
│       ├── resourceAllocator.js
│       └── tailwindConfig.js
└── setup/
    └── mockDatabase.db
```

#### Step 4: Set File Permissions

After uploading, set the following permissions:

| Path | Permission | Purpose |
|------|------------|---------|
| `setup/` | 755 or 777 | Directory write access |
| `setup/mockDatabase.db` | 666 | Database file write access |

To set permissions in FileZilla:
1. Right-click the file/folder
2. Select "File permissions"
3. Enter the numeric value and click OK

---

## Configuration Changes

### Step 1: Update Database Path

Edit `backend/dbConnector.php` and verify the database path:

```php
// Current path (relative)
$dbPath = "../setup/mockDatabase.db";
```

If needed, update to an absolute path:

```php
// Absolute path example
$dbPath = "/home/vol1_1/infinityfree.com/YOUR_USERNAME/htdocs/setup/mockDatabase.db";
```

### Step 2: Disable PHP Error Display (Production)

For production, edit PHP files to disable error display. In `backend/dbConnector.php`:

```php
// Change from:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// To:
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
```

### Step 3: Update Session Configuration (if needed)

InfinityFree may require specific session configurations. If sessions don't work, add to the top of PHP files that use sessions:

```php
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '.yourdomain.infinityfreeapp.com');
```

---

## Post-Deployment Verification

### Step 1: Access Your Website

1. Navigate to your website URL (e.g., `https://acrl.infinityfreeapp.com/frontend/login_signup.php`)
2. Verify the login page loads correctly

### Step 2: Test Core Functionality

Perform the following tests:

| Test | Expected Result |
|------|-----------------|
| Login page loads | Page displays without errors |
| User registration | New user can register |
| User login | Existing user can log in |
| Home page | Dashboard displays correctly |
| Campus map | Mapbox map renders with markers |
| Resource booking | Users can create bookings |
| Admin functions | Admin can allocate resources |

### Step 3: Check Error Logs

If issues occur:
1. Go to InfinityFree control panel
2. Navigate to "Error Logs"
3. Review any PHP errors

---

## Troubleshooting

### Common Issues and Solutions

#### Issue: "Database is locked" or write errors

**Cause:** SQLite database lacks write permissions

**Solution:**
1. Set `setup/` directory permission to 777
2. Set `mockDatabase.db` permission to 666
3. Verify via FTP that permissions are applied

#### Issue: Blank white page

**Cause:** PHP fatal error

**Solution:**
1. Temporarily enable error display in the PHP file
2. Check error logs in control panel
3. Common causes: missing file, syntax error, incorrect path

#### Issue: Session not persisting

**Cause:** Session configuration incompatibility

**Solution:**
1. Add session configuration at the start of PHP files
2. Ensure cookies are enabled in browser
3. Check session save path permissions

#### Issue: Mapbox map not loading

**Cause:** Mixed content or API key issues

**Solution:**
1. Ensure all resources load over HTTPS
2. Verify Mapbox access token is valid
3. Check browser console for errors

#### Issue: 500 Internal Server Error

**Cause:** Various server-side issues

**Solution:**
1. Check `.htaccess` file for incompatible directives
2. Verify PHP version compatibility (InfinityFree uses PHP 7.4+)
3. Review error logs

---

## Important Notes

### InfinityFree Limitations

Be aware of the following InfinityFree restrictions:

| Limitation | Details |
|------------|---------|
| Daily hits | 50,000 hits per day |
| Disk space | 5 GB |
| Bandwidth | Unlimited (fair use) |
| File size | Max 10 MB per file |
| Execution time | 60 seconds max |
| Inodes | 400,000 files max |

### Security Recommendations

1. **Change default credentials:** Update any default admin passwords
2. **Protect sensitive files:** Ensure `dbConnector.php` is not directly accessible
3. **Use HTTPS:** InfinityFree provides free SSL certificates
4. **Regular backups:** Download your database periodically

### Performance Tips

1. Enable browser caching via `.htaccess`
2. Optimize images before upload
3. Minimize external API calls
4. Use CDN-hosted libraries (Tailwind, jQuery)

---

## Support Resources

- InfinityFree Knowledge Base: https://support.infinityfree.com/
- InfinityFree Forums: https://forum.infinityfree.com/
- Project Repository: https://github.com/AshesiWebTech2025/ResourceLocator

---

## File Checklist

Before going live, verify all files are uploaded:

- [ ] `backend/` - All PHP backend files
- [ ] `frontend/` - All PHP frontend files
- [ ] `frontend/css/` - Stylesheet
- [ ] `frontend/js/` - JavaScript files
- [ ] `frontend/images/` - Image assets
- [ ] `setup/mockDatabase.db` - SQLite database
- [ ] Permissions set correctly on database file

---

*Document Version: 1.0*  
*Last Updated: December 2025*  
*Ashesi Campus Resource Locator - Web Technologies 213*

