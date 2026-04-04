import { useAuth0 } from '@auth0/auth0-vue'
import { http, HttpError } from '@/services/http'

export type AuthHttp = <T>(path: string, init?: RequestInit) => Promise<T>

export function useAuthHttp(): AuthHttp {
  const { getAccessTokenSilently } = useAuth0()

  return async function authHttp<T>(path: string, init?: RequestInit): Promise<T> {
    const token = await getAccessTokenSilently()
    try {
      return await http<T>(path, {
        ...init,
        headers: { Authorization: `Bearer ${token}`, ...init?.headers },
      })
    } catch (err) {
      if (err instanceof HttpError && err.status === 401) {
        // Token may have expired in-flight - force refresh and retry once
        const freshToken = await getAccessTokenSilently({ cacheMode: 'off' })
        return http<T>(path, {
          ...init,
          headers: { Authorization: `Bearer ${freshToken}`, ...init?.headers },
        })
      }
      throw err
    }
  }
}
