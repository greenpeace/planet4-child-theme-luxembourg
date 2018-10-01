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

const path = require ('path')

let cssLoaders = [
                    'style-loader',
                    { loader: 'css-loader', options: { importLoaders: 1 }},
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
                    'sass-loader'
                ]



module.exports = {

    entry: './app.js',

    output: {

        path : path.resolve('dist'),
        filename: 'bundle.js'

    },
    module: {
        rules: [
            {
                test:/\.js$/,
                exclude:/node_modules/,
                use: ['babel-loader']
            },
            {
                test: /\.css$/,
                use: cssLoaders
            },
            {
                test: /\.scss$/,
                use: [...cssLoaders,'sass-loader']
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
}
