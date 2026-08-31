# Front Page Setup Instructions

## Steps to Set Up Static Front Page

1. **Log into WordPress Admin**
   - Navigate to: http://localhost:8888/wp-admin
   - Log in with your admin credentials

2. **Create a New Page for the Homepage**
   - Go to Pages → Add New
   - Title: "Home" (or "Homepage")
   - You can leave the content blank for now
   - Click "Publish"

3. **Set Static Front Page**
   - Go to Settings → Reading
   - Under "Your homepage displays", select "A static page"
   - For "Homepage", select the "Home" page you just created
   - For "Posts page", you can leave it as is or select "Select" to disable
   - Click "Save Changes"

4. **Verify the Front Page**
   - Visit http://localhost:8888
   - You should now see the custom homepage template with:
     - Hero section with "One More Tree" heading
     - About section with mission statement
     - Impact section with images
     - Call to action section

## Troubleshooting

### If you still see the blog posts:
- Clear your browser cache
- Check that the theme is activated (Appearance → Themes)
- Verify the front-page.php file exists in the theme directory

### If styling is still broken:
- Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
- Check browser DevTools (F12) to see if CSS is loading
- Verify the compiled CSS file exists: `/wp-content/themes/one-more-tree/assets/css/main.css`

### If images are not loading:
- Check that images were copied to `/wp-content/themes/one-more-tree/assets/images/`
- Verify file permissions on the images directory

## Next Steps

Once the front page is displaying correctly with proper styling, we can proceed to Phase 2: React to ACF Blocks migration.
