import { defineWorkspace } from 'vitest/config'

export default defineWorkspace([
  {
    test: {
      name: 'unit',
      include: ['src/**/*.test.{ts,tsx}'],
      exclude: ['e2e/**/*'],
      environment: 'jsdom',
    },
  },
])
