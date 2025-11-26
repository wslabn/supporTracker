# SupportTracker v2.0 - MSP Customer Management System

A complete web-based support and billing tracker for managed service providers (MSPs) with modern UI, user management, and multi-location support.

## ✅ COMPLETED FEATURES

### Core System
- ✅ **User Management**: Role-based permissions (Admin/Manager/Technician) with granular permission control
- ✅ **Multi-Location Support**: Location-based access control with switcher for multi-store MSPs
- ✅ **Modern Authentication**: Secure login with forced password changes and session management
- ✅ **Company Settings**: Configurable rates, tax settings, logo, and business information

### User Interface
- ✅ **Modern Bootstrap 5.3 UI**: Dark/light mode support with system theme detection
- ✅ **Responsive Design**: Clean, professional interface that works on all devices
- ✅ **Location Context**: Always-visible location switcher for proper business operations
- ✅ **Role-Based Navigation**: Menu items show/hide based on user permissions

### Database Architecture
- ✅ **Clean v2 Schema**: Proper relationships with auto-generated ticket/invoice numbers
- ✅ **Multi-Location Tables**: Location-based customer and user assignments
- ✅ **Permission System**: JSON-based granular permissions per user
- ✅ **Auto-Numbering**: TKT000001, INV-000001 format with database triggers

## System Modules

### ✅ User Management
- Create/edit users with custom permission sets
- Role-based defaults (Admin/Manager/Technician)
- Location access control per user
- Password management and forced changes

### ✅ Location Management
- Multi-location support for MSPs with multiple offices
- Location-specific tax rates and addresses
- User assignment to locations with access control
- Location switcher for proper business context

### ✅ Customer Management
- Business and individual customer support
- Location-based customer assignments
- Contact management and billing information

### ✅ Asset Management
- Equipment tracking per customer
- Asset categories and status management
- Location and customer relationships

### ✅ Ticket System
- Auto-generated ticket numbers
- Status tracking and priority management
- Technician assignment and time tracking

### ✅ Project Management
- Project organization with ticket relationships
- Status tracking and budget management
- Customer and location context

### ✅ Invoice System
- Auto-generated invoice numbers
- Integration with ticket system
- Customer and location-based billing

### ✅ Reporting System
- Dashboard metrics and overviews
- Revenue and activity tracking

## Technical Stack

- **Backend**: PHP 7.4+ with PDO MySQL
- **Frontend**: Bootstrap 5.3 with dark/light mode
- **Database**: MySQL 5.7+ with proper foreign keys
- **Authentication**: Session-based with role permissions
- **Architecture**: MVC pattern with clean routing

## Security Features

- Role-based access control with granular permissions
- Location-based data access restrictions
- Secure password storage and forced changes
- Session management with proper logout
- Input validation and SQL injection protection

## Business Model Support

- Monthly support contracts with flexible pricing
- Multi-location MSP operations
- Technician location assignments
- Proper business context for all operations
- Asset and customer management per location

## Installation Requirements

- Ubuntu server with Apache/PHP/MySQL
- PHP 7.4+ with PDO MySQL extension
- MySQL 5.7+ with trigger support
- Bootstrap 5.3 (CDN)
- Modern web browser with JavaScript

## Development Workflow

- Development in `/home/appligeeks/projects/SupporTracker/`
- Production deployment to `/var/www/html/SupporTracker/`
- Git-based version control with branch management
- Backup system integrated with development directory

## Next Steps

1. **Add First Customer** - Start using the customer management system
2. **Asset Tracking** - Add customer equipment and devices
3. **Ticket Management** - Begin tracking support requests
4. **Invoice Generation** - Create bills from completed work
5. **User Training** - Set up additional technicians and managers

---

**SupportTracker v2.0** - Complete MSP management solution with modern architecture and multi-location support.