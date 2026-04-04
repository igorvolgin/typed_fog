import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export type ColorMode = 'dark' | 'light' | 'auto'
export type ThemeName = 'indigo' | 'rose'

const STORAGE_MODE_KEY = 'theme:mode'
const STORAGE_THEME_KEY = 'theme:name'

export const useThemeStore = defineStore('theme', () => {
  const mode = ref<ColorMode>((localStorage.getItem(STORAGE_MODE_KEY) as ColorMode) ?? 'auto')
  const theme = ref<ThemeName>((localStorage.getItem(STORAGE_THEME_KEY) as ThemeName) ?? 'indigo')

  const systemDark = window.matchMedia('(prefers-color-scheme: dark)')

  const resolvedMode = computed<'dark' | 'light'>(() => {
    if (mode.value === 'auto') return systemDark.matches ? 'dark' : 'light'
    return mode.value
  })

  function applyToDOM() {
    const html = document.documentElement
    html.classList.remove('dark', 'light')
    html.classList.add(resolvedMode.value)
    html.setAttribute('data-theme', theme.value)
  }

  function setMode(value: ColorMode) {
    mode.value = value
    localStorage.setItem(STORAGE_MODE_KEY, value)
    applyToDOM()
  }

  function setTheme(value: ThemeName) {
    theme.value = value
    localStorage.setItem(STORAGE_THEME_KEY, value)
    applyToDOM()
  }

  function init() {
    applyToDOM()
    systemDark.addEventListener('change', () => {
      if (mode.value === 'auto') applyToDOM()
    })
  }

  return { mode, theme, resolvedMode, setMode, setTheme, init }
})
