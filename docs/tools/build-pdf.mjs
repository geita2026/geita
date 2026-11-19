/**
 * Builds a PDF of every GeMEMIS document: docs/*.md and the README, into
 * docs/pdf/. Run after changing any document:
 *
 *   cd docs/tools && npm install && npm run pdf
 *
 * Each Markdown file becomes a styled HTML page (gold on black, the GeMEMIS
 * brand), Mermaid diagrams are drawn in place, and Google Chrome prints it to
 * A4 PDF. Links between documents are rewritten to point at the sibling PDF,
 * so they still work when the PDFs are read on their own.
 *
 * Needs Node 20+ and Google Chrome (set CHROME to its path if it is not in the
 * usual place).
 */
import { execFileSync } from "node:child_process";
import { existsSync, mkdirSync, mkdtempSync, readFileSync, readdirSync, rmSync, writeFileSync } from "node:fs";
import { tmpdir } from "node:os";
import { basename, dirname, join, resolve } from "node:path";
import { fileURLToPath, pathToFileURL } from "node:url";

import { Marked } from "marked";

const here = dirname(fileURLToPath(import.meta.url));
const docsDir = resolve(here, "..");
const rootDir = resolve(docsDir, "..");
const outDir = join(docsDir, "pdf");

const CHROME =
  process.env.CHROME ??
  [
    "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome",
    "/usr/bin/google-chrome",
    "/usr/bin/chromium",
    "C:/Program Files/Google/Chrome/Application/chrome.exe",
  ].find((p) => existsSync(p));

if (!CHROME) {
  console.error("Google Chrome was not found. Set CHROME=/path/to/chrome and run again.");
  process.exit(1);
}

const mermaidJs = pathToFileURL(join(here, "node_modules/mermaid/dist/mermaid.min.js")).href;

/** README first, then the numbered documents in order. */
const sources = [
  { file: join(rootDir, "README.md"), out: "00-readme.pdf" },
  ...readdirSync(docsDir)
    .filter((f) => /^\d\d-.*\.md$/.test(f))
    .sort()
    .map((f) => ({ file: join(docsDir, f), out: f.replace(/\.md$/, ".pdf") })),
];

const escape = (s) => s.replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" })[c]);

const marked = new Marked({
  gfm: true,
  renderer: {
    // ```mermaid blocks become diagrams; everything else stays a code block.
    code({ text, lang }) {
      if (lang === "mermaid") return `<figure class="diagram"><pre class="mermaid">${escape(text)}</pre></figure>`;

      return `<pre class="code"><code>${escape(text)}</code></pre>`;
    },
    // A link to another document points at its PDF instead of its Markdown.
    link({ href, tokens }) {
      const local = href && !/^[a-z]+:/i.test(href) && /\.md(#.*)?$/.test(href);
      const target = local ? basename(href).replace(/^README\.md/, "00-readme.md").replace(/\.md/, ".pdf") : href;

      return `<a href="${escape(target)}">${this.parser.parseInline(tokens)}</a>`;
    },
  },
});

const css = (title) => `
@page {
  size: A4;
  margin: 22mm 18mm 20mm;
  @bottom-left { content: "GeMEMIS · ${title.replace(/"/g, "'")}"; font: 600 8pt var(--sans); color: #6b6a63; }
  @bottom-right { content: counter(page) " / " counter(pages); font: 600 8pt var(--sans); color: #6b6a63; }
}
@page :first { margin-top: 0; @bottom-left { content: none; } }
:root {
  --gold: #eab308; --gold-deep: #a16207; --gold-soft: #fdf6db;
  --ink: #111114; --muted: #5c5b55; --rule: #e7e3d6;
  --sans: "Manrope", "Helvetica Neue", Helvetica, Arial, sans-serif;
  --mono: ui-monospace, "SF Mono", Menlo, Consolas, monospace;
}
* { box-sizing: border-box; }
html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
body { margin: 0; font: 10pt/1.55 var(--sans); color: var(--ink); }

/* Cover band: black with a gold rule — the brand. */
.cover { margin: 0 0 10mm; padding: 14mm 10mm 8mm; background: var(--ink); color: #fff; border-bottom: 4px solid var(--gold); border-radius: 0 0 10px 10px; }
.brand { display: flex; align-items: center; gap: 10px; }
.mark { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 9px; background: var(--gold); color: var(--ink); font-weight: 800; font-size: 12pt; }
.brand b { font-size: 13pt; letter-spacing: .02em; }
.brand small { display: block; font-size: 7pt; letter-spacing: .18em; text-transform: uppercase; color: #c9c6ba; }
.cover .doc-title { margin: 10mm 0 2mm; font-size: 24pt; line-height: 1.15; font-weight: 800; color: #fff; }
.cover .meta { font-size: 8.5pt; color: var(--gold); letter-spacing: .06em; text-transform: uppercase; }

h1 { display: none; } /* the cover carries the title */
h2 { margin: 9mm 0 3mm; padding-bottom: 1.5mm; font-size: 14pt; border-bottom: 2px solid var(--gold); break-after: avoid; }
h3 { margin: 6mm 0 2mm; font-size: 11.5pt; color: var(--gold-deep); break-after: avoid; }
h4 { margin: 5mm 0 1.5mm; font-size: 10.5pt; break-after: avoid; }
p, ul, ol { margin: 0 0 3mm; }
li { margin: .6mm 0; }
a { color: var(--gold-deep); text-decoration: none; border-bottom: 1px solid rgb(161 98 7 / .35); }
strong { font-weight: 700; }
hr { border: 0; border-top: 1px solid var(--rule); margin: 6mm 0; }
blockquote { margin: 0 0 3mm; padding: 2mm 4mm; border-left: 3px solid var(--gold); background: var(--gold-soft); }

table { width: 100%; border-collapse: collapse; margin: 2mm 0 5mm; font-size: 8.6pt; break-inside: auto; }
thead { display: table-header-group; }
tr { break-inside: avoid; }
th { background: var(--ink); color: #fff; text-align: left; font-weight: 700; padding: 1.8mm 2.2mm; }
td { padding: 1.6mm 2.2mm; border-bottom: 1px solid var(--rule); vertical-align: top; }
tbody tr:nth-child(even) td { background: #faf8f1; }

code { font: 8.6pt var(--mono); background: #f3f0e6; padding: .2mm 1mm; border-radius: 3px; }
pre.code { margin: 0 0 4mm; padding: 3mm 4mm; background: var(--ink); color: #f5f1e3; border-radius: 6px; border-left: 3px solid var(--gold); white-space: pre-wrap; word-break: break-word; break-inside: avoid; }
pre.code code { background: none; padding: 0; color: inherit; }

figure.diagram { margin: 3mm 0 6mm; padding: 4mm; border: 1px solid var(--rule); border-radius: 8px; text-align: center; break-inside: avoid; }
figure.diagram svg { max-width: 100%; height: auto; max-height: 200mm; }
`;

function page(title, body, source) {
  return `<!doctype html><html lang="en"><head><meta charset="utf-8"><title>${escape(title)}</title><style>${css(title)}</style></head>
<body>
<header class="cover">
  <div class="brand"><span class="mark">Ge</span><span><b>GeMEMIS</b><small>Geita Mineral Exhibition Management Information System</small></span></div>
  <div class="doc-title">${escape(title)}</div>
  <div class="meta">Version 1.0.0 · ${escape(source)} · ${new Date().toISOString().slice(0, 10)}</div>
</header>
<main>${body}</main>
<script src="${mermaidJs}"></script>
<script>
  mermaid.initialize({ startOnLoad: false, theme: "base", securityLevel: "strict",
    themeVariables: { primaryColor: "#fdf6db", primaryBorderColor: "#a16207", primaryTextColor: "#111114",
      lineColor: "#5c5b55", secondaryColor: "#f3f0e6", tertiaryColor: "#ffffff", fontFamily: "Helvetica, Arial, sans-serif" } });
  mermaid.run().finally(() => { document.body.dataset.ready = "1"; });
</script>
</body></html>`;
}

mkdirSync(outDir, { recursive: true });
const work = mkdtempSync(join(tmpdir(), "gememis-pdf-"));

try {
  for (const { file, out } of sources) {
    const md = readFileSync(file, "utf8");
    const title = (md.match(/^#\s+(.+)$/m)?.[1] ?? basename(file)).replace(/^\d\d\s+—\s+/, "");
    const html = page(title, marked.parse(md), basename(file) === "README.md" ? "README" : `docs/${basename(file)}`);
    const htmlFile = join(work, out.replace(/\.pdf$/, ".html"));

    writeFileSync(htmlFile, html);
    execFileSync(
      CHROME,
      [
        "--headless=new",
        "--disable-gpu",
        "--no-pdf-header-footer",
        "--allow-file-access-from-files",
        // Gives Mermaid time to draw before the page is printed.
        "--virtual-time-budget=20000",
        `--print-to-pdf=${join(outDir, out)}`,
        pathToFileURL(htmlFile).href,
      ],
      { stdio: "ignore" },
    );
    console.log(`docs/pdf/${out}`);
  }
} finally {
  rmSync(work, { recursive: true, force: true });
}
