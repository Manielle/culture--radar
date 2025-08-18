#!/bin/bash

echo "🔍 Railway Deployment Checklist"
echo "==============================="
echo ""

# Check current branch
echo "📌 Current Git Branch:"
git branch --show-current
echo ""

# Check for required files
echo "📁 Required Files:"
files=("railway.json" "Dockerfile.railway" "config.php" "config-railway.php" "index.php" "setup-database.php")
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file exists"
    else
        echo "  ❌ $file MISSING!"
    fi
done
echo ""

# Check environment file
echo "🔧 Environment Configuration:"
if [ -f ".env.railway" ]; then
    echo "  ✅ .env.railway exists"
    grep -E "MYSQL|DB_" .env.railway | head -5
else
    echo "  ❌ .env.railway missing"
fi
echo ""

# Git status
echo "📦 Git Status:"
git status --short
echo ""

echo "🚀 Next Steps:"
echo "1. Commit all changes: git add -A && git commit -m 'Fix database connection'"
echo "2. Push to Railway: git push origin essai-safe"
echo "3. Check Railway logs for connection status"
echo "4. Visit: https://ias-b3-g7-paris.up.railway.app/test-db-connection.php"
echo ""
echo "⚠️  Important: Update Railway environment variables:"
echo "   MYSQLHOST=centerbeam.proxy.rlwy.net"
echo "   MYSQLPORT=48330"
echo "   Remove all \${{}} template syntax!"