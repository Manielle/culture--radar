# Fix Railway Environment Variables

## The Problem
Your Railway variables are using template syntax `${{}}` which creates circular references. This prevents the database from connecting.

## Current Issues:
1. `MYSQLHOST="${{RAILWAY_PRIVATE_DOMAIN}}"` - This is for INTERNAL Railway connections only
2. `MYSQL_PUBLIC_URL` has unresolved variables
3. Your PHP app needs the PUBLIC endpoint, not the internal one

## Solution - Update Your Railway Variables:

### In Railway Dashboard → Your PHP Service → Variables:

**Delete all current MySQL variables and replace with these EXACT values:**

```env
# Database Connection (REQUIRED - Use PUBLIC endpoint)
MYSQLHOST=centerbeam.proxy.rlwy.net
MYSQLPORT=48330
MYSQLDATABASE=railway
MYSQLUSER=root
MYSQLPASSWORD=tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH

# Alternative names (for compatibility)
DB_HOST=centerbeam.proxy.rlwy.net
DB_PORT=48330
DB_NAME=railway
DB_USER=root
DB_PASSWORD=tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH

# Direct connection URL (Railway will use this)
MYSQL_PUBLIC_URL=mysql://root:tBixYXRKGkGAZuyxGHFZzaTxQAGXvJJH@centerbeam.proxy.rlwy.net:48330/railway

# API Keys (keep these as-is)
OPENAGENDA_API_KEY=b6cea4ca5dcf4054ae4dd58482b389a1
OPENWEATHERMAP_API_KEY=4f70ce6daf82c0e77d6128bc7fadf972
GOOGLE_MAPS_API_KEY=AIzaSyClDB39stO1egB2L1P3aRymydGjPZ72uNo
RATP_PRIM_API_KEY=CNcmVauFV8dbo3sMWmemifQah7aopdsf
SERPAPI_KEY=b56aa6ec92f9f569f50f671e5133d46d5131c74c260086c37f5222bf489f2d4d

# Application
APP_ENV=production
APP_DEBUG=false
```

## Why This Works:
- `centerbeam.proxy.rlwy.net:48330` is your MySQL PUBLIC endpoint
- PHP apps MUST use the public endpoint (not `mysql.railway.internal`)
- The internal domain only works for services in the same Railway project

## Steps to Fix:
1. Go to Railway Dashboard
2. Click on your PHP service (not the MySQL service)
3. Go to Variables tab
4. Click "RAW Editor"
5. Delete everything
6. Paste the variables above
7. Click "Update Variables"
8. Railway will auto-redeploy

## To Verify It's Working:
After deployment, your app should connect successfully. Check the logs for:
- "Database connection successful!"
- No PDO connection errors

## Important Notes:
- DO NOT use `${{RAILWAY_PRIVATE_DOMAIN}}` - that's only for internal services
- DO NOT use template syntax `${{}}` in your PHP service variables
- The MySQL service can keep its template variables, but YOUR APP needs resolved values