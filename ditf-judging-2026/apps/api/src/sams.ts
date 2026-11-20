import mysql from "mysql2/promise";

// Read-only connection pool to the SAMS (tradefair) database.
// Created lazily so the API still boots if SAMS_DATABASE_URL isn't set.
let pool: mysql.Pool | null = null;

export function samsConfigured(): boolean {
  return !!process.env.SAMS_DATABASE_URL;
}

export function getSamsPool(): mysql.Pool {
  if (!process.env.SAMS_DATABASE_URL) {
    throw new Error("SAMS_DATABASE_URL is not configured");
  }
  if (!pool) {
    pool = mysql.createPool({
      uri: process.env.SAMS_DATABASE_URL,
      connectionLimit: 3,
      waitForConnections: true,
    });
  }
  return pool;
}

export type SamsExhibitorRow = {
  companyID: string;
  companyName: string;
  companyPhone: string | null;
  judging_location: string | null;
  judgingCategory: string | null;
  categoryName: string | null;
  pStatus: number | null;
};

/**
 * Confirmed judging exhibitors for the active SAMS event.
 * Mirrors SAMS JudgingController: live event = events.event_status = 1,
 * exhibitors with judging_status = 1.
 */
/**
 * The 4-digit admin PIN SAMS uses for its scanner/POS/eTAC apps.
 * Mirrors SAMS ValidationApiController::adminPin():
 *   DefaultSettings::limit(1)->value('scanner_pin') ?? '0000'
 * Read-only — never written.
 */
export async function fetchScannerPin(): Promise<string> {
  const db = getSamsPool();
  const [rows] = await db.query<any[]>("SELECT scanner_pin FROM default_settings LIMIT 1");
  const pin = rows?.[0]?.scanner_pin;
  return pin === null || pin === undefined || pin === "" ? "0000" : String(pin);
}

export async function fetchConfirmedExhibitors(): Promise<SamsExhibitorRow[]> {
  const db = getSamsPool();
  const [rows] = await db.query<any[]>(
    `SELECT c.companyID,
            c.companyName,
            c.companyPhone,
            e.judging_location,
            e.judgingCategory,
            jc.categoryName,
            e.pStatus
       FROM exhibitors e
       JOIN companies c           ON e.exhibitor_id = c.companyID
       LEFT JOIN judging_categories jc ON e.judgingCategory = jc.categoryId
      WHERE e.judging_status = 1
        AND e.exhibitor_event = (
              SELECT event_id FROM events WHERE event_status = 1 ORDER BY event_id LIMIT 1
            )`
  );
  return rows as SamsExhibitorRow[];
}
