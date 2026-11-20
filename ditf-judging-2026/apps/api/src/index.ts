import "dotenv/config";
import express from "express";
import cors from "cors";

import authRoutes from "./routes/auth.js";
import categoryRoutes from "./routes/categories.js";
import exhibitorRoutes from "./routes/exhibitors.js";
import judgeRoutes from "./routes/judge.js";
import adminRoutes from "./routes/admin.js";
import resultRoutes from "./routes/results.js";
import surveyRoutes from "./routes/survey.js";

const app = express();
app.set("trust proxy", true); // behind nginx — use X-Forwarded-For for req.ip (rate-limiting)
app.use(express.json());
app.use(
  cors({
    origin: (process.env.CORS_ORIGIN || "http://localhost:3000").split(","),
  })
);

app.get("/health", (_req, res) => res.json({ ok: true, service: "ditf-judging-2026 api" }));

app.use("/auth", authRoutes);
app.use("/categories", categoryRoutes);
app.use("/exhibitors", exhibitorRoutes);
app.use("/judge", judgeRoutes);
app.use("/admin", adminRoutes);
app.use("/results", resultRoutes);
app.use("/survey", surveyRoutes);

const port = Number(process.env.PORT || 4000);
app.listen(port, () => console.log(`API listening on http://localhost:${port}`));
