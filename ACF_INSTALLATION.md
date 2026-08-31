# ACF PRO Installation Instructions

## Important: ACF PRO License Required

To complete the ACF PRO installation, you will need an active ACF PRO license key. If you don't have one, you can purchase it from https://www.advancedcustomfields.com/pro/

## Installation Steps

### 1. Start the WordPress Environment

```bash
cd "/home/nati/Documents/One More Tree WP"
docker-compose up -d
```

Wait for the containers to start (usually 30-60 seconds).

### 2. Access WordPress Admin

1. Open your browser and go to: http://localhost:8888/wp-admin
2. Complete the WordPress installation wizard:
   - Site Title: One More Tree
   - Username: (choose your admin username)
   - Password: (choose a strong password)
   - Email: (your email address)
3. Click "Install WordPress"

### 3. Install ACF PRO

Once logged into WordPress admin:

**Option A: Upload Plugin**
1. Go to Plugins → Add New
2. Click "Upload Plugin"
3. Download ACF PRO from your account at https://www.advancedcustomfields.com/my-account/
4. Upload the zip file
5. Click "Install Now" then "Activate Plugin"

**Option B: Manual Installation**
1. Download ACF PRO from your account
2. Extract the zip file
3. Upload the `advanced-custom-fields-pro` folder to:
   `/home/nati/Documents/One More Tree WP/wp-content/plugins/`
4. Go to Plugins → Installed Plugins
5. Activate "Advanced Custom Fields PRO"

### 4. Activate Your License

1. Go to Custom Fields → Updates
2. Enter your ACF PRO license key
3. Click "Activate License"

## Verification

After installation, verify that:
- ACF PRO appears in your WordPress admin menu
- You can access Custom Fields → Field Groups
- The license is activated (no "inactive" warnings)

## Next Steps

Once ACF PRO is installed and activated, I can proceed with creating the custom ACF blocks that will replicate the Next.js frontend components.
