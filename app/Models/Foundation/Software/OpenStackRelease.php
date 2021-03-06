<?php namespace App\Models\Foundation\Software;
/**
 * Copyright 2017 OpenStack Foundation
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
use Doctrine\ORM\Mapping AS ORM;
use models\utils\SilverstripeBaseModel;
use DateTime;
/**
 * @ORM\Entity
 * @ORM\Table(name="OpenStackRelease")
 * Class OpenStackRelease
 * @package App\Models\Foundation\Software
 */
class OpenStackRelease extends SilverstripeBaseModel
{
    /**
     * @ORM\Column(name="Name", type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="ReleaseNumber", type="string")
     * @var string
     */
    private $release_number;

    /**
     * @ORM\Column(name="ReleaseDate", type="datetime")
     * @var DateTime
     */
    private $release_date;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReleaseNumber()
    {
        return $this->release_number;
    }

    /**
     * @return DateTime
     */
    public function getReleaseDate()
    {
        return $this->release_date;
    }
}