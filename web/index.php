<?php

require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;

//Register the security service provider like this:
use Silex\Provider;
//events
use SimpleUser\UserEvents;

$app = new Silex\Application();
 
//To configure the Silex security service to use the SimpleUser\UserManager as its user provider, add it to your security.firewalls configuration like this:
$app->register(new Provider\SecurityServiceProvider());

//Enable Doctrine something like this:
$app->register(new Provider\DoctrineServiceProvider());
 
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'dbname'   => 'MY_DBNAME',
    'user'     => 'MY_DB_USER',
    'password' => 'MY_DB_PASSWORD',
);
$app['debug'] = true;


$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views',
]);
//To use SimpleUser’s built-in routes and controllers, first register these additional services:
$app->register(new Provider\RememberMeServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\UrlGeneratorServiceProvider());

$app->register(new Provider\SwiftmailerServiceProvider());

//Then mount the SimpleUser routes and controllers like this:
$userServiceProvider = new SimpleUser\UserServiceProvider();
$app->register($userServiceProvider);
 
// Mount SimpleUser routes.
$app->mount('/user', $userServiceProvider);



//Configure the firewall to use these routes for form-based authentication. (Replace “/user” with whatever mount point you used with mount() above).
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/user/login$',
    ),
    'admin' => array(
        'pattern' => '^.*$',
        'anonymous' => false,
        'remember_me' => array(),
        'form' => array(
            'login_path' => '/user/login',
            'check_path' => '/user/login_check',
        ),
        'logout' => array(
            'logout_path' => '/user/logout',
        ),
        'users' => $app->share(function($app) { return $app['user.manager']; }),
    ),
);
if ($app['user.manager']->findOneBy(array('email' => 'christinarossi22@gmail.com')) == false) {
$user = $app['user.manager']->createUser('christinarossi22@gmail.com', 'password', 'christina rossi', array('ROLE_ADMIN'));
$app['user.manager']->insert($user);
}



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
