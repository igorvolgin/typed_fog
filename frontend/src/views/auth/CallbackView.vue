<script setup lang="ts">
import { useAuth0 } from '@auth0/auth0-vue'
import { useRouter } from 'vue-router'
import { watchEffect } from 'vue'

const { isLoading, isAuthenticated } = useAuth0()
const router = useRouter()

watchEffect(() => {
  if (!isLoading.value) {
    router.replace(isAuthenticated.value ? '/countries' : '/')
  }
})
</script>

<template>
  <div class="min-h-screen bg-theme-bg flex items-center justify-center">
    <div
      class="w-10 h-10 rounded-full border-4 border-theme-accent border-t-transparent animate-spin"
    />
  </div>
</template>
