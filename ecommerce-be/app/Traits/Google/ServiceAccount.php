<?php 

namespace App\Traits\Google;
use Google\Client;

trait ServiceAccount {

    public function createServiceAccount( $params ): Client {

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path() . '\google-service-account.json');

        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->setApplicationName($params['appName']);
        $client->setScopes($params['scopes']);
        $client->setAccessType($params['accessType']);
        $client->setSubject($params['subject']);

        return $client;
    }
}