import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Fraction",
  description: "A small Laravel package that simplifies your experience with the Action Pattern.",
  head: [['link', { rel: 'icon', href: '/favicon.ico' }]],
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config

    search: {
      provider: 'local'
    },

    sidebar: [
      {
        text: 'Meeting Fraction',
        items: [
          { text: 'About', link: '/' },
          { text: 'Installation', link: '/installation' },
          { text: 'File Mapping', link: '/mapping' },
          { text: 'Using', link: '/using' },
          { text: 'Helpers', link: '/helpers' },
          { text: 'Hooks', link: '/hooks' },
          { text: 'Deployment', link: '/deployment' },
          { text: 'Testing', link: '/testing' },
        ]
      }
    ],

    socialLinks: [
      {
        icon: 'github', link: 'https://github.com/devajmeireles/fraction',
      },
      {
        icon: 'linkedin', link: 'https://linkedin.com/in/devajmeireles',
      },
      {
        icon: 'x', link: 'https://x.com/devajmeireles',
      }
    ],

    logo: '/logo.png',
  }
})
