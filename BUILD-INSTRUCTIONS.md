# Build Instructions for Tailwind CSS

## Setup

1. Install Node.js (if not already installed): https://nodejs.org/

2. Install dependencies:
```bash
npm install
```

## Building CSS

### Development (watch mode):
```bash
npm run watch:css
```

### Production (minified):
```bash
npm run build:css
```

This will compile Tailwind CSS from `src/input.css` and output to `assets/styles.css`.

## Deployment

After building the CSS:

1. Make sure `assets/styles.css` is generated
2. Upload all files to Hostinger (including the compiled `assets/styles.css`)
3. The site will use the compiled CSS instead of the CDN

## Note

- The CDN version has been removed from `includes/header.php`
- You must run `npm run build:css` before deploying to production
- The compiled CSS file (`assets/styles.css`) should be committed to your repository
- If you update Tailwind classes, rebuild the CSS file

