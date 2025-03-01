const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
// Note: We're removing the WebpackManifestPlugin since we no longer need to generate the asset file

module.exports = {
  mode: 'production',
  entry: {
    'imagine-editor': ['./src/editor.js', './src/editor.css'],
    'imagine-admin': './src/admin.js',
    'imagine-blocks': './src/blocks.css'  // Add frontend styles for block rendering
  },
  output: {
    path: path.resolve(__dirname, 'dist/assets'),
    filename: '[name].js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
            plugins: [
              // Add Vue-specific Babel transforms if needed
              '@babel/plugin-proposal-class-properties',
              '@babel/plugin-transform-runtime'
            ]
          }
        }
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader'
        ]
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: 'sass-loader',
            options: {
              sassOptions: {
                quietDeps: true, // Suppress deprecation warnings
              },
              additionalData: `@use "sass:color";`,
              webpackImporter: false,
            },
          },
        ],
      },
      // Add support for images/icons used in the editor
      {
        test: /\.(png|jpg|gif|svg)$/,
        type: 'asset/resource',
        generator: {
          filename: 'images/[name][ext]'
        }
      }
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: '[name].css',
    })
    // Removed WebpackManifestPlugin
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
      'vue$': 'vue/dist/vue.esm.js'
    },
    extensions: ['.js', '.vue', '.json', '.css']
  },
  optimization: {
    // Optimize for production
    minimize: true,
    splitChunks: {
      cacheGroups: {
        vendors: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendors',
          chunks: 'all'
        }
      }
    }
  },
  performance: {
    hints: false  // Disable size warnings
  },
  // Add source maps in development mode
  devtool: process.env.NODE_ENV === 'development' ? 'source-map' : false,
  // Optional: externalize WordPress dependencies
  externals: {
    // These libraries should be provided by WordPress
    'jquery': 'jQuery',
    'react': 'React',
    'react-dom': 'ReactDOM',
  }
};
