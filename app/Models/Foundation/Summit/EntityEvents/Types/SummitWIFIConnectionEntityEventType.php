<?php namespace Models\foundation\summit\EntityEvents;

use models\utils\IEntity;
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


/**
 * Class SummitWIFIConnectionEntityEventType
 * @package Models\foundation\summit\EntityEvents
 */
class SummitWIFIConnectionEntityEventType extends GenericSummitEntityEventType
{
    /**
     * @return IEntity|null
     */
    protected function registerEntity()
    {
        $this->evictEntity();
        $entity = $this->entity_event->getSummit()->getWifiConnection($this->entity_event->getEntityId());
        if(!is_null($entity))
            $this->entity_event->registerEntity($entity);
        return $entity;
    }
}