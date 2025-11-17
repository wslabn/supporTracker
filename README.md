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
- [ ] Database schema design
- [ ] Basic PHP framework setup
- [ ] Customer and company management
- [ ] Asset tracking system
- [ ] Basic admin interface

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
├── admin/
│   ├── dashboard.php
│   ├── customers.php
│   ├── assets.php
│   ├── workorders.php
│   └── billing.php
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

## Next Steps
1. **Database Design** - Create schema for customers, assets, work orders, invoices
2. **Basic Framework** - Set up PHP structure and routing
3. **Customer Management** - Build company and asset management
4. **Work Order System** - Implement ticket creation and tracking
5. **Billing Integration** - Add invoice generation and payment processing

## Notes
- Prioritize portability for easy shared hosting migration
- Focus on simplicity and reliability over complex features
- Maintain compatibility with existing business processes
- Plan for future scalability and feature additions