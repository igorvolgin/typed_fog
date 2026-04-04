<script setup lang="ts">
import { useThemeStore } from '@/stores/theme'
import type { ColorMode, ThemeName } from '@/stores/theme'
import { SunIcon, MoonIcon, ComputerDesktopIcon } from '@heroicons/vue/24/outline'

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
        <SunIcon v-if="m.value === 'light'" class="w-3.5 h-3.5" />
        <ComputerDesktopIcon v-else-if="m.value === 'auto'" class="w-3.5 h-3.5" />
        <MoonIcon v-else class="w-3.5 h-3.5" />
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
