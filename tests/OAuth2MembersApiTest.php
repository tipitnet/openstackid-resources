<?php

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
final class OAuth2MembersApiTest extends ProtectedApiTest
{

    public function testGetMembers()
    {

        $params = [
            //AND FILTER
            'filter' => ['first_name=@Seba', 'last_name=@Marcet'],
            'order'  => '+first_name,-last_name'
        ];

        $headers = array("HTTP_Authorization" => " Bearer " . $this->access_token);
        $response = $this->action(
            "GET",
            "OAuth2MembersApiController@getAll",
            $params,
            array(),
            array(),
            array(),
            $headers
        );

        $content = $response->getContent();
        $members = json_decode($content);
        $this->assertTrue(!is_null($members));
        $this->assertResponseStatus(200);
    }

    public function testGetMembersEmpty()
    {

        $params = [
            'filter' => ['first_name=@', 'last_name=@'],
            //AND FILTER
            'order'  => '+first_name,-last_name'
        ];

        $headers = ["HTTP_Authorization" => " Bearer " . $this->access_token];
        $response = $this->action(
            "GET",
            "OAuth2MembersApiController@getAll",
            $params,
            [],
            [],
            [],
            $headers
        );

        $content = $response->getContent();
        $this->assertResponseStatus(412);
    }

    public function testGetMembersByEmail()
    {
        $params = [
            'filter' => 'email=@sebastian@tipit.net',
            'order'  => '+first_name,-last_name',
            'expand' => 'groups'
        ];

        $headers  = array("HTTP_Authorization" => " Bearer " . $this->access_token);
        $response = $this->action(
            "GET",
            "OAuth2MembersApiController@getAll",
            $params,
            array(),
            array(),
            array(),
            $headers
        );

        $content = $response->getContent();
        $members = json_decode($content);
        $this->assertTrue(!is_null($members));
        $this->assertResponseStatus(200);
    }

    public function testGetMembersByEmail2()
    {
        $params = [
            'filter' => ['email==sean.mcginnis@gmail.com', "email_verified==0"],
        ];

        $headers  = array("HTTP_Authorization" => " Bearer " . $this->access_token);
        $response = $this->action(
            "GET",
            "OAuth2MembersApiController@getAll",
            $params,
            array(),
            array(),
            array(),
            $headers
        );

        $content = $response->getContent();
        $members = json_decode($content);
        $this->assertTrue(!is_null($members));
        $this->assertResponseStatus(200);
    }

    public function testGetMyMember()
    {
        $params = [
            'expand' => 'groups'
        ];

        $headers  = array("HTTP_Authorization" => " Bearer " . $this->access_token);
        $response = $this->action(
            "GET",
            "OAuth2MembersApiController@getMyMember",
            $params,
            array(),
            array(),
            array(),
            $headers
        );

        $content = $response->getContent();
        $member = json_decode($content);
        $this->assertTrue(!is_null($member));
        $this->assertResponseStatus(200);
    }

    public function testGetMembersByGitHubUser()
    {
        $params = [
            'filter' => 'github_user=@smarcet',
            'order'  => '+first_name,-last_name',
            'expand' => 'groups, ccla_teams'
        ];

        $headers  = ["HTTP_Authorization" => " Bearer " . $this->access_token];
        $response = $this->action(
            "GET",
            "OAuth2MembersApiController@getAll",
            $params,
            array(),
            array(),
            array(),
            $headers
        );

        $content = $response->getContent();
        $members = json_decode($content);
        $this->assertTrue(!is_null($members));
        $this->assertResponseStatus(200);
    }

    public function testUpdateMemberAffiliation(){
        $params = [
            'member_id'      => 11624,
            'affiliation_id' => 61749,
        ];

        $data = [
            'is_current' => true,
            'end_date'   => 0,
            'job_title'  => 'test update'
        ];

        $headers = [
            "HTTP_Authorization" => " Bearer " . $this->access_token,
            "CONTENT_TYPE"       => "application/json"
        ];

        $response = $this->action(
            "PUT",
            "OAuth2MembersApiController@updateAffiliation",
            $params,
            [],
            [],
            [],
            $headers,
            json_encode($data)
        );

        $content = $response->getContent();
        $this->assertResponseStatus(200);
        $affiliation = json_decode($content);
        $this->assertTrue(!is_null($affiliation));
        return $affiliation;
    }

    public function testGetMemberAffiliation($member_id = 11624)
    {

        $params = [
            //AND FILTER
            'member_id' => $member_id
        ];

        $headers = ["HTTP_Authorization" => " Bearer " . $this->access_token];
        $response = $this->action(
            "GET",
            "OAuth2MembersApiController@getMemberAffiliations",
            $params,
            [],
            [],
            [],
            $headers
        );

        $content = $response->getContent();
        $affiliations = json_decode($content);
        $this->assertTrue(!is_null($affiliations));
        $this->assertResponseStatus(200);
    }

}