import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        'resources/views/**/*.blade.php',
        '../../resources/views/components/auth/**/*.blade.php',
        'resources/views/components/**/*.blade.php'
    ],

    theme: {},

    plugins: [forms],
};
