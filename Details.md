# DoBu Martial Arts Website Details

DoBu Martial Arts Website
├── Home (index.php)
│ ├── About Section
│ ├── Classes Preview
│ ├── Instructors Section
│ └── Facilities Section
│
├── Member Area
│ ├── Login (login.php)
│ ├── Sign Up (signup.php)
│ └── User Dashboard (user-profile.php)
│ ├── Personal Information
│ ├── Membership Details
│ ├── Class Schedule
│ ├── Private Tuition Booking
│ └── Account Management
│
├── Classes (classes.php)
│ ├── Class Descriptions
│ ├── Training Plans
│ └── Pricing Information
│
└── Schedule (schedule.php)
├── Weekly Timetable
├── Class Times
└── Instructor Assignments

## Pages and Their Usage

| Page | Description | Usage |
|------|-------------|--------|
| index.php | Homepage | Main landing page with overview of services, featured classes, and call-to-action buttons |
| classes.php | Classes Information | Detailed information about available martial arts classes and pricing |
| schedule.php | Class Schedule | Weekly schedule of all classes and sessions |
| login.php | Login Page | User authentication page for members |
| signup.php | Registration | New user registration page with membership selection |
| user-profile.php | User Dashboard | Member dashboard for managing account, bookings, and viewing schedule |
| forgot-password.html | Password Recovery | Simple page for password recovery (currently displays "skill issue") |
| verify_password.php | Password Verification | Backend script for verifying user passwords |
| change_password.php | Password Change | Handles password change requests |
| delete_account.php | Account Deletion | Processes account deletion requests |

## Database Schema

The website uses the following main database tables:

- users: Stores user account information and membership details
- classes: Contains information about available martial arts classes
- schedules: Stores the weekly class schedule
- user_classes: Links users to their enrolled classes
- payments: Tracks user payments and transactions
- instructors: Stores instructor information
- class_instructors: Links instructors to classes
- private_tuitions: Records private tuition bookings
- specialist_bookings: Tracks specialist course and fitness training bookings

## Security Measures

1. Password Hashing
   - All user passwords are hashed using PHP's password_hash() function
   - Passwords are never stored in plain text

2. Session Management
   - User sessions are managed securely
   - Session validation on all protected pages

3. Input Validation
   - All user inputs are validated and sanitized
   - Prepared statements used for database queries

4. Access Control
   - Role-based access control for different user types
   - Protected routes require authentication

## Future Enhancements

1. Online Payment Integration
   - Integration with payment gateways
   - Automated billing system

2. Advanced Booking Features
   - Waitlist system for full classes
   - Automated reminder system

3. Communication Features
   - Internal messaging system
   - Automated email notifications

4. Mobile Application
   - Native mobile app development
   - Push notifications
