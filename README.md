# Queue Management System

A comprehensive PHP-based Queue Management System with Admin Dashboard, Staff Interface, and Public Display Screen. Includes customer queuing, staff management, counter control, sound alerts, and PDF reports.

## Features

### 👥 Admin Dashboard
- **Dashboard Overview**: Real-time statistics of queue status
- **Staff Management**: Add, edit, delete, and manage staff members
- **Counter Management**: Create and manage service counters/windows
- **Queue Monitor**: Live queue monitoring with real-time updates
- **Reports**: Generate PDF reports by day, week, or month with staff performance metrics
- **Services Management**: Configure service types

### 🎯 Staff Interface
- **Add Customers**: Quick customer registration to queue
- **Counter Control**: Call next customer, complete service, or cancel
- **Sound Alerts**: Audio and text-to-speech notifications when calling customers
- **Queue Statistics**: View waiting customers and performance metrics
- **Next Customers Display**: See upcoming customers in queue

### 📺 Public Display Screen
- **Live Queue Display**: Shows customers being served at each counter
- **Waiting Queue**: Displays waiting customers with positions
- **Statistics**: Shows total customers, average wait time, active counters
- **Auto-refresh**: Updates every 5 seconds

### 📊 Reports & Analytics
- **Daily Reports**: Queue statistics for any date range
- **Staff Performance**: Staff efficiency and customer handling metrics
- **PDF Generation**: Export reports as formatted PDF documents
- **Custom Date Ranges**: Generate reports for day, week, month, or custom ranges

### 🔔 Sound & Alerts
- **Customer Call Announcements**: Audio alert when customer is called
- **Text-to-Speech**: Reads out ticket numbers
- **Visual Notifications**: Real-time alerts and status updates

## System Requirements

- **PHP**: 7.4 or higher
- **Database**: MySQL 5.7 or higher
- **Web Server**: Apache/Nginx
- **Browser**: Modern browser with JavaScript enabled
- **Additional PHP Extensions**:
  - MySQLi
  - MB String
  - JSON

## Installation

### 1. Database Setup

```bash
# Create database
mysql -u root -p < database/schema.sql
```

Or manually:
1. Create a new database called `queue_management_system`
2. Import `database/schema.sql` file

### 2. Configuration

Edit `config/database.php` and update database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'queue_management_system');
```

### 3. Install PDF Library (Optional for reports)

```bash
composer require tecnickcom/tcpdf
```

Or manually download TCPDF and extract to `vendor/` directory.

### 4. File Permissions

Ensure proper permissions for uploaded files:
```bash
chmod -R 755 /path/to/queue-management/
chmod -R 777 /path/to/queue-management/assets/uploads/
```

### 5. Web Server Configuration

**Apache (.htaccess already included)**:
```
AllowOverride All
```

**Nginx**:
```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

## Usage

### Admin Login
- **URL**: `http://localhost/index.php`
- **Username**: `admin`
- **Password**: `admin123`

### Staff Login
- **URL**: `http://localhost/index.php`
- **Username**: `staff1` / `staff2` / `staff3`
- **Password**: `staff123`

### Public Queue Display
- **URL**: `http://localhost/display.php`
- Display on a public screen/TV

## Directory Structure

```
Queue-Management-System/
├── admin/                    # Admin panel pages
│   ├── dashboard.php        # Admin dashboard
│   ├── manage-staff.php    # Staff management
│   ├── manage-counters.php # Counter/Window management
│   ├── queue-monitor.php   # Real-time queue monitor
│   └── reports.php         # Reports generation
├── staff/                   # Staff interface pages
│   └── dashboard.php       # Staff dashboard
├── api/                     # API endpoints
│   └── queue.php           # Queue management API
├── assets/                  # Static assets
│   ├── css/                # Stylesheets
│   │   ├── style.css      # Main styles
│   │   ├── admin.css      # Admin panel styles
│   │   ├── staff.css      # Staff interface styles
│   │   └── queue-display.css # Display styles
│   ├── js/                 # JavaScript files
│   │   ├── common.js      # Common functions
│   │   ├── admin-dashboard.js
│   │   ├── queue-monitor.js
│   │   ├── manage-staff.js
│   │   ├── manage-counters.js
│   │   ├── reports.js
│   │   ├── staff-dashboard.js
│   │   └── queue-display.js
│   └── sounds/            # Audio files
│       ├── call.mp3       # Call sound
│       └── notification.mp3 # Notification sound
├── classes/               # PHP classes
│   ├── QueueManager.php   # Queue management
│   ├── UserManager.php    # User/Auth management
│   ├── CounterManager.php # Counter management
│   └── ReportGenerator.php # PDF report generation
├── config/               # Configuration files
│   └── database.php     # Database config
├── database/            # SQL files
│   └── schema.sql      # Database schema
├── index.php           # Login page
├── display.php        # Public queue display
└── logout.php         # Logout handler
```

## API Endpoints

### Queue Management
- `GET /api/queue.php?action=get_current` - Get current queue
- `POST /api/queue.php?action=add` - Add customer to queue
- `POST /api/queue.php?action=call_next` - Call next customer
- `POST /api/queue.php?action=complete` - Complete service
- `POST /api/queue.php?action=cancel` - Cancel customer
- `GET /api/queue.php?action=get_statistics` - Get statistics

## Database Schema

### Tables
- **users** - Admin and staff users
- **services** - Service types
- **counters** - Counter/window configuration
- **queue** - Customer queue records
- **staff_activity** - Staff activity log
- **daily_stats** - Daily statistics

## Features Details

### Real-time Updates
- Uses AJAX for live data updates
- Auto-refresh every 5-10 seconds depending on view
- No page reloads required

### Sound Alerts
- Automatic audio when customer is called
- Text-to-speech announcement of ticket numbers
- Customizable sound files

### PDF Reports
- Formatted queue reports
- Staff performance analytics
- Custom date range filtering
- Export with professional formatting

### User Roles
- **Admin**: Full system control, reports, staff management
- **Staff**: Add customers, call customers, complete services

### Security
- Password hashing with bcrypt
- Session management
- CSRF protection ready
- SQL prepared statements

## Customization

### Change Database Parameters
Edit `config/database.php`

### Add New Services
In admin dashboard → Manage Services (can be extended)

### Customize Styles
Edit CSS files in `assets/css/`

### Customize Sound Files
Replace MP3 files in `assets/sounds/`

### Add Counter Assignments
In admin dashboard → Manage Counters

## Troubleshooting

### Database Connection Error
- Check database credentials in `config/database.php`
- Ensure MySQL server is running
- Verify database exists

### Login Issues
- Check database has sample users
- Clear browser cache and cookies

### Sound Not Playing
- Check browser permissions for audio
- Ensure sound files exist in `assets/sounds/`

### Reports Not Generating
- Ensure TCPDF library is installed
- Check write permissions on output directory

## Demo Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Staff 1 | staff1 | staff123 |
| Staff 2 | staff2 | staff123 |
| Staff 3 | staff3 | staff123 |

## Future Enhancements

- SMS notifications to customers
- Email reports
- Mobile app
- Advanced analytics
- Multi-language support
- Appointment scheduling
- Customer satisfaction survey
- Real-time notifications via WebSocket

## License

This project is open source and available under the MIT License.

## Support

For issues, questions, or suggestions, please create an issue in the repository.

## Version

**Version**: 1.0.0  
**Release Date**: February 2026

---

**Built with ❤️ using PHP, MySQL, and JavaScript**
