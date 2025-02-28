# Imagine Page Builder

A modern, Vue.js-based page builder for WordPress that provides an intuitive drag-and-drop interface for creating custom page layouts without writing code.

![Imagine Page Builder](assets/imagine-banner.png)

## Features

- **Modern Vue.js Architecture**: Built with Vue 3 and Pinia for state management
- **Drag-and-Drop Interface**: Create complex layouts with simple drag-and-drop operations
- **Block-Based Design**: Create pages using pre-designed blocks (headers, paragraphs, and more)
- **WordPress Integration**: Seamlessly integrates with WordPress admin area
- **Responsive Design**: All created pages are fully responsive out of the box
- **Modern Development Stack**: Uses Vite 5.4+, Sass with modern API, and ES modules

## Requirements

- WordPress 5.9+
- PHP 7.4+
- Node.js 16+ (for development only)
- Modern browser support (Chrome, Firefox, Safari, Edge)

## Installation

### As a WordPress Plugin (End Users)

1. Download the plugin ZIP file from the [releases page](https://github.com/imagewize/imagine/releases)
2. In your WordPress admin, navigate to Plugins > Add New > Upload Plugin
3. Upload the ZIP file and click "Install Now"
4. Activate the plugin
5. Access the builder by clicking "Edit with Imagine" on any post or page

### For Development

1. Clone this repository
```
git clone https://github.com/imagewize/imagine.git
cd imagine
```

2. Install dependencies using Yarn
```
yarn install
```

3. Start the development server
```
yarn dev
```

4. Build for production
```
yarn build
```

5. To test in WordPress, symlink or copy the built plugin to your WordPress plugins directory
```
ln -s /path/to/imagine /path/to/wordpress/wp-content/plugins/
```

## GitHub Actions Workflow

This project uses GitHub Actions to automate builds, tests, and releases:

1. When code is pushed to the `main` branch, the CI workflow runs:
   - Verifies that the `yarn.lock` file is unchanged
   - Builds the project using `yarn build`
   - Runs linting using `yarn lint`
2. When a new tag is created (e.g., `v1.0.0`), the release workflow:
   - Builds the frontend assets
   - Packages the plugin as a ZIP file
   - Creates a GitHub release with the ZIP attached

You can find the workflow configuration in the `.github/workflows` directory.

## Development Workflow

This project uses a protected main branch workflow:

1. The `main` branch is protected and cannot be pushed to directly
2. All development happens in feature branches
3. Changes are submitted via Pull Requests to main
4. CI runs automatically on all PRs to validate the build
5. At least one code review approval is required
6. When approved and CI passes, the PR can be merged

### Creating a New Feature

```bash
# Create a new branch from main
git checkout main
git pull
git checkout -b feature/awesome-new-feature

# Make your changes and commit
git add .
git commit -m "Add awesome new feature"

# Push to GitHub
git push -u origin feature/awesome-new-feature
```

Then create a Pull Request on GitHub from your feature branch to main.

## Usage Guide

### Creating a New Page with Imagine

1. Create a new page/post in WordPress
2. Click the "Edit with Imagine" button
3. Use the sidebar to add blocks to your page
4. Customize blocks using the options panel
5. Drag blocks to reorder them
6. Click "Save Changes" to publish your page

### Available Blocks

- **Header Block**: Create headings with customizable levels (H1-H6)
- **Paragraph Block**: Add text content with formatting options

## Development

### Project Structure

```
imagine/
├── .github/workflows/     # GitHub Actions workflows
├── dist/                  # Compiled assets (generated)
├── includes/              # PHP classes
│   ├── class-imagine-admin.php
│   ├── class-imagine-blocks.php
│   └── class-imagine-editor.php
├── src/                   # Frontend source code
│   ├── components/        # Vue components
│   │   ├── blocks/        # Block-specific components
│   │   └── options/       # Block options components
│   ├── stores/            # Pinia stores
│   ├── styles/            # Sass styles
│   ├── admin.js           # Admin panel entry
│   └── main.js            # Editor entry
├── .gitignore             # Git ignore file
├── .nvmrc                 # Node version file
├── imagine-page-builder.php  # Main plugin file
├── package.json           # NPM/Yarn package configuration
├── vite.config.js         # Vite build configuration
└── README.md              # This file
```

### Architecture

Imagine uses a modern frontend stack:

- **Vue 3.4+**: Component-based UI framework with Composition API
- **Pinia**: State management for Vue
- **Vite 5.4+**: Modern build tool with HMR and optimized builds
- **Sass**: CSS preprocessor using the modern module API

The build process generates optimized assets that are included in the WordPress plugin. The PHP portion handles WordPress integration, block rendering, and data persistence.

### Modern Sass Usage

This project uses the modern Sass module system:

```scss
// In component .vue files
<style lang="scss">
/* Variables are globally imported with namespaces */
@use "@/styles/_variables.scss" as vars;

.component {
  color: vars.$color-primary;
  padding: vars.$spacing-md;
}
</style>
```

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests and ensure code quality
5. Commit your changes (`git commit -m 'Add some amazing feature'`)
6. Push to your branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## License

Imagine Page Builder is licensed under the MIT License. See the LICENSE file for details.
