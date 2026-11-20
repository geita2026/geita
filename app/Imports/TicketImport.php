<?php

namespace App\Imports;

use App\Models\Tickets\PosAgent;
use App\Models\Tickets\PosTransaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;

class TicketImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        foreach ($row as $data) {
            $check = PosTransaction::where('ticketId', 'xxxxx')->first();
            if (empty($row[0])) {
                continue;
            } elseif (!empty($check)) {
                continue;
            } else {
                $agent = PosAgent::where('agentId', $row[3])->first();
                if ($agent) {
                    $totalAmount = PosTransaction::sum('ticketAmount');
                    if ($totalAmount <= 875931000) {

                        return new PosTransaction([
                            'ticketId' => Str::uuid(),
                            'ticketAmount' => $row[1],
                            'serviceId' => $row[2],
                            'printedBy' => $row[3],
                            'locationId' => $agent->asignedLocation,
                            'posUsed' => $agent->asignedPos,
                            'eventId' => $agent->asignedEvent,
                            'ticketNo' => random_int(10000, 99999) . date('ydm') . 'POS' . random_int(1, 9),
                            'ticketStatus' => 1,
                            'scanStatus' => 0,
                            'plateNumber' => $row[5],
                        ]);
                    }
                }
            }
        }
    }
    public function startRow(): int
    {
        return 2;
    }
}
