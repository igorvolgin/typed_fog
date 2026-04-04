import { useAuthHttp } from '@/services/authHttp'
import { HttpError } from '@/services/http'

export interface Country {
  code: string
  name: string
}

const MAX_RETRIES = 3
const RETRY_STATUSES = [429, 500, 502, 503, 504]

async function withRetry<T>(fn: () => Promise<T>): Promise<T> {
  let attempt = 0
  while (true) {
    try {
      return await fn()
    } catch (err) {
      const isRetryable = err instanceof HttpError && RETRY_STATUSES.includes(err.status)
      if (!isRetryable || attempt >= MAX_RETRIES) throw err

      const delay = err.status === 429 ? 2000 * (attempt + 1) : 500 * 2 ** attempt
      await new Promise((resolve) => setTimeout(resolve, delay))
      attempt++
    }
  }
}

export const countriesRepository = {
  list: () => withRetry(() => useAuthHttp()<Country[]>('/api/countries')),
}
