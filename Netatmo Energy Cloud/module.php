<?php

declare(strict_types=1);
class NetatmoEnergyCloud extends IPSModule
{
    private $oauthIdentifer = 'netatmo';
    private $apiURL = 'https://api.netatmo.net/api';

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->RegisterAttributeString('Token', '');
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        $this->RegisterOAuth($this->oauthIdentifer);

        if ($this->ReadAttributestring('Token') == '') {
            $this->SetStatus(IS_INACTIVE);
        } else {
            $this->SetStatus(IS_ACTIVE);
        }
    }

    public function ForwardData($JSONString)
    {
        $data = json_decode($JSONString);

        $this->SendDebug('1', print_r($data, true), 0);

        if (strlen($data->Payload) > 0) {
            $this->SendDebug('ForwardData', $data->Endpoint . ', Payload: ' . $data->Payload, 0);
            return $this->PostData($this->apiURL . $data->Endpoint, $data->Payload == '{}' ? '' : $data->Payload);
        } else {
            $this->SendDebug('ForwardData', $data->Endpoint, 0);
            return $this->GetData($this->apiURL . $data->Endpoint);
        }
    }

    /**
     * This function will be called by the register button on the property page!
     */
    public function Register()
    {

            //Return everything which will open the browser
        return 'https://oauth.ipmagic.de/authorize/' . $this->oauthIdentifer . '?username=' . urlencode(IPS_GetLicensee());
    }

    public function RequestStatus()
    {
        echo $this->FetchData('https://oauth.ipmagic.de/forward');
    }

    /**
     * This function will be called by the OAuth control. Visibility should be protected!
     */
    protected function ProcessOAuthData()
    {

            //Lets assume requests via GET are for code exchange. This might not fit your needs!
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if (!isset($_GET['code'])) {
                die('Authorization Code expected');
            }

            $token = $this->FetchRefreshToken($_GET['code']);

            $this->SendDebug('ProcessOAuthData', "OK! Let's save the Refresh Token permanently", 0);
            $this->WriteAttributeString('Token', $token);
            IPS_ApplyChanges($this->InstanceID);
        } else {

                //Just print raw post data!
            echo file_get_contents('php://input');
        }
    }

    private function RegisterOAuth($WebOAuth)
    {
        $ids = IPS_GetInstanceListByModuleID('{F99BF07D-CECA-438B-A497-E4B55F139D37}');
        if (count($ids) > 0) {
            $clientIDs = json_decode(IPS_GetProperty($ids[0], 'ClientIDs'), true);
            $found = false;
            foreach ($clientIDs as $index => $clientID) {
                if ($clientID['ClientID'] == $WebOAuth) {
                    if ($clientID['TargetID'] == $this->InstanceID) {
                        return;
                    }
                    $clientIDs[$index]['TargetID'] = $this->InstanceID;
                    $found = true;
                }
            }
            if (!$found) {
                $clientIDs[] = ['ClientID' => $WebOAuth, 'TargetID' => $this->InstanceID];
            }
            IPS_SetProperty($ids[0], 'ClientIDs', json_encode($clientIDs));
            IPS_ApplyChanges($ids[0]);
        }
    }

    private function FetchRefreshToken($code)
    {
        $this->SendDebug('FetchRefreshToken', 'Use Authentication Code to get our precious Refresh Token!', 0);

        //Exchange our Authentication Code for a permanent Refresh Token and a temporary Access Token
        $options = [
            'http' => [
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query(['code' => $code])
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents('https://oauth.ipmagic.de/access_token/' . $this->oauthIdentifer, false, $context);

        $data = json_decode($result);

        if (!isset($data->token_type) || $data->token_type != 'Bearer') {
            //die('Bearer Token expected');
        }

        //Save temporary access token
        $this->FetchAccessToken($data->access_token, time() + $data->expires_in);

        //Return RefreshToken
        return $data->refresh_token;
    }

    private function FetchAccessToken($Token = '', $Expires = 0)
    {

            //Exchange our Refresh Token for a temporary Access Token
        if ($Token == '' && $Expires == 0) {

                //Check if we already have a valid Token in cache
            $data = $this->GetBuffer('AccessToken');
            if ($data != '') {
                $data = json_decode($data);
                if (time() < $data->Expires) {
                    $this->SendDebug('FetchAccessToken', 'OK! Access Token is valid until ' . date('d.m.y H:i:s', $data->Expires), 0);
                    return $data->Token;
                }
            }

            $this->SendDebug('FetchAccessToken', 'Use Refresh Token to get new Access Token!', 0);

            //If we slipped here we need to fetch the access token
            $options = [
                'http' => [
                    'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query(['refresh_token' => $this->ReadAttributeString('Token')])
                ]
            ];
            $context = stream_context_create($options);
            $result = file_get_contents('https://oauth.ipmagic.de/access_token/' . $this->oauthIdentifer, false, $context);

            $data = json_decode($result);

            if (!isset($data->token_type) || $data->token_type != 'Bearer') {
                //die('Bearer Token expected');
            }

            //Update parameters to properly cache it in the next step
            $Token = $data->access_token;
            $Expires = time() + $data->expires_in;

            //Update Refresh Token if we received one! (This is optional)
            if (isset($data->refresh_token)) {
                $this->SendDebug('FetchAccessToken', "NEW! Let's save the updated Refresh Token permanently", 0);

                $this->WriteAttributeString('Token', $data->refresh_token);
            }
        }

        $this->SendDebug('FetchAccessToken', 'CACHE! New Access Token is valid until ' . date('d.m.y H:i:s', $Expires), 0);

        //Save current Token
        $this->SetBuffer('AccessToken', json_encode(['Token' => $Token, 'Expires' => $Expires]));

        //Return current Token
        return $Token;
    }

    private function FetchData($url)
    {
        $opts = [
            'http'=> [
                'method'  => 'POST',
                'header'  => 'Authorization: Bearer ' . $this->FetchAccessToken() . "\r\n" . 'Content-Type: application/json' . "\r\n",
                'content' => '{"JSON-KEY":"THIS WILL BE LOOPED BACK AS RESPONSE!"}'
            ]
        ];
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

    private function GetData($url)
    {
        $opts = [
            'http'=> [
                'method'        => 'GET',
                'header'        => 'Authorization: Bearer ' . $this->FetchAccessToken() . "\r\n" . 'Ocp-Apim-Subscription-Key: 3770d021505641cfba58f23b7daec00d' . "\r\n" . 'Content-Type: application/json' . "\r\n",
                'ignore_errors' => true
            ]
        ];
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

    private function PostData($url, $content)
    {
        $opts = [
            'http'=> [
                'method'        => 'POST',
                'header'        => 'Authorization: Bearer ' . $this->FetchAccessToken() . "\r\n" . 'Ocp-Apim-Subscription-Key: 3770d021505641cfba58f23b7daec00d' . "\r\n" . 'Content-Type: application/json' . "\r\n",
                'content'       => $content,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($opts);
        return file_get_contents($url, false, $context);
    }
}
