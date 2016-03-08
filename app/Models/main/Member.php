<?php
/**
 * Copyright 2015 OpenStack Foundation
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

namespace models\main;

use models\utils\SilverstripeBaseModel;

class Member extends SilverstripeBaseModel
{
    protected $table = 'Member';

    protected $array_mappings = array
    (
        'ID'            => 'id:json_int',
        'FirstName'     => 'first_name:json_string',
        'Surname'       => 'last_name:json_string',
        'Email'         => 'email:datetime_epoch',
    );

    /**
     * @return Image
     */
    public function photo()
    {
        return $this->hasOne('models\main\Image', 'ID', 'PhotoID')->first();
    }
}