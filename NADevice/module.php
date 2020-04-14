<?php
    class NADevice extends IPSModule
    {
        public function Create()
        {
            //Never delete this line!
            parent::Create();

            $this->ConnectParent("{19718E4A-B0D5-21ED-2106-B48BB368C14E}");
            $this->RegisterPropertyString('ModuleID', '');

            $this->RegisterVariableInteger('firmware_revision', $this->Translate('Firmware Revision'), '', 0);
            $this->RegisterVariableInteger('rf_strength', $this->Translate('RF Strength'), '', 0);
            $this->RegisterVariableInteger('wifi_strenght', $this->Translate('Wifi Strenght'), '', 0);

            $this->RegisterVariableInteger('battery_level', $this->Translate('Battery Level'), '', 0);
            $this->RegisterVariableString('battery_state', $this->Translate('Battery State'), '', 0);
            $this->RegisterVariableString('bridge', $this->Translate('Bridge'), '', 0);
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
        }

        public function ReceiveData($JSONString)
        {
            $modules = json_decode($JSONString)->Buffer->body->home->modules;
            
            foreach ($modules as $key => $module) {
                $this->SetValue('firmware_revision', $module->firmware_revision);
                $this->SetValue('rf_strength', $module->rf_strength);
                if (property_exists($module, 'wifi_strenght')) {
                    $this->SetValue('wifi_strenght', $module->wifi_strenght);
                }

                if (property_exists($module, 'battery_level')) {
                    $this->SetValue('battery_level', $module->battery_level);
                }

                if (property_exists($module, 'battery_state')) {
                    $this->SetValue('battery_state', $module->battery_state);
                }

                if (property_exists($module, 'bridge')) {
                    $this->SetValue('bridge', $module->bridge);
                }
            }
        }
    }
