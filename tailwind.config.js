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
            },
            colors: {
                brand: {
                    cream:       '#FDF5EF',   // hangat — aksen, hover, highlight
                    steel:       '#40647E',   // mid-navy
                    navy:        '#244C6B',   // primary dark
                    red:         '#D03F42',   // aksen/CTA
                    slate:       '#7B91A1',   // muted text/border
                    'bg':        '#EDF2F7',   // cool-light background (body admin)
                    'navy-dark': '#1a3850',   // lebih gelap dari navy
                    'navy-deep': '#112636',   // paling gelap
                    'red-dark':  '#b03035',
                    'red-light': '#e86166',
                    'steel-light':'#567d93',
                }
            },
        },
    },

    plugins: [forms],
};
