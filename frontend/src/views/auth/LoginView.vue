<script setup lang="ts">
import { useAuth0 } from '@auth0/auth0-vue'
import { useRouter } from 'vue-router'
import { watchEffect } from 'vue'

const { isAuthenticated, isLoading, loginWithRedirect } = useAuth0()
const router = useRouter()

watchEffect(() => {
  if (!isLoading.value && isAuthenticated.value) {
    router.replace('/countries')
  }
})
</script>

<template>
  <div class="min-h-screen bg-theme-bg flex items-center justify-center px-4">
    <div class="text-center max-w-sm w-full">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-theme-text mb-2">Typed Fog</h1>
        <p class="text-theme-muted">Sign in to explore countries of the world</p>
      </div>
      <button
        class="w-full py-3 px-6 rounded-xl bg-theme-accent hover:bg-theme-accent-hover text-white font-medium transition-colors"
        @click="loginWithRedirect()"
      >
        Log in with Auth0
      </button>
    </div>
  </div>
</template>
