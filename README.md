# Obsidian Forms

Obsidian Forms is an early-release, block-based form builder for WordPress. Reusable forms are created with native blocks and can be embedded and edited in the context of the block editor.

## Requirements

- WordPress 6.5 or newer
- PHP 7.4 or newer
- Node.js 20 and npm 10 for development

## Development

Install dependencies and build the committed browser assets:

```sh
composer install
npm ci
npm run build
```

Run the available checks with:

```sh
composer lint:php
npm run lint
```

Create an installable release archive after a successful build with:

```sh
npm run plugin-zip
```

## Current beta behavior

- Forms are reusable entities. Editing a linked form updates every embed.
- Copying a form creates an independent reusable form.
- Valid submissions are emailed to the WordPress administration email address by default.
- Developers can change the notification recipient with the `obsidian_forms_notification_recipient` filter and process sanitized submissions with the `obsidian_forms_valid_submission` action.

File uploads, entry storage, conditional logic, and third-party delivery integrations are not included in this beta.
