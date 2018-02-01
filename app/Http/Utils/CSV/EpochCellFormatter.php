<?php namespace App\Http\Utils;
/**
 * Copyright 2018 OpenStack Foundation
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
use DateTime;
use DateTimeZone;
/**
 * Class EpochCellFormatter
 * @package App\Http\Utils
 */
final class EpochCellFormatter implements ICellFormatter
{
    /**
     * @var string
     */
    private $format;

    /**
     * EpochCellFormatter constructor.
     * @param string $format
     */
    public function __construct($format = 'Y-m-d H:i:s' )
    {
        $this->format = $format;
    }

    /**
     * @param string $val
     * @return string
     */
    public function format($val)
    {
        if(empty($val)) return '';
        $date = new DateTime("@$val");
        return $date->format($this->format);
    }
}