"use client";

import { useEffect, useRef, useState } from "react";
import { Button } from "@heroui/button";
import { Chip } from "@heroui/chip";
import { EVENT_DATE, events, T } from "./data";

const pad = (n) => String(n).padStart(2, "0");
const startDate = () => new Date(`${EVENT_DATE}T${events[0].start}:00+03:00`);
const evStart = (i) => new Date(`${EVENT_DATE}T${events[i].start}:00+03:00`);

// Draw the Tanzania flag with code (green/blue triangles + black-and-yellow
// diagonal band). Drawn on the canvas itself so getImageData isn't tainted.
function drawTZFlag(ctx, x0, y0, W, H) {
  ctx.save();
  ctx.translate(x0, y0);
  ctx.beginPath();
  ctx.rect(0, 0, W, H);
  ctx.clip();
  ctx.fillStyle = "#1EB53A";
  ctx.beginPath();
  ctx.moveTo(0, 0);
  ctx.lineTo(W, 0);
  ctx.lineTo(0, H);
  ctx.closePath();
  ctx.fill();
  ctx.fillStyle = "#00A3DD";
  ctx.beginPath();
  ctx.moveTo(W, 0);
  ctx.lineTo(W, H);
  ctx.lineTo(0, H);
  ctx.closePath();
  ctx.fill();
  const dx = W,
    dy = -H,
    len = Math.hypot(dx, dy),
    ux = dx / len,
    uy = dy / len,
    ext = H * 0.6;
  const ax = -ux * ext,
    ay = H - uy * ext,
    bx = W + ux * ext,
    by = uy * ext;
  ctx.strokeStyle = "#FCD116";
  ctx.lineWidth = H * 0.36;
  ctx.beginPath();
  ctx.moveTo(ax, ay);
  ctx.lineTo(bx, by);
  ctx.stroke();
  ctx.strokeStyle = "#000000";
  ctx.lineWidth = H * 0.2;
  ctx.beginPath();
  ctx.moveTo(ax, ay);
  ctx.lineTo(bx, by);
  ctx.stroke();
  ctx.restore();
}

// Canvas waving flag (adapted from the classic HTML-canvas flag-wave demo).
function WavingFlag({ w = 60, h = 40, amp = 6, displayH = 26 }) {
  const ref = useRef(null);
  useEffect(() => {
    const canvas = ref.current;
    if (!canvas) return;
    const fullH = h + amp * 2;
    canvas.width = w;
    canvas.height = fullH;
    const ctx = canvas.getContext("2d");
    drawTZFlag(ctx, 0, amp, w, h);
    const od = ctx.getImageData(0, 0, w, fullH).data;
    const wavelength = w / 6,
      amplitude = amp,
      period = 200,
      shading = 90,
      fps = 30;
    const id = setInterval(() => {
      const img = ctx.getImageData(0, 0, w, fullH);
      const d = img.data;
      const now = Date.now() / period;
      for (let y = 0; y < fullH; ++y) {
        let lastO = 0;
        for (let x = 0; x < w; ++x) {
          const px = (y * w + x) * 4;
          const pct = x / w;
          const o = Math.sin(x / wavelength - now) * amplitude * pct;
          const y2 = y + (o << 0);
          if (y2 < 0 || y2 >= fullH) {
            d[px + 3] = 0;
            lastO = o;
            continue;
          }
          const opx = (y2 * w + x) * 4;
          const shade = (o - lastO) * shading;
          d[px] = od[opx] + shade;
          d[px + 1] = od[opx + 1] + shade;
          d[px + 2] = od[opx + 2] + shade;
          d[px + 3] = od[opx + 3];
          lastO = o;
        }
      }
      ctx.putImageData(img, 0, 0);
    }, 1000 / fps);
    return () => clearInterval(id);
  }, [w, h, amp]);

  return (
    <canvas
      ref={ref}
      aria-label="Tanzania flag"
      role="img"
      style={{ height: `${displayH}px`, width: "auto", verticalAlign: "middle" }}
    />
  );
}

function MasterUnit({ value, label }) {
  return (
    <div className="gold-pill min-w-[82px] py-3 px-2 text-center">
      <div className="text-3xl md:text-4xl font-bold leading-none tabular text-[#FFBF00]">
        {value}
      </div>
      <div className="text-[11px] uppercase tracking-widest text-white/55 mt-1">
        {label}
      </div>
    </div>
  );
}

export default function Page() {
  const [lang, setLang] = useState("en");
  // Start as null so SSR / static export and the first client render match,
  // then fill in the live time after mount (avoids hydration mismatch).
  const [now, setNow] = useState(null);

  useEffect(() => {
    setNow(new Date());
    const id = setInterval(() => setNow(new Date()), 1000);
    return () => clearInterval(id);
  }, []);

  const t = T[lang];
  const start = startDate();
  const last = evStart(events.length - 1);
  const diff = now ? start - now : 0;

  let master;
  if (!now) {
    master = { d: "--", h: "--", m: "--", s: "--", msg: "" };
  } else if (diff > 0) {
    master = {
      d: Math.floor(diff / 86400000),
      h: pad(Math.floor((diff % 86400000) / 3600000)),
      m: pad(Math.floor((diff % 3600000) / 60000)),
      s: pad(Math.floor((diff % 60000) / 1000)),
      msg: t.before,
    };
  } else if (now <= last) {
    master = { d: "—", h: "—", m: "—", s: "—", msg: `🔴 ${t.live}` };
  } else {
    master = { d: "✓", h: "✓", m: "✓", s: "✓", msg: t.done };
  }

  function rowState(i) {
    if (!now) return { kind: "upcoming", text: "" };
    const s = evStart(i);
    const next =
      i < events.length - 1 ? evStart(i + 1) : new Date(s.getTime() + 15 * 60000);
    const d = s - now;
    if (d > 0) {
      const dd = Math.floor(d / 86400000);
      const hh = pad(Math.floor((d % 86400000) / 3600000));
      const mm = pad(Math.floor((d % 3600000) / 60000));
      const ss = pad(Math.floor((d % 60000) / 1000));
      return { kind: "upcoming", text: dd > 0 ? `${dd}d ${hh}:${mm}:${ss}` : `${hh}:${mm}:${ss}` };
    }
    if (now < next) return { kind: "now" };
    return { kind: "done" };
  }

  return (
    <>
      {/* Director General — greyscale ambient background */}
      <div className="dg-bg" aria-hidden="true" />
      {/* Mbwasi — peeking from the right */}
      <div className="mbwasi-bg" aria-hidden="true" />
      {/* Amb. Waziri Rajabu Salum (Permanent Secretary) — right-front
          (the spot Judith held before) — .minister-bg layer now uses his photo */}
      <div className="minister-bg" aria-hidden="true" />
      {/* Hon. Judith Kapinga — prominent, left-front
          (replaces the former H.E. President portrait for the Closing Ceremony) */}
      <div className="president-bg" aria-hidden="true" />
      {/* black scrim over the portraits, under the content */}
      <div className="portrait-scrim" aria-hidden="true" />
      <main className="relative z-10 min-h-screen pb-10">
        {/* HERO */}
      <header className="text-center px-4 pt-12 pb-6">
        <div className="mx-auto max-w-4xl">
          <p className="text-base md:text-lg tracking-[0.18em] uppercase text-[#FFBF00] font-semibold flex items-center justify-center gap-2">
            <WavingFlag />
            <span className="font-tourney">Dar es Salaam International Trade Fair</span>
          </p>
          <h1 className="text-4xl md:text-6xl font-bold my-3 leading-tight flex items-center justify-center gap-3 flex-wrap">
            <img
              src="/timetable/50th.png"
              alt="50th DITF"
              className="h-16 md:h-24 w-auto object-contain"
            />
            <span className="text-white">· SabaSaba 2026</span>
          </h1>
          <h3 className="text-lg md:text-xl font-medium text-white/90 [text-shadow:0_1px_10px_rgba(0,0,0,0.85)]">{t.title}</h3>
          <p className="text-sm md:text-base mt-1 text-white/70 max-w-2xl mx-auto [text-shadow:0_1px_10px_rgba(0,0,0,0.9)]">{t.meta}</p>

          <div className="flex justify-center gap-2.5 mt-7 flex-wrap">
            <MasterUnit value={master.d} label={t.days} />
            <MasterUnit value={master.h} label={t.hrs} />
            <MasterUnit value={master.m} label={t.min} />
            <MasterUnit value={master.s} label={t.sec} />
          </div>
          <p className="mt-4 text-white/70">{master.msg}</p>

          {/* HeroUI Buttons — gold language toggle */}
          <div className="flex justify-center gap-2 mt-6">
            <Button
              radius="full"
              size="sm"
              variant={lang === "en" ? "solid" : "bordered"}
              className={
                lang === "en"
                  ? "bg-[#FFBF00] text-black font-semibold"
                  : "text-[#FFBF00] border-[#FFBF00]"
              }
              onPress={() => setLang("en")}
            >
              English
            </Button>
            <Button
              radius="full"
              size="sm"
              variant={lang === "sw" ? "solid" : "bordered"}
              className={
                lang === "sw"
                  ? "bg-[#FFBF00] text-black font-semibold"
                  : "text-[#FFBF00] border-[#FFBF00]"
              }
              onPress={() => setLang("sw")}
            >
              Kiswahili
            </Button>
          </div>

        </div>
      </header>

      {/* TIMETABLE — gold-bordered dark card */}
      <section className="mx-auto max-w-5xl px-3 mt-4">
        <div className="gold-card overflow-hidden p-1.5">
          <div className="overflow-x-auto rounded-[22px]">
            <table className="table w-full">
              <thead>
                <tr className="text-[#FFBF00] text-xs uppercase tracking-wider border-b border-[#FFBF00]/30">
                  <th className="w-32 bg-transparent">{t.time}</th>
                  <th className="bg-transparent">{t.prog}</th>
                  <th className="hidden md:table-cell bg-transparent">{t.resp}</th>
                  <th className="w-36 bg-transparent">{t.cd}</th>
                </tr>
              </thead>
              <tbody>
                {events.map((ev, i) => {
                  const d = ev[lang];
                  const st = rowState(i);
                  return (
                    <tr
                      key={i}
                      className={
                        "border-b border-white/5 " +
                        (st.kind === "now"
                          ? "row-now"
                          : st.kind === "done"
                          ? "text-white/35"
                          : "text-white/85")
                      }
                    >
                      <td className="font-semibold align-top text-white">{d.t}</td>
                      <td className="align-top">{d.a}</td>
                      <td className="align-top hidden md:table-cell text-sm text-white/45">
                        {d.r}
                      </td>
                      <td className="align-top">
                        {st.kind === "upcoming" && st.text && (
                          <span className="gold-chip font-semibold text-sm">
                            {st.text}
                          </span>
                        )}
                        {st.kind === "now" && (
                          <Chip size="sm" className="bg-[#FFBF00] text-black font-semibold">
                            ● {t.now}
                          </Chip>
                        )}
                        {st.kind === "done" && (
                          <Chip size="sm" className="bg-white/10 text-white/45">
                            {t.ended}
                          </Chip>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
        <p className="text-center text-xs text-white/35 py-5">
          Live countdowns based on your device&apos;s local time · Event time zone: EAT (UTC+3)
        </p>
      </section>

      <footer className="text-center py-6 px-4 text-sm">
        <span className="shimmer-text">
          “Dar es Salaam International Trade Fair · SabaSaba · Pride of Tanzania”
        </span>
      </footer>
      </main>
    </>
  );
}
