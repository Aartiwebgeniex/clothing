# Child Theme Refactoring Outline

## Overview
This document outlines code quality improvements, optimizations, and best practice suggestions for the child theme. All suggestions maintain current functionality while improving code maintainability, performance, and WordPress best practices.

---

## 1. functions.php

### 1.1 Enqueue Scripts & Styles
**Issues:**
- Mixed versioning strategy (hardcoded versions + `filemtime()`)
- Inconsistent version numbers (e.g., `v=34` vs `v=216`)
- Duplicate `get_stylesheet_directory_uri()` calls
- Missing dependency checks

**Suggestions:**
- Standardize on `filemtime()` for all assets (remove hardcoded versions)
- Cache `get_stylesheet_directory_uri()` in a variable
- Add proper dependency arrays
- Group related enqueues together

**Example:**
```php
// Current: Mixed approach
wp_enqueue_style('custom-css', $stylesheet_directory_uri . '/css/custom.css?v=34', array(), '39');

// Suggested: Consistent filemtime()
$theme_uri = get_stylesheet_directory_uri();
$theme_dir = get_stylesheet_directory();
wp_enqueue_style('custom-css', $theme_uri . '/css/custom.css', array(), filemtime($theme_dir . '/css/custom.css'));
```

### 1.2 Code Organization
**Issues:**
- Functions not grouped by functionality
- No clear separation between hooks and utility functions
- Commented-out code blocks (lines 65, 151, 838, 877-897, 993, 1007-1009, 1126, 1129-1254, 1260, 1293)

**Suggestions:**
- Group functions by feature (enqueues, WooCommerce hooks, cart functions, etc.)
- Remove all commented-out code
- Add section headers with comments
- Move utility functions to separate file if needed

### 1.3 Function Naming & Consistency
**Issues:**
- Inconsistent naming (`my_jquery_enqueue` vs `dequeue_media_css_js`)
- Some functions lack prefix
- Generic names (`remove_protected_text`)

**Suggestions:**
- Use consistent prefix (e.g., `p2c_` for all custom functions)
- Use descriptive names
- Follow WordPress naming conventions

### 1.4 Security & Sanitization
**Issues:**
- Direct `$_COOKIE` access without sanitization (line 94-95)
- Direct `$_SERVER` access (line 21)
- Missing nonce checks in AJAX handlers
- Direct SQL query without `$wpdb->prepare()` (line 51 in cart-addon-product.php)

**Suggestions:**
- Sanitize all user inputs
- Use `wp_unslash()` for cookie values
- Add nonce verification for AJAX
- Use `$wpdb->prepare()` for all queries

### 1.5 Performance Optimizations
**Issues:**
- `add_product_to_cart5()` runs on every cart calculation
- Multiple loops in cart functions
- No caching for ACF field calls

**Suggestions:**
- Add early returns where possible
- Cache ACF field values
- Optimize cart loops (combine where possible)
- Use `wp_cache_get()` for repeated queries

### 1.6 Code Duplication
**Issues:**
- Similar patterns repeated (e.g., ACF field checks)
- Duplicate cart iteration logic

**Suggestions:**
- Extract common patterns into helper functions
- Create reusable utility functions

### 1.7 Specific Function Issues

**`CurrencyConverterCustom()` (lines 89-99)**
- Function name doesn't match functionality (just returns currency)
- Unused `$currencies` array
- Direct cookie access

**`add_product_to_cart5()` (lines 155-227)**
- Very long function (72 lines)
- Multiple responsibilities
- Complex nested logic
- Should be split into smaller functions

**`custom_override_and_filter_checkout_fields()` (lines 242-270)**
- Array manipulation could be cleaner
- Use `array_merge()` more efficiently

**`video_shortcode()` (lines 719-746)**
- Inline script generation (should be enqueued)
- Uses deprecated `extract()`
- No sanitization of `$vid` parameter

**`custom_yoast_breadcrumb()` (lines 1051-1082)**
- String concatenation for HTML (use proper escaping)
- Missing error handling

---

## 2. header.php

### 2.1 Domain Check Function
**Issues:**
- Function defined inline in template
- Logic could be simplified

**Suggestions:**
- Move function to `functions.php`
- Use `wp_parse_url()` instead of manual parsing
- Cache domain check result

**Example:**
```php
// Current: Inline function
function get_domain_name() {
    $host = $_SERVER['HTTP_HOST'];
    $host_parts = explode('.', $host);
    return $host_parts[count($host_parts) - 2];
}

// Suggested: In functions.php with caching
function p2c_get_domain_name() {
    static $domain = null;
    if (null === $domain) {
        $host = wp_parse_url(home_url(), PHP_URL_HOST);
        $parts = explode('.', $host);
        $domain = $parts[count($parts) - 2] ?? '';
    }
    return $domain;
}
```

### 2.2 Template Part Loading
**Issues:**
- Conditional logic in template
- Could be cleaner

**Suggestions:**
- Move conditional to `functions.php` hook
- Use template hierarchy more effectively

---

## 3. footer.php

### 3.1 Inline Styles & Scripts
**Issues:**
- Inline `<style>` blocks (lines 32-38, 46-54, 57-62)
- Inline `<script>` blocks (lines 63-85)
- Hardcoded values

**Suggestions:**
- Move styles to CSS file or enqueue properly
- Move scripts to JS file and enqueue
- Use `wp_add_inline_style()` and `wp_add_inline_script()` if dynamic

### 3.2 jQuery Code
**Issues:**
- jQuery code in PHP template
- `setInterval` without cleanup
- No error handling

**Suggestions:**
- Move all jQuery to separate JS file
- Use proper event delegation
- Add cleanup for intervals

### 3.3 Hardcoded Values
**Issues:**
- Hardcoded year calculation
- Hardcoded class names

**Suggestions:**
- Use WordPress date functions
- Make class names filterable

---

## 4. js/custom.js

### 4.1 File Organization
**Issues:**
- Very long file (636 lines)
- Multiple responsibilities
- No clear structure

**Suggestions:**
- Split into modules by feature
- Group related functions together
- Add section comments

### 4.2 jQuery Best Practices
**Issues:**
- Mixed use of `$` and `jQuery`
- Inconsistent selector caching
- Some inefficient selectors

**Suggestions:**
- Use `jQuery` consistently (WordPress best practice)
- Cache frequently used selectors
- Use more specific selectors

**Example:**
```javascript
// Current: Repeated queries
jQuery('.product').each(function() {
    jQuery(this).find('.title').text();
});

// Suggested: Cache selectors
var $products = jQuery('.product');
$products.each(function() {
    var $title = jQuery(this).find('.title');
    $title.text();
});
```

### 4.3 Code Duplication
**Issues:**
- Duplicate size guide code (lines 65-75, 168-173)
- Similar click handlers repeated
- Duplicate autocomplete code (lines 333-335, 627-629)

**Suggestions:**
- Extract common functionality
- Create reusable functions
- Use event delegation more effectively

### 4.4 Performance Issues
**Issues:**
- Multiple `setTimeout` calls without cleanup
- `setInterval` in `dropdowns_to_bubbles_oncat` (potential memory leak)
- Inefficient DOM queries in loops

**Suggestions:**
- Store timeout IDs for cleanup
- Use `requestAnimationFrame` where appropriate
- Cache DOM queries outside loops

### 4.5 Commented Code
**Issues:**
- Large commented blocks (lines 124-162, 207-211, 247-265, 338-344, 509-544, 602-623)

**Suggestions:**
- Remove all commented code
- Use version control for history

### 4.6 Specific Issues

**`dropdowns_to_bubbles_oncat()` (lines 358-503)**
- Very long function (145 lines)
- Complex nested logic
- Should be split into smaller functions
- Global variable `globalctr` (use closure)

**Size selection handlers**
- Similar code repeated multiple times
- Could use a single delegated handler

**Fancybox initialization**
- Multiple initialization blocks
- Could be consolidated

---

## 5. js/archive.js

### 5.1 Code Organization
**Issues:**
- Functions not clearly separated
- Some duplicate patterns

**Suggestions:**
- Group related functions
- Extract common patterns

### 5.2 Event Handlers
**Issues:**
- Multiple similar click handlers
- Could use more delegation

**Suggestions:**
- Consolidate similar handlers
- Use event delegation where possible

### 5.3 Specific Issues

**`filterProductsByStyle()` (lines 73-88)**
- Could be more efficient
- Consider using CSS classes instead of show/hide

**Size filter logic (lines 49-71)**
- Complex nested logic
- Could be simplified

**Commented code (lines 96-101, 134-138, 177-187, 222-240)**
- Remove commented blocks

---

## 6. js/single-product.js

### 6.1 setInterval Usage
**Issues:**
- `setInterval` without cleanup (lines 203-220)
- Potential memory leak
- Runs indefinitely

**Suggestions:**
- Use event-driven approach instead
- Store interval ID for cleanup
- Or use MutationObserver

**Example:**
```javascript
// Current: setInterval without cleanup
setInterval(function() {
    jQuery(".single_variation_wrap").on("show_variation", ...);
}, 2000);

// Suggested: Event-driven
jQuery(document).on('show_variation', '.single_variation_wrap', function() {
    // Handle variation show
});
```

### 6.2 Code Duplication
**Issues:**
- YouTube player code duplicated (lines 100-133)
- Similar to code in custom.js

**Suggestions:**
- Extract to shared utility function
- Create reusable module

### 6.3 Variable Scope
**Issues:**
- Global `size_change` variable (line 55)
- Could cause conflicts

**Suggestions:**
- Use closure or data attributes
- Namespace variables

### 6.4 Specific Issues

**Size change handlers (lines 57-96)**
- Complex state management
- Could use data attributes instead

**YouTube player (lines 100-133)**
- Duplicate of custom.js code
- Should be extracted to shared file

---

## 7. js/cart-checkout.js

### 7.1 Inline HTML Generation
**Issues:**
- String concatenation for HTML (lines 15-17, 22)
- No escaping
- Hard to maintain

**Suggestions:**
- Use template literals with proper escaping
- Or use WordPress `wp.template` if available
- Consider creating elements with jQuery

**Example:**
```javascript
// Current: String concatenation
var linkHtml = "<div class='c-cart-logo'>...";

// Suggested: jQuery element creation
var $linkHtml = jQuery('<div>', {
    'class': 'c-cart-logo',
    html: jQuery('<span>', { ... })
});
```

### 7.2 Character Limit Functions
**Issues:**
- Similar functions (`calculate_order_notes_char`, `calculate_address_char`, `calculate_address_char2`)
- Code duplication

**Suggestions:**
- Create generic character limit function
- Pass element and limit as parameters

### 7.3 setTimeout Usage
**Issues:**
- Multiple `setTimeout` calls (lines 45, 161)
- No cleanup mechanism

**Suggestions:**
- Use proper event listeners
- Or store timeout IDs for cleanup

### 7.4 Specific Issues

**Cart/Checkout URL logic (lines 3-24)**
- Could be simplified
- Hardcoded URLs

**Character limit handlers**
- Three similar functions
- Should be consolidated

---

## 8. woocommerce/ Folder

### 8.1 Template Overrides
**Issues:**
- Most files are standard WooCommerce overrides
- Some custom code mixed in

**Suggestions:**
- Document which files have custom modifications
- Keep custom code minimal and well-commented

### 8.2 Specific Custom Files

**`global/breadcrumb.php`**
- Custom breadcrumb function call
- Commented-out code block (lines 16-56)
- Should remove commented code

**`cart-addon-product.php`**
- Direct SQL query without `$wpdb->prepare()` (line 51)
- Security risk
- Should use `$wpdb->prepare()`

**`content-single-product.php`**
- Minimal custom code (line 24)
- Just adds product ID to container
- Well done, minimal change

---

## 9. General Best Practices

### 9.1 WordPress Coding Standards
**Issues:**
- Inconsistent spacing
- Mixed quote styles
- Inconsistent indentation

**Suggestions:**
- Follow WordPress PHP Coding Standards
- Use WordPress Code Sniffer
- Consistent quote style (single quotes for PHP)

### 9.2 JavaScript Standards
**Issues:**
- No consistent code style
- Mixed function declarations

**Suggestions:**
- Follow WordPress JavaScript Coding Standards
- Use consistent formatting
- Add JSDoc comments for functions

### 9.3 Performance
**Issues:**
- No lazy loading considerations
- Some heavy operations on page load

**Suggestions:**
- Consider lazy loading for non-critical scripts
- Defer non-critical JavaScript
- Optimize DOM queries

### 9.4 Maintainability
**Issues:**
- Large files
- No clear documentation
- Hard to find specific functionality

**Suggestions:**
- Add file headers with purpose
- Document complex functions
- Consider splitting large files

### 9.5 Security
**Issues:**
- Some user input not sanitized
- Direct database queries
- Missing nonce checks

**Suggestions:**
- Sanitize all inputs
- Escape all outputs
- Add nonce verification
- Use prepared statements

---

## 10. Priority Recommendations

### High Priority
1. Remove all commented-out code
2. Fix security issues (SQL injection, unsanitized inputs)
3. Standardize versioning strategy in enqueue functions
4. Fix `setInterval` memory leaks
5. Sanitize all user inputs

### Medium Priority
1. Split large files into modules
2. Extract duplicate code into functions
3. Move inline styles/scripts to files
4. Optimize cart calculation functions
5. Consolidate jQuery handlers

### Low Priority
1. Improve code organization
2. Add documentation
3. Standardize naming conventions
4. Optimize DOM queries
5. Refactor long functions

---

## Notes
- All suggestions maintain current functionality
- Changes should be tested thoroughly
- Consider implementing incrementally
- Backup before making changes
- Test on staging environment first

