// Simple in-memory rate limiter for public API endpoints.
// In production, use Redis or a proper rate limiting service.

interface RateLimitEntry {
  count: number;
  resetTime: number;
}

const store = new Map<string, RateLimitEntry>();

/**
 * Check if request should be rate limited.
 * @param key - Unique identifier (e.g., IP address or user ID)
 * @param limit - Max requests allowed
 * @param windowMs - Time window in milliseconds
 * @returns true if under limit, false if rate limited
 */
export function checkRateLimit(key: string, limit: number, windowMs: number): boolean {
  const now = Date.now();
  const entry = store.get(key);

  if (!entry || now >= entry.resetTime) {
    // New window
    store.set(key, { count: 1, resetTime: now + windowMs });
    return true;
  }

  if (entry.count < limit) {
    entry.count++;
    return true;
  }

  return false;
}

/**
 * Get current usage for a key.
 */
export function getRateLimitStatus(key: string, limit: number, windowMs: number): {
  remaining: number;
  resetAt: number;
} {
  const entry = store.get(key);
  const now = Date.now();

  if (!entry || now >= entry.resetTime) {
    return {
      remaining: limit,
      resetAt: now + windowMs
    };
  }

  return {
    remaining: Math.max(0, limit - entry.count),
    resetAt: entry.resetTime
  };
}

// Clean up old entries periodically (every 5 minutes)
setInterval(() => {
  const now = Date.now();
  for (const [key, entry] of store.entries()) {
    if (now >= entry.resetTime) {
      store.delete(key);
    }
  }
}, 5 * 60 * 1000);
