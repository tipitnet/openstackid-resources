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

use models\main\Tag;
use DB;

/**
 * Class Presentation
 * @package models\summit
 */
class Presentation extends SummitEvent
{
    protected $table = 'Presentation';

    protected $mtiClassType = 'concrete';

    /**
     * @var bool
     */
    private $from_speaker;

    protected $array_mappings = array
    (
        'ID'              => 'id:json_int',
        'Title'           => 'title:json_string',
        'Description'     => 'description:json_string',
        'StartDate'       => 'start_date:datetime_epoch',
        'EndDate'         => 'end_date:datetime_epoch',
        'LocationID'      => 'location_id:json_int',
        'SummitID'        => 'summit_id:json_int',
        'TypeID'          => 'type_id:json_int',
        'ClassName'       => 'class_name',
        'CategoryID'      => 'track_id:json_int',
        'ModeratorID'     => 'moderator_speaker_id:json_int',
        'Level'           => 'level',
        'AllowFeedBack'   => 'allow_feedback:json_boolean',
        'AvgFeedbackRate' => 'avg_feedback_rate:json_float',
        'Published'       => 'is_published:json_boolean',
        'HeadCount'       => 'head_count:json_int',
        'RSVPLink'        => 'rsvp_link:json_string',
    );

    /**
     * @return PresentationSpeaker[]
     */
    public function speakers()
    {
        return $this->belongsToMany('models\summit\PresentationSpeaker','Presentation_Speakers','PresentationID','PresentationSpeakerID')->get();
    }


    public function getSpeakerIds()
    {
        $ids = array();
        foreach($this->speakers() as $speaker)
        {
            array_push($ids, intval($speaker->ID));
        }
        return $ids;
    }

    public function setFromSpeaker()
    {
        $this->from_speaker = true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $values = parent::toArray();
        if(!$this->from_speaker)
        $values['speakers'] = $this->getSpeakerIds();

        $slides = array();
        foreach($this->slides() as $s)
        {
            array_push($slides, $s->toArray());
        }
        $values['slides'] = $slides;

        $videos = array();
        foreach($this->videos() as $v)
        {
            array_push($videos, $v->toArray());
        }
        $values['videos'] = $videos;

        return $values;
    }
    /**
     * @return PresentationVideo[]
     */
    public function videos()
    {
        $bindings = array('presentation_id' => $this->ID);
        $rows     = DB::connection('ss')->select("select * from `PresentationVideo` left join `PresentationMaterial` on `PresentationVideo`.`ID` = `PresentationMaterial`.`ID`
where `PresentationMaterial`.`PresentationID` = :presentation_id and `PresentationMaterial`.`PresentationID` is not null", $bindings);

        $videos = array();
        foreach($rows as $row)
        {
            $instance = new PresentationVideo;
            $instance->setRawAttributes((array)$row, true);
            array_push($videos, $instance);
        }
        return $videos;
    }

    /**
     * @return PresentationSlide[]
     */
    public function slides()
    {
        $bindings = array('presentation_id' => $this->ID);
        $rows     = DB::connection('ss')->select("select * from `PresentationSlide` left join `PresentationMaterial` on `PresentationSlide`.`ID` = `PresentationMaterial`.`ID`
where `PresentationMaterial`.`PresentationID` = :presentation_id and `PresentationMaterial`.`PresentationID` is not null", $bindings);

        $slides = array();
        foreach($rows as $row)
        {
            $instance = new PresentationSlide;
            $instance->setRawAttributes((array)$row, true);
            array_push($slides, $instance);
        }
        return $slides;
    }
}