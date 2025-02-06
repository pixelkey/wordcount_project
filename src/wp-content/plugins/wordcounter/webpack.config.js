const path = require('path');

module.exports = {
    entry: {
        selectstyle: 'assets/js/backend/selectstyle.js',       // Entry point for selectstyle.js
        wordcount: 'assets/js/backend/wordcount.js',           // Entry point for wordcount.js
        displaywordcount: 'assets/js/frontend/displaywordcount.js' // Entry point for displaywordcount.js
    },
    output: {
        filename: '[name].js', // Output file name based on entry point keys
        path: path.resolve(__dirname, 'public/js'), // Output directory
    },
    module: {
        rules: [
            {
                test: /\.js$/, // Match JavaScript files
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'] // Transpile ES6+ to ES5
                    }
                }
            }
        ]
    },
    mode: 'production', // 'production' for optimized output
    devtool: 'source-map' // Source maps for debugging
};