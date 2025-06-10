var Encore = require('@symfony/webpack-encore');
var dotenv = require('dotenv');
const path = require('path');

var modules = require('./src/Command/Webpack/webpack-modules.json');
var styles = require('./src/Command/Frontend/webpack-styles.json');
var aliases = require('./src/Command/Frontend/webpack-aliases.json');

const TerserPlugin = require('terser-webpack-plugin');
let aliasesMappings = {
    '@core': path.resolve(__dirname, 'assets'),
    'Bundle': path.resolve(__dirname, './bundle')
};
// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer')
//     .BundleAnalyzerPlugin;

for (let i =0; i < modules.length; i++) {
    Encore.addEntry(modules[i]['module'], modules[i]['path']);
}

for (let i = 0; i < styles.length; i++) {
    Encore.addStyleEntry(styles[i]['style'], styles[i]['path']);
}

for (let i = 0; i < aliases.length; i++) {
    let aliasKey = aliases[i]['alias'];
    let newAlias = {};

    newAlias[aliasKey] = path.resolve(__dirname, aliases[i]['path']);
    
    aliasesMappings = {
        ...aliasesMappings,
        ...newAlias
    };
}


Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    .configureDefinePlugin(options => {
        const env = dotenv.config();

        if (env.error) {
            throw env.error;
        }

        options['process.env'].patreonClientId = JSON.stringify(env.parsed.PATREON_CLIENT_ID)
        options['process.env'].patreonReturnUrl = JSON.stringify(env.parsed.PATREON_RETURN_URL)
        options['process.env'].apiURL = JSON.stringify(env.parsed.BACKEND_HOST)
        options['process.env'].frontendURL = JSON.stringify(env.parsed.SITE_HOST)
    })
    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()
    .configureSplitChunks(function(splitChunks) {
        splitChunks.minSize = 10000;
        splitChunks.enforceSizeThreshold = 30000,
        splitChunks.chunks = 'all';
    })

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .disableSingleRuntimeChunk()

    // enables Sass/SCSS support
    .enableSassLoader()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    // .enableBuildNotifications()
    .enableSourceMaps(true)
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(true)

    // enables @babel/preset-env polyfills
    .configureBabel((babelConfig) => {
        babelConfig.plugins.push('@babel/plugin-transform-runtime');
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    .enableVueLoader(() => {}, {
        useJsx: true,
        runtimeCompilerBuild: true
    })

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes()

    // enable ESLint
    .addLoader({
        enforce: 'pre',
        test: /\.(js|vue)$/,
        loader: 'eslint-loader',
        exclude: /node_modules/,
        options: {
            fix: true,
            emitError: true,
            emitWarning: true,
        },
    })

    .enablePostCssLoader((options) => {
        options.postcssOptions = {
            config: './postcss.config.js'
        }
    })

    .addPlugin(new TerserPlugin({parallel: true}) )
    // .addPlugin(new BundleAnalyzerPlugin())
;


const config = Encore.getWebpackConfig();

config.experiments = {
    topLevelAwait: true
};
config.output.pathinfo = false;
config.resolve.alias = aliasesMappings;

module.exports = config;
