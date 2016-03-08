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

namespace models\summit;

use models\utils\SilverstripeBaseModel;

/**
 * Class SummitType
 * @package models\summit
 */
class SummitType extends SilverstripeBaseModel
{
    protected $table = 'SummitType';

    protected $array_mappings = array
    (
        'ID'    => 'id:json_int',
        'Title' => 'name:json_string',
        'Color' => 'color:json_string',
        'Type'  => 'type:json_string',
    );

    public function toArray()
    {
        $values = parent::toArray();
        $color  = isset($values['color']) ? $values['color']:'';
        if(empty($color))
            $color = 'f0f0ee';
        if (strpos($color,'#') === false) {
            $color = '#'.$color;
        }
        $values['color'] = $color;
        return $values;
    }
}