INSTANTIO PLUGIN CUSTOMIZATION BACKUPS
========================================
Created: December 16, 2025

This folder contains backups of files that were modified to implement the "Suggested Products Sidebar" feature.

FILES INCLUDED:
--------------

1. App.php
   - Origin: wp-content/plugins/instantio/includes/controller/App.php
   - Changes: 
     - Moved suggested products container outside the main cart layout.
     - Added toggle button HTML.
     - Changed logic to use custom 'p2c_get_suggested_products' function.
     - Changed template loading to use 'locate_template' for theme override.

2. _cart-contents.scss
   - Origin: wp-content/plugins/instantio/assets/app/sass/components/_cart-contents.scss
   - Changes:
     - CSS for positioning the sidebar (left: 935px).
     - CSS for the toggle button.
     - CSS for vertical product layout.

3. instantio-script.js
   - Origin: wp-content/plugins/instantio/assets/app/js/instantio-script.js
   - Changes:
     - Added toggle button click handler.
     - Added auto-open logic (1.5s delay).
     - Added auto-close logic.

4. suggested-products.php
   - Origin: wp-content/plugins/instantio/includes/templates/suggested-products.php
   - Changes:
     - Added 'collapsed' class by default.
     - Removed internal header toggle (arrow).

5. functions.php
   - Origin: wp-content/themes/flatsome-child/functions.php
   - Changes:
     - Added 'p2c_get_suggested_products' function (Size-based/Random logic).
     - Added 'p2c_enqueue_instantio_custom' function.
     - Added 'p2c_instantio_template_override' filter.

RESTORATION INSTRUCTIONS:
------------------------
If the Instantio plugin is updated, the plugin files (1-4) will be overwritten and your changes will be lost.
To restore functionality:

1. Compare the new plugin files with these backups.
2. Re-apply the changes to the new plugin files.
   (Do not just overwrite the new files with backups, as the plugin update might contain other important changes).

The 'functions.php' file is in your child theme and is safe from plugin updates, but is included here for reference.
