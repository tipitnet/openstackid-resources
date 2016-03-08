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

namespace models\utils;

/***
 * Class SilverstripeBaseModel
 * @package models\utils
 */
class SilverstripeBaseModel extends BaseModelEloquent implements IEntity
{
    protected $primaryKey ='ID';

    protected $connection = 'ss';

    protected $stiClassField = 'ClassName';

    const CREATED_AT = 'Created';

    const UPDATED_AT = 'LastEdited';

    protected function isAllowedParent($parent_name)
    {
        $res = parent::isAllowedParent($parent_name);
        if(!$res) return false;
        return !(str_contains($parent_name, 'SilverstripeBaseModel'));
    }

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->ClassName = $this->table;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->ID;
    }
}