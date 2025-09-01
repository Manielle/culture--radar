# Culture Radar - User Registration Flow

## Overview
The registration process is split into two distinct phases to improve user experience:

1. **Registration** (`register.php`) - Create account
2. **Onboarding** (`onboarding.php`) - Set preferences

## Detailed Flow

### Phase 1: Registration (`register.php`)

**Purpose:** Create a new user account

**Process:**
1. User fills in basic information:
   - First Name + Last Name
   - Email address
   - Password (with confirmation)
   - Optional: City location
   - Optional: Select initial preferences
   - Accept terms and conditions

2. On successful registration:
   - Account is created in database
   - User is automatically logged in
   - User is redirected to `onboarding.php`

**Key Functions:**
- Form validation (client-side)
- Password strength checker
- API call to `/api/auth.php?action=register`
- Auto-login after registration

### Phase 2: Onboarding (`onboarding.php`)

**Purpose:** Collect user preferences for personalized recommendations

**Access Control:**
- Only accessible to logged-in users
- Automatically skipped if already completed
- Cannot be accessed before registration

**Process:**
1. **Step 1: Welcome**
   - Introduction to Culture Radar
   - Overview of features

2. **Step 2: Cultural Preferences**
   - Select at least 3 cultural interests
   - Options: Music, Theatre, Art, Cinema, etc.

3. **Step 3: Location**
   - Specify city/area for local events
   - Used for proximity-based recommendations

4. **Step 4: Budget**
   - Set budget preference (Free, Low, Medium, High)
   - Helps filter events by price

5. **Step 5: Notifications**
   - Configure notification preferences
   - Email, push notifications, etc.

6. On completion:
   - Preferences saved to user profile
   - Onboarding marked as complete
   - User redirected to dashboard

**Key Functions:**
- Multi-step form navigation
- Progress tracking
- Skip option for non-mandatory steps
- Form submission to same page (POST)

## Database Structure

### Users Table
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email
- `password` - Hashed password
- `onboarding_completed` - Boolean flag
- `created_at` - Registration timestamp

### User Profiles Table
- `user_id` - Foreign key to users
- `preferences` - JSON array of cultural interests
- `location` - User's city/area
- `budget_max` - Maximum budget preference
- `notification_settings` - JSON notification preferences
- `onboarding_completed` - Profile completion flag

## Navigation Logic

### For New Users:
1. Visit site → `index.php`
2. Click "Register" → `register.php`
3. Complete registration → Auto-redirect to `onboarding.php`
4. Complete onboarding → Redirect to `dashboard.php`

### For Existing Users:
1. Visit site → `index.php`
2. Click "Login" → `login.php`
3. If onboarding not completed → Redirect to `onboarding.php`
4. If onboarding completed → Redirect to `dashboard.php`

### Protection Mechanisms:

**register.php:**
- If already logged in → Redirect to dashboard

**onboarding.php:**
- If not logged in → Redirect to login
- If onboarding completed → Redirect to dashboard

**dashboard.php:**
- If not logged in → Redirect to login
- If onboarding not completed → Suggest completing profile

## Common Issues & Solutions

### Issue: "Both pages have the same function"
**Solution:** They serve different purposes:
- `register.php` creates the account
- `onboarding.php` collects preferences
- No duplicate functions between them

### Issue: Can't access onboarding
**Cause:** Not logged in or already completed
**Solution:** Check login status and onboarding flag

### Issue: Stuck in redirect loop
**Cause:** Onboarding flag not properly set
**Solution:** Check database flags and session state

## Testing the Flow

1. **Test New Registration:**
   ```
   1. Go to /register.php
   2. Fill form with test data
   3. Submit → Should redirect to /onboarding.php
   4. Complete onboarding → Should reach /dashboard.php
   ```

2. **Test Existing User:**
   ```
   1. Go to /login.php
   2. Login with existing account
   3. If onboarding done → Dashboard
   4. If not → Onboarding page
   ```

3. **Test Protection:**
   ```
   1. Try accessing /onboarding.php without login
      → Should redirect to login
   2. Try accessing /register.php while logged in
      → Should redirect to dashboard
   ```

## API Endpoints

### Registration
- **Endpoint:** `/api/auth.php?action=register`
- **Method:** POST
- **Required Fields:**
  - name
  - email
  - password
  - confirmPassword

### Login
- **Endpoint:** `/api/auth.php?action=login`
- **Method:** POST
- **Required Fields:**
  - email
  - password

### Update Preferences (Onboarding)
- **Endpoint:** `/api/auth.php?action=update_preferences`
- **Method:** POST
- **Required Fields:**
  - categories (array)
  - location
  - budget_max
  - notification_enabled

## Summary

The two-phase approach separates concerns:
- **Registration** handles account creation and security
- **Onboarding** focuses on user experience and personalization

This separation allows for:
- Cleaner code organization
- Better user experience
- Optional onboarding (can be skipped)
- Progressive information collection
- Easier testing and maintenance