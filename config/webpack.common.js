const path = require('path');
// const pkg = require('./package');

const UglifyJSPlugin = require("uglifyjs-webpack-plugin");

const hotMiddlewareScript = 'webpack-hot-middleware/client?path=/__webpack_hmr&timeout=20000&reload=true';
// const Conf = require('./conf');
const args = require('yargs').argv;
// const glob = require('glob');
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
// const CopyWebpackPlugin = require('copy-webpack-plugin');
const APP_DIR = path.resolve(__dirname, '../application');

let IS_DEV = process.env.NODE_ENV === 'dev';
let APP_PREFIX = require('../conf').prefix;

/**
 * Webpack Configuration
 *
 **/
const config = {
  context: path.resolve(__dirname, '../application'),
  mode: 'development',
  entry: {
    core: [APP_DIR + '/themes/parentalguidance/modules/core.module.js', hotMiddlewareScript],
    header: [APP_DIR + '/themes/parentalguidance/modules/header/header.module.js', hotMiddlewareScript],
    auth: [APP_DIR + '/themes/parentalguidance/modules/auth/auth.module.js', hotMiddlewareScript],
    reviews_home: [APP_DIR + '/themes/parentalguidance/modules/reviews/home.module.js', hotMiddlewareScript],
    reviews_view: [APP_DIR + '/themes/parentalguidance/modules/reviews/view.module.js', hotMiddlewareScript],
    reviews_create: [APP_DIR + '/themes/parentalguidance/modules/reviews/create.module.js', hotMiddlewareScript],
    footer: [APP_DIR + '/themes/parentalguidance/modules/footer/footer.module.js', hotMiddlewareScript],
    guides_home: [APP_DIR + '/themes/parentalguidance/modules/guides/home.module.js', hotMiddlewareScript],
    browse_listing: [APP_DIR + '/themes/parentalguidance/modules/browse-listing/home.module.js', hotMiddlewareScript],
    community_leaderboard: [APP_DIR + '/themes/parentalguidance/modules/community/leaderboard.module.js', hotMiddlewareScript],
  },
  output: {
    filename: 'scripts/[name].bundle.js',
    path: path.resolve(__dirname, '/application/themes/parentalguidance/dist'),
    publicPath: 'http://localhost:3030/'
  },
  module: {
    rules: [
      // JS
      {
        test: /\.(js|jsx)$/,
        include: /application/,
        loader: 'babel-loader'
      },
      // SCSS / CSS
      {
        test: /\.s?css$/,
        use: ['css-hot-loader'].concat(ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: [
            {
              loader: 'css-loader',
              options: {
                minimize: !IS_DEV,
                sourceMap: IS_DEV,
                publicPath: '/'
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: IS_DEV,
                plugins: [
                  require('postcss-flexbugs-fixes'),
                  require('autoprefixer')({
                    browsers: ['last 3 versions']
                  })
                ]
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: IS_DEV,
                data: '$prefix: ' + APP_PREFIX + ';'
              }
            }
          ]
        })),
      },
      // FONTS/IMAGES
      {
        test: /\.(woff|woff2|ttf|eot|otf|svg|gif|png|jp(e?)g)$/i,
        use: [
          {
            loader: 'url-loader',
            options: {
              limit: 1024,
              name(file) {
                if (file.indexOf('fonts') > -1) {
                  return 'fonts/[name].[ext]';
                }
                else {
                  return 'images/[name].[ext]';
                }
              },
              fallback: 'file-loader',
              outputPath: './',
              publicPath: '/'
            }
          }
        ]
      }
    ]
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        vendors: {
          test: /[\\/]node_modules[\\/]/,
          priority: -10,
          name: 'vendor'
        },
        default: {
          minChunks: 2,
          priority: -20,
          reuseExistingChunk: true
        }
      }
    },
    minimizer: [
      new UglifyJSPlugin({
        uglifyOptions: {
          exclude: /node_modules/,
          sourceMap: IS_DEV,
          mangle: {
            except: ['$super', '$', 'exports', 'require']
          },
          compress: {
            warnings: false
          }
        }
      }),
    ],
  },
  resolve: {
    modules: [
      'node_modules',
      path.resolve(__dirname, 'src')
    ]
  },
  plugins: [
    new webpack.DefinePlugin({
      IS_DEV: IS_DEV,
      APP_PREFIX: APP_PREFIX
    }),
    new webpack.ProvidePlugin({
      _: 'lodash',
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery'
    }),
    new ExtractTextPlugin({
      filename: 'styles/[name].bundle.css'
    })
  ]
};

if (IS_DEV) {
  config.devtool = 'source-map'; // source-map
} else {
  config.devtool = 'cheap-module-source-map'; // cheap-module-source-map
}
console.dir('Running: ' + IS_DEV ? 'development' : 'production' + ' mode.');

module.exports = config;