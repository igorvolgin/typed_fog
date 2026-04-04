const baseUrl = import.meta.env.VITE_BACKEND_API_URL
const TIMEOUT_MS = 10_000

export class HttpError extends Error {
  constructor(public readonly status: number) {
    super(`HTTP ${status}`)
  }
}

export async function http<T>(path: string, init?: RequestInit): Promise<T> {
  const controller = new AbortController()
  const timer = setTimeout(() => controller.abort(), TIMEOUT_MS)

  let res: Response
  try {
    res = await fetch(`${baseUrl}${path}`, {
      signal: controller.signal,
      headers: { 'Content-Type': 'application/json', ...init?.headers },
      ...init,
    })
  } finally {
    clearTimeout(timer)
  }

  if (!res.ok) throw new HttpError(res.status)

  return res.json() as Promise<T>
}
