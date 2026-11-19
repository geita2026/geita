"use client";

import { motion, useReducedMotionConfig, type Transition } from "motion/react";
import { Children, cloneElement, isValidElement, useId, useState } from "react";
import clsx from "clsx";

/** What this reads off each child, and what it writes back onto it. */
type ChildProps = {
  "data-id": string;
  /** Set to "false" to make a child inert: no highlight, no hover handling. */
  "data-highlight"?: string;
  "data-checked"?: string;
  className?: string;
  children?: React.ReactNode;
  onClick?: React.MouseEventHandler;
  onMouseEnter?: React.MouseEventHandler;
};

/**
 * motion-primitives' AnimatedBackground: one highlight, shared by every child,
 * that slides from wherever it is to the item you point at.
 *
 * Each child needs a unique `data-id`. The highlight is a single element with a
 * `layoutId`, so Motion tweens it between positions instead of cross-fading two
 * backgrounds — which is the whole effect.
 *
 * Three additions to the published API:
 *   • `containerClassName` wraps the children and moves the "pointer left"
 *     handling onto that wrapper. Per-item mouseleave fires *before* the next
 *     item's mouseenter, so the highlight is briefly nowhere and flickers out
 *     and back in as you sweep down a list. Leaving the container is the only
 *     moment it should actually go.
 *   • `data-highlight="false"` on a child opts it out entirely — for an item
 *     that already carries a permanent background of its own.
 *   • `contentClassName` styles the wrapper this puts around each child's own
 *     children. The wrapper has to exist (it holds the stacking context above
 *     the highlight) and would otherwise flatten a child's flex layout.
 */
export type AnimatedBackgroundProps = {
  children: React.ReactElement<ChildProps>[];
  /** The item the highlight starts on. */
  defaultValue?: string;
  onValueChange?: (id: string | null) => void;
  /** Classes for the highlight itself — its shape and colour. */
  className?: string;
  /** Classes for the wrapper around each child's content. */
  contentClassName?: string;
  /** Classes for a wrapper around the whole set; enables container-level exit. */
  containerClassName?: string;
  /** The wrapper's element: `span` when the set sits inside phrasing content, such as a button. */
  containerAs?: "div" | "span";
  transition?: Transition;
  /**
   * What the highlight tweens between positions. `"position"` snaps its size
   * and only slides — the right choice when the container's own width is
   * animating, because otherwise the highlight chases a moving target and
   * judders. Default `true` (stock): both position and size.
   */
  layoutMode?: boolean | "position" | "size";
  /**
   * Where the highlight sits relative to the items' own contents.
   *
   * `"under"` (default) is the published behaviour and suits items with no
   * background of their own: the highlight can be opaque, because the only
   * thing it ever passes behind is another item's transparent box.
   *
   * `"over"` is for items that carry their own opaque background — cards. The
   * highlight is a child of the item it is travelling *to*, so under those it
   * spends half a trip hidden behind the ones it crosses and looks like it
   * teleports in partway. Travelling above the whole set fixes that, at the
   * price that the highlight must be translucent: it tints whatever it crosses
   * instead of hiding it.
   */
  overlay?: "under" | "over";
  /** Follow the pointer instead of clicks. */
  enableHover?: boolean;
};

export function AnimatedBackground({
  children,
  defaultValue,
  onValueChange,
  className,
  contentClassName,
  containerClassName,
  containerAs: Container = "div",
  transition,
  layoutMode = true,
  overlay = "under",
  enableHover = false,
}: AnimatedBackgroundProps) {
  const [activeId, setActiveId] = useState<string | null>(defaultValue ?? null);
  const uniqueId = useId();
  const reduced = useReducedMotionConfig();

  function select(id: string | null) {
    setActiveId(id);
    onValueChange?.(id);
  }

  const items = Children.map(children, (child) => {
    if (!isValidElement<ChildProps>(child)) return child;

    const id = child.props["data-id"];
    const inert = child.props["data-highlight"] === "false";
    const on = !inert && activeId === id;

    const interaction = inert
      ? {}
      : enableHover
        ? {
            onMouseEnter: () => select(id),
            /* Without a container to catch it, each item has to clear on its
               own way out — accepting the flicker described above. */
            ...(containerClassName ? {} : { onMouseLeave: () => select(null) }),
          }
        : { onClick: () => select(id) };

    return cloneElement(
      child,
      {
        className: clsx("relative", child.props.className),
        "data-checked": on ? "true" : "false",
        ...interaction,
      },
      <>
        {/* No AnimatePresence around this. An exiting element keeps its
            layoutId mounted while the incoming one claims the same id, and two
            claimants make Motion's layout pass pick a lead and snap — which is
            a visible backwards jerk right as the highlight sets off. Mounting
            exactly one at a time is what makes the slide clean; the cost is
            that the highlight pops rather than fades when it leaves the set,
            the same way a plain :hover background always did. */}
        {on && (
          <motion.div
            aria-hidden
            className={clsx("absolute inset-0", overlay === "over" && "z-30", className)}
            layout={layoutMode}
            layoutId={`animated-background-${uniqueId}`}
            transition={reduced ? { duration: 0 } : transition}
          />
        )}
        <span className={clsx("relative z-10", contentClassName)}>{child.props.children}</span>
      </>,
    );
  });

  if (!containerClassName) return items;

  return (
    <Container
      className={containerClassName}
      onMouseLeave={enableHover ? () => select(defaultValue ?? null) : undefined}
    >
      {items}
    </Container>
  );
}
