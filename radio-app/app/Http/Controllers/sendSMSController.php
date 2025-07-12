<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class sendSMSController extends Controller
{
    public function send(){
        $basic  = new \Vonage\Client\Credentials\Basic("34340558", "h3l2427Qjf0CdStw");
        $client = new \Vonage\Client($basic);
        $response = $client->sms()->send(
    new \Vonage\SMS\Message\SMS("+212680475084", "RadiologyApp Ayoub", 'A text message sent using the Nexmo SMS API')
);

        $message = $response->current();

        if ($message->getStatus() == 0) {
            echo "The message was sent successfully\n";
        } else {
            echo "The message failed with status: " . $message->getStatus() . "\n";
        }
        dd(config('services.vonage'));

}
}
