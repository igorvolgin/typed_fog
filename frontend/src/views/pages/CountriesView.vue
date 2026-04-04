<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useAuth0 } from '@auth0/auth0-vue'
import { RouterLink } from 'vue-router'
import { useCountriesRepository } from '@/repositories/countriesRepository'
import type { Country } from '@/repositories/countriesRepository'
import { HttpError } from '@/services/http'
import { useCountriesStore } from '@/stores/countries'
import { UserIcon, ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'
import AppHeader from '@/components/AppHeader.vue'

const { isAuthenticated, isLoading, loginWithRedirect } = useAuth0()
const countriesRepository = useCountriesRepository()
const countriesStore = useCountriesStore()

const countries = ref<Country[]>([])
const loading = ref(false)
const errorMessage = ref<string | null>(null)
const fetched = ref(false)

const sortedCountries = computed(() =>
  [...countries.value].sort((a, b) =>
    countriesStore.sortOrder === 'asc'
      ? a.name.localeCompare(b.name)
      : b.name.localeCompare(a.name),
  ),
)

async function fetchCountries() {
  loading.value = true
  errorMessage.value = null
  try {
    countries.value = await countriesRepository.list()
    fetched.value = true
  } catch (err) {
    if (err instanceof HttpError) {
      if (err.status === 401 || err.status === 403) {
        errorMessage.value = 'Session expired. Please log in again.'
      } else if (err.status === 429) {
        errorMessage.value = 'Too many requests. Please wait a moment and try again.'
      } else {
        errorMessage.value = `Server error (${err.status}). Please try again later.`
      }
    } else if ((err as Error).name === 'AbortError') {
      errorMessage.value = 'Request timed out. Please check your connection.'
    } else {
      errorMessage.value = 'Failed to load countries. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

watch(
  [isLoading, isAuthenticated],
  ([loading, authenticated]) => {
    if (!loading && authenticated && !fetched.value) {
      fetchCountries()
    }
  },
  { immediate: true },
)
</script>

<template>
  <div class="min-h-screen bg-theme-bg text-theme-text">
    <AppHeader />

    <main class="px-6 py-10 max-w-6xl mx-auto">

      <template v-if="!isLoading && !isAuthenticated">
        <div class="flex flex-col items-center gap-6 py-24 text-center">
          <div class="w-16 h-16 rounded-full flex items-center justify-center" :style="{ backgroundColor: 'color-mix(in srgb, var(--color-accent) 15%, transparent)' }">
            <UserIcon class="w-8 h-8 text-theme-accent" />
          </div>
          <div>
            <h2 class="text-xl font-semibold text-theme-text mb-2">Sign in to view countries</h2>
            <p class="text-theme-muted">You need to be logged in to access this page.</p>
          </div>
          <button
            class="px-6 py-2.5 rounded-xl bg-theme-accent hover:bg-theme-accent-hover text-white font-medium transition-colors"
            @click="loginWithRedirect()"
          >
            Log in
          </button>
        </div>
      </template>

      <template v-else-if="loading && countries.length === 0">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <div
            v-for="i in 15"
            :key="i"
            class="flex flex-col items-center gap-3 p-4 rounded-xl bg-theme-card animate-pulse"
          >
            <div class="w-16 h-10 rounded bg-theme-hover" />
            <div class="w-20 h-3 rounded bg-theme-hover" />
          </div>
        </div>
      </template>

      <template v-else-if="errorMessage">
        <div class="flex flex-col items-center gap-4 py-20 text-center">
          <p class="text-theme-muted">{{ errorMessage }}</p>
          <button
            class="px-5 py-2 rounded-xl bg-theme-accent hover:bg-theme-accent-hover text-white text-sm font-medium transition-colors"
            @click="fetchCountries()"
          >
            Try again
          </button>
        </div>
      </template>

      <template v-else>
        <div class="flex justify-end mb-4">
          <button
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg bg-theme-card hover:bg-theme-hover border border-theme-border transition-colors text-theme-text"
            @click="countriesStore.toggleSortOrder()"
          >
            Name
            <ChevronUpIcon v-if="countriesStore.sortOrder === 'asc'" class="w-4 h-4" />
            <ChevronDownIcon v-else class="w-4 h-4" />
          </button>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <RouterLink
            v-for="country in sortedCountries"
            :key="country.code"
            :to="`/countries/${country.code}`"
            class="flex flex-col items-center gap-3 p-4 rounded-xl bg-theme-card hover:bg-theme-hover transition-colors cursor-pointer"
          >
            <img
              :src="country.flag.svg || country.flag.png"
              :alt="country.flag.alt || country.name"
              class="w-16 h-auto rounded shadow"
            />
            <span class="text-sm text-theme-text text-center">{{ country.name }}</span>
          </RouterLink>
        </div>
      </template>

    </main>
  </div>
</template>
