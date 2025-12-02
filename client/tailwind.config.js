const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        '../server/vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        '../server/storage/framework/views/*.php',
        '../server/resources/views/**/*.blade.php',
        './src/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};