import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                seait: {
                    50: '#FFF5F0',
                    100: '#FFE8DB',
                    200: '#FFD3C0',
                    300: '#FFB899',
                    400: '#FF8C5A',
                    500: '#FF6B35',
                    600: '#E5512A',
                    700: '#C44020',
                    800: '#993C1D',
                    900: '#662618',
                    950: '#331009',
                },
                navy: {
                    50: '#F2F5F8',
                    100: '#E3E8EF',
                    300: '#8B9DB5',
                    500: '#4A6175',
                    700: '#2D4258',
                    800: '#1E2D3D',
                    850: '#162330',
                    900: '#101920',
                    950: '#080E14',
                },
                amber: {
                    50: '#FFFBEB',
                    400: '#FBBF24',
                    500: '#F59E0B',
                    600: '#D97706',
                },
                emerald: {
                    50: '#ECFDF5',
                    400: '#34D399',
                    500: '#10B981',
                    600: '#059669',
                },
                slate: {
                    50: '#F8FAFC',
                    100: '#F1F5F9',
                    200: '#E2E8F0',
                    300: '#CBD5E1',
                    400: '#94A3B8',
                    500: '#64748B',
                    600: '#475569',
                    700: '#334155',
                    800: '#1E293B',
                    900: '#0F172A',
                },
            },
            boxShadow: {
                'card': '0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)',
                'card-hover': '0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04)',
                'card-lg': '0 8px 30px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.04)',
            },
            animation: {
                'fade-in': 'fadeIn 0.4s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'scale-in': 'scaleIn 0.3s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
        },
    },

    plugins: [forms],
};
