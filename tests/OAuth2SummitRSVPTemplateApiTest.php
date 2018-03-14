<?php
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

final class OAuth2SummitRSVPTemplateApiTest extends ProtectedApiTest
{
    public function testGetSummitRSVPTemplates($summit_id = 23)
    {
        $params = [
            'id'       => $summit_id,
            'page'     => 1,
            'per_page' => 5,
            'order'    => '-id'
        ];

        $headers =
            [
                "HTTP_Authorization" => " Bearer " . $this->access_token,
                "CONTENT_TYPE"       => "application/json"
            ];

        $response = $this->action
        (
            "GET",
            "OAuth2SummitRSVPTemplatesApiController@getAllBySummit",
            $params,
            [],
            [],
            [],
            $headers
        );

        $content = $response->getContent();
        $this->assertResponseStatus(200);

        $rsvp_templates = json_decode($content);
        $this->assertTrue(!is_null($rsvp_templates));
        return $rsvp_templates;
    }

    public function testGetRSVPTemplateById($summit_id = 23){

        $templates = $this->testGetSummitRSVPTemplates($summit_id);


        $params = [
            'id'          => $summit_id,
            'template_id' => $templates->data[0]->id,
        ];

        $headers =
            [
                "HTTP_Authorization" => " Bearer " . $this->access_token,
                "CONTENT_TYPE"       => "application/json"
            ];

        $response = $this->action
        (
            "GET",
            "OAuth2SummitRSVPTemplatesApiController@getRSVPTemplate",
            $params,
            [],
            [],
            [],
            $headers
        );

        $content = $response->getContent();
        $this->assertResponseStatus(200);

        $rsvp_template = json_decode($content);
        $this->assertTrue(!is_null($rsvp_template));
        return $rsvp_template;
    }

    public function testDeleteRSVPTemplate($summit_id = 23){

        $template = $this->testGetRSVPTemplateById($summit_id);

        $params = [
            'id'          => $summit_id,
            'template_id' => $template->id
        ];

        $headers =
            [
                "HTTP_Authorization" => " Bearer " . $this->access_token,
                "CONTENT_TYPE"       => "application/json"
            ];

        $response = $this->action
        (
            "DELETE",
            "OAuth2SummitRSVPTemplatesApiController@deleteRSVPTemplate",
            $params,
            [],
            [],
            [],
            $headers
        );

        $content = $response->getContent();
        $this->assertResponseStatus(204);

    }
}