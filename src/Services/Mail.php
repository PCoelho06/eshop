<?php

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = 'b06369da468331bf099564aa39e1b404';
    private $api_key_secret = '72ffaa5c6c8903c41889eca4fd3903da';
    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "p.coelho@lapinou.tech",
                        'Name' => "My boutique"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 5681421, // voir dans mailjet
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => json_decode('{"content": $content}', true)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
