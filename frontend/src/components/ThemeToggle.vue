<script setup lang="ts">
import { useThemeStore } from '@/stores/theme'
import type { ColorMode, ThemeName } from '@/stores/theme'

const themeStore = useThemeStore()

const modes: { value: ColorMode; label: string }[] = [
  { value: 'light', label: 'light' },
  { value: 'auto', label: 'auto' },
  { value: 'dark', label: 'dark' },
]

const themes: { value: ThemeName; color: string; label: string }[] = [
  { value: 'indigo', color: '#6366f1', label: 'Indigo' },
  { value: 'rose', color: '#f43f5e', label: 'Rose' },
]
</script>

<template>
  <div class="flex items-center gap-3">
    <!-- Color mode toggle -->
    <div class="flex items-center gap-0.5 p-1 rounded-lg bg-theme-card border border-theme-border">
      <button
        v-for="m in modes"
        :key="m.value"
        class="p-1.5 rounded-md transition-colors"
        :class="themeStore.mode === m.value
          ? 'bg-theme-accent text-white shadow-sm'
          : 'text-theme-muted hover:text-theme-text'"
        :title="m.label"
        @click="themeStore.setMode(m.value)"
      >
        <!-- Sun -->
        <svg v-if="m.value === 'light'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 100 10A5 5 0 0012 7z" />
        </svg>
        <!-- Monitor -->
        <svg v-else-if="m.value === 'auto'" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2" />
        </svg>
        <!-- Moon -->
        <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
      </button>
    </div>

    <!-- Theme color picker -->
    <div class="flex items-center gap-1.5">
      <button
        v-for="t in themes"
        :key="t.value"
        class="w-5 h-5 rounded-full transition-all"
        :class="themeStore.theme === t.value
          ? 'scale-110'
          : 'opacity-50 hover:opacity-100'"
        :style="{ backgroundColor: t.color, ...(themeStore.theme === t.value ? { outline: `2px solid ${t.color}`, outlineOffset: '2px' } : {}) }"
        :title="t.label"
        @click="themeStore.setTheme(t.value)"
      />
    </div>
  </div>
</template>
