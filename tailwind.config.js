import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Preflight (Tailwind's base CSS reset) is disabled while Bootstrap CSS is
    // still loaded as a temporary compatibility shim (see layouts/app.blade.php) —
    // Preflight's global resets on *, ::before, ::after were overriding Bootstrap's
    // own base styles (e.g. breaking .collapse height transitions on inventory/
    // warehouse filter panels), since @vite loads after the Bootstrap CSS link.
    // Re-enable once every view is migrated off Bootstrap.
    corePlugins: {
        preflight: false,
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
