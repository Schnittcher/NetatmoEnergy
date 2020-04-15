<?php

declare(strict_types=1);
trait CloudDataHelper
{
    private function getData($endpoint, $homeid = '')
    {
        return $this->checkResult(json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{49AF9FD0-B49D-4E9B-BCDF-8FDBCC23E0B5}',
            'HomeID'   => $homeid,
            'Endpoint' => $endpoint,
            'Payload'  => ''
        ]))));
    }

    private function postData($endpoint, $homeid = '', $payload = '{}')
    {
        return $this->checkResult(json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{49AF9FD0-B49D-4E9B-BCDF-8FDBCC23E0B5}',
            'HomeID'   => $homeid,
            'Endpoint' => $endpoint,
            'Payload'  => $payload
        ]))));
    }

    private function checkResult($result)
    {
        if (isset($result->errors)) {
            throw new Exception($result->errors->internalMessage);
        }

        return $result;
    }
}

trait SplitterDataHelper
{
    private function getData($endpoint)
    {
        $endpoint = str_replace('{homeid}', $this->ReadPropertyString('HomeID'), $endpoint);
        $this->SendDebug('getData Endpoint', $endpoint, 0);
        return $this->checkResult(json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{2FD9D574-BB1D-EE43-6B7F-304C8940DBD9}',
            'Endpoint' => $endpoint,
            'Payload'  => ''
        ]))));
    }

    private function postData($endpoint, $payload = '{}')
    {
        $this->SendDebug('postData Endppoint', $endpoint, 0);
        $this->SendDebug('postData Payload', $payload, 0);
        return $this->checkResult(json_decode($this->SendDataToParent(json_encode([
            'DataID'   => '{2FD9D574-BB1D-EE43-6B7F-304C8940DBD9}',
            'Endpoint' => $endpoint,
            'Payload'  => $payload
        ]))));
    }

    private function checkResult($result)
    {
        if (isset($result->errors)) {
            throw new Exception($result->errors[0]->internalMessage);
        }

        return $result;
    }
}
