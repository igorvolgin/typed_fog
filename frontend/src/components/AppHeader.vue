<script setup lang="ts">
import { useAuth0 } from '@auth0/auth0-vue'
import { RouterLink } from 'vue-router'
import ThemeToggle from '@/components/ThemeToggle.vue'

const { isAuthenticated, user, loginWithRedirect, logout } = useAuth0()
const logoutUrl = import.meta.env.VITE_AUTH0_LOGOUT_URL ?? window.location.origin
</script>

<template>
  <header class="border-b border-theme-border px-6 py-4 flex items-center justify-between bg-theme-bg">
    <nav class="flex items-center gap-6">
      <RouterLink
        to="/countries"
        class="text-sm font-medium transition-colors"
        :class="$route.path === '/countries' ? 'text-theme-text' : 'text-theme-muted hover:text-theme-text'"
      >
        Countries
      </RouterLink>
      <RouterLink
        to="/health"
        class="text-sm font-medium transition-colors"
        :class="$route.path === '/health' ? 'text-theme-text' : 'text-theme-muted hover:text-theme-text'"
      >
        Health
      </RouterLink>
    </nav>

    <div class="flex items-center gap-4">
      <ThemeToggle />

      <template v-if="isAuthenticated">
        <span class="text-sm text-theme-muted">{{ user?.email }}</span>
        <button
          class="text-sm px-3 py-1.5 rounded-lg bg-theme-hover hover:bg-theme-card text-theme-muted transition-colors border border-theme-border"
          @click="logout({ logoutParams: { returnTo: logoutUrl } })"
        >
          Log out
        </button>
      </template>
      <template v-else>
        <button
          class="text-sm px-3 py-1.5 rounded-lg bg-theme-accent hover:bg-theme-accent-hover text-white transition-colors"
          @click="loginWithRedirect()"
        >
          Log in
        </button>
      </template>
    </div>
  </header>
</template>
