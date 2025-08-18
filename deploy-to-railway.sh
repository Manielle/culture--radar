#!/bin/bash

echo "🚀 Preparing Culture Radar for Railway deployment..."

# Check if we're in the right directory
if [ ! -f "index.php" ]; then
    echo "❌ Error: Not in the Culture Radar directory"
    exit 1
fi

# Create Railway-specific configuration
echo "📝 Setting up Railway configuration..."

# Backup current .env if exists
if [ -f ".env" ]; then
    cp .env .env.local.backup
    echo "✅ Backed up local .env to .env.local.backup"
fi

# Copy Railway environment
cp .env.railway .env
echo "✅ Railway environment configured"

# Check for required files
echo "🔍 Checking required files..."
required_files=("railway.json" "Dockerfile.railway" "config.php")
for file in "${required_files[@]}"; do
    if [ ! -f "$file" ]; then
        echo "❌ Missing required file: $file"
        exit 1
    else
        echo "✅ Found: $file"
    fi
done

# Create .htaccess.railway if not exists
if [ ! -f ".htaccess.railway" ]; then
    cat > .htaccess.railway << 'EOF'
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Security headers
Header set X-Frame-Options "DENY"
Header set X-Content-Type-Options "nosniff"
EOF
    echo "✅ Created .htaccess.railway"
fi

# Commit changes
echo "📦 Preparing for deployment..."
git add -A
git commit -m "Configure for Railway deployment"

# Push to Railway
echo "🚂 Deploying to Railway..."
echo ""
echo "Now run these commands:"
echo "1. railway login"
echo "2. railway link"
echo "3. railway up"
echo ""
echo "Or push to GitHub and Railway will auto-deploy:"
echo "git push origin essai-safe"
echo ""
echo "⚠️  Make sure these environment variables are set in Railway dashboard:"
echo "   - MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD"
echo "   - OPENAGENDA_API_KEY, GOOGLE_MAPS_API_KEY, etc."
echo ""
echo "✅ Deployment preparation complete!"