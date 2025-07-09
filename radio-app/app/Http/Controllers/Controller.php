<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;
#[
    OA\Info(version: '1.0.0',description: 'Appointment booking application for a radiology department', title: 'Radiology API'),
    OA\Server(url: 'http://localhost:8000/api', description: 'Local server'),
    OA\Server(url: 'https://radiologie-rdv.com/api', description: 'Production server'),
    OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', name: 'Authorization', description: 'Bearer token for authentication'),
]
abstract class Controller
{
    //
}
