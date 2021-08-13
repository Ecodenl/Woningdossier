const _ = require('lodash')
const plugin = require('tailwindcss/plugin')

const fractionTen = {
    '1/10': '10%',
    '2/10': '20%',
    '3/10': '30%',
    '4/10': '40%',
    '5/10': '50%',
    '6/10': '60%',
    '7/10': '70%',
    '8/10': '80%',
    '9/10': '90%',
};

const fractionTwenty = {
    '1/20': '5%',
    '2/20': '10%',
    '3/20': '15%',
    '4/20': '20%',
    '5/20': '25%',
    '6/20': '30%',
    '7/20': '35%',
    '8/20': '40%',
    '9/20': '45%',
    '10/20': '50%',
    '11/20': '55%',
    '12/20': '60%',
    '13/20': '65%',
    '14/20': '70%',
    '15/20': '75%',
    '16/20': '80%',
    '17/20': '85%',
    '18/20': '90%',
    '19/20': '95%',
};

const fractionTwelve = {
    '1/12': '8.333333%',
    '2/12': '16.666667%',
    '3/12': '25%',
    '4/12': '33.333333%',
    '5/12': '41.666667%',
    '6/12': '50%',
    '7/12': '58.333333%',
    '8/12': '66.666667%',
    '9/12': '75%',
    '10/12': '83.333333%',
    '11/12': '91.666667%',
};

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

                // Yellow
                'center-yellow-25': '0 15px 15px -10px rgba(255,189,90,0.25)',
                'center-yellow-50': '0 15px 15px -10px rgba(255,189,90,0.5)',
                'center-yellow-75': '0 15px 15px -10px rgba(255,189,90,0.75)',
                'center-yellow': '0 15px 15px -10px rgba(255,189,90,1)',
                'center-md-yellow-25': '0 20px 15px -10px rgba(255,189,90,0.25)',
                'center-md-yellow-50': '0 20px 15px -10px rgba(255,189,90,0.5)',
                'center-md-yellow-75': '0 20px 15px -10px rgba(255,189,90,0.75)',
                'center-md-yellow': '0 20px 15px -10px rgba(255,189,90,1)',
                'center-lg-yellow-25': '0 25px 15px -10px rgba(255,189,90,0.25)',
                'center-lg-yellow-50': '0 25px 15px -10px rgba(255,189,90,0.5)',
                'center-lg-yellow-75': '0 25px 15px -10px rgba(255,189,90,0.75)',
                'center-lg-yellow': '0 25px 15px -10px rgba(255,189,90,1)',
                
                // Green
                'center-green-25': '0 15px 15px -10px rgba(44,169,130,0.25)',
                'center-green-50': '0 15px 15px -10px rgba(44,169,130,0.5)',
                'center-green-75': '0 15px 15px -10px rgba(44,169,130,0.75)',
                'center-green': '0 15px 15px -10px rgba(44,169,130,1)',
                'center-md-green-25': '0 20px 15px -10px rgba(44,169,130,0.25)',
                'center-md-green-50': '0 20px 15px -10px rgba(44,169,130,0.5)',
                'center-md-green-75': '0 20px 15px -10px rgba(44,169,130,0.75)',
                'center-md-green': '0 20px 15px -10px rgba(44,169,130,1)',
                'center-lg-green-25': '0 25px 15px -10px rgba(44,169,130,0.25)',
                'center-lg-green-50': '0 25px 15px -10px rgba(44,169,130,0.5)',
                'center-lg-green-75': '0 25px 15px -10px rgba(44,169,130,0.75)',
                'center-lg-green': '0 25px 15px -10px rgba(44,169,130,1)',

                // Red
                'center-red-25': '0 15px 15px -10px rgba(227,20,64,0.25)',
                'center-red-50': '0 15px 15px -10px rgba(227,20,64,0.5)',
                'center-red-75': '0 15px 15px -10px rgba(227,20,64,0.75)',
                'center-red': '0 15px 15px -10px rgba(227,20,64,1)',
                'center-md-red-25': '0 20px 15px -10px rgba(227,20,64,0.25)',
                'center-md-red-50': '0 20px 15px -10px rgba(227,20,64,0.5)',
                'center-md-red-75': '0 20px 15px -10px rgba(227,20,64,0.75)',
                'center-md-red': '0 20px 15px -10px rgba(227,20,64,1)',
                'center-lg-red-25': '0 25px 15px -10px rgba(227,20,64,0.25)',
                'center-lg-red-50': '0 25px 15px -10px rgba(227,20,64,0.5)',
                'center-lg-red-75': '0 25px 15px -10px rgba(227,20,64,0.75)',
                'center-lg-red': '0 25px 15px -10px rgba(227,20,64,1)',
            },
            width: {
                ...fractionTen,
                ...fractionTwenty,
                'inherit': 'inherit',
            },
            height: {
                ...fractionTen,
                ...fractionTwenty,
            },
            minHeight: {
                ...fractionTwenty,
            },
            minWidth: {
                ...fractionTwenty,
            },
            maxHeight: {
                ...fractionTwenty,
            },
            maxWidth: {
                ...fractionTwenty,
            },
            spacing: {
                ...fractionTwelve
            },
            inset: {
                ...fractionTwenty,
            },
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },
            backgroundOpacity: {
                '85': '0.85',
            },
            borderRadius: {
                ...fractionTen,
                '1/2': '50%',
            }
        },
        colors: {
            transparent: 'transparent',
            black: '#000000',
            white: '#FFFFFF',
            gray: '#CDD2D7',
            green: '#2CA982',
            purple: {
                DEFAULT: '#622191',
                100: '#E1DCF2',
            },
            blue: {
                900: '#1122C8',
                800: '#3781F0',
                DEFAULT: '#414C57',
                500: '#647585',
                100: '#F0F1F2'
            },

            orange: '#FF7F4A',
            yellow: '#FFBD5A',
            red: '#E41440',
        },
        fontSize: {
            'xs': ['10px', '10px'],
            'sm': ['14px', '24px'],
            'base': ['18px', '24px'],
            'md': ['24px', '28px'],
            'lg': ['32px', '36px'],
            'xl': ['36px', '42px'],
            'xxl': ['48px', '54px'],
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active'],
            backgroundOpacity: ['active'],
            borderOpacity: ['active'],
            borderColor: ['active'],
            textColor: ['active'],
            boxShadow: ['active'],
        },
    },
    plugins: [
        plugin(function({ addUtilities, theme, e }) {
            const spacing = theme('spacing', {});

            const pads = _.map(spacing,(value, key) => {
                return {
                    [`.pad-${e(key)} > :not([hidden]) ~ :not([hidden])`]: {
                        padding: value
                    },
                    [`.pad-y-${e(key)} > :not([hidden]) ~ :not([hidden])`]: {
                        'padding-top': value
                    },
                    [`.pad-x-${e(key)} > :not([hidden]) ~ :not([hidden])`]: {
                        'padding-left': value
                    },
                }
            });

            const newUtilities = [
                ...pads,
            ];

            addUtilities(newUtilities, {
                variants: ['responsive'],
            });
        })
    ],
}
