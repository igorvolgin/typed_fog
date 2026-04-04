import { http } from '@/services/http'

interface HealthResponse {
  status: 'ok' | 'error'
}

export const healthRepository = {
  check: () => http<HealthResponse>('/api/health'),
}
