<?php namespace App\ModelSerializers\Locations;
/**
 * Copyright 2019 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
use Libs\ModelSerializers\AbstractSerializer;
/**
 * Class SummitBookableVenueRoomAvailableSlotSerializer
 * @package App\ModelSerializers\Locations
 */
final class SummitBookableVenueRoomAvailableSlotSerializer extends AbstractSerializer
{
    protected static $array_mappings = [
        'StartDate'       => 'start_date:datetime_epoch',
        'EndDate'         => 'end_date:datetime_epoch',
        'LocalStartDate'  => 'local_start_date:datetime_epoch',
        'LocalEndDate'    => 'local_end_date:datetime_epoch',
        'Free'            => 'is_free:json_boolean',
        'Status'          => 'status:json_string',
    ];
}