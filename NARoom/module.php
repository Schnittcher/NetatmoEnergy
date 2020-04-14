<?php
declare(strict_types=1);
include_once __DIR__ . '/../libs/data.php';
    class NARoom extends IPSModule
    {
        use SplitterDataHelper;
        
        public function Create()
        {
            //Never delete this line!
            parent::Create();

            $this->ConnectParent("{19718E4A-B0D5-21ED-2106-B48BB368C14E}");
            $this->RegisterPropertyString('RoomID', '');

            $this->RegisterVariableFloat('measured_temperature', $this->Translate('Measured Temperature'), '~Temperature', 0);
            $this->RegisterVariableFloat('setpoint_temperature', $this->Translate('Setpoint Temperature'), '~Temperature', 0);

            $ProfileName = 'NA.SetPointMode';
            if (!IPS_VariableProfileExists($ProfileName)) {
                IPS_CreateVariableProfile($ProfileName, 1);
            }
            IPS_SetVariableProfileAssociation($ProfileName, 1, $this->Translate('manual'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 2, $this->Translate('home'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 3, $this->Translate('max'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 4, $this->Translate('off'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 5, $this->Translate('schedule'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 6, $this->Translate('away'), '', 0x000000);
            IPS_SetVariableProfileAssociation($ProfileName, 7, $this->Translate('hg'), '', 0x000000);
            IPS_SetVariableProfileIcon($ProfileName, 'Radiator');

            $this->RegisterVariableInteger('setpoint_mode', $this->Translate('Mode'), 'NA.SetPointMode', 0);
            $this->RegisterVariableBoolean('reachable', $this->Translate('Reachable'), '~Switch', 0);
            $this->RegisterVariableBoolean('anticipating', $this->Translate('Anticipating'), '', 0);
            $this->RegisterVariableBoolean('open_windows', $this->Translate('Open Windows'), '', 0);

            $this->EnableAction('setpoint_temperature');
            $this->EnableAction('setpoint_mode');
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
            $this->SetReceiveDataFilter('.*rooms.*');
        }
        
        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'setpoint_mode':
                        switch ($Value) {
                            case 1:
                                $temperature = $this->GetValue('setpoint_temperature');
                                $result = $this->postData('/setroomthermpoint?home_id={homeid}&room_id='.$this->ReadPropertyString('RoomID').'&mode=manual&temp='.$temperature);
                                break;
                            case 2:
                                $result = $this->postData('/setroomthermpoint?home_id={homeid}&room_id='.$this->ReadPropertyString('RoomID').'&mode=home');
                                break;
                            case 3:
                                //Max
                                echo $this->translate('This mode can not be set');
                                exit;
                            case 4:
                                //Off
                                echo $this->translate('This mode can not be set');
                                exit;
                            case 5:
                                //Schedule
                                echo $this->translate('This mode can not be set');
                                //$result = $this->postData('/setthermmode?home_id={homeid}&room_id='.$this->ReadPropertyString('RoomID').'&mode=schedule');
                                exit;
                            case 6:
                                //away
                                echo $this->translate('This mode can not be set');
                                //$result = $this->postData('/setthermmode?home_id={homeid}&room_id='.$this->ReadPropertyString('RoomID').'&mode=away');
                                exit;
                            case 7:
                                //hg
                                echo $this->translate('This mode can not be set');
                                //$result = $this->postData('/setthermmode?home_id={homeid}&room_id='.$this->ReadPropertyString('RoomID').'&mode=hg');
                                exit;
                        }
                        if (@$result->status == 'ok' || $result == 'ok') {
                            $this->SetValue('setpoint_mode', $Value);
                        } else {
                            echo $result->error->message;
                        }
                        break;
                case 'setpoint_temperature':
                    $result = $this->postData('/setroomthermpoint?home_id={homeid}&room_id='.$this->ReadPropertyString('RoomID').'&mode=manual&temp='.$Value);
                    if (@$result->status == 'ok') {
                        $this->SetValue('setpoint_mode', 1);
                        $this->SetValue('setpoint_temperature', $Value);
                    } else {
                        echo $result->error->message;
                    }
                    break;
            }
        }

        public function ReceiveData($JSONString)
        {
            $body = json_decode($JSONString)->Buffer->body;
            if (!property_exists($body, 'home')) {
                return;
            }
            
            $rooms = $body->home->rooms;
            $this->SendDebug('JSON', $JSONString, 0);
            foreach ($rooms as $key => $room) {
                if ($room->id == $this->ReadPropertyString('RoomID')) {
                    $this->SetValue('reachable', $room->reachable);
                    $this->SetValue('measured_temperature', $room->therm_measured_temperature);
                    $this->SetValue('setpoint_temperature', $room->therm_setpoint_temperature);
                    switch ($room->therm_setpoint_mode) {
                    case 'manual':
                        $this->SetValue('setpoint_mode', 1);
                        break;
                    case 'max':
                        $this->SetValue('setpoint_mode', 2);
                        break;
                    case 'off':
                        $this->SetValue('setpoint_mode', 3);
                        break;
                    case 'schedule':
                        $this->SetValue('setpoint_mode', 4);
                        break;
                    case 'away':
                        $this->SetValue('setpoint_mode', 5);
                        break;
                    case 'hg':
                        $this->SetValue('setpoint_mode', 6);
                        break;
                    }
                    $this->SetValue('anticipating', $room->anticipating);
                    $this->SetValue('open_windows', $room->open_window);
                }
            }
        }
    }
