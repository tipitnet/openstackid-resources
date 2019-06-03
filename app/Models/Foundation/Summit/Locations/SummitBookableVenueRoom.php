<?php namespace models\summit;
/**
 * Copyright 2019 OpenStack Foundation
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
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping AS ORM;
use models\exceptions\ValidationException;
/**
 * @ORM\Entity
 * @ORM\Table(name="SummitBookableVenueRoom")
 * Class SummitBookableVenueRoom
 * @package models\summit
 */
class SummitBookableVenueRoom extends SummitVenueRoom
{

    const ClassName = 'SummitBookableVenueRoom';

    /**
     * @ORM\Column(name="TimeSlotCost", type="decimal")
     * @var float
     */
    private $time_slot_cost;

    /**
     * @var string
     * @ORM\Column(name="Currency", type="string")
     */
    private $currency;

    /**
     * @ORM\OneToMany(targetEntity="models\summit\SummitRoomReservation", mappedBy="room", cascade={"persist"}, orphanRemoval=true)
     * @var ArrayCollection
     */
    private $reservations;

    /**
     * @ORM\ManyToMany(targetEntity="models\summit\SummitBookableVenueRoomAttributeValue")
     * @ORM\JoinTable(name="SummitBookableVenueRoom_Attributes",
     *      joinColumns={@ORM\JoinColumn(name="SummitBookableVenueRoomID", referencedColumnName="ID")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="SummitBookableVenueRoomAttributeValueID", referencedColumnName="ID", unique=true)}
     *      )
     */
    private $attributes;

    public function __construct()
    {
        parent::__construct();
        $this->reservations = new ArrayCollection();
        $this->attributes   = new ArrayCollection();
    }

    /**
     * @param SummitRoomReservation $reservation
     * @return $this
     * @throws ValidationException
     */
    public function addReservation(SummitRoomReservation $reservation){
        $criteria = Criteria::create();

        $start_date = $reservation->getStartDatetime();
        $end_date   = $reservation->getEndDatetime();

        $criteria
            ->where(Criteria::expr()->eq('start_datetime', $start_date))
            ->andWhere(Criteria::expr()->eq('end_datetime',$end_date))
            ->andWhere(Criteria::expr()->notIn("status", [SummitRoomReservation::RequestedRefundStatus, SummitRoomReservation::RefundedStatus]));

        if($this->reservations->matching($criteria)->count() > 0)
            throw new ValidationException(sprintf("reservation overlaps an existent reservation"));

        $criteria
            ->where(Criteria::expr()->lte('start_datetime', $end_date))
            ->andWhere(Criteria::expr()->gte('end_datetime', $start_date))
            ->andWhere(Criteria::expr()->notIn("status", [SummitRoomReservation::RequestedRefundStatus, SummitRoomReservation::RefundedStatus]));

        if($this->reservations->matching($criteria)->count() > 0)
            throw new ValidationException(sprintf("reservation overlaps an existent reservation"));

        $summit = $this->summit;

        $local_start_date = $summit->convertDateFromUTC2TimeZone($start_date);
        $local_end_date   = $summit->convertDateFromUTC2TimeZone($end_date);
        $start_time       = $summit->getMeetingRoomBookingStartTime();
        $end_time         = $summit->getMeetingRoomBookingEndTime();

        if(!$summit->isTimeFrameInsideSummitDuration($local_start_date, $local_end_date))
            throw new ValidationException("requested reservation period does not belong to summit period");
        $local_start_time = new \DateTime("now", $this->summit->getTimeZone());
        $local_start_time->setTime(
            intval($start_time->format("H")),
            intval($start_time->format("i")),
            intval($start_time->format("s"))
        );

        $local_end_time = new \DateTime("now", $this->summit->getTimeZone());
        $local_end_time->setTime(
            intval($end_time->format("H")),
            intval($end_time->format("i")),
            intval($end_time->format("s"))
        );

        $local_start_time->setDate
        (
            intval($start_date->format("Y")),
            intval($start_date->format("m")),
            intval($start_date->format("d"))
        );

        $local_end_time->setDate
        (
            intval($start_date->format("Y")),
            intval($start_date->format("m")),
            intval($start_date->format("d"))
        );

        if(!($local_start_time <= $local_start_date
        && $local_end_date <= $local_end_time))
            throw new ValidationException("requested booking time slot is not allowed!");

        $interval = $end_date->diff($start_date);
        $minutes  =  ($interval->d * 24 * 60) + ($interval->h * 60) + $interval->i;
        if($minutes != $summit->getMeetingRoomBookingSlotLength())
            throw new ValidationException("requested booking time slot is not allowed!");

        $this->reservations->add($reservation);
        $reservation->setRoom($this);
        return $this;
    }

    /**
     * @return float
     */
    public function getTimeSlotCost(): float
    {
        return floatval($this->time_slot_cost);
    }

    /**
     * @param float $time_slot_cost
     */
    public function setTimeSlotCost(float $time_slot_cost): void
    {
        $this->time_slot_cost = $time_slot_cost;
    }

    /**
     * @return ArrayCollection
     */
    public function getReservations(): ArrayCollection
    {
        return $this->reservations;
    }

    public function clearReservations(){
        $this->reservations->clear();
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return self::ClassName;
    }

    /**
     * @param \DateTime $day should be on local summit day
     * @return array
     * @throws ValidationException
     */
    public function getAvailableSlots(\DateTime $day):array{
        $availableSlots     = [];
        $summit             = $this->summit;
        $day                = $day->setTimezone($summit->getTimeZone())->setTime(0, 0,0);
        $booking_start_time = $summit->getMeetingRoomBookingStartTime();
        if(is_null($booking_start_time))
            throw new ValidationException("MeetingRoomBookingStartTime is null!");

        $booking_end_time   = $summit->getMeetingRoomBookingEndTime();
        if(is_null($booking_end_time))
            throw new ValidationException("MeetingRoomBookingEndTime is null!");

        $booking_slot_len   = $summit->getMeetingRoomBookingSlotLength();
        $start_datetime     = clone $day;
        $end_datetime       = clone $day;

        $start_datetime->setTime(
            intval($booking_start_time->format("H")),
            intval($booking_start_time->format("i")),
            0);
        $start_datetime->setTimezone($summit->getTimeZone());

        $end_datetime->setTime(
            intval($booking_end_time->format("H")),
            intval($booking_end_time->format("i")),
            00);
        $end_datetime->setTimezone($summit->getTimeZone());
        $criteria = Criteria::create();
        if(!$summit->isTimeFrameInsideSummitDuration($start_datetime, $end_datetime))
            throw new ValidationException("requested day does not belong to summit period");

        $criteria
            ->where(Criteria::expr()->gte('start_datetime', $summit->convertDateFromTimeZone2UTC($start_datetime)))
            ->andWhere(Criteria::expr()->lte('end_datetime', $summit->convertDateFromTimeZone2UTC($end_datetime)))
            ->andWhere(Criteria::expr()->notIn("status", [SummitRoomReservation::RequestedRefundStatus, SummitRoomReservation::RefundedStatus]));

        $reservations = $this->reservations->matching($criteria);

        while($start_datetime <= $end_datetime) {
            $current_time_slot_end = clone $start_datetime;
            $current_time_slot_end->add(new \DateInterval("PT" . $booking_slot_len . 'M'));
            if($current_time_slot_end<=$end_datetime)
                $availableSlots[$start_datetime->format('Y-m-d H:i:s').'|'.$current_time_slot_end->format('Y-m-d H:i:s')] = true;
            $start_datetime = $current_time_slot_end;
        }

        foreach ($reservations as $reservation){
            if(!$reservation instanceof SummitRoomReservation) continue;
            $availableSlots[
                $summit->convertDateFromUTC2TimeZone($reservation->getStartDatetime())->format("Y-m-d H:i:s")
                .'|'.
                $summit->convertDateFromUTC2TimeZone($reservation->getEndDatetime())->format("Y-m-d H:i:s")
            ] = false;
        }

        return $availableSlots;
    }

    /**
     * @param \DateTime $day
     * @return array
     * @throws ValidationException
     */
    public function getFreeSlots(\DateTime $day):array{
        $slots = $this->getAvailableSlots($day);
        $free_slots = [];
        foreach ($slots as $label => $status){
            if(!$status) continue;
            $free_slots[] = $label;
        }
        return $free_slots;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


}