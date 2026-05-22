import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                seait: {
                    50: '#FFE8DB',
                    100: '#FFD3C0',
                    200: '#FFAC89',
                    400: '#FF8C5A',
                    500: '#FF6B35',
                    600: '#E5512A',
                    800: '#993C1D',
                    900: '#4D1E0E',
                },
                navy: {
                    50: '#F0F4F8',
                    500: '#4A6175',
                    900: '#1E2D3D',
                },
            },
        },
    },

    plugins: [forms],
};
