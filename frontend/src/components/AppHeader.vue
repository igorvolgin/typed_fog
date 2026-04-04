<script setup lang="ts">
import { ref } from 'vue'
import { useAuth0 } from '@auth0/auth0-vue'
import { RouterLink } from 'vue-router'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import ThemeToggle from '@/components/ThemeToggle.vue'

const { isAuthenticated, user, loginWithRedirect, logout } = useAuth0()
const logoutUrl = import.meta.env.VITE_AUTH0_LOGOUT_URL ?? window.location.origin
const menuOpen = ref(false)
</script>

<template>
  <header class="border-b border-theme-border bg-theme-bg">
    <!-- Desktop -->
    <div class="px-6 py-4 flex items-center justify-between">
      <nav class="hidden sm:flex items-center gap-6">
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

      <!-- Mobile: burger left, auth right -->
      <button class="sm:hidden shrink-0 p-1 text-theme-muted" @click="menuOpen = !menuOpen">
        <Bars3Icon v-if="!menuOpen" class="w-6 h-6" />
        <XMarkIcon v-else class="w-6 h-6" />
      </button>

      <div class="sm:hidden flex items-center gap-2 min-w-0">
        <span v-if="isAuthenticated" class="text-sm text-theme-muted truncate min-w-0">{{ user?.email }}</span>
        <button
          v-if="isAuthenticated"
          class="shrink-0 text-sm px-3 py-1.5 rounded-lg bg-theme-hover hover:bg-theme-card text-theme-muted transition-colors border border-theme-border"
          @click="logout({ logoutParams: { returnTo: logoutUrl } })"
        >
          Log out
        </button>
        <button
          v-else
          class="shrink-0 text-sm px-3 py-1.5 rounded-lg bg-theme-accent hover:bg-theme-accent-hover text-white transition-colors"
          @click="loginWithRedirect()"
        >
          Log in
        </button>
      </div>

      <!-- Desktop: right side -->
      <div class="hidden sm:flex items-center gap-4">
        <ThemeToggle />

        <template v-if="isAuthenticated">
          <span class="text-sm text-theme-muted truncate max-w-48">{{ user?.email }}</span>
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
    </div>

    <!-- Mobile menu -->
    <div v-if="menuOpen" class="sm:hidden border-t border-theme-border px-6 py-4 space-y-4">
      <nav class="flex flex-col gap-3">
        <RouterLink
          to="/countries"
          class="text-sm font-medium transition-colors"
          :class="$route.path === '/countries' ? 'text-theme-text' : 'text-theme-muted'"
          @click="menuOpen = false"
        >
          Countries
        </RouterLink>
        <RouterLink
          to="/health"
          class="text-sm font-medium transition-colors"
          :class="$route.path === '/health' ? 'text-theme-text' : 'text-theme-muted'"
          @click="menuOpen = false"
        >
          Health
        </RouterLink>
      </nav>

      <div class="pt-3 border-t border-theme-border">
        <ThemeToggle />
      </div>
    </div>
  </header>
</template>
