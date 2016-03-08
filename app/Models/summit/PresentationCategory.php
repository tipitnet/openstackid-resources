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
 * Class PresentationCategory
 * @package models\summit
 */
class PresentationCategory extends SilverstripeBaseModel
{
    protected $table = 'PresentationCategory';

    protected $array_mappings = array
    (
        'ID'    => 'id:json_int',
        'Title' => 'name:json_string',
    );


    /**
     * @return PresentationCategoryGroup[]
     */
    public function groups()
    {
        return $this->belongsToMany('models\summit\PresentationCategoryGroup','PresentationCategoryGroup_Categories','PresentationCategoryID', 'PresentationCategoryGroupID')->get();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $values = parent::toArray();
        $groups = array();
        foreach($this->groups() as $g)
        {
            array_push($groups, intval($g->ID));
        }
        $values['track_groups'] = $groups;
        return $values;
    }
}