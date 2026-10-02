import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: { sans: ['Inter', ...defaultTheme.fontFamily.sans] },
            colors: {
                // Diambil dari desain Figma
                brand: { 50: '#e6f4f1', 100: '#d3ece7', 200: '#b3dcd4', 600: '#057d6b', 700: '#006253', 800: '#004f43' },
                mint: '#edfcff',
                surface: '#f4faf9',
            },
            boxShadow: { card: '0 1px 2px rgba(16,24,40,.04), 0 4px 16px rgba(0,98,83,.06)' },
        },
    },
    plugins: [],
};
