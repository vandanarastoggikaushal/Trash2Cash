# Sync and Version Management

## Overview
The version display and sitemap are now fully dynamic and update automatically based on file modification dates.

## Version Management

### Current Setup
- Version is read from the `VERSION` file
- Displayed dynamically on the homepage (bottom right)
- Automatically updates when `VERSION` file changes

### Updating Version
1. Edit the `VERSION` file with the new version number (e.g., `1.0.1`)
2. The homepage will automatically display the new version
3. No code changes needed!

## Sitemap Management

### Automatic Sitemap Generation
The sitemap is now generated dynamically with actual file modification dates.

### Update Sitemap After Sync
After syncing files to the server, run:

```bash
php generate-sitemap.php
```

This will:
- ✅ Read actual file modification dates
- ✅ Generate `sitemap.xml` with current dates
- ✅ Update all lastmod dates automatically

### Full Sync Process
To sync version and sitemap together:

```bash
php sync-version.php
```

This will:
- ✅ Check/update version
- ✅ Generate sitemap with current file dates
- ✅ Ensure everything is in sync

## Manual Sitemap Update
If you prefer to update sitemap manually, edit `sitemap.xml` directly. However, using `generate-sitemap.php` ensures dates are always accurate.

## Deployment Workflow

1. **Make changes** to your PHP files
2. **Update version** (if needed) in `VERSION` file
3. **Sync files** to server
4. **Run sitemap generator**:
   ```bash
   php generate-sitemap.php
   ```
5. **Upload updated sitemap.xml** to server
6. **Submit to Google Search Console** (if needed)

## Files

- `VERSION` - Version number file (edit to update version)
- `generate-sitemap.php` - Generates sitemap with current file dates
- `sync-version.php` - Syncs version and sitemap together
- `sitemap.xml` - Generated sitemap (auto-updated by script)

## Notes

- Version is read dynamically on every page load
- Sitemap dates reflect actual file modification times
- No manual date entry required
- Everything stays in sync automatically

