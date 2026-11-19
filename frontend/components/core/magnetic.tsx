"use client";

import React, { useEffect, useRef, useState } from "react";
import { motion, useMotionValue, useReducedMotionConfig, useSpring, type SpringOptions } from "motion/react";

const SPRING_CONFIG = { stiffness: 26.7, damping: 4.1, mass: 0.2 };

export type MagneticProps = {
  children: React.ReactNode;
  /** How far the element travels toward the pointer, as a fraction of the distance. */
  intensity?: number;
  /** Pixels from the element's centre within which the pull applies. */
  range?: number;
  actionArea?: "self" | "parent" | "global";
  springOptions?: SpringOptions;
  className?: string;
};

/**
 * motion-primitives `Magnetic`: the element leans toward the cursor while it is
 * within `range` of its centre. Adapted from the published component —
 * `className` lets the wrapper stretch inside a grid, and the pull is skipped
 * entirely for anyone who prefers reduced motion.
 */
export function Magnetic({
  children,
  intensity = 0.6,
  range = 100,
  actionArea = "self",
  springOptions = SPRING_CONFIG,
  className,
}: MagneticProps) {
  const [selfHovered, setSelfHovered] = useState(false);
  const reduceMotion = useReducedMotionConfig();
  const ref = useRef<HTMLDivElement>(null);

  // `global` is derived rather than set in an effect, which keeps the pull
  // out of a setState-inside-useEffect that React's compiler flags.
  const isHovered = actionArea === "global" || selfHovered;

  const x = useMotionValue(0);
  const y = useMotionValue(0);

  const springX = useSpring(x, springOptions);
  const springY = useSpring(y, springOptions);

  useEffect(() => {
    if (reduceMotion) return;

    const calculateDistance = (e: MouseEvent) => {
      if (!ref.current) return;
      const rect = ref.current.getBoundingClientRect();
      const distanceX = e.clientX - (rect.left + rect.width / 2);
      const distanceY = e.clientY - (rect.top + rect.height / 2);
      const absoluteDistance = Math.sqrt(distanceX ** 2 + distanceY ** 2);

      if (isHovered && absoluteDistance <= range) {
        const scale = 1 - absoluteDistance / range;

        x.set(distanceX * intensity * scale);
        y.set(distanceY * intensity * scale);
      } else {
        x.set(0);
        y.set(0);
      }
    };

    document.addEventListener("mousemove", calculateDistance);

    return () => document.removeEventListener("mousemove", calculateDistance);
  }, [isHovered, intensity, range, reduceMotion, x, y]);

  useEffect(() => {
    if (actionArea !== "parent") return;
    const parent = ref.current?.parentElement;

    if (!parent) return;

    const enter = () => setSelfHovered(true);
    const leave = () => setSelfHovered(false);

    parent.addEventListener("mouseenter", enter);
    parent.addEventListener("mouseleave", leave);

    return () => {
      parent.removeEventListener("mouseenter", enter);
      parent.removeEventListener("mouseleave", leave);
    };
  }, [actionArea]);

  return (
    <motion.div
      ref={ref}
      className={className}
      style={{ x: springX, y: springY }}
      onMouseEnter={actionArea === "self" ? () => setSelfHovered(true) : undefined}
      onMouseLeave={
        actionArea === "self"
          ? () => {
              setSelfHovered(false);
              x.set(0);
              y.set(0);
            }
          : undefined
      }
    >
      {children}
    </motion.div>
  );
}
