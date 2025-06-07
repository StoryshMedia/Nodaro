let tailwindcss = require('tailwindcss');

module.exports = {
  plugins: [
    'postcss-import',
    tailwindcss('./tailwind.config.js'), // your tailwind.js configuration file path
    require('autoprefixer'),
    require('postcss-import')
  ]
}