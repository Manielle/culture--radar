#!/bin/bash

# Railway startup script for Apache
# This script configures Apache to listen on the PORT environment variable

# Get the PORT from environment (Railway provides this)
PORT=${PORT:-8080}

echo "=========================================="
echo "Culture Radar - Starting Apache"
echo "PORT environment variable: $PORT"
echo "=========================================="

# Create a new ports.conf file with the correct port
cat > /etc/apache2/ports.conf << EOF
# Apache ports configuration for Railway
# Automatically configured to use PORT environment variable
Listen ${PORT}

<IfModule ssl_module>
    Listen 443
</IfModule>

<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF

# Update the default VirtualHost configuration
cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:${PORT}>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public
    
    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Copy to sites-enabled
cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

echo "Apache configured to listen on port $PORT"
echo "DocumentRoot: /var/www/html/public"
echo "Starting Apache in foreground mode..."

# Start Apache in foreground
exec apache2-foreground