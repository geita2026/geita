<?php

namespace App\Traits;

use App\Models\Logs\ApiLogs;
use Illuminate\Support\Str;

trait BulkSmsTrait
{

    private $user_id = 'ict.list@tantrade.go.tz';

    private $live_end_point = "https://mgov.gov.go.tz/gateway/sms/quick_sms";
    private $local_end_point = "https://mgov.gov.go.tz/gateway/sms/quick_sms";

    //private $auth_token = null;
    private $request_data = null;
    private $results = array();

    /**
     * Generate authentication token header for all requests
     *
     * @return Array
     */
    protected function generateAuthHeader()
    {
        $type = 'api';
        return array(
            'X-Auth-Request-Hash:' . $this->getRequestHash(),
            'X-Auth-Request-Id:' . $this->user_id,
            'X-Auth-Request-Type:' . $type
        );
    }


    /**
     * Return the request hash, used for authenticating the user request
     *
     * @return String
     */
    public function getRequestHash()
    {

        $APIKey = 'LXupStknkJE9XEwdLnn9Hz7ootohSIsTreLqKXMv';
        return base64_encode(hash_hmac('sha256', $this->request_data, $APIKey, true));
    }




    /**
     * Perform http request
     *
     *
     * @param Array $get_variables
     * @param Array $post_variables
     * @param Array $headers
     */
    protected function httpRequest($url, $get_variables = null, $post_variables = null, $headers = null)
    {
        $this->request_data = $post_variables['data'];
        if ($headers != null) {
            $headers = array_merge($this->generateAuthHeader(), $headers);
        } else {
            $headers = $this->generateAuthHeader();
        }
        $interface = 'curl';

        if ($interface == 'auto') {
            if (function_exists('curl_exec')) {
                $interface = 'curl';
            } else {
                $interface = 'fopen';
            }
        }

        if ($interface == 'curl') {

            return $this->curlRequest($url, $get_variables, $post_variables, $headers);
        } elseif ($interface == 'fopen') {
            return $this->fopenRequest($url, $get_variables, $post_variables, $headers);
        } else {
            return null;
            //throw new Exception('Invalid http interface defined. No such interface "' . Msdgapi::HTTP_INTERFACE . '"');
        }
    }



    /**
     * HTTP request using PHP CURL functions
     * Requires curl library installed and configured for PHP
     *
     * @param Array $get_variables
     * @param Array $post_variables
     * @param Array $headers
     */
    private function curlRequest($url, $get_variables = null, $post_variables = null, $headers = null)
    {
        $ch = curl_init();

        if (is_array($get_variables)) {
            $get_variables = '?' . str_replace('&amp;', '&', urldecode(http_build_query($get_variables)));
        } else {
            $get_variables = null;
        }

        curl_setopt($ch, CURLOPT_URL, $url . $get_variables);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        if (is_array($post_variables)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_FALSESTART, false); // check SSL
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_variables);
        }

        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);



        curl_close($ch);
        return $response;
        //return array('body'=>json_decode($response),'code'=>$code);
    }

    public function sendQuickSms($post_variables)
    {

        if (env('APP_URL') == 'http://127.0.0.4') {
            $end_point = $this->local_end_point;
        } else {
            $end_point = $this->live_end_point;
        }

        $response = $this->httpRequest($end_point, null, $post_variables);
        $reminder = Str::uuid();
        ApiLogs::create(['response' => $response, 'reminder' => $reminder]); // adding request response



    }


    public function sendNormalQuickSms($post_variables)
    {
        $response = $this->httpRequest($this->sms_gateway, null, $post_variables);
        return $response;
    }
}
