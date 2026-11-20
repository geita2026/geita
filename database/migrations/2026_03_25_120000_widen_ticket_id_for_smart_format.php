<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Widen produced_tickets.ticketId from VARCHAR(36) to VARCHAR(100).
 *
 * The smart QR format embeds type + date into the ticketId:
 *   TICKET|YYYYMMDD|<36-char-uuid>         → 53 chars
 *   VIP|YYYYMMDD-YYYYMMDD|<36-char-uuid>   → 58 chars
 *
 * 100 chars gives safe headroom for any future format extensions.
 * Existing plain UUIDs (36 chars) and SAMS badge IDs are unaffected.
 */
class WidenTicketIdForSmartFormat extends Migration
{
    public function up()
    {
        // Raw ALTER — avoids requiring doctrine/dbal for ->change()
        DB::statement('ALTER TABLE produced_tickets MODIFY ticketId VARCHAR(100) NOT NULL');
    }

    public function down()
    {
        // Only safe to revert if no smart-format rows exist
        DB::statement('ALTER TABLE produced_tickets MODIFY ticketId VARCHAR(36) NOT NULL');
    }
}
