<?php namespace App\Events;
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

/**
 * Class SummitOrderRefundAccepted
 * @package App\Events
 */
class SummitOrderRefundAccepted extends SummitRegistrationOrderAction
{
    /**
     * @var array
     */
    private $tickets_to_return;
    /**
     * @var array
     */
    private $promo_codes_to_return;

    /**
     * SummitOrderRefundAccepted constructor.
     * @param int $order_id
     * @param array $tickets_to_return
     * @param array $promo_codes_to_return
     */
    public function __construct(int $order_id, array $tickets_to_return, array $promo_codes_to_return)
    {
        parent::__construct($order_id);
        $this->promo_codes_to_return = $promo_codes_to_return;
        $this->tickets_to_return = $tickets_to_return;
    }

    /**
     * @return array
     */
    public function getTicketsToReturn(): array
    {
        return $this->tickets_to_return;
    }

    /**
     * @return array
     */
    public function getPromoCodesToReturn(): array
    {
        return $this->promo_codes_to_return;
    }

}