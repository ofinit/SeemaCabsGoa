/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/customer/**/*.blade.php',
    './resources/views/errors/**/*.blade.php',
    './resources/js/customer.js',
  ],
  // Safe unprefixed + preflight-on: this stylesheet is only ever linked from
  // the customer Blade layout, never alongside the admin theme's Bootstrap CSS.
  theme: {
    extend: {
      colors: {
        gold: {
          DEFAULT: '#F6C018',
          light: '#FDE989',
          hover: '#EAA914',
        },
        peach: {
          DEFAULT: '#FFCD85',
          light: '#F5C79F',
        },
        ink: {
          DEFAULT: '#0E1015',
          soft: '#181B20',
        },
        muted: '#707A8A',
        success: '#059669',
        danger: '#DC4632',
        'danger-2': '#F54129',
        google: '#518EF8',
        cream: '#F8F6F0',
        sand: '#EBE6DE',
      },
      fontFamily: {
        sans: ["'Plus Jakarta Sans'", 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        card: '1.25rem',
        'card-outer': '1.65rem',
        'card-inner': '1.35rem',
        pill: '999px',
      },
      boxShadow: {
        card: '0 4px 24px 0 rgba(14, 16, 21, 0.05)',
        bezel: '0 1px 3px rgba(14, 16, 21, 0.04), 0 8px 24px -4px rgba(14, 16, 21, 0.06)',
        sheet: '0 -10px 32px 0 rgba(14, 16, 21, 0.08)',
      },
      maxWidth: {
        app: '480px',
      },
    },
  },
  plugins: [],
};
