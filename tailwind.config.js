module.exports = {
  purge: {
    enabled: false,
    content: ['./templates/frontend/base.html.twig', './assets/**/*.{js,ts,jsx,tsx,vue}']
  },
  darkMode: false, // or 'media' or 'class'
  content: [
    "./assets/**/*.{vue,js,ts,jsx,tsx}",
    "./templates/**/*.{html,twig}",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    colors: {
      'transparent': 'transparent',
      'current': 'currentColor',
      'white': '#ffffff',
      'purple': '#3f3cbb',
      'midnight': '#121063',
      'metal': '#565584',
      'tahiti': '#3ab7bf',
      'silver': '#ecebff',
      'bubble-gum': '#ff77e9',
      'bermuda': '#78dcca',
    },
    extend: {
      backgroundImage: (theme) => ({
      }),
      fontFamily: {
        'norican': ['"Norican-Regular"', 'serif'],
        'nunito': ['"Nunito-Regular"', 'sans-serif'],
        'nunito-semibold': ['"Nunito-SemiBold"', 'sans-serif'],
        'nunito-bold': ['"Nunito-Bold"', 'sans-serif'],
        'jakarta-medium': ['"PlusJakartaSans-Medium"', 'serif'],
        'jakarta-light': ['"PlusJakartaSans-Light"', 'serif'],
        'jakarta-semibold': ['"PlusJakartaSans-Semibold"', 'sans-serif']
      },
      fontSize: {
        bigIcon: "6rem"
      },
      borderRadius: {
        extraLarge: '12rem',
        large: '6rem'
      },
      zIndex: {
        '60': '60',
        '70': '70',
        '80': '80',
        '90': '90',
        '100': '100'
      },
      height: {
        reviewTextArea: '15rem',
        searchTeaser: '200px',
        profileImage: '28rem',
        mobilePaginatedListItem: '34rem',
        tabletPaginatedListItem: '30rem',
        heroSection: '48rem',
        paginatedListItem: '40rem',
        preSelectionWrapper: '90%',
        responsibleListWrapper: '80%',
        '100': '28.5rem',
        '130': '40rem',
        '40vh': '40vh',
        '50vh': '50vh',
        '60vh': '60vh',
        '75vh': '75vh',
        '80p': '80%',
        '90p': '90%',
        '80vh': '80vh',
        '90vh': '90vh',
        '100vh': '100vh',
      },
      maxHeight: {
        '68': '20rem',
        '50vh': '50vh',
        '60vh': '60vh',
        '70vh': '70vh',
        '75vh': '75vh',
        '80vh': '80vh',
        '100vh': '100vh',
        'heroSection': '900px',
        'mobilePaginatedListItem': '34rem',
        'tabletPaginatedListItem': '30rem',
        'paginatedListItem': '36rem'
      },
      maxWidth: {
        '28': '7rem',
        '3/4': '75%',
        '100': '100%'
      },
      inset: {
        '15': '3.75rem',
        '88': '22rem',
        'minus-20': '-5rem',
        'minus-40': '-10rem'
      },
      minWidth: {
        '13': '13rem'
      },
      width: {
        'carouselFull': '576px',
        'carouselHalf': '288px',
        '50vw': '50vw',
        '75vw': '75vw',
        '90vw': '90vw',
        '100vw': '100vw'
      },
      minHeight: {
        '0': '0',
        '1/4': '25%',
        '1/2': '50%',
        '3/4': '75%',
        'full': '100%',
        '112': '28rem',
        'rem10': '10rem',
        'rem20': '20rem',
        'rem30': '30rem',
        'paginatedListItem': '36rem',
        '68': '20rem',
        '50vh': '50vh',
        '80vh': '80vh',
      },
      colors: {
          primary: {
              DEFAULT: '#ff6f00',
              light: '#ff6f00',
              'dark-light': 'rgba(67,97,238,.15)',
          },
          background: {
              DEFAULT: '#f4f5f7',
              light: 'rgba(67,97,238,.25)'
          },
          overlay: {
              DEFAULT: 'rgba(28, 38, 55, 0.6)',
              light: 'rgba(67,97,238,.25)'
          },
          secondary: {
              DEFAULT: '#08F7FE',
              light: '#08F7FE',
              'dark-light': 'rgb(128 93 202 / 15%)',
          },
          success: {
              DEFAULT: '#16a085',
              light: '#16a085',
              'dark-light': 'rgba(0,171,85,.15)',
          },
          danger: {
              DEFAULT: '#c0392b',
              light: '#c0392b',
              'dark-light': 'rgba(231,81,90,.15)',
          },
          warning: {
              DEFAULT: '#e2a03f',
              light: '#fff9ed',
              'dark-light': 'rgba(226,160,63,.15)',
          },
          info: {
              DEFAULT: '#2fd35b',
              light: '#e7f7ff',
              dark: '#185729'
          },
          dark: {
              DEFAULT: '#1D3B46',
              light: '#111317',
              'dark-light': 'rgba(59,63,92,.15)',
          },
          black: {
              DEFAULT: '#0e1726',
              light: '#e3e4eb',
              'dark-light': 'rgba(14,23,38,.15)',
          },
          white: {
              DEFAULT: '#ffffff',
              light: '#e0e6ed',
              dark: '#888ea8',
          },
          drag: {
              DEFAULT: '#1d3b46',
              light: '#95a5a6',
              item: '#ffffff',
          },
        'tertiary': '#00207F',
        'gray': '#d4d4d4',
        'espresso': {
          '50': '#f7f4f4',
          '100': '#efeae9',
          '200': '#d7cac7',
          '300': '#bfa9a5',
          '400': '#906962',
          '500': '#60291e',
          '600': '#56251b',
          '700': '#481f17',
          '800': '#3a1912',
          '900': '#2f140f'
        },
        'corduroy': {
          '50': '#f7f8f7',
          '100': '#eff1f0',
          '200': '#d8dbd9',
          '300': '#c1c5c1',
          '400': '#929a93',
          '500': '#636f65',
          '600': '#59645b',
          '700': '#4a534c',
          '800': '#3b433d',
          '900': '#313631'
        },
        'tussock': {
          '50': '#fdfaf6',
          '100': '#faf4ec',
          '200': '#f3e4d0',
          '300': '#ebd3b4',
          '400': '#dcb27c',
          '500': '#cd9144',
          '600': '#b9833d',
          '700': '#9a6d33',
          '800': '#7b5729',
          '900': '#644721'
        },
        'kelp': {
          '50': '#f5f6f5',
          '100': '#ececeb',
          '200': '#cfd0cc',
          '300': '#b1b3ad',
          '400': '#777a70',
          '500': '#3d4132',
          '600': '#373b2d',
          '700': '#2e3126',
          '800': '#25271e',
          '900': '#1e2019'
        }
      },
      transitionProperty: {
        'height': 'height'
      }
    },
  },
  variants: {
    display: ['responsive', 'group-hover', 'group-focus'],
    extend: {
      backgroundColor: ['checked'],
      borderColor: ['checked'],
      opacity: ['disabled'],
      scale: ['group-hover'],
      padding: ['group-hover'],
      transition: ['group-hover'],
      inset: ['group-hover'],
      maxHeight: ['group-hover'],
      height: ['group-hover'],
    }
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
