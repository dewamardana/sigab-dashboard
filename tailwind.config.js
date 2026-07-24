import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand — hijau, HANYA untuk aksen: tombol, ikon, heading, link, badge AMAN
                primary: {
                    50:  '#eaf7ec',
                    100: '#d1eed7',
                    200: '#a8dfb3',
                    300: '#7bcf8c',
                    400: '#4fbd68',
                    500: '#2ba84a', // hijau cerah — warna kamu
                    600: '#248232', // hijau sedang — warna kamu
                    700: '#1e6b2a',
                    800: '#1a5624',
                    900: '#15421c',
                    950: '#0a2410',
                },
                // Struktur — abu-teal netral, dipakai untuk background, border,
                // teks body. Dipisah dari primary supaya card/section punya
                // kontras tegas tanpa "berebut" warna dengan aksen brand.
                neutral: {
                    50:  '#fcfffc', // warna kamu — dipakai untuk card/putih
                    100: '#f1f3f2', // background halaman (kontras dari card putih)
                    200: '#e2e6e5', // border card, garis pemisah
                    300: '#c7cecd',
                    400: '#9aa6a4',
                    500: '#6b7876',
                    600: '#4a5654',
                    700: '#2d3a3a', // warna kamu — teks body, subheading
                    800: '#1c2626',
                    900: '#111818',
                    950: '#040f0f', // warna kamu — heading utama, teks tegas
                },
                // Status — standar semantik, mudah dikenali (mengikuti konvensi
                // warna Flowbite: hijau=sukses, kuning=peringatan, merah=bahaya)
                status: {
                    aman: '#2ba84a',
                    siaga: '#e3a008',
                    bahaya: '#e02424',
                },
            },
            borderRadius: {
                xl: '1rem',
                '2xl': '1.25rem',
            },
        },
    },

    plugins: [forms, flowbite, require('flowbite/plugin')({ charts: true }),],
};