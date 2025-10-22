import { beforeAll } from 'vitest'
import { config } from '@vue/test-utils'

// Stub v-mask directive to silence warnings during tests
beforeAll(() => {
    config.global.directives = {
        ...(config.global.directives || {}),
        mask: () => { },
    }
})
