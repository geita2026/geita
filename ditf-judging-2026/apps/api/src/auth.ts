import type { Request, Response, NextFunction } from "express";
import jwt from "jsonwebtoken";

const SECRET = process.env.JWT_SECRET || "dev-secret-change-me";

export type JwtUser = { id: string; role: "ADMIN" | "JUDGE" | "EXHIBITOR"; judgeId?: string };

export function sign(user: JwtUser): string {
  return jwt.sign(user, SECRET, { expiresIn: "12h" });
}

declare global {
  // eslint-disable-next-line @typescript-eslint/no-namespace
  namespace Express {
    interface Request {
      user?: JwtUser;
    }
  }
}

export function auth(required = true) {
  return (req: Request, res: Response, next: NextFunction) => {
    const header = req.headers.authorization;
    const token = header?.startsWith("Bearer ") ? header.slice(7) : null;
    if (!token) {
      if (required) return res.status(401).json({ error: "Missing token" });
      return next();
    }
    try {
      req.user = jwt.verify(token, SECRET) as JwtUser;
      next();
    } catch {
      return res.status(401).json({ error: "Invalid token" });
    }
  };
}

export function requireRole(...roles: JwtUser["role"][]) {
  return (req: Request, res: Response, next: NextFunction) => {
    if (!req.user) return res.status(401).json({ error: "Unauthenticated" });
    if (!roles.includes(req.user.role)) return res.status(403).json({ error: "Forbidden" });
    next();
  };
}
