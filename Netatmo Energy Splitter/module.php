<?php

declare(strict_types=1);
include_once __DIR__ . '/../libs/data.php';
class NetatmoEnergySplitter extends IPSModule
{
    use CloudDataHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->ConnectParent('{E09148EF-7EA2-AE8D-7244-5B0BB30F8534}');
        $this->RegisterPropertyString('HomeID', '');
        $this->RegisterPropertyInteger('UpdateInterval', 0);

        $this->RegisterTimer('NA_UpdateStatus', 0, 'NA_updateStatus($_IPS[\'TARGET\']);');
        $this->RegisterTimer('NA_UpdateSchedules', 0, 'NA_updateSchedules($_IPS[\'TARGET\']);');
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
        $this->SetTimerInterval('NA_UpdateStatus', $this->ReadPropertyInteger('UpdateInterval') * 1000);
        $this->SetTimerInterval('NA_UpdateSchedules', $this->ReadPropertyInteger('UpdateInterval') * 1000);
    }

    public function updateStatus()
    {
        $Data['DataID'] = '{CE473B2A-C23E-9561-BC09-6A09E286C22D}';
        $Data['Buffer'] = $this->getData('/homestatus?home_id=' . $this->ReadPropertyString('HomeID'));

        $Data = json_encode($Data);
        $this->SendDataToChildren($Data);
    }

    public function ForwardData($JSONString)
    {
        $data = json_decode($JSONString, true);
        if (array_key_exists('Endpoint', $data)) {
            $data['DataID'] = '{49AF9FD0-B49D-4E9B-BCDF-8FDBCC23E0B5}';
            $data['Endpoint'] = str_replace('{homeid}', $this->ReadPropertyString('HomeID'), $data['Endpoint']);
            $this->SendDebug(__FUNCTION__ . 'JSON', json_encode($data), 0);
            return $this->SendDataToParent(json_encode($data));
        }

        if (array_key_exists('Function', $data)) {
            switch ($data['Function']) {
                case 'updateSchedules':
                    return $this->updateSchedules();
                    break;
                }
        }
    }

    public function updateSchedules()
    {
        $Data['DataID'] = '{CE473B2A-C23E-9561-BC09-6A09E286C22D}';
        $Data['Buffer'] = $this->getData('/homesdata?home_id=' . $this->ReadPropertyString('HomeID'));

        $Data = json_encode($Data);
        $this->SendDataToChildren($Data);
    }
}
