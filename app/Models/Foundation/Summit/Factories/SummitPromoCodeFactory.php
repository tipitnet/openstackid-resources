<?php namespace App\Models\Foundation\Summit\Factories;
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
use models\summit\MemberSummitRegistrationPromoCode;
use models\summit\SpeakerSummitRegistrationPromoCode;
use models\summit\SponsorSummitRegistrationPromoCode;
use models\summit\Summit;
/**
 * Class SummitPromoCodeFactory
 * @package App\Models\Foundation\Summit\Factories
 */
final class SummitPromoCodeFactory
{
    public static function build(Summit $summit, array $data, array $params = []){
        $promo_code = null;
        switch ($data['class_name']){
            case MemberSummitRegistrationPromoCode::ClassName:{
                $promo_code = new MemberSummitRegistrationPromoCode();
                if(isset($params['owner']))
                    $promo_code->setOwner($params['owner']);
                if(isset($data['type']))
                    $promo_code->setType($data['type']);
                if(isset($data['first_name']))
                    $promo_code->setFirstName(trim($data['first_name']));
                if(isset($data['last_name']))
                    $promo_code->setLastName(trim($data['last_name']));
                if(isset($data['email']))
                    $promo_code->setEmail(trim($data['email']));
            }
            break;
            case SpeakerSummitRegistrationPromoCode::ClassName:{
                $promo_code = new SpeakerSummitRegistrationPromoCode();
                if(isset($data['type']))
                    $promo_code->setType($data['type']);
                $promo_code->setSpeaker($params['speaker']);
            }
            break;
            case SponsorSummitRegistrationPromoCode::ClassName:{
                $promo_code = new SponsorSummitRegistrationPromoCode();
                $promo_code->setSponsor($params['sponsor']);
            }
            break;
        }

        if(is_null($promo_code)) return null;

        $promo_code->setCode(trim($data['code']));
        $summit->addPromoCode($promo_code);
        return $promo_code;
    }
}