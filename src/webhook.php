<?php

/**
 * Advertise TeamLeader events on Discord
 *
 * This application reformats TeamLeader webhook events into Discord
 * embeded cards.
 *
 * @author    Geert Hauwaerts <geert@hauwaerts.be>
 * @copyright Copyright (c) THINFACTORY NV
 * @license   MIT LIcense
 */

use Dotenv\Dotenv;
use SumoCoders\Teamleader\Teamleader;
use SumoCoders\Teamleader\Deals\Deal;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ . '/../');
$dotenv->load();

$cfg = json_decode(json_encode([
    'discord' => [
        'colors' => [
            'green' => 3779158,
            'red' => 14370117,
            'yellow' =>  15588927,
            'grey' =>  10329501
        ]
    ],
    'teamleader' => [
        'api' => [
            'group' => getenv('TEAMLEADER_API_GROUP'),
            'key' => getenv('TEAMLEADER_API_KEY')
        ],
        'uri' => [
            'base' => getenv('TEAMLEADER_APP_URL'),
            'sale_detail' => 'sale_detail.php?id=',
            'company_detail' => 'company_detail.php?id='
        ]
    ],
    'events' => [
        'default' => getenv('DISCORD_WEBHOOK')
    ]
]));

$req = json_decode(file_get_contents("php://input"));
$teamleader = new Teamleader($cfg->teamleader->api->group, $cfg->teamleader->api->key);
$webhook = isset($cfg->events->{$req->event_type}) ? $cfg->events->{$req->event_type} : $cfg->events->default;

if (!is_object($req)) {
    http_response_code(400);
    exit;
}

switch ($req->event_type) {
    case 'deal_signed':
        $deal = $teamleader->dealsGetDeal($req->object_id);
        $company = $teamleader->crmGetCompany($deal->getcompanyId());
        $payload = [
            'embeds' => [
                [
                    'color' => $cfg->discord->colors->green,
                    'author' => [
                        'name' => 'Deal #' . $deal->getOfferteNr() . ' Signed - ' . $deal->getTitle(),
                        'url' => $cfg->teamleader->uri->base . $cfg->teamleader->uri->sale_detail . $deal->getId()
                    ],
                    'title' => $company->getName() . ' ' . $company->getBusinessType(),
                    'url' => $cfg->teamleader->uri->base . $cfg->teamleader->uri->company_detail . $company->getId(),
                    'description' => 'Deal value: ' . $deal->getTotalPriceExclVat() . ' €',
                    'timestamp' => date(DateTime::ISO8601)
                ]
            ]
        ];
    break;

    default:
        $payload = [
            'embeds' => [
                [
                    'color' => $cfg->discord->colors->grey,
                    'title' => 'Unknown event - ' . $req->event_type,
                    'description' => json_encode($req, JSON_PRETTY_PRINT),
                    'timestamp' => date(DateTime::ISO8601)
                ]
            ]
        ];
    break;
}

file_get_contents(
    $webhook,
    false,
    stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($payload)
        ]
    ])
);
