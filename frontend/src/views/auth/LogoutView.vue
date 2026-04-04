<script setup lang="ts">
import { useAuth0 } from '@auth0/auth0-vue'

const { logout } = useAuth0()

logout({ logoutParams: { returnTo: import.meta.env.VITE_AUTH0_LOGOUT_URL ?? window.location.origin } })
</script>

<template>
  <div class="min-h-screen bg-theme-bg flex items-center justify-center">
    <div class="w-10 h-10 rounded-full border-4 border-theme-accent border-t-transparent animate-spin" />
  </div>
</template>
