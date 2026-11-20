import type { Request, Response, NextFunction } from "express";

// Tiny in-memory sliding-window rate limiter (no extra dependency). State is
// per-process and resets on restart — fine for light abuse protection on public
// endpoints. Keyed by client IP (req.ip honours X-Forwarded-For via trust proxy).
export function rateLimit({ windowMs, max, message }: { windowMs: number; max: number; message?: string }) {
  const hits = new Map<string, number[]>();

  return (req: Request, res: Response, next: NextFunction) => {
    const now = Date.now();
    const key = req.ip || "unknown";
    const recent = (hits.get(key) || []).filter((t) => now - t < windowMs);
    if (recent.length >= max) {
      res.setHeader("Retry-After", Math.ceil(windowMs / 1000));
      return res.status(429).json({ error: message || "Too many requests. Please wait a moment." });
    }
    recent.push(now);
    hits.set(key, recent);

    // Opportunistic cleanup so the map can't grow unbounded.
    if (hits.size > 5000) {
      for (const [k, ts] of hits) {
        const live = ts.filter((t) => now - t < windowMs);
        if (live.length) hits.set(k, live);
        else hits.delete(k);
      }
    }
    next();
  };
}
