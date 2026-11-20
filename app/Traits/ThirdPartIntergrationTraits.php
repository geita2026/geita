<?php

namespace App\Traits;

use App\Models\setting\ExchangeRate;
use Illuminate\Support\Str;

trait ThirdPartIntergrationTraits
{
    public function getAllAddress($clientId, $levelId, $id)
    {
        #*************** REQUEST *************#
        $esbHelper = new EsbHelper();
        $payload = [
            'clientCode' => $clientId,
            'levelId' => $levelId,
            'id' => $id
        ];
        $apiCode = 'xi8EqFSj';
        //  dd($payload);
        $esbData = $esbHelper->requestData($apiCode, $payload, "json");
        return $esbData;
    }

    public function getAddressServices($clientId, $levelId, $id)
    {
        $esbHelper = new EsbHelper();
        $payload = [
            'clientCode' => $clientId,
            'levelId' => $levelId,
            'id' => $id
        ];
        $apiCode = 'QXH2PCxB';
        $esbData = $esbHelper->requestData($apiCode, $payload, "json");
        return $esbData;
    }

    public function getAllExchageRate()
    {
        $esbHelper = new EsbHelper();
        $connectionCode = 'kZp1z06T';
        $payload = [];
        $response = $esbHelper->requestData($connectionCode, $payload, "json");

        if ($response != Null) {
            if ($response['esbBody']) {
                $data = $response['esbBody'];
                foreach ($data as  $value) {
                    $ExchangeRate = ExchangeRate::where('currencyCode', $value['CCY'])->first();
                    if ($ExchangeRate) {
                        $ExchangeRate->update([
                            'BUYING' => $value['BUYING'],
                            'SELLING' => $value['SELLING'],
                            'MEAN' => $value['MEAN'],
                            'lastUpdatedAt' => $value['TRADE_DATE']
                        ]);
                    } else {
                        ExchangeRate::create([
                            'currencyId' => Str::uuid(),
                            'currencyCode' => $value['CCY'],
                            'BUYING' => $value['BUYING'],
                            'SELLING' => $value['SELLING'],
                            'MEAN' => $value['MEAN'],
                            'lastUpdatedAt' => $value['TRADE_DATE']
                        ]);
                    }
                }
                return 1;
            }
        }

        return 0;
    }


    public function getCompanyRegDetails($regNumber)
    {
        $esbHelper = new EsbHelper();
        $connectionCode = 'DAg6tgbO';   // end point brela requests.
        #$connectionCode='5NZfTYGY'; // Get Minimum Info
        $apiKey = "TEST-400c0a84-da97-46a8-b93cbcabnmz"; // api Key From Company Registration

        $payload = [
            'requestdata' => [
                "RegistrationNumber" => $regNumber,
                "ApiKey" => $apiKey,
                "EntityType" => 1
            ]
        ];
        $response = $esbHelper->requestData($connectionCode, $payload, "json");
        if ($response['success'] == true) {
            session(['brnStatus' => 1]);
            return $response['esbBody'];
        } elseif ($response['success'] == false) {
            session(['brnStatus' => 0]);
            return $response['message'];
        } elseif (!$response) {
            session(['brnStatus' => 0]);
            return 0;
        }
    }
}

//
