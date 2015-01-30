<?php

require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider;

$app = new Silex\Application();
 
$app[ 'db.options' ] = include( "config.php" );
$app[ 'debug' ] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views',
]);


//$app->register(new Provider\SecurityServiceProvider());
//$app->register(new Provider\RememberMeServiceProvider());
$app->register(new Provider\DoctrineServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\UrlGeneratorServiceProvider());
$app->register(new Provider\SwiftmailerServiceProvider());

$app->get('/', function(Request $request)  use ( $app ) {
    $pageData = [
        'pageTitle' => 'Home',
        'pageClass' => 'homepage',
        'Welcome' => 'Monitoring App',
    ];
    
    return $app['twig']->render( 'homepage.twig', $pageData );
});

$app->get('/admin', function () use ( $app ) {
    $pageData = [
        'pageTitle' => 'Dashboard',
        'pageClass' => 'dashboard',
    ];
    return $app['twig']->render( 'dashboard.twig', $pageData );
});

$app->get('/logout', function () use ( $app ) {
    // @TODO: Add redirct to
    return false;
});

$app->post('/login', function () use ( $app ) {
    $username = filter_input( INPUT_POST, 'username', FILTER_SANITIZE_STRING );
    $password = filter_input( INPUT_POST, 'password', FILTER_SANITIZE_STRING );
    
    if ($username == 'admin' && $password == 'password')
    {
        return $app->redirect('/profile');
    }
    return $app->redirect('/');

});

$app->post('/data', function () use ( $app ) {
    $data = filter_input( INPUT_POST, 'data', FILTER_SANITIZE_STRING );
    return $data;
});
  
$app->run();
