<?php namespace App\Services\Model;
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
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use libs\utils\ITransactionService;
use models\exceptions\EntityNotFoundException;
use models\exceptions\ValidationException;
use models\main\IMemberRepository;
use models\main\Member;
use models\summit\factories\SummitAttendeeFactory;
use models\summit\factories\SummitAttendeeTicketFactory;
use models\summit\ISummitAttendeeRepository;
use models\summit\ISummitAttendeeTicketRepository;
use models\summit\ISummitRegistrationPromoCodeRepository;
use models\summit\ISummitTicketTypeRepository;
use models\summit\Summit;
use models\summit\SummitAttendee;
use models\summit\SummitAttendeeBadge;
use models\summit\SummitAttendeeTicket;
use models\summit\SummitTicketType;
use services\apis\IEventbriteAPI;
/**
 * Class AttendeeService
 * @package App\Services\Model
 */
final class AttendeeService extends AbstractService implements IAttendeeService
{

    /**
     * @var ISummitAttendeeRepository
     */
    private $attendee_repository;

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var ISummitTicketTypeRepository
     */
    private $ticket_type_repository;

    /**
     * @var ISummitAttendeeTicketRepository
     */
    private $ticket_repository;

    /**
     * @var IEventbriteAPI
     */
    private $eventbrite_api;

    /**
     * @var ISummitRegistrationPromoCodeRepository
     */
    private $promo_code_repository;


    public function __construct
    (
        ISummitAttendeeRepository $attendee_repository,
        IMemberRepository $member_repository,
        ISummitAttendeeTicketRepository $ticket_repository,
        ISummitTicketTypeRepository $ticket_type_repository,
        ISummitRegistrationPromoCodeRepository $promo_code_repository,
        IEventbriteAPI $eventbrite_api,
        ITransactionService $tx_service
    )
    {
        parent::__construct($tx_service);
        $this->attendee_repository    = $attendee_repository;
        $this->ticket_repository      = $ticket_repository;
        $this->member_repository      = $member_repository;
        $this->ticket_type_repository = $ticket_type_repository;
        $this->promo_code_repository  = $promo_code_repository;
        $this->eventbrite_api         = $eventbrite_api;
    }

    /**
     * @param Summit $summit
     * @param array $data
     * @return mixed|SummitAttendee
     * @throws \Exception
     */
    public function addAttendee(Summit $summit, array $data)
    {
        return $this->tx_service->transaction(function() use($summit, $data){

            $member    = null;
            $member_id = $data['member_id'] ?? 0;
            $member_id = intval($member_id);
            $email     = $data['email'] ?? null;

            if($member_id > 0 && !empty($email)){
                // both are defined
                throw new ValidationException("you should define a member_id or an email, not both");
            }

            if($member_id > 0 ) {

                $member = $this->member_repository->getById($member_id);
                if (is_null($member) || !$member instanceof Member)
                    throw new EntityNotFoundException("member not found");

                $old_attendee = $this->attendee_repository->getBySummitAndMember($summit, $member);

                if (!is_null($old_attendee))
                    throw new ValidationException(sprintf("attendee already exist for summit id %s and member id %s", $summit->getId(), $member->getIdentifier()));

            }

            if(!empty($email)) {
                $old_attendee = $this->attendee_repository->getBySummitAndEmail($summit, trim($email));
                if (!is_null($old_attendee))
                    throw new ValidationException(sprintf("attendee already exist for summit id %s and email %s", $summit->getId(), trim($data['email'])));
            }

            $attendee = SummitAttendeeFactory::build($summit, $data, $member);

            $this->attendee_repository->add($attendee);

            return $attendee;
        });
    }

    /**
     * @param Summit $summit
     * @param int $attendee_id
     * @return void
     * @throws ValidationException
     * @throws EntityNotFoundException
     */
    public function deleteAttendee(Summit $summit, $attendee_id)
    {
        return $this->tx_service->transaction(function() use($summit, $attendee_id){

            $attendee = $summit->getAttendeeById($attendee_id);
            if(is_null($attendee))
                throw new EntityNotFoundException();

            $this->attendee_repository->delete($attendee);
        });
    }

    /**
     * @param Summit $summit
     * @param int $attendee_id
     * @param array $data
     * @return SummitAttendee
     * @throws ValidationException
     * @throws EntityNotFoundException
     */
    public function updateAttendee(Summit $summit, $attendee_id, array $data)
    {
        return $this->tx_service->transaction(function() use($summit, $attendee_id, $data){

            $attendee = $summit->getAttendeeById($attendee_id);
            if(is_null($attendee))
                throw new EntityNotFoundException(sprintf("attendee does not belongs to summit id %s", $summit->getId()));

            $member = null;
            if(isset($data['member_id'])) {
                $member_id = intval($data['member_id']);
                $member = $this->member_repository->getById($member_id);

                if (is_null($member))
                    throw new EntityNotFoundException("member not found");

                $old_attendee = $this->attendee_repository->getBySummitAndMember($summit, $member);
                if(!is_null($old_attendee) && $old_attendee->getId() != $attendee->getId())
                    throw new ValidationException(sprintf("another attendee (%s) already exist for summit id %s and member id %s", $old_attendee->getId(), $summit->getId(), $member->getIdentifier()));
            }

            if(isset($data['email'])) {
                $old_attendee = $this->attendee_repository->getBySummitAndEmail($summit, trim($data['email']));
                if(!is_null($old_attendee) && $old_attendee->getId() != $attendee->getId())
                    throw new ValidationException(sprintf("attendee already exist for summit id %s and email %s", $summit->getId(), trim($data['email'])));
            }

            // check if attendee already exist for this summit

            SummitAttendeeFactory::populate($summit, $attendee , $data, $member);

            return $attendee;
        });
    }

    /**
     * @param SummitAttendee $attendee
     * @param int $ticket_id
     * @throws ValidationException
     * @throws EntityNotFoundException
     * @return SummitAttendeeTicket
     */
    public function deleteAttendeeTicket(SummitAttendee $attendee, $ticket_id)
    {
        return $this->tx_service->transaction(function() use($attendee, $ticket_id){
            $ticket = $attendee->getTicketById($ticket_id);
            if(is_null($ticket)){
                throw new EntityNotFoundException(sprintf("ticket id %s does not belongs to attendee id %s", $ticket_id, $attendee->getId()));
            }
            $attendee->removeTicket($ticket);
        });
    }

    /**
     * @param Summit $summit
     * @param int $page_nbr
     * @return mixed
     */
    public function updateRedeemedPromoCodes(Summit $summit, $page_nbr = 1)
    {
        return $this->tx_service->transaction(function() use($summit, $page_nbr){
            $response = $this->eventbrite_api->getAttendees($summit, $page_nbr);

            if(!isset($response['pagination'])) return false;
            if(!isset($response['attendees'])) return false;
            $pagination = $response['pagination'];
            $attendees  = $response['attendees'];
            $has_more_items = boolval($pagination['has_more_items']);

            foreach($attendees as $attendee){
                if(!isset($attendee['promotional_code'])) continue;
                $promotional_code = $attendee['promotional_code'];
                if(!isset($promotional_code['code'])) continue;
                $code = $promotional_code['code'];

                $promo_code = $this->promo_code_repository->getByCode($code);
                if(is_null($promo_code)) continue;
                $promo_code->setRedeemed(true);
            }

            return $has_more_items;
        });
    }

    /**
     * @param Summit $summit
     * @param SummitAttendee $attendee
     * @param Member $other_member
     * @param int $ticket_id
     * @return SummitAttendeeTicket
     * @throws \Exception
     */
    public function reassignAttendeeTicketByMember(Summit $summit, SummitAttendee $attendee, Member $other_member, int $ticket_id):SummitAttendeeTicket
    {
        return $this->tx_service->transaction(function() use($summit, $attendee, $other_member, $ticket_id){
            $ticket = $this->ticket_repository->getByIdExclusiveLock($ticket_id);

            if(is_null($ticket) || !$ticket instanceof SummitAttendeeTicket){
                throw new EntityNotFoundException("ticket not found");
            }

            $new_owner = $this->attendee_repository->getBySummitAndMember($summit, $other_member);
            if(is_null($new_owner)){
                $new_owner = SummitAttendeeFactory::build($summit,[
                    'first_name' => $other_member->getFirstName(),
                    'last_name'  => $other_member->getLastName(),
                    'email'      => $other_member->getEmail(),
                ], $other_member);
                $this->attendee_repository->add($new_owner);
            }

            $attendee->sendRevocationTicketEmail($ticket);

            $attendee->removeTicket($ticket);

            $new_owner->addTicket($ticket);

            $ticket->generateQRCode();
            $ticket->generateHash();

            $new_owner->sendInvitationEmail($ticket);

            return $ticket;
        });
    }


    /**
     * @param Summit $summit
     * @param SummitAttendee $attendee
     * @param int $ticket_id
     * @param array $payload
     * @return SummitAttendeeTicket
     * @throws \Exception
     */
    public function reassignAttendeeTicket(Summit $summit, SummitAttendee $attendee, int $ticket_id, array $payload):SummitAttendeeTicket
    {
        return $this->tx_service->transaction(function() use($summit, $attendee, $ticket_id, $payload){
            $ticket = $this->ticket_repository->getByIdExclusiveLock($ticket_id);

            if(is_null($ticket) || !$ticket instanceof SummitAttendeeTicket){
                throw new EntityNotFoundException("ticket not found");
            }

            $attendee_email = $payload['attendee_email'] ?? null;

            $new_owner = $this->attendee_repository->getBySummitAndEmail($summit , $attendee_email);

            if(is_null($new_owner)){
                Log::debug(sprintf("attendee %s does no exists .. creating it ", $attendee_email));
                $attendee_payload = [
                    'email'  => $attendee_email
                ];

                $new_owner = SummitAttendeeFactory::build
                (
                    $summit,
                    $attendee_payload,
                    $this->member_repository->getByEmail($attendee_email)
                );

                $this->attendee_repository->add($new_owner);
            }

            $attendee_payload = [];

            if(isset($payload['attendee_first_name']))
                $attendee_payload['first_name'] = $payload['attendee_first_name'];

            if(isset($payload['attendee_last_name']))
                $attendee_payload['last_name'] = $payload['attendee_last_name'];

            if(isset($payload['attendee_company']))
                $attendee_payload['company'] = $payload['attendee_company'];

            if(isset($payload['extra_questions']))
                $attendee_payload['extra_questions'] = $payload['extra_questions'];

            SummitAttendeeFactory::populate($summit, $new_owner , $attendee_payload);

            $attendee->sendRevocationTicketEmail($ticket);

            $attendee->removeTicket($ticket);

            $new_owner->addTicket($ticket);

            $ticket->generateQRCode();
            $ticket->generateHash();

            $new_owner->sendInvitationEmail($ticket);

            return $ticket;
        });

    }

}