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

/**
 * Class File
 * @package models\main
 */
class File extends SilverstripeBaseModel
{
    protected $table = 'File';

    protected $stiBaseClass = 'models\main\File';

    protected $mtiClassType = 'concrete';

    protected $array_mappings = array
    (
        'ID'        => 'id:json_int',
        'Name'      => 'name:json_string',
        'Title'     => 'description:json_string',
        'Filename'  => 'file_name:json_string',
        'Content'   => 'content:json_string',
        'ClassName' => 'class_name',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->ID;
    }
}