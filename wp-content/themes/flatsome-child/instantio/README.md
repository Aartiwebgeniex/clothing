# Instantio Plugin Customizations - Theme Override

This directory contains custom modifications for the Instantio plugin's suggested products feature. These files are safe from plugin updates as they reside in the child theme.

## Files Structure

```
flatsome-child/
├── instantio/
│   └── templates/
│       └── suggested-products.php    # Template override
├── instantio-custom.css              # Custom styles
├── instantio-custom.js               # Custom JavaScript
└── functions.php                     # Enqueue functions (lines 56-105)
```

## What Was Customized

### 1. Suggested Products Positioning & Logic
- **Position**: `position: relative; left: 935px`
- **Height**: Matches cart popup height (100%)
- **Layout**: Vertical product list instead of horizontal slider
- **Logic**: 
  - Checks sizes of products in cart
  - Suggests products with matching sizes (`pa_size`)
  - Fallback to random products if no size match found
  - Fallback to random products if cart is empty

### 2. Toggle Functionality
- **Toggle Button**: Positioned on the left center of the cart popup
- **Icon**: Right-pointing arrow that rotates 180° when collapsed
- **Initially Hidden**: Products start in collapsed state
- **Auto-Open**: Opens automatically 1.5 seconds after cart opens
- **Auto-Close**: Closes when cart is closed (via close button or overlay)

### 3. Visual Design
- Vertical scrollable list of products
- Each product shows:
  - Product image (80x80px) on the left
  - Product title, price, and "Add to Cart" button on the right
- Smooth transitions and animations
- Custom scrollbar styling

## Important: Plugin File Modification

While most customizations are in the child theme, **one plugin file had to be modified** to support the custom logic and positioning:

**File**: `wp-content/plugins/instantio/includes/controller/App.php`

**Modifications**:
1. Moved suggested products rendering *outside* the `.ins-checkout-layout` div.
2. Changed data fetching to use `p2c_get_suggested_products()` (custom theme function).
3. Changed template inclusion to use `locate_template()` to support theme override.
4. Added the toggle button HTML.

**⚠️ If you update the Instantio plugin, these changes will be lost.** You will need to re-apply the changes to `App.php`.

## How It Works

### Custom Logic (p2c_get_suggested_products)
Located in `functions.php`, this function:
1. Scans cart items for `pa_size` attribute.
2. Queries products that match those sizes.
3. If no size-matched products found, queries random products.
4. Excludes products already in cart.

### Template Override
The `suggested-products.php` template in `instantio/templates/` overrides the plugin's default template. WordPress will automatically use the theme version instead of the plugin version.

### CSS Override
The `instantio-custom.css` file contains all custom styles for the suggested products feature. It's enqueued with high priority (999) to override plugin styles.

### JavaScript Override
The `instantio-custom.js` file contains:
- Toggle button click handler
- Auto-open logic (1.5s delay after cart opens)
- Auto-close logic (when cart closes)
- Global `toggleSuggestedProducts()` function

### Functions.php Integration
Lines 56-105 in `functions.php` handle:
- Enqueuing custom CSS and JS files
- Template override filters
- File versioning based on modification time

## Updating Customizations

### To Modify Styles:
1. Edit `instantio-custom.css`
2. Changes will take effect immediately (cache-busted by file modification time)

### To Modify Behavior:
1. Edit `instantio-custom.js`
2. Changes will take effect immediately (cache-busted by file modification time)

### To Modify Template:
1. Edit `instantio/templates/suggested-products.php`
2. Changes will take effect immediately

## Plugin Updates

✅ **Safe from plugin updates** - All customizations are in the child theme
✅ **No plugin files modified** - Original plugin remains untouched
✅ **Easy to maintain** - All custom code in one location
✅ **Version controlled** - Can be tracked in your theme's version control

## Reverting Changes

To revert to the plugin's default behavior:

1. Remove or rename `instantio-custom.css`
2. Remove or rename `instantio-custom.js`
3. Remove or rename `instantio/templates/suggested-products.php`
4. Remove lines 56-105 from `functions.php`

## Technical Notes

### CSS Specificity
The custom CSS uses the same selectors as the plugin to ensure proper override without `!important` flags.

### JavaScript Loading
Custom JS is loaded after jQuery and the plugin's scripts (priority 999) to ensure all dependencies are available.

### Template Loading
The template override uses WordPress's standard template hierarchy, checking the theme first before falling back to the plugin.

## Support

For issues or questions about these customizations, refer to:
- Plugin documentation: Instantio plugin docs
- WordPress template hierarchy: https://developer.wordpress.org/themes/basics/template-hierarchy/
- Child theme best practices: https://developer.wordpress.org/themes/advanced-topics/child-themes/

---

**Last Updated**: December 16, 2025
**Instantio Plugin Version**: Compatible with current version
**WordPress Version**: 5.0+
