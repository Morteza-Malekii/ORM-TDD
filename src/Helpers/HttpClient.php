<?php
namespace App\Helpers;

use GuzzleHttp\Client;
use App\Helpers;

class HttpClient extends Client
{
    public function __construct()
    {
        $config = config::get('app');
        parent::__construct(['base_uri' => $config['base_uri']]);
    }
}

