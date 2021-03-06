<?php namespace App\Services\Apis;
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
use Illuminate\Http\Request as LaravelRequest;
use Exception;

/**
 * Class CartAlreadyPaidException
 * @package App\Services\Apis
 */
class CartAlreadyPaidException extends Exception {

}
/**
 * Interface IPaymentGatewayAPI
 * @package App\Services\Apis
 */
interface IPaymentGatewayAPI
{
    /**
     * @param array $info
     * @return array
     */
    public function generatePayment(array $payload):array;

    /**
     * @param LaravelRequest $request
     * @return array
     */
    public function processCallback(LaravelRequest $request):array;

    /**
     * @param array $payload
     * @return bool
     */
    public function isSuccessFullPayment(array $payload):bool;

    /**
     * @param array $payload
     * @return string
     */
    public function getPaymentError(array $payload):?string;

    /**
     * @param string $cart_id
     * @param float $amount
     * @param string $currency
     * @throws \InvalidArgumentException
     */
    public function refundPayment(string $cart_id, float $amount,  string $currency): void;

    /**
     * @param string $cart_id
     * @return mixed|void
     * @throws CartAlreadyPaidException
     */
    public function abandonCart(string $cart_id);

    /**
     * @param string $status
     * @return bool
     */
    public function canAbandon(string $status):bool;

    /**
     * @param string $cart_id
     * @return string
     */
    public function getCartStatus(string $cart_id):string;

    /**
     * @param string $status
     * @return bool
     */
    public function isSucceeded(string $status):bool;
}