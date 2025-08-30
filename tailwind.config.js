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
  theme: {
    extend: {
      colors: {
        brand: {
          orange: '#E97325',     // oranye signature
          orangeDark: '#C85E1C',
          green: '#1E5E46',      // hijau logo
          ink: '#0E1A1B',        // hitam kebiruan untuk teks
          sand: '#FFF7F1',       // background krem halus
        },
      },
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
      boxShadow: {
        soft: '0 10px 30px rgba(0,0,0,.08)',
      },
      backgroundImage: {
        'brand-radial':
          'radial-gradient(1200px 600px at 10% 0%, rgba(233,115,37,.10), transparent 60%), radial-gradient(700px 400px at 100% 10%, rgba(30,94,70,.12), transparent 50%)',
        'brand-dots':
          'radial-gradient(currentColor 1.2px, transparent 1.2px)',
      },
    },
  },
  plugins: [forms],
};
