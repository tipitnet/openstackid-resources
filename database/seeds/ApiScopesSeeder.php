<?php
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

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;;
use App\Models\ResourceServer\ApiScope;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Illuminate\Support\Facades\DB;

/**
 * Class ApiScopesSeeder
 */
final class ApiScopesSeeder extends Seeder
{

    public function run()
    {
        DB::table('endpoint_api_scopes')->delete();
        DB::table('api_scopes')->delete();

        $this->seedSummitScopes();
        $this->seedMembersScopes();
        $this->seedTeamsScopes();
    }

    private function seedSummitScopes()
    {

        $current_realm = Config::get('app.url');
        $api = EntityManager::getRepository(\App\Models\ResourceServer\Api::class)->findOneBy(['name' => 'summits']);

        $scopes = [
            array(
                'name' => sprintf('%s/summits/read', $current_realm),
                'short_description' => 'Get Summit Data',
                'description' => 'Grants read only access for Summits Data',
            ),
            array(
                'name' => sprintf('%s/me/read', $current_realm),
                'short_description' => 'Get own summit member data',
                'description' => 'Grants read only access for our own summit member data',
            ),
            array(
                'name' => sprintf('%s/me/summits/events/favorites/add', $current_realm),
                'short_description' => 'Allows to add Summit events as favorite',
                'description' => 'Allows to add Summit events as favorite',
            ),
            array(
                'name' => sprintf('%s/me/summits/events/favorites/delete', $current_realm),
                'short_description' => 'Allows to remove Summit events as favorite',
                'description' => 'Allows to remove Summit events as favorite',
            ),
            array(
                'name' => sprintf('%s/summits/write', $current_realm),
                'short_description' => 'Write Summit Data',
                'description' => 'Grants write access for Summits Data',
            ),
            array(
                'name' => sprintf('%s/summits/write-event', $current_realm),
                'short_description' => 'Write Summit Events',
                'description' => 'Grants write access for Summits Events',
            ),
            array(
                'name' => sprintf('%s/summits/delete-event', $current_realm),
                'short_description' => 'Delete Summit Events',
                'description' => 'Grants delete access for Summits Events',
            ),
            array(
                'name' => sprintf('%s/summits/publish-event', $current_realm),
                'short_description' => 'Publish/UnPublish Summit Events',
                'description' => 'Grants Publish/UnPublish access for Summits Events',
            ),
            array(
                'name' => sprintf('%s/summits/read-external-orders', $current_realm),
                'short_description' => 'Allow to read External Orders',
                'description' => 'Allow to read External Orders',
            ),
            array(
                'name' => sprintf('%s/summits/confirm-external-orders', $current_realm),
                'short_description' => 'Allow to confirm External Orders',
                'description' => 'Allow to confirm External Orders',
            ),
            array(
                'name' => sprintf('%s/summits/write-videos', $current_realm),
                'short_description' => 'Allow to write presentation videos',
                'description' => 'Allow to write presentation videos',
            ),
            array(
                'name' => sprintf('%s/summits/read-notifications', $current_realm),
                'short_description' => 'Allow to read summit notifications',
                'description' => 'Allow to read summit notifications',
            )
        ];

        foreach ($scopes as $scope_info) {
            $scope = new ApiScope();
            $scope->setName($scope_info['name']);
            $scope->setShortDescription($scope_info['short_description']);
            $scope->setDescription($scope_info['description']);
            $scope->setActive(true);
            $scope->setDefault(false);
            $scope->setApi($api);
            EntityManager::persist($scope);
        }

        EntityManager::flush();

    }

    private function seedMembersScopes(){
        $current_realm = Config::get('app.url');
        $api = EntityManager::getRepository(\App\Models\ResourceServer\Api::class)->findOneBy(['name' => 'members']);

        $scopes = [
            array(
                'name' => sprintf('%s/members/read', $current_realm),
                'short_description' => 'Get Members Data',
                'description' => 'Grants read only access for Members Data',
            ),
            array(
                'name' => sprintf('%s/members/read/me', $current_realm),
                'short_description' => 'Get My Member Data',
                'description' => 'Grants read only access for My Member',
            ),
            array(
                'name' => sprintf('%s/members/invitations/read', $current_realm),
                'short_description' => 'Allows read only access to invitations',
                'description' => 'Allows read only access to invitations',
            ),
            array(
                'name' => sprintf('%s/members/invitations/write', $current_realm),
                'short_description' => 'Allows write only access to invitations',
                'description' => 'Allows write only access to invitations',
            ),
        ];

        foreach ($scopes as $scope_info) {
            $scope = new ApiScope();
            $scope->setName($scope_info['name']);
            $scope->setShortDescription($scope_info['short_description']);
            $scope->setDescription($scope_info['description']);
            $scope->setActive(true);
            $scope->setDefault(false);
            $scope->setApi($api);
            EntityManager::persist($scope);
        }

        EntityManager::flush();
    }

    private function seedTeamsScopes(){
        $current_realm = Config::get('app.url');
        $api = EntityManager::getRepository(\App\Models\ResourceServer\Api::class)->findOneBy(['name' => 'teams']);

        $scopes = [
            array(
                'name' => sprintf('%s/teams/read', $current_realm),
                'short_description' => 'Get Teams Data',
                'description' => 'Grants read only access for Teams Data',
            ),
            array(
                'name' => sprintf('%s/teams/write', $current_realm),
                'short_description' => 'Write Teams Data',
                'description' => 'Grants write access for Teams Data',
            ),
        ];

        foreach ($scopes as $scope_info) {
            $scope = new ApiScope();
            $scope->setName($scope_info['name']);
            $scope->setShortDescription($scope_info['short_description']);
            $scope->setDescription($scope_info['description']);
            $scope->setActive(true);
            $scope->setDefault(false);
            $scope->setApi($api);
            EntityManager::persist($scope);
        }

        EntityManager::flush();
    }

}