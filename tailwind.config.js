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
        },
    },

    // Tailwind's `.collapse` utility (`visibility: collapse`) has the same class
    // name as Bootstrap's `.collapse` component (used for the show/hide filter
    // panels on inventory/warehouse etc., loaded as a temporary compatibility
    // shim — see layouts/app.blade.php). Whichever stylesheet wins the cascade
    // clobbers the other's meaning; disabling Tailwind's `collapse` core plugin
    // removes the collision entirely without touching Bootstrap or Preflight.
    // Safe to remove once Bootstrap CSS itself is removed from the layout.
    corePlugins: {
        collapse: false,
    },

    plugins: [forms],
};
