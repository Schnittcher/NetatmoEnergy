<?php

declare(strict_types=1);
include_once __DIR__ . '/../libs/data.php';
    class NADiscovery extends IPSModule
    {
        use CloudDataHelper;

        public function Create()
        {
            //Never delete this line!
            parent::Create();

            $this->ConnectParent('{E09148EF-7EA2-AE8D-7244-5B0BB30F8534}');
        }

        public function Destroy()
        {
            //Never delete this line!
            parent::Destroy();
        }

        public function GetConfigurationForm()
        {
            $data = json_decode(file_get_contents(__DIR__ . '/form.json'), true);

            if ($this->HasActiveParent()) {
                $result = $this->getData('/homesdata');
                $Values = [];

                foreach ($result->body->homes as $home) {
                    $AddValue = [
                        'name'            => $home->name,
                        'home'       => $home->name,
                        'country'         => $home->country,
                        'rooms'         => count($home->rooms),
                        'devices'         => count($home->modules),
                        'instanceID'      => $this->searchHome($home->id)
                    ];

                    $AddValue['create'] = [
                        [
                            'moduleID'      => '{0F90DCFC-B606-4D94-A579-49AD85AC5A78}', // Konfigurator
                            'configuration' => [
                                'HomeID' => $home->id
                            ]
                        ],
                        [
                            'moduleID'      => '{19718E4A-B0D5-21ED-2106-B48BB368C14E}', // Splitter
                            'configuration' => [
                                'HomeID' => $home->id
                            ]
                        ]
                    ];
                    $Values[] = $AddValue;
                }
                $data['actions'][0]['values'] = $Values;
            }
            return json_encode($data);
        }

        private function searchHome($homeID)
        {
            $ids = IPS_GetInstanceListByModuleID('{0F90DCFC-B606-4D94-A579-49AD85AC5A78}'); //HCConfigurator
            foreach ($ids as $id) {
                if (IPS_GetProperty($id, 'HomeID') == $homeID) {
                    return $id;
                }
            }
            return 0;
        }
    }
