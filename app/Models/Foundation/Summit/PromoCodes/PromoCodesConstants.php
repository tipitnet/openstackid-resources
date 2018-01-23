<?php namespace App\Models\Foundation\Summit\PromoCodes;
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
/**
 * Class PromoCodesValidClasses
 * @package App\Models\Foundation\Summit\PromoCodes
 */
final class PromoCodesConstants
{
    public static $valid_class_names = [
        SpeakerSummitRegistrationPromoCode::ClassName,
        SponsorSummitRegistrationPromoCode::ClassName,
        MemberSummitRegistrationPromoCode::ClassName,
    ];

    /**
     * @return array
     */
    public static function getValidTypes(){
        return array_merge(MemberSummitRegistrationPromoCode::$valid_type_values, SpeakerSummitRegistrationPromoCode::$valid_type_values);
    }
}