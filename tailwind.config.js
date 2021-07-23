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
}

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
            },
            width: {
                ...fractionTen,
                'inherit': 'inherit',
            },
            height: {
                ...fractionTen
            },
            minHeight: {
                ...fractionTwenty
            },
            minWidth: {
                ...fractionTwenty
            },
            inset: {
                ...fractionTwenty
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
            }
        },
        colors: {
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
            textColor: ['active'],
            boxShadow: ['active'],
        },
    },
    plugins: [],
}
