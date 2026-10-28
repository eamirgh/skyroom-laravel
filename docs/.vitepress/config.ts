import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Skyroom Laravel',
  description: 'Production-ready, test-driven Laravel wrapper package for the Skyroom Web Service API',
  base: '/',
  lastUpdated: true,
  cleanUrls: true,

  themeConfig: {
    siteTitle: 'Skyroom Laravel',
    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'API Reference', link: '/api/overview' },
      { text: 'Real-World Examples', link: '/examples/school-lms' },
      {
        text: 'v1.0.0',
        items: [
          { text: 'Changelog', link: 'https://github.com/eamirgh/skyroom-laravel/releases' },
          { text: 'Packagist', link: 'https://packagist.org/packages/eamirgh/skyroom-laravel' }
        ]
      }
    ],

    sidebar: [
      {
        text: 'Getting Started',
        collapsed: false,
        items: [
          { text: 'Introduction', link: '/guide/getting-started' },
          { text: 'Configuration', link: '/guide/configuration' },
          { text: 'Architecture & DI', link: '/guide/architecture' },
          { text: 'Error Handling', link: '/guide/error-handling' },
          { text: 'Testing & Faking', link: '/guide/testing' }
        ]
      },
      {
        text: 'API Reference',
        collapsed: false,
        items: [
          { text: 'Overview', link: '/api/overview' },
          { text: 'Rooms API', link: '/api/rooms' },
          { text: 'Users API', link: '/api/users' },
          { text: 'Services API', link: '/api/services' },
          { text: 'Login URLs (SSO)', link: '/api/login-urls' },
          { text: 'Enums', link: '/api/enums' }
        ]
      },
      {
        text: 'Real-World Examples',
        collapsed: false,
        items: [
          { text: 'School & LMS Integration', link: '/examples/school-lms' },
          { text: 'Webinar & Event Platform', link: '/examples/webinar-platform' },
          { text: 'Bulk User Synchronization', link: '/examples/bulk-user-sync' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/eamirgh/skyroom-laravel' }
    ],

    search: {
      provider: 'local'
    },

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2026 Amir Ghaffari'
    }
  }
})
