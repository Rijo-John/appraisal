<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\InternalUser;

class CommonController extends Controller
{
    
    public function syncUsers()
    {
        $userInstance = new InternalUser();
        $client = new Client();

        // Get URL from .env file
        $url = env('USERSYNCURL');

        // Make a GET request
        $response = $client->get($url);

        // Get the response body as a string
        $body = $response->getBody()->getContents();

        // Decode the JSON response
        $data = json_decode($body, true);

        // Insert users (assuming insertUsers is a method in your InternalUser model)
        $userInstance->insertUsers($data);
    }
}
