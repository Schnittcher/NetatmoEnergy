<?php

declare(strict_types=1);
include_once __DIR__ . '/../libs/data.php';
class NetatmoEnergyKonfigurator extends IPSModule
{
    use SplitterDataHelper;

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->ConnectParent('{19718E4A-B0D5-21ED-2106-B48BB368C14E}');
        $this->RegisterPropertyString('HomeID', '');
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function GetConfigurationForm()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/form.json'), true);

        if ($this->ReadPropertyString('HomeID') == '') {
            return json_encode($data);
        }

        if ($this->HasActiveParent()) {
            $result = $this->getData('/homesdata?home_id={homeid}');

            if (empty($result)) {
                return json_encode($data);
            }

            $Values = [];
            $ValuesAll = [];

            $GUIDs['Home'] = '{C945480C-F0C3-3D03-A8F1-7651B5C747BF}';
            $GUIDs['Room'] = '{FB29D1C4-26CF-4F36-9ED2-EDDEA87977DC}';
            $GUIDs['Device'] = '{77923143-C6A1-DD4E-BD0D-5BD7187729F3}';
            $location = 0;
            $AddValueRooms[] = [
                'id'                    => 9999,
                'name'                  => 'Rooms',
                'DisplayName'           => $this->Translate('Rooms'),
                'DeviceID'              => '',
                'hwtype'                => ''
            ];
            $AddValueRooms[] = [
                'id'                    => 9000,
                'name'                  => 'Unsorted',
                'DisplayName'           => $this->Translate('Unsorted'),
                'DeviceID'              => '',
                'hwtype'                => ''
            ];
            $AddValueRooms[] = [
                'id'                    => 9001,
                'name'                  => 'Home',
                'DisplayName'           => $this->Translate('Home'),
                'DeviceID'              => '',
                'hwtype'                => '',
                'instanceID'            => $this->searchNAHome(),
                'create'                => [
                    [
                        'moduleID'      => $GUIDs['Home'],
                        'configuration' => [],
                        'location' => $location
                    ],
                    [
                        'moduleID'      => '{19718E4A-B0D5-21ED-2106-B48BB368C14E}', // Splitter
                        'configuration' => [
                            'HomeID' => $result->body->homes[0]->id
                        ]
                    ]
                ]
            ];
            foreach ($result->body->homes[0]->rooms as $keyRoom => $room) {
                $AddValueRooms[] = [
                    'parent'                => 9999,
                    'id'                    => $room->id,
                    'name'                  => $room->name,
                    'DisplayName'           => $room->name,
                    'DeviceID'              => '',
                    'hwtype'                => '',
                    'instanceID'            => $this->searchNARoom($room->id),
                    'create'                => [
                        [
                            'moduleID'      => $GUIDs['Room'],
                            'configuration' => [
                                'RoomID'    => $room->id
                            ],
                            'location' => $location
                        ],
                        [
                            'moduleID'      => '{19718E4A-B0D5-21ED-2106-B48BB368C14E}', // Splitter
                            'configuration' => [
                                'HomeID' => $result->body->homes[0]->id
                            ]
                        ]
                    ]
                ];
            }
            foreach ($result->body->homes[0]->modules as $keyModule => $module) {
                if (property_exists($module, 'room_id')) {
                    $parentID = $module->room_id;
                } else {
                    $parentID = 9000;
                }
                $AddValueRooms[] = [
                    'parent'                => $parentID,
                    'id'                    => 0,
                    'name'                  => $module->name,
                    'DisplayName'           => $module->name,
                    'DeviceID'              => $module->id,
                    'hwtype'                => $module->type,
                    'instanceID'            => $this->searchNADevice($module->id),
                    'create'                => [
                        [
                            'moduleID'      => $GUIDs['Device'],
                            'configuration' => [
                                'ModuleID'    => $module->id
                            ],
                            'location' => $location
                        ],
                        [
                            'moduleID'      => '{19718E4A-B0D5-21ED-2106-B48BB368C14E}', // Splitter
                            'configuration' => [
                                'HomeID' => $result->body->homes[0]->id
                            ]
                        ]
                    ]
                ];
            }
            $data['actions'][0]['values'] = $AddValueRooms;
            return json_encode($data);
        }
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
    }

    private function searchNARoom($roomID)
    {
        $ids = IPS_GetInstanceListByModuleID('{FB29D1C4-26CF-4F36-9ED2-EDDEA87977DC}');
        foreach ($ids as $id) {
            if (IPS_GetProperty($id, 'RoomID') == $roomID) {
                return $id;
            }
        }
        return 0;
    }

    private function searchNADevice($moduleID)
    {
        $ids = IPS_GetInstanceListByModuleID('{77923143-C6A1-DD4E-BD0D-5BD7187729F3}');
        foreach ($ids as $id) {
            if (IPS_GetProperty($id, 'ModuleID') == $moduleID) {
                return $id;
            }
        }
        return 0;
    }


    private function searchNAHome()
    {
        $ids = IPS_GetInstanceListByModuleID('{C945480C-F0C3-3D03-A8F1-7651B5C747BF}');
        if (array_key_exists(0, $ids)) {
            return $ids[0];
        }
        return 0;
    }
}
