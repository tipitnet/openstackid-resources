<?php namespace ModelSerializers;

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
use App\Http\Exceptions\HTTP403ForbiddenException;
use App\Security\SummitScopes;
use Illuminate\Support\Facades\Config;
use Libs\ModelSerializers\AbstractSerializer;
use models\summit\Summit;
use DateTime;
/**
 * Class SummitSerializer
 * @package ModelSerializers
 */
class SummitSerializer extends SilverStripeSerializer
{
    protected static $array_mappings = [
        'Name'                                           => 'name:json_string',
        'BeginDate'                                      => 'start_date:datetime_epoch',
        'EndDate'                                        => 'end_date:datetime_epoch',
        'RegistrationBeginDate'                          => 'registration_begin_date:datetime_epoch',
        'RegistrationEndDate'                            => 'registration_end_date:datetime_epoch',
        'StartShowingVenuesDate'                         => 'start_showing_venues_date:datetime_epoch',
        'ScheduleDefaultStartDate'                       => 'schedule_start_date:datetime_epoch',
        'Active'                                         => 'active:json_boolean',
        'TypeId'                                         => 'type_id:json_int' ,
        'DatesLabel'                                     => 'dates_label:json_string' ,
        'MaxSubmissionAllowedPerUser'                    => 'max_submission_allowed_per_user:json_int',
        // calculated attributes
        'PresentationVotesCount'                         => 'presentation_votes_count:json_int' ,
        'PresentationVotersCount'                        => 'presentation_voters_count:json_int' ,
        'AttendeesCount'                                 => 'attendees_count:json_int',
        'PaidTicketsCount'                               => 'paid_tickets_count:json_int',
        'SpeakersCount'                                  => 'speakers_count:json_int',
        'PresentationsSubmittedCount'                    => 'presentations_submitted_count:json_int',
        'PublishedEventsCount'                           => 'published_events_count:json_int',
        'SpeakerAnnouncementEmailAcceptedCount'          => 'speaker_announcement_email_accepted_count:json_int',
        'SpeakerAnnouncementEmailRejectedCount'          => 'speaker_announcement_email_rejected_count:json_int',
        'SpeakerAnnouncementEmailAlternateCount'         => 'speaker_announcement_email_alternate_count:json_int',
        'SpeakerAnnouncementEmailAcceptedAlternateCount' => 'speaker_announcement_email_accepted_alternate_count:json_int',
        'SpeakerAnnouncementEmailAcceptedRejectedCount'  => 'speaker_announcement_email_accepted_rejected_count:json_int',
        'SpeakerAnnouncementEmailAlternateRejectedCount' => 'speaker_announcement_email_alternate_rejected_count:json_int',
        'TimeZoneId'                                     => 'time_zone_id:json_string',
        'SecondaryRegistrationLink'                      => 'secondary_registration_link:json_string',
        'SecondaryRegistrationLabel'                     => 'secondary_registration_label:json_string',
        'RawSlug'                                        => 'slug:json_string',
        // Bookable rooms attributes
        'MeetingRoomBookingStartTime'                    => 'meeting_room_booking_start_time:datetime_epoch',
        'MeetingRoomBookingEndTime'                      => 'meeting_room_booking_end_time:datetime_epoch',
        'MeetingRoomBookingSlotLength'                   => 'meeting_room_booking_slot_length:json_int',
        'MeetingRoomBookingMaxAllowed'                   => 'meeting_room_booking_max_allowed:json_int',
        'BeginAllowBookingDate'                          => 'begin_allow_booking_date:datetime_epoch',
        'EndAllowBookingDate'                            => 'end_allow_booking_date:datetime_epoch',
        'LogoUrl'                                        => 'logo:json_url',
        // External Feeds
        'ApiFeedType'                                    => 'api_feed_type:json_string',
        'ApiFeedUrl'                                     => 'api_feed_url:json_string',
        'ApiFeedKey'                                     => 'api_feed_key:json_string',
        // registration
        'OrderQRPrefix'                                  => 'order_qr_prefix:json_string',
        'TicketQRPrefix'                                 => 'ticket_qr_prefix:json_string',
        'BadgeQRPrefix'                                  => 'badge_qr_prefix:json_string',
        'QRRegistryFieldDelimiter'                       => 'qr_registry_field_delimiter:json_string',
        'ReassignTicketTillDate'                         => 'reassign_ticket_till_date:datetime_epoch',
        'RegistrationDisclaimerContent'                  => 'registration_disclaimer_content:json_string',
        'RegistrationDisclaimerMandatory'                => 'registration_disclaimer_mandatory:json_boolean',
        'RegistrationReminderEmailDaysInterval'          => 'registration_reminder_email_days_interval:json_int',
    ];

    protected static $allowed_relations = [
        'ticket_types',
        'locations',
        'wifi_connections',
        'selection_plans',
        'meeting_booking_room_allowed_attributes',
        'summit_sponsors',
        'order_extra_questions',
        'tax_types',
    ];

    /**
     * @param null $expand
     * @param array $fields
     * @param array $relations
     * @param array $params
     * @return array
     * @throws HTTP403ForbiddenException
     */
    public function serialize($expand = null, array $fields = [], array $relations = [], array $params = [])
    {
        $summit              = $this->object;
        if(!$summit instanceof Summit) return [];
        $values              = parent::serialize($expand, $fields, $relations, $params);
        if(!count($relations)) $relations = $this->getAllowedRelations();

        $timezone            = $summit->getTimeZone();
        $values['time_zone'] = null;

        if (!is_null($timezone)) {
            $time_zone_info = $timezone->getLocation();
            $time_zone_info['name']   = $timezone->getName();
            $now                      = new DateTime($summit->getLocalBeginDate()->format('Y-m-d H:i:s'), $timezone);
            $time_zone_info['offset'] = $timezone->getOffset($now);
            $values['time_zone']      = $time_zone_info;
        }
        // pages info
        $main_page                             = $summit->getMainPage();
        $schedule_page                         = $summit->getSchedulePage();
        $values['page_url']                    =
            empty($main_page)? null :
            sprintf("%s%s", Config::get("server.assets_base_url", 'https://www.openstack.org/'), $main_page);
        $values['schedule_page_url'] = empty($schedule_page) ? null :
            sprintf("%s%s", Config::get("server.assets_base_url", 'https://www.openstack.org/'), $schedule_page);
        $values['schedule_event_detail_url'] =  empty($schedule_page) ? null : sprintf("%s%s/%s", Config::get("server.assets_base_url", 'https://www.openstack.org/'), $schedule_page, 'events/:event_id/:event_title');

        // tickets
        if(in_array('ticket_types', $relations)) {
            $ticket_types = [];
            foreach ($summit->getTicketTypes() as $ticket) {
                $ticket_types[] = SerializerRegistry::getInstance()->getSerializer($ticket)->serialize($expand);
            }
            $values['ticket_types'] = $ticket_types;
        }

        if(in_array('order_extra_questions', $relations)) {
            $order_extra_questions = [];
            foreach ($summit->getOrderExtraQuestions() as $question) {
                $order_extra_questions[] = SerializerRegistry::getInstance()->getSerializer($question)->serialize(AbstractSerializer::filterExpandByPrefix($expand,"order_extra_questions"));
            }
            $values['order_extra_questions'] = $order_extra_questions;
        }

        if(in_array('tax_types', $relations)) {
            $tax_types = [];
            foreach ($summit->getTaxTypes() as $tax_type) {
                $tax_types[] = SerializerRegistry::getInstance()->getSerializer($tax_type)->serialize(AbstractSerializer::filterExpandByPrefix($expand,"tax_types"));
            }
            $values['tax_types'] = $tax_types;
        }

        // meeting_booking_room_allowed_attributes
        if(in_array('meeting_booking_room_allowed_attributes', $relations)) {
            $meeting_booking_room_allowed_attributes = [];
            foreach ($summit->getMeetingBookingRoomAllowedAttributes() as $attr) {
                $meeting_booking_room_allowed_attributes[] = SerializerRegistry::getInstance()->getSerializer($attr)->serialize($expand);
            }
            $values['meeting_booking_room_allowed_attributes'] = $meeting_booking_room_allowed_attributes;
        }

        // summit sponsors
        if(in_array('summit_sponsors', $relations)) {
            $summit_sponsors = [];
            foreach ($summit->getSummitSponsors() as $sponsor) {
                $summit_sponsors[] = SerializerRegistry::getInstance()->getSerializer($sponsor)->serialize($expand);
            }
            $values['summit_sponsors'] = $summit_sponsors;
        }

        // locations
        if(in_array('locations', $relations)) {
            $locations = [];
            foreach ($summit->getLocations() as $location) {
                $locations[] = SerializerRegistry::getInstance()->getSerializer($location)->serialize($expand);
            }
            $values['locations'] = $locations;
        }

        // wifi connections
        if(in_array('wifi_connections', $relations)) {
            $wifi_connections = [];
            foreach ($summit->getWifiConnections() as $wifi_connection) {
                $wifi_connections[] = SerializerRegistry::getInstance()->getSerializer($wifi_connection)->serialize($expand);
            }
            $values['wifi_connections'] = $wifi_connections;
        }

        // selection plans
        if(in_array('selection_plans', $relations)) {
            $selection_plans = [];
            foreach ($summit->getSelectionPlans() as $selection_plan) {
                $selection_plans[] = SerializerRegistry::getInstance()->getSerializer($selection_plan)->serialize($expand);
            }
            $values['selection_plans'] = $selection_plans;
        }

        if (!empty($expand)) {
            $expand = explode(',', $expand);
            foreach ($expand as $relation) {
                switch (trim($relation)) {
                    case 'event_types':{
                        $event_types = [];
                        foreach ($summit->getEventTypes() as $event_type) {
                            $event_types[] = SerializerRegistry::getInstance()->getSerializer($event_type)->serialize();
                        }
                        $values['event_types'] = $event_types;
                    }
                    break;
                    case 'tracks':{
                        $presentation_categories = [];
                        foreach ($summit->getPresentationCategories() as $cat) {
                            $presentation_categories[] = SerializerRegistry::getInstance()->getSerializer($cat)->serialize();
                        }
                        $values['tracks'] = $presentation_categories;
                    }
                    break;
                    case 'track_groups':{
                        // track_groups
                        $track_groups = [];
                        foreach ($summit->getCategoryGroups() as $group) {
                            $track_groups[] = SerializerRegistry::getInstance()->getSerializer($group)->serialize();
                        }
                        $values['track_groups'] = $track_groups;
                    }
                    break;
                    case 'sponsors':{
                        $sponsors = [];
                        foreach ($summit->getEventSponsors() as $company) {
                            $sponsors[] = SerializerRegistry::getInstance()->getSerializer($company)->serialize();
                        }
                        $values['sponsors'] = $sponsors;
                    }
                    break;
                    case 'speakers':{
                        $speakers = [];
                        foreach ($summit->getSpeakers() as $speaker) {
                            $speakers[] =
                                SerializerRegistry::getInstance()->getSerializer($speaker)->serialize
                                (
                                    null, [], [],
                                    [
                                        'summit_id' => $summit->getId(),
                                        'published' => true
                                    ]
                                );

                        }
                        $values['speakers'] = $speakers;
                    }
                    break;
                    case 'schedule': {
                        // only could get schedule expanded if summit its available to public or
                        // we had proper scopes
                        if(!$summit->isAvailableOnApi()) {
                            $scopes = $this->resource_server_context->getCurrentScope();
                            $current_realm = Config::get('app.url');
                            $needed_scope = sprintf(SummitScopes::ReadAllSummitData, $current_realm);
                            if (!in_array($needed_scope, $scopes))
                                throw new HTTP403ForbiddenException;
                        }

                        $event_types = [];
                        foreach ($summit->getEventTypes() as $event_type) {
                            $event_types[] = SerializerRegistry::getInstance()->getSerializer($event_type)->serialize();
                        }
                        $values['event_types'] = $event_types;

                        $presentation_categories = [];
                        foreach ($summit->getPresentationCategories() as $cat) {
                            $presentation_categories[] = SerializerRegistry::getInstance()->getSerializer($cat)->serialize();
                        }
                        $values['tracks'] = $presentation_categories;

                        // track_groups
                        $track_groups = [];
                        foreach ($summit->getCategoryGroups() as $group) {
                            $track_groups[] = SerializerRegistry::getInstance()->getSerializer($group)->serialize();
                        }
                        $values['track_groups'] = $track_groups;

                        $schedule = [];
                        foreach ($summit->getScheduleEvents() as $event) {
                            $schedule[] = SerializerRegistry::getInstance()->getSerializer($event)->serialize();
                        }
                        $values['schedule'] = $schedule;

                        $sponsors = [];
                        foreach ($summit->getEventSponsors() as $company) {
                            $sponsors[] = SerializerRegistry::getInstance()->getSerializer($company)->serialize();
                        }
                        $values['sponsors'] = $sponsors;

                        $speakers = [];
                        foreach ($summit->getSpeakers() as $speaker) {
                            $speakers[] =
                                SerializerRegistry::getInstance()->getSerializer($speaker)->serialize
                                (
                                    null, [], [],
                                    [
                                        'summit_id' => $summit->getId(),
                                        'published' => true
                                    ]
                                );

                        }
                        $values['speakers'] = $speakers;
                    }
                    break;
                    case 'type':{
                        if(isset($values['type_id']))
                        {
                            unset($values['type_id']);
                            $values['type'] = $summit->hasType() ?
                                SerializerRegistry::getInstance()->getSerializer($summit->getType())->serialize() : null;
                        }
                    }
                    break;
                }
            }
        }

        $values['supported_currencies'] = $summit->getSupportedCurrencies();
        $values['timestamp'] = time();

        return $values;
    }
}