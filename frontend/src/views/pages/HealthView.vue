<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { healthRepository } from '@/repositories/healthRepository'
import AppHeader from '@/components/AppHeader.vue'

const status = ref<'loading' | 'ok' | 'error'>('loading')

const phrases = [
  'Everything is running smoothly!',
  "All systems go. You're unstoppable.",
  'The backend is happy and so should you be.',
  'Green across the board. Keep building great things.',
  'Up and running — just like your ambitions.',
  'Backend alive. Ideas can flow freely.',
  'Perfect health. Time to ship something amazing.',
]

const phrase = phrases[Math.floor(Math.random() * phrases.length)]

onMounted(async () => {
  try {
    const data = await healthRepository.check()
    status.value = data.status === 'ok' ? 'ok' : 'error'
  } catch {
    status.value = 'error'
  }
})
</script>

<template>
  <div class="min-h-screen bg-theme-bg text-theme-text">
    <AppHeader />

    <div class="flex items-center justify-center px-4 py-24">
      <div class="text-center max-w-md">
        <div v-if="status === 'loading'" class="flex flex-col items-center gap-4">
          <div class="w-12 h-12 rounded-full border-4 border-theme-accent border-t-transparent animate-spin" />
          <p class="text-theme-muted text-lg">Checking backend…</p>
        </div>

        <div v-else-if="status === 'ok'" class="flex flex-col items-center gap-6">
          <div class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center">
            <svg class="w-10 h-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-semibold text-theme-text mb-2">Backend is operational</h1>
            <p class="text-theme-muted">{{ phrase }}</p>
          </div>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-sm font-medium">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
            status: ok
          </span>
        </div>

        <div v-else class="flex flex-col items-center gap-6">
          <div class="w-20 h-20 rounded-full bg-red-500/10 flex items-center justify-center">
            <svg class="w-10 h-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-semibold text-theme-text mb-2">Backend unreachable</h1>
            <p class="text-theme-muted">Something went wrong. Check if the backend is running.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
