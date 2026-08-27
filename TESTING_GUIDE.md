# Obsidian Forms - Frontend Rendering Testing Guide

This guide will help you test the new frontend form rendering and submission functionality.

## Prerequisites

1. Build the blocks: `npm run build`
2. Ensure the plugin is activated
3. The database tables should be created automatically on first load

## Testing Standard POST Submission

1. **Create a Test Form:**
   - Go to Obsidian Forms > Add Form
   - Add a form title (e.g., "Contact Form")
   - Add field groups and fields (text, email, textarea, etc.)
   - Mark some fields as required
   - Save the form

2. **Insert Form on a Page:**
   - Create or edit a page
   - Add the Obsidian Form block
   - Select your test form
   - Publish the page

3. **Test Form Submission:**
   - View the page on the frontend
   - Fill out the form
   - Click Submit
   - You should be redirected back with a success message
   - Check the database for the entry in `wp_obsidian_form_entries` and `wp_obsidian_form_entry_meta`

4. **Test Validation:**
   - Try submitting with empty required fields
   - Try submitting with invalid email
   - Verify error messages appear

## Testing AJAX Submission

1. **Enable AJAX:**
   - Forms have AJAX enabled by default via `data-ajax="true"` attribute

2. **Test AJAX Submission:**
   - Fill out the form
   - Open browser console to monitor requests
   - Click Submit
   - The page should NOT reload
   - Success message should appear inline
   - Form should reset
   - Check console for any errors

3. **Test AJAX Validation:**
   - Submit with invalid data
   - Errors should appear without page reload
   - Field-specific errors should display next to fields

## Testing Form Settings

1. **Configure Form Settings:**
   - Edit your form in the admin
   - Look for the Form Settings panel in the sidebar
   - Configure:
     - Submit button text
     - Success message
     - Error message
     - Redirect URL (optional)
     - Email notifications

2. **Test Settings:**
   - Verify submit button shows custom text
   - Submit form and check custom success message
   - Test redirect URL (if set)

## Testing Email Notifications

1. **Configure Email Settings:**
   - Edit form settings
   - Enable email notifications
   - Set recipient email
   - Customize subject and message
   - Use merge tags: `{field:field_name}`, `{form_title}`, `{all_fields}`

2. **Test Emails:**
   - Submit the form
   - Check the recipient inbox
   - Verify merge tags are replaced correctly
   - Check that all fields are displayed in `{all_fields}`

## Testing Hooks and Filters

### Form-Level Hooks

```php
// Modify form classes
add_filter( 'obsidian_forms_form_classes', function( $classes, $form ) {
    $classes[] = 'custom-form-class';
    return $classes;
}, 10, 2 );

// Customize submit button
add_filter( 'obsidian_forms_submit_button', function( $html, $form, $settings ) {
    return '<button type="submit">Custom Button</button>';
}, 10, 3 );

// Before form submission
add_action( 'obsidian_forms_before_submission', function( $form_id, $data ) {
    error_log( 'Form submission started for form ' . $form_id );
}, 10, 2 );

// After successful submission
add_action( 'obsidian_forms_after_submission', function( $entry_id, $form_id, $data ) {
    error_log( 'Entry ' . $entry_id . ' created successfully' );
}, 10, 3 );
```

### Field-Level Hooks

```php
// Custom field validation
add_filter( 'obsidian_forms_validate_field_email', function( $is_valid, $value, $field ) {
    // Add custom email validation
    if ( strpos( $value, '@example.com' ) !== false ) {
        $field->add_error( 'Example.com emails are not allowed' );
        return false;
    }
    return $is_valid;
}, 10, 3 );

// Modify field rendering
add_filter( 'obsidian_forms_field_render_text', function( $html, $field, $settings ) {
    // Return custom HTML for text fields
    return null; // or return custom HTML
}, 10, 3 );
```

## Testing Rate Limiting

1. **Test Rate Limit:**
   - Submit the same form multiple times rapidly
   - After 5 submissions (default), you should be rate limited
   - Wait 5 minutes for the limit to reset

2. **Customize Rate Limit:**
   ```php
   // Allow 10 submissions per 10 minutes
   add_filter( 'obsidian_forms_rate_limit_max_attempts', function() {
       return 10;
   } );
   
   add_filter( 'obsidian_forms_rate_limit_time_window', function() {
       return 600; // 10 minutes
   } );
   ```

## Testing Different Field Types

Create a form with all field types:

- Text
- Email
- Textarea
- Select
- Checkbox (multiple)
- Radio
- Tel (phone)
- URL
- Number
- Hidden

Test that each:
- Renders correctly
- Validates correctly
- Saves data properly
- Displays in email notifications

## Checking Database Entries

1. **View Entries:**
   ```sql
   SELECT * FROM wp_obsidian_form_entries ORDER BY created_at DESC;
   SELECT * FROM wp_obsidian_form_entry_meta WHERE entry_id = <entry_id>;
   ```

2. **Verify Data:**
   - Form ID is correct
   - IP address is captured
   - User ID is captured (if logged in)
   - Status is 'unread'
   - All field values are saved in meta table

## Common Issues

### Forms Not Submitting

- Check browser console for JavaScript errors
- Verify nonce is being generated (check page source)
- Check REST endpoint is accessible: `/wp-json/obsidian-forms/v1/submit`

### Email Not Sending

- Verify email notifications are enabled in form settings
- Check recipient email is valid
- Test WordPress email with a plugin like "Check Email"
- Check server email logs

### AJAX Not Working

- Verify `window.obsidianFormsData` is defined (check console)
- Check for JavaScript errors
- Verify REST API is enabled
- Ensure nonce is valid

### Database Errors

- Check that tables were created: `wp_obsidian_form_entries` and `wp_obsidian_form_entry_meta`
- Manually run the Database::create_tables() method if needed
- Check WordPress debug logs for SQL errors

## Next Steps

After testing, you may want to:

1. Build an admin interface to view entries
2. Add export functionality (CSV, PDF)
3. Add conditional logic for fields
4. Add field types (file upload, date picker, etc.)
5. Add integrations (Mailchimp, Zapier, etc.)
6. Add spam protection (reCAPTCHA, honeypot)

