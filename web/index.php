<?php

require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;


$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
            'http' => true,
            'users' => array(
                'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
            ),
        ),
    )
));

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views',
]);

$app->get('/', function(Request $request)  use ( $app ) {
    $pageData = [
        'pageTitle' => 'Home',
        'pageClass' => 'homepage',
        'Welcome' => 'Monitoring App',
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
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
  
$app->run();
