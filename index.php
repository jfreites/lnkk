<?php

session_cache_limiter(false);
session_start();

require 'vendor/autoload.php';

require_once 'lib/MySql.php';
require_once 'lib/ShortUrl.php';
require_once 'config.php';

// Init objects...

$app = new \Slim\Slim(
	array(
        'log.enabled'    => $config['debug'],
        'templates.path' => $config['view_path'],
    )
);

$db = connect_db();
$shortUrl = new ShortUrl($db);

// Setup some things for the view...

$app->hook('slim.before', function() use ($app) {
	$app->view()->appendData(
		array('baseUrl' => 'http://lnkk.mx')
	);
});

// Routes...

$app->get('/', function() use ($app) {
    $app->render('home.php', 
    	array('title' => 'Lnkk.mx - Acorta | Comparte | Mide')
    );
});

$app->get('/error', function() use ($app) {
    echo 'ERROR 404';
});

$app->post('/encode', function() use ($app, $shortUrl) {

	try {

		$code = $shortUrl->urlToShortCode($app->request->post('long_url'));

		header("Content-Type: application/json");
		echo json_encode($code); exit;
	}
	catch (Exception $e) {
        $app->flash('error', $e->getMessage());
	}
});


$app->get('/:hash', function($hash) use ($app, $shortUrl) {

	try {

        $url = $shortUrl->shortCodeToUrl($hash);
        $app->redirect($url);

	} catch (Exception $e) {
        $app->flash('error', $e->getMessage());
        //var_dump($e->getMessage());
	}

});


// Go, go, go...

$app->run();
