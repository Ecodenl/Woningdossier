module.exports = {
  purge: [
    './resources/**/*.blade.php',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      // So, box-shadows don't have 'separate' values for e.g. color. We apply a class name based on
      // the following syntax:
      // {Logical offset name}-{?size}-{color}-{?opacity}
      boxShadow: {
        // Purple
        'center-purple-25': '0 15px 15px -10px rgba(98,33,145,0.25)',
        'center-purple-50': '0 15px 15px -10px rgba(98,33,145,0.5)',
        'center-purple-75': '0 15px 15px -10px rgba(98,33,145,0.75)',
        'center-purple': '0 15px 15px -10px rgba(98,33,145,1)',
        'center-md-purple-25': '0 20px 15px -10px rgba(98,33,145,0.25)',
        'center-md-purple-50': '0 20px 15px -10px rgba(98,33,145,0.5)',
        'center-md-purple-75': '0 20px 15px -10px rgba(98,33,145,0.75)',
        'center-md-purple': '0 20px 15px -10px rgba(98,33,145,1)',
        'center-lg-purple-25': '0 25px 15px -10px rgba(98,33,145,0.25)',
        'center-lg-purple-50': '0 25px 15px -10px rgba(98,33,145,0.5)',
        'center-lg-purple-75': '0 25px 15px -10px rgba(98,33,145,0.75)',
        'center-lg-purple': '0 25px 15px -10px rgba(98,33,145,1)',

        // Orange
        'center-orange-25': '0 15px 15px -10px rgba(255,127,74,0.25)',
        'center-orange-50': '0 15px 15px -10px rgba(255,127,74,0.5)',
        'center-orange-75': '0 15px 15px -10px rgba(255,127,74,0.75)',
        'center-orange': '0 15px 15px -10px rgba(255,127,74,1)',
        'center-md-orange-25': '0 20px 15px -10px rgba(255,127,74,0.25)',
        'center-md-orange-50': '0 20px 15px -10px rgba(255,127,74,0.5)',
        'center-md-orange-75': '0 20px 15px -10px rgba(255,127,74,0.75)',
        'center-md-orange': '0 20px 15px -10px rgba(255,127,74,1)',
        'center-lg-orange-25': '0 25px 15px -10px rgba(255,127,74,0.25)',
        'center-lg-orange-50': '0 25px 15px -10px rgba(255,127,74,0.5)',
        'center-lg-orange-75': '0 25px 15px -10px rgba(255,127,74,0.75)',
        'center-lg-orange': '0 25px 15px -10px rgba(255,127,74,1)',
      }
    },
    colors: {
      black: '#000000',
      white: '#FFFFFF',
      gray: '#CDD2D7',
      green: '#2ca982',
      purple: {
        DEFAULT: '#622191',
        100: '#E1DCF2',
      },
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
    extend: {
      backgroundColor: ['active'],
      backgroundOpacity: ['active'],
      borderOpacity: ['active'],
      textColor: ['active'],
      boxShadow: ['active'],
    },
  },
  plugins: [],
}
