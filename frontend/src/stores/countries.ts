import { ref } from 'vue'
import { defineStore } from 'pinia'

export type SortOrder = 'asc' | 'desc'

const STORAGE_KEY = 'countries:sortOrder'

export const useCountriesStore = defineStore('countries', () => {
  const sortOrder = ref<SortOrder>(
    (localStorage.getItem(STORAGE_KEY) as SortOrder) ?? 'asc',
  )

  function setSortOrder(value: SortOrder) {
    sortOrder.value = value
    localStorage.setItem(STORAGE_KEY, value)
  }

  function toggleSortOrder() {
    setSortOrder(sortOrder.value === 'asc' ? 'desc' : 'asc')
  }

  return { sortOrder, setSortOrder, toggleSortOrder }
})
