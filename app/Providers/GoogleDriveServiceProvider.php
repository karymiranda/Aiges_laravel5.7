<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \Storage::extend('google', function ($app, $config) {
    $client = new \Google_Client();
    $client->setClientId($config['clientId']);
    $client->setClientSecret($config['clientSecret']);
    $client->refreshToken($config['refreshToken']);

     $client->fetchAccessTokenWithRefreshToken($config['refreshToken']); //prueba

    $service = new \Google_Service_Drive($client);
    $adapter = new \Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter($service, $config['folderId']);
    return new \League\Flysystem\Filesystem($adapter);
    // $adapter = new GoogleDriveAdapter($service, $config['folderId']);
    //return new Filesystem($adapter);
});
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}