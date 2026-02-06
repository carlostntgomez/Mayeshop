/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Bodoni Moda', 'serif'],
                title: ['Aguafina Script', 'cursive'],
            },
        },
    },
    plugins: [],
};
