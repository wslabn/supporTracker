#!/bin/bash
# SupportTracker Deployment Script
echo "Deploying SupportTracker from dev to web server..."

# Copy all files except config.php
rsync -av --exclude='config.php' --exclude='.git' --exclude='deploy.sh' . /var/www/html/SupporTracker/

# Copy config and update credentials
cp config.example.php /var/www/html/SupporTracker/config.php
sed -i "s/DB_PASS', '');/DB_PASS', '3Ga55ociates1nc!');/" /var/www/html/SupporTracker/config.php

echo "Deployment complete!"