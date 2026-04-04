import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createAuth0 } from '@auth0/auth0-vue'
import { useThemeStore } from '@/stores/theme'

import App from './App.vue'
import router from './router'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

const domain = import.meta.env.VITE_AUTH0_DOMAIN
const clientId = import.meta.env.VITE_AUTH0_CLIENT_ID

if (domain && clientId) {
  app.use(
    createAuth0({
      domain,
      clientId,
      authorizationParams: {
        redirect_uri: `${window.location.origin}/callback`,
        audience: import.meta.env.VITE_AUTH0_AUDIENCE,
      },
      cacheLocation: 'localstorage',
      useRefreshTokens: true,
      useRefreshTokensFallback: false,
    }),
  )
}

useThemeStore().init()

app.mount('#app')
