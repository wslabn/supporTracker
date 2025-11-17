# SupportTracker - MSP Customer Management System

A web-based support and billing tracker for managed service providers (MSPs) handling IT support, phone services, and asset management.

## Project Overview

**Business Model:**
- Monthly support contracts with flexible pricing per customer
- Ad-hoc billable work outside contracts
- Bundled services: IT support, Azure backups, Twilio phone service, FreePBX management
- Asset management for business customers (20+ devices per company)
- Check-based payments with online customer portal

## Core Features

### Customer & Asset Management
- [ ] Company profiles with custom monthly contract rates
- [ ] Asset tracking (computers, servers, printers, phones, network equipment)
- [ ] Asset categories and location tracking
- [ ] Secure storage for passwords, notes, and documents per asset
- [ ] Asset history and service records

### Work Order System
- [ ] Admin work order creation (manual entry)
- [ ] Customer portal work order submission (per asset)
- [ ] Work order status tracking and updates
- [ ] Billable vs contract-covered work designation
- [ ] Time tracking and labor costs

### Billing & Invoicing
- [ ] Monthly contract billing automation
- [ ] Ad-hoc work billing integration
- [ ] Customer balance management with aging
- [ ] Partial payment handling (oldest invoice first)
- [ ] Payment processing (check recording, Stripe/PayPal integration)
- [ ] Late payment tracking and automated reminders

### Twilio Service Management
- [ ] Phone number and service tracking per customer
- [ ] Usage monitoring and cost tracking
- [ ] Service bundling with markup pricing
- [ ] FreePBX configuration management

### Customer Portal
- [ ] Invoice viewing and download (PDF)
- [ ] Payment history and account balance
- [ ] Work order submission by asset
- [ ] Service overview and asset list
- [ ] Payment instructions and online payment options

## Technical Requirements

### Development Environment
- [ ] Ubuntu server with Webmin setup
- [ ] PHP 7.4+ and MySQL 5.7+
- [ ] Apache web server configuration
- [ ] Local SSL certificate for HTTPS testing

### Deployment Strategy
- [ ] GitHub repository setup
- [ ] Webhook deployment script (deploy.php)
- [ ] Shared hosting compatibility (portable PHP/MySQL)
- [ ] Environment configuration management
- [ ] Database migration system

### Security & Data Management
- [ ] Secure password storage for customer assets
- [ ] User authentication and session management
- [ ] Role-based access control
- [ ] Data backup and export functionality
- [ ] HTTPS enforcement

## Development Phases

### Phase 1: Core Foundation
- [x] Database schema design
- [x] Basic PHP framework setup
- [x] Customer and company management
- [x] Asset tracking system
- [x] Basic admin interface

### Phase 2: Work Order System
- [ ] Work order creation and management
- [ ] Status tracking and updates
- [ ] Time tracking integration
- [ ] Billable work designation

### Phase 3: Billing System
- [ ] Invoice generation and management
- [ ] Payment processing and recording
- [ ] Customer balance tracking
- [ ] Late payment management

### Phase 4: Customer Portal
- [ ] Customer authentication
- [ ] Invoice and payment viewing
- [ ] Work order submission
- [ ] Account management features

### Phase 5: Service Integration
- [ ] Twilio service tracking
- [ ] Email invoice delivery
- [ ] Automated billing cycles
- [ ] Reporting and analytics

### Phase 6: Deployment & Production
- [ ] GitHub webhook deployment
- [ ] Shared hosting migration
- [ ] SSL certificate setup
- [ ] Production testing and launch

## File Structure
```
/SupporTracker/
├── README.md
├── config/
│   ├── database.php
│   └── settings.php
├── includes/
│   ├── functions.php
│   └── auth.php
├── dashboard.php
├── companies.php
├── company_detail.php
├── employees.php
├── employee_detail.php
├── assets.php
├── asset_detail.php
├── credentials.php
├── search.php
├── portal/
│   ├── login.php
│   ├── dashboard.php
│   ├── invoices.php
│   └── workorders.php
├── api/
│   └── webhook.php
├── assets/
│   ├── css/
│   └── js/
└── deploy.php
```

## Completed Features

### Core System
- ✅ **Database Schema**: Complete 11-table schema with companies, employees, assets, credentials, work orders, invoices, payments
- ✅ **Company Management**: Full CRUD with modal interface, monthly rates, contact info
- ✅ **Employee Management**: Per-company employee tracking with contact details and asset assignments
- ✅ **Asset Management**: Comprehensive asset tracking with auto-incrementing tags, employee assignment, categories
- ✅ **Credential Management**: Encrypted password storage per asset with secure viewing
- ✅ **Global Search**: Search across companies, employees, and assets with modal integration

### Dashboard System
- ✅ **Company Detail Dashboard**: Tabbed interface showing employees, assets, work orders, billing with embedded modals
- ✅ **Asset Detail Dashboard**: Complete asset view with credentials, work orders, and related information
- ✅ **Employee Detail Dashboard**: Employee overview with assigned assets, credentials, and work orders
- ✅ **Cross-linking Navigation**: Clickable names throughout application for seamless navigation

### User Interface
- ✅ **Modern Modal System**: All CRUD operations use modal popups instead of page navigation
- ✅ **Responsive Design**: Clean, professional interface with Bootstrap styling
- ✅ **Consistent Navigation**: Unified header with search functionality across all pages

## Next Steps
1. **Work Order System** - Implement ticket creation and tracking
2. **Billing Integration** - Add invoice generation and payment processing
3. **Twilio Integration** - Phone service tracking and management
4. **Customer Portal** - Client-facing interface for work orders and invoices
5. **Deployment Setup** - GitHub webhook and shared hosting migration

## Notes
- Prioritize portability for easy shared hosting migration
- Focus on simplicity and reliability over complex features
- Maintain compatibility with existing business processes
- Plan for future scalability and feature additions