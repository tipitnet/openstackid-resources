<?php namespace models\summit;
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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use models\main\Group;
use models\main\Member;
use models\main\PushNotificationMessage;
/**
 * Class SummitPushNotificationChannel
 * @package models\summit
 */
final class SummitPushNotificationChannel {

    const Everyone  = 'EVERYONE';
    const Speakers  = 'SPEAKERS';
    const Attendees = 'ATTENDEES';
    const Members   = 'MEMBERS';
    const Summit    = 'SUMMIT';
    const Event     = 'EVENT';
    const Group     = 'GROUP';

    /**
     * @return array
     */
    public static function getPublicChannels(){
        return [self::Everyone, self::Speakers, self::Attendees, self::Summit, self::Event, self::Group];
    }

    /**
     * @param string $channel
     * @return bool
     */
    public static function isPublicChannel($channel){
        return in_array($channel, self::getPublicChannels());
    }
}
/**
 * @ORM\Entity(repositoryClass="App\Repositories\Summit\DoctrineSummitNotificationRepository")
 * @ORM\AssociationOverrides({
 *     @ORM\AssociationOverride(
 *          name="summit",
 *          inversedBy="notifications"
 *     )
 * })
 * @ORM\Table(name="SummitPushNotification")
 * Class SummitPushNotification
 * @package models\summit
 */
class SummitPushNotification extends PushNotificationMessage
{
    use SummitOwned;

    /**
     * @ORM\Column(name="Channel", type="string")
     * @var string
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="models\summit\SummitEvent")
     * @ORM\JoinColumn(name="EventID", referencedColumnName="ID")
     * @var SummitEvent
     */
    private $summit_event;

    /**
     * @ORM\ManyToOne(targetEntity="models\main\Group")
     * @ORM\JoinColumn(name="GroupID", referencedColumnName="ID")
     * @var Group
     */
    private $group;

    /**
     * @ORM\ManyToMany(targetEntity="models\main\Member")
     * @ORM\JoinTable(name="SummitPushNotification_Recipients",
     *      joinColumns={@ORM\JoinColumn(name="SummitPushNotificationID", referencedColumnName="ID")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="MemberID", referencedColumnName="ID")}
     *      )
     */
    private $recipients;

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return SummitEvent
     */
    public function getSummitEvent()
    {
        return $this->summit_event;
    }

    /**
     * @param SummitEvent $summit_event
     */
    public function setSummitEvent($summit_event)
    {
        $this->summit_event = $summit_event;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * SummitPushNotification constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->recipients = new ArrayCollection;
    }

    /**
     * @param Member $member
     * @return $this
     */
    public function addRecipient(Member $member){
        $this->recipients->add($member);
        return $this;
    }
}