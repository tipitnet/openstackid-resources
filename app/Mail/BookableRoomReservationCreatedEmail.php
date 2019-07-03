<?php namespace App\Mail;
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
use Illuminate\Support\Facades\Config;
/**
 * Class BookableRoomReservationCreatedEmail
 * @package App\Mail
 */
final class BookableRoomReservationCreatedEmail extends AbstractBookableRoomReservationEmail
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = Config::get("mail.bookable_room_reservation_created_email_subject");
        if(empty($subject))
            $subject = sprintf("[%s] Room Reservation Created", Config::get('app.app_name'));

        return $this->from(Config::get("mail.from"))
            ->to($this->reservation->getOwner()->getEmail())
            ->subject($subject)
            ->view('emails.bookable_rooms.reservation_created');
    }
}