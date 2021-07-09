module.exports = {
  purge: [
    './resources/**/*.blade.php',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    colors: {
      black: '#000000',
      white: '#FFFFFF',
      green: '#2ca982',
      purple: '#622191',
      blue: {
        900: '#1122C8',
        800: '#3781F0',
        DEFAULT: '#414c57',
        500: '#647585',
        100: '#F0F1F2'
      },

      orange: '#FF7F4A',
      yellow: '#FFBD5A',
      red: '#E41440',
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
