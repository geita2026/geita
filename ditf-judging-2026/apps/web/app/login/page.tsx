"use client";

import { useRef, useState } from "react";
import { useRouter } from "next/navigation";
import { Input, Button } from "@heroui/react";
import { api, setSession } from "../../lib/api";

export default function LoginPage() {
  const router = useRouter();
  const pwRef = useRef<HTMLInputElement>(null);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function submit() {
    setLoading(true);
    setError(null);
    try {
      const res = await api<{ token: string; user: any }>("/auth/login", {
        method: "POST",
        body: JSON.stringify({ email, password }),
      });
      setSession(res.token, res.user);
      router.push(res.user.role === "ADMIN" ? "/admin/results" : "/judge");
    } catch (e: any) {
      setError("Login failed. Check your email and password.");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="flex min-h-[calc(100vh-9rem)] items-center justify-center">
      <div className="card bg-base-100 shadow w-full max-w-md">
        <div className="card-body">
          <img
            src={`${process.env.NEXT_PUBLIC_BASE_PATH ?? ""}/50th.png`}
            alt="50th DITF"
            className="mx-auto mb-2 h-24 w-24 object-contain"
          />
          <h1 className="card-title text-primary">Sign in</h1>
          <p className="text-sm text-base-content/60">
            Judges sign in with their email. Administrators use username <span className="font-semibold">admin</span> and the PIN.
          </p>
          {error && <div className="alert alert-error text-sm">{error}</div>}
          <form
            className="flex flex-col gap-3"
            onSubmit={(e) => {
              e.preventDefault();
              submit();
            }}
          >
            <Input
              label="Email or username"
              value={email}
              onValueChange={setEmail}
              onKeyDown={(e) => {
                if (e.key === "Enter") {
                  e.preventDefault();
                  pwRef.current?.focus();
                }
              }}
            />
            <Input
              ref={pwRef}
              label="Password / PIN"
              type="password"
              value={password}
              onValueChange={setPassword}
            />
            <Button type="submit" color="primary" isLoading={loading}>Login</Button>
          </form>
        </div>
      </div>
    </div>
  );
}
