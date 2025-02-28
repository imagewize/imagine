# Project Cleanup Checklist - Vite 5.4+ Migration

## Completed Changes
- ✅ Upgraded to Vite 5.4+ which supports the modern Sass API
- ✅ Updated package.json with Vue 3.4.0 and other compatible dependencies
- ✅ Modified vite.config.js to use `api: 'modern'` for Sass
- ✅ Restored global `@use "sass:color";` in additionalData to avoid namespace errors
- ✅ Simplified component Sass imports to avoid duplications

## Files Removed (No Longer Needed)
- ✅ `imagine/sass-config.js` - Custom Sass processor (redundant with Vite 5.4+)
- ✅ `imagine/src/styles/scss-modern-api.js` - Legacy workaround
- ✅ `imagine/src/styles/_modern-api.scss` - Old compatibility layer
- ✅ `imagine/.sasshintrc` - Silencing config no longer needed

## Files to Keep
- `imagine/src/styles/_variables.scss` - Core variable definitions
- `imagine/src/styles/main.scss` - Main styles entry point
- `imagine/src/styles/_components.scss` - Component styles
- `imagine/src/styles/_vue-components.scss` - Vue-specific shared styles

## Best Practices Going Forward
1. Component style imports:
   ```scss
   /* Import variables with namespace */
   @use "@/styles/_variables.scss" as vars;
   
   /* No need to import color module as it's in additionalData */
   .component {
     background-color: color.adjust($color-light, $lightness: -5%);
   }
   ```

2. Never use `@import` - always use `@use` with the modern Sass module system

3. Keep the vite.config.js configuration:
   ```javascript
   css: {
     preprocessorOptions: {
       scss: {
         additionalData: `
           @use "sass:color";
           @use "@/styles/_variables.scss" as *;
         `,
         api: 'modern',
         style: 'compressed'
       },
     },
   },
   ```

4. For new components, follow the established pattern that avoids duplicate module imports

## Maintenance Notes
- The project now requires Node.js 18.12.0+ (updated from 16+) as specified in .nvmrc and package.json
- This requirement is due to Yarn 4.6.0 compatibility needs
- Make sure Sass-related dependencies stay on version 1.85.1+ to maintain modern API compatibility
- Vite 5.4+ and Vue 3.4.0+ are now the minimum supported versions
