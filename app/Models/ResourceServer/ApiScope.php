<?php namespace models\resource_server;
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

use models\utils\BaseModelEloquent;

/**
* Class ApiScope
* @package models\resource_server
*/
class ApiScope extends BaseModelEloquent implements IApiScope
{

	protected $table = 'api_scopes';

	protected $hidden = array('');

	protected $fillable = array('name' ,'short_description', 'description','active','default','system', 'api_id');

	/**
	* @return IApi
	*/
	public function api()
	{
		return $this->belongsTo('models\resource_server\Api', 'api_id');
	}

	public function getShortDescription()
	{
		return $this->short_description;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function isActive()
	{
		return $this->active;
	}
}