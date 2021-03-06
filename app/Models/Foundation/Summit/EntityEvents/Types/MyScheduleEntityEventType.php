<?php namespace Models\foundation\summit\EntityEvents;
/**
 * Copyright 2016 OpenStack Foundation
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
 * Class MyScheduleEntityEventType
 * @package Models\foundation\summit\EntityEvents
 */
final class MyScheduleEntityEventType extends SummitEventEntityEventType
{

    /**
     * @return void
     */
    public function process()
    {
        $member_id = $this->process_ctx->getCurrentMemberId();
        if (is_null($member_id) || intval($member_id) !== $this->entity_event->getOwnerId()) return;
        if($this->entity_event->getType() === 'INSERT'){
            $entity = $this->registerEntity();
            if (is_null($entity)) return;
        }
        $this->process_ctx->registerEntityEvent($this->entity_event);
    }
}