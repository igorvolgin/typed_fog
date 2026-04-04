<script setup lang="ts">
import { ref, watch, watchEffect } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useAuth0 } from '@auth0/auth0-vue'
import { useCountriesRepository } from '@/repositories/countriesRepository'
import type { CountryDetail } from '@/repositories/countriesRepository'
import { HttpError } from '@/services/http'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'
import AppHeader from '@/components/AppHeader.vue'

const route = useRoute()
const { isAuthenticated, isLoading, loginWithRedirect } = useAuth0()
const countriesRepository = useCountriesRepository()

const country = ref<CountryDetail | null>(null)
const loading = ref(false)
const errorMessage = ref<string | null>(null)

async function fetchCountry() {
  loading.value = true
  errorMessage.value = null
  try {
    country.value = await countriesRepository.get(route.params.code as string)
  } catch (err) {
    if (err instanceof HttpError) {
      if (err.status === 404) {
        errorMessage.value = 'Country not found.'
      } else if (err.status === 401 || err.status === 403) {
        errorMessage.value = 'Session expired. Please log in again.'
      } else {
        errorMessage.value = `Server error (${err.status}). Please try again later.`
      }
    } else {
      errorMessage.value = 'Failed to load country details. Please try again.'
    }
  } finally {
    loading.value = false
  }
}

function formatPopulation(n: number): string {
  return n.toLocaleString('en-US')
}

watchEffect(() => {
  if (!isLoading.value && isAuthenticated.value && !country.value) {
    fetchCountry()
  }
})

watch(
  () => route.params.code,
  () => {
    country.value = null
    if (isAuthenticated.value) {
      fetchCountry()
    }
  },
)
</script>

<template>
  <div class="min-h-screen bg-theme-bg text-theme-text">
    <AppHeader />

    <main class="px-6 py-10 max-w-4xl mx-auto">
      <RouterLink
        to="/countries"
        class="inline-flex items-center gap-1.5 text-sm text-theme-muted hover:text-theme-text transition-colors mb-6"
      >
        <ChevronLeftIcon class="w-4 h-4" />
        Back to countries
      </RouterLink>

      <template v-if="!isLoading && !isAuthenticated">
        <div class="flex flex-col items-center gap-6 py-24 text-center">
          <h2 class="text-xl font-semibold">Sign in to view country details</h2>
          <button
            class="px-6 py-2.5 rounded-xl bg-theme-accent hover:bg-theme-accent-hover text-white font-medium transition-colors"
            @click="loginWithRedirect()"
          >
            Log in
          </button>
        </div>
      </template>

      <template v-else-if="loading">
        <div class="animate-pulse space-y-6">
          <div class="w-48 h-32 rounded-lg bg-theme-hover" />
          <div class="w-64 h-8 rounded bg-theme-hover" />
          <div class="grid grid-cols-2 gap-4">
            <div v-for="i in 6" :key="i" class="h-16 rounded-lg bg-theme-hover" />
          </div>
        </div>
      </template>

      <template v-else-if="errorMessage">
        <div class="flex flex-col items-center gap-4 py-20 text-center">
          <p class="text-theme-muted">{{ errorMessage }}</p>
          <button
            class="px-5 py-2 rounded-xl bg-theme-accent hover:bg-theme-accent-hover text-white text-sm font-medium transition-colors"
            @click="fetchCountry()"
          >
            Try again
          </button>
        </div>
      </template>

      <template v-else-if="country">
        <div class="space-y-8">
          <div class="flex items-start gap-6">
            <img
              :src="country.flag.svg || country.flag.png"
              :alt="country.flag.alt || country.name"
              class="w-40 h-auto rounded-lg shadow"
            />
            <div>
              <h1 class="text-3xl font-bold">{{ country.name }}</h1>
              <p v-if="country.officialName !== country.name" class="text-theme-muted mt-1">
                {{ country.officialName }}
              </p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-if="country.capital.length" class="p-4 rounded-xl bg-theme-card">
              <p class="text-xs text-theme-muted uppercase tracking-wide mb-1">Capital</p>
              <p class="font-medium">{{ country.capital.join(', ') }}</p>
            </div>

            <div class="p-4 rounded-xl bg-theme-card">
              <p class="text-xs text-theme-muted uppercase tracking-wide mb-1">Region</p>
              <p class="font-medium">
                {{ country.subregion ? `${country.subregion}, ${country.region}` : country.region }}
              </p>
            </div>

            <div class="p-4 rounded-xl bg-theme-card">
              <p class="text-xs text-theme-muted uppercase tracking-wide mb-1">Population</p>
              <p class="font-medium">{{ formatPopulation(country.population) }}</p>
            </div>

            <div v-if="country.timezones.length" class="p-4 rounded-xl bg-theme-card">
              <p class="text-xs text-theme-muted uppercase tracking-wide mb-1">Timezones</p>
              <p class="font-medium">{{ country.timezones.join(', ') }}</p>
            </div>

            <div v-if="country.languages.length" class="p-4 rounded-xl bg-theme-card">
              <p class="text-xs text-theme-muted uppercase tracking-wide mb-1">Languages</p>
              <p class="font-medium">{{ country.languages.join(', ') }}</p>
            </div>

            <div v-if="Object.keys(country.currencies).length" class="p-4 rounded-xl bg-theme-card">
              <p class="text-xs text-theme-muted uppercase tracking-wide mb-1">Currencies</p>
              <p class="font-medium">
                <span v-for="(info, code) in country.currencies" :key="code" class="mr-3">
                  {{ info.name }} ({{ info.symbol }})
                </span>
              </p>
            </div>
          </div>

          <div v-if="country.borders.length" class="space-y-3">
            <h2 class="text-sm text-theme-muted uppercase tracking-wide">Bordering countries</h2>
            <div class="flex flex-wrap gap-2">
              <RouterLink
                v-for="border in country.borders"
                :key="border"
                :to="`/countries/${border}`"
                class="px-3 py-1.5 text-sm rounded-lg bg-theme-card hover:bg-theme-hover transition-colors border border-theme-border"
              >
                {{ border }}
              </RouterLink>
            </div>
          </div>
        </div>
      </template>
    </main>
  </div>
</template>
