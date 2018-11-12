//const merge = require('webpack-merge');
//const common = require('./webpack.common.js');


// npm install --save-dev webpack webpack-cli
// npm install --save-dev babel-loader babel-core babel-preset-env
// npm install --save-dev css-loader
// npm install --save-dev style-loader
// npm install --save-dev sass-loader node-sass
// npm install --save-dev postcss-loader
// npm install --save-dev autoprefixer
// npm install --save-dev file-loader
// npm install --save-dev mini-css-extract-plugin
// npm install --save-dev optimize-css-assets-webpack-plugin
// npm install --save-dev uglifyjs-webpack-plugin

const path = require ('path')
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");




module.exports = (env) => ({
    entry:  './app.js',

    output: {
        path : path.resolve('dist'),
        filename: 'bundle.js'

    },
    optimization: {
        minimizer: [
          new UglifyJsPlugin({
            cache: true,
            parallel: true,
            sourceMap: true // set to true if you want JS source maps
          }),
          new OptimizeCSSAssetsPlugin({})
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
          filename:  'gplux.min.css',
        })
    ],
    module: {
        rules: [
            {
                test:/\.js$/,
                exclude:/node_modules/,
                use: ['babel-loader']
            },{
                test: /\.(sa|sc|c)ss$/,
                use: [
                  env.production ? MiniCssExtractPlugin.loader : 'style-loader',
                  'css-loader',
                  {
                    loader : 'postcss-loader',
                    options: {
                        plugins: (loader) => [
                            require ('autoprefixer')({
                                browsers: ['last 2 versions']
                            }),
                        ]
                    }
                },
                  'sass-loader',
                ]
            },
            {
                test: /\.(png|svg|jpg|gif)$/,
                loader: 'file-loader',
                options: {
                    name: '[name].[ext]',
                    publicPath: path.resolve('dist')
                }
            }
        ]
    }
})
