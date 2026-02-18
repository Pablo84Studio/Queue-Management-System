# 🚀 Quick Start Guide

## 5-Minute Setup

### Step 1: Import Database
```bash
mysql -u root -p < database/schema.sql
# Enter your MySQL password when prompted
```

### Step 2: Configure Database Connection
Edit `config/database.php`:
```php
$db_host = 'localhost';
$db_user = 'root';           // Your MySQL username
$db_pass = '';               // Your MySQL password
$db_name = 'queue_management';
```

### Step 3: Start Web Server
```bash
# Using PHP built-in server (development only)
php -S localhost:8000

# Then visit: http://localhost:8000
```

### Step 4: Login with Demo Accounts

**Admin Account:**
- Username: `admin`
- Password: `admin123`

**Staff Accounts:**
- Username: `staff1`, `staff2`, `staff3`
- Password: `staff123` (same for all)

---

## 📊 Testing the System

### 1. Admin Dashboard
- Login as admin
- View dashboard with live statistics
- Check counter statuses
- Monitor queue in real-time

### 2. Staff Dashboard
- Login as staff1
- Add a test customer:
  - Name: "John Doe"
  - Phone: "123-456-7890"
  - Service: "Consultation"
- Click "Call Next Customer"
- Listen for sound alert

### 3. Public Display
- Open another browser tab: `http://localhost:8000/display.php`
- Watch it update in real-time
- See ticket numbers and customers

### 4. Generate Reports
- Go to Admin > Reports
- Select "Queue Report"
- Choose "Today"
- Click "Generate PDF"

---

## 🎮 Key Features to Try

### Access Points
| Role | URL | Username | Password |
|------|-----|----------|----------|
| Admin | `/admin/dashboard.php` | admin | admin123 |
| Staff | `/staff/dashboard.php` | staff1 | staff123 |
| Public | `/display.php` | (none) | (none) |
| Login | `/index.php` | (any user) | - |

### Admin Functions
1. **Manage Staff** - Add/edit/delete staff members
2. **Manage Counters** - Create windows and assign staff
3. **Monitor Queue** - Watch all customers in queue
4. **Generate Reports** - Daily/weekly/monthly PDFs
5. **View Settings** - Configure system options

### Staff Functions
1. **Add Customer** - Register customer queue entry
2. **Call Next** - Assign to your counter
3. **Complete** - Mark service finished
4. **View Queue** - See waiting customers

### Public Display
1. **Now Serving** - Shows tickets at each counter
2. **Waiting Queue** - Position and wait time
3. **Auto-Refresh** - Updates every 5 seconds
4. **Sound Alert** - Plays when customer called

---

## 📁 Directory Structure

```
Queue-Management-System/
├── index.php                 # Login page
├── display.php              # Public queue display
├── logout.php               # Logout handler
├── admin/                   # Admin dashboard pages
│   ├── dashboard.php        # Main admin dashboard
│   ├── manage-staff.php     # Staff management
│   ├── manage-counters.php  # Counter management
│   ├── queue-monitor.php    # Queue monitoring
│   ├── reports.php          # Report generation
│   └── settings.php         # System settings
├── staff/                   # Staff interface
│   └── dashboard.php        # Staff dashboard
├── api/                     # REST API endpoints
│   ├── queue.php           # Queue operations
│   ├── staff.php           # Staff management
│   ├── counters.php        # Counter management
│   └── system.php          # System data
├── classes/                # PHP classes
│   ├── QueueManager.php    # Queue logic
│   ├── UserManager.php     # User/auth logic
│   ├── CounterManager.php  # Counter logic
│   └── ReportGenerator.php # Report logic
├── config/                 # Configuration
│   └── database.php        # Database connection
├── database/               # Database files
│   └── schema.sql         # SQL schema
└── assets/                 # Frontend resources
    ├── css/               # Stylesheets
    ├── js/                # JavaScript files
    └── sounds/            # Audio files (add your sounds here)
```

---

## 🔊 Sound Setup

1. **Add sound files:**
   - Place `.mp3` or `.wav` files in `assets/sounds/`
   - Name them: `callAudio.mp3`, `notification.mp3`, etc.

2. **Update sound paths in JavaScript:**
   - Edit `assets/js/common.js`
   - Modify `playSound()` function path as needed

---

## 🐛 Troubleshooting

### Can't login?
- Check database password in `config/database.php`
- Verify MySQL is running
- Check if database `queue_management` exists

### No sound playing?
- Check browser console for errors (F12)
- Verify `.mp3` files exist in `assets/sounds/`
- Try different audio formats (.wav)
- Check browser autoplay policy

### Queue not updating?
- Check AJAX calls in browser Network tab (F12)
- Verify PHP error log
- Check if JavaScript errors exist in console
- Ensure refresh interval is set correctly

### Can't access pages?
- Check web server is running
- Verify file permissions (should be readable)
- Check URL paths match directory structure
- Ensure PHP is enabled on web server

---

## 📞 Common Operations

### Add Staff User
1. Login as admin
2. Go to **Manage Staff**
3. Click **Add New Staff**
4. Fill form:
   - Name: "John Smith"
   - Username: "john_smith"
   - Email: "john@example.com"
   - Password: "securepass123"
5. Click **Add Staff**

### Create Counter/Window
1. Go to **Manage Counters**
2. Click **Add Counter**
3. Fill form:
   - Name: "Counter 1"
   - Service: "Consultation"
   - Assign Staff: "john_smith"
4. Click **Add Counter**

### Test Queue
1. Login as staff
2. Click **Add Customer**
3. Enter customer details
4. Press Enter
5. Customer appears in public display
6. Click **Call Next Customer**
7. Listen for alert

### Generate Report
1. Go to **Reports**
2. Select **Queue Report**
3. Choose date range (Today/Week/Month)
4. Click **Generate PDF**
5. Save or print

---

## 🎯 Next Steps

1. **Customize:** Edit styles in `assets/css/`
2. **Add Services:** Update database table `services`
3. **Configure:** Change settings in `admin/settings.php`
4. **Deploy:** Follow INSTALLATION.md for production setup
5. **Monitor:** Check reports regularly
6. **Backup:** Export database periodically

---

## 📖 More Documentation

- **Full Features:** See [FEATURES.md](FEATURES.md)
- **Installation:** See [INSTALLATION.md](INSTALLATION.md)
- **Technical Details:** See [DOCUMENTATION.md](DOCUMENTATION.md)
- **Project Overview:** See [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)

---

## ✅ Checklist

- [ ] Database imported successfully
- [ ] Database configured in `config/database.php`
- [ ] Web server started
- [ ] Login page accessible at `/index.php`
- [ ] Admin login works with `admin/admin123`
- [ ] Staff login works with `staff1/staff123`
- [ ] Public display at `/display.php` shows queue
- [ ] Admin dashboard shows statistics
- [ ] Staff can add customers
- [ ] Sound plays when customer called
- [ ] Reports generate PDF files
- [ ] All pages responsive on mobile

---

**Ready to go!** 🎉

For issues, check the troubleshooting section or refer to full documentation files.
