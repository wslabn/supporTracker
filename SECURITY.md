# Security Setup Instructions

## Initial Setup

1. **Copy configuration file:**
   ```bash
   cp config.example.php config.php
   ```

2. **Update config.php with your values:**
   - Database credentials
   - Generate secure encryption key: `openssl rand -hex 32`
   - Set your domain/IP address
   - Configure SMTP settings

3. **Set proper file permissions:**
   ```bash
   chmod 600 config.php
   chmod 755 uploads/
   ```

## Security Notes

- `config.php` is excluded from git for security
- Change default passwords after setup
- Use strong encryption keys (32+ characters)
- Enable HTTPS in production
- Regularly update database passwords
- Backup encryption keys securely

## Database Security

- Create dedicated database user with minimal privileges
- Use strong passwords (12+ characters)
- Limit database access to localhost only
- Regular security updates

## Production Deployment

- Enable HTTPS/SSL
- Set secure session settings
- Configure proper error reporting
- Set up regular backups
- Monitor access logs