<?php

declare(strict_types=1);
include_once __DIR__ . '/../libs/data.php';

    class NetatmoEnergyHome extends IPSModule
    {
        use SplitterDataHelper;
        public function Create()
        {
            //Never delete this line!
            parent::Create();

            $this->ConnectParent('{19718E4A-B0D5-21ED-2106-B48BB368C14E}');

            $this->RegisterAttributeString('Schedules', '');
            //Scene Profile for Groups
            if ($this->HasActiveParent()) {
                $this->updateSchedules();
            } else {
                $ProfileName = 'NA.Schedules';
                if (!IPS_VariableProfileExists($ProfileName)) {
                    IPS_CreateVariableProfile($ProfileName, 1);
                }
            }
            $this->RegisterVariableInteger('Schedules', $this->Translate('Schedules'), 'NA.Schedules', 0);

            $ProfileName = 'NA.HomeMode';
            if (!IPS_VariableProfileExists($ProfileName)) {
                IPS_CreateVariableProfile($ProfileName, 1);
            }
            IPS_SetVariableProfileAssociation($ProfileName, 1, $this->Translate('schedule'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 2, $this->Translate('away'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 3, $this->Translate('hg'), '', 0x000000);
            IPS_SetVariableProfileIcon($ProfileName, 'Radiator');

            $this->RegisterVariableInteger('Mode', $this->Translate('Mode'), 'NA.HomeMode', 0);
            $this->EnableAction('Schedules');
            $this->EnableAction('Mode');
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
            $this->SetReceiveDataFilter('.*homes.*');
        }

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'Schedules':
                    $schedules = json_decode($this->ReadAttributeString('Schedules'), true);
                    $result = $this->postData('/setthermmode?home_id={homeid}&mode=schedule&schedule_id=' . $schedules[$Value]['id'], '{}');
                    if (@$result->status == 'ok' || $result == 'ok') {
                        $this->SetValue('Schedules', $Value);
                        $this->SetValue('Mode', 1);
                    } else {
                        echo $result->error->message;
                    }
                break;
                case 'Mode':
                    switch ($Value) {
                        case 1:
                            echo $this->Translate('Please use the Schedules Variable to selcet a Schedule');
                            exit;
                        case 2:
                            $result = $this->postData('/setthermmode?home_id={homeid}&mode=away');
                            break;
                        case 3:
                            $result = $this->postData('/setthermmode?home_id={homeid}&mode=hg');
                            break;
                    }
                    if (@$result->status == 'ok' || $result == 'ok') {
                        $this->SetValue('Mode', $Value);
                    } else {
                        echo $result->error->message;
                    }
            }
        }

        public function ReceiveData($JSONString)
        {
            $data = json_decode($JSONString)->Buffer->body->homes[0];

            $ProfileName = 'NA.Schedules';
            if (!IPS_VariableProfileExists($ProfileName)) {
                IPS_CreateVariableProfile($ProfileName, 1);
            } else {
                IPS_DeleteVariableProfile($ProfileName);
                IPS_CreateVariableProfile($ProfileName, 1);
            }
            $schedulesAttribute = [];
            IPS_SetVariableProfileAssociation($ProfileName, -1, '-', '', 0x000000);
            foreach ($data->schedules as $key => $schedule) {
                IPS_SetVariableProfileAssociation($ProfileName, $key, $schedule->name, '', 0x000000);
                $schedulesAttribute[$key]['name'] = $schedule->name;
                $schedulesAttribute[$key]['id'] = $schedule->id;

                if (property_exists($schedule, 'selected')) {
                    if ($schedule->selected) {
                        $this->SetValue('Schedules', $key);
                    }
                }
            }
            IPS_SetVariableProfileIcon($ProfileName, 'Database');
            if (!empty($schedulesAttribute)) {
                $this->WriteAttributeString('Schedules', json_encode($schedulesAttribute));
            }
        }

        public function updateSchedules()
        {
            $Data = [];
            $Data['DataID'] = '{2FD9D574-BB1D-EE43-6B7F-304C8940DBD9}';
            $Data['Function'] = 'updateSchedules';

            $Data = json_encode($Data);
            $this->SendDataToParent($Data);
        }
    }
