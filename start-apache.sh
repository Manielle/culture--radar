#!/bin/bash

# Railway startup script for Apache
# Force Apache to listen on port 80

# Use port 80 regardless of Railway's PORT variable
PORT=80

echo "=========================================="
echo "Culture Radar - Starting Apache"
echo "PORT environment variable: $PORT"
echo "=========================================="

# Create a new ports.conf file with the correct port
cat > /etc/apache2/ports.conf << EOF
# Apache ports configuration for Railway
# Automatically configured to use PORT environment variable
Listen 0.0.0.0:${PORT}

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
    ServerName localhost
    DocumentRoot /var/www/html/public
    
    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Allow proxy headers from Railway
    SetEnvIf X-Forwarded-Proto https HTTPS=on
    
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Also update Apache's main config to ensure it listens correctly
cat >> /etc/apache2/apache2.conf << EOF

# Railway specific configuration
ServerName localhost
AcceptFilter http none
AcceptFilter https none
EOF

# Copy to sites-enabled
cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

echo "Apache configured to listen on port $PORT"
echo "DocumentRoot: /var/www/html/public"
echo "Starting Apache in foreground mode..."

# Start Apache in foreground
exec apache2-foreground