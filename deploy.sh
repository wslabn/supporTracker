#!/bin/bash
# SupportTracker Deployment Script
echo "Deploying SupportTracker from dev to web server..."

# Copy all files except config.php
rsync -av --exclude='config.php' --exclude='.git' --exclude='deploy.sh' . /var/www/html/SupporTracker/

# Update web server config with real credentials
sed 's/DB_PASS.*$/DB_PASS, '\''3Ga55ociates1nc!'\'');/' config.example.php > /var/www/html/SupporTracker/config.php

echo "Deployment complete!"
