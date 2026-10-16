import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    safelist: [
        // Hero menu carousel — sesuai data di hero_menus table
        'from-primary-500', 'to-primary-700',
        'from-emerald-500', 'to-emerald-700',
        'from-amber-500',   'to-orange-600',
        'from-sky-500',     'to-blue-700',
        'from-violet-500',  'to-purple-700',
        'from-rose-500',    'to-red-700',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // ============================================
                // PRIMARY — warna lama sekolah (jangan diubah)
                // ============================================
                primary: {
                    50:  '#f4f1fa',
                    100: '#e8e1f5',
                    200: '#d0c3eb',
                    300: '#b8a5e1',
                    400: '#a087d7',
                    500: '#8869cd',
                    600: '#6C4AB6',
                    700: '#5a3d99',
                    800: '#48307b',
                    900: '#36245d',
                    950: '#24183e',
                    DEFAULT: '#6C4AB6',
                    light: '#8869CD',
                    dark: '#48307B',
                },

                // ============================================
                // SURFACE — netral (untuk editorial off-white & near-black)
                // ============================================
                surface: {
                    50:  '#FAFAF7',   // off-white editorial (background utama)
                    100: '#F4F4F0',
                    200: '#E7E7E2',
                    300: '#D4D4CE',
                    400: '#A1A19B',
                    500: '#71716B',
                    600: '#525250',
                    700: '#3F3F3D',
                    800: '#272725',
                    900: '#0A0A0A',   // near-black editorial
                },

                // ============================================
                // ACCENT — aksen editorial untuk highlight
                // ============================================
                accent: {
                    amber:  '#FBBF24',   // untuk CTA sekunder / badge prestasi
                    violet: '#A78BFA',   // untuk hover effect / teks highlight
                    cream:  '#FEF9EE',   // background lembut untuk card
                },
            },

            boxShadow: {
                'soft':     '0 1px 2px 0 rgb(0 0 0 / 0.04)',
                'card':     '0 1px 3px 0 rgb(0 0 0 / 0.04), 0 1px 2px -1px rgb(0 0 0 / 0.03)',
                'elevated': '0 4px 12px -2px rgb(0 0 0 / 0.06), 0 2px 6px -2px rgb(0 0 0 / 0.04)',
                'dropdown': '0 10px 30px -10px rgb(0 0 0 / 0.12), 0 4px 12px -4px rgb(0 0 0 / 0.06)',
                // Shadow editorial pakai warna primary (ungu sekolah)
                'editorial': '0 20px 40px -12px rgb(108 74 182 / 0.15), 0 8px 20px -8px rgb(108 74 182 / 0.08)',
            },

            borderRadius: {
                'card': '0.875rem',
            },

            // ============================================
            // ANIMATIONS — untuk marquee, blob, float
            // ============================================
            animation: {
                'marquee':      'marquee 40s linear infinite',
                'marquee-fast': 'marquee 20s linear infinite',
                'blob':         'blob 18s ease-in-out infinite',
                'blob-delay':   'blob 22s ease-in-out infinite',
                'float':        'float 4s ease-in-out infinite',
                'float-delay':  'float 4s ease-in-out infinite',
                'pulse-soft':   'pulse-soft 2.5s ease-in-out infinite',
            },

            keyframes: {
                marquee: {
                    from: { transform: 'translateX(0)' },
                    to:   { transform: 'translateX(-50%)' },
                },
                blob: {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '33%':      { transform: 'translate(30px, -30px) scale(1.05)' },
                    '66%':      { transform: 'translate(-20px, 20px) scale(0.98)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%':      { transform: 'translateY(-10px)' },
                },
                'pulse-soft': {
                    '0%, 100%': { opacity: '1' },
                    '50%':      { opacity: '0.5' },
                },
            },
        },
    },

    plugins: [forms],
};