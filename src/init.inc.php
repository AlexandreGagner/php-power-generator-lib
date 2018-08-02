<?php

define('INCLUDE_PATH', get_include_path());

set_include_path(INCLUDE_PATH.'./'.PATH_SEPARATOR.__DIR__);

if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $config['debug_ip']))
  define('DEBUG', true);
else
  define('DEBUG', false);

$autoloader = require __DIR__.'/../vendor/autoload.php';

foreach (glob(__DIR__.'/controller/*.php', GLOB_BRACE) as $filename)
    $autoloader->add(basename($filename, '.php'), dirname($filename));
foreach (glob(__DIR__.'/classes/*.php', GLOB_BRACE) as $filename)
    $autoloader->add(basename($filename, '.php'), dirname($filename));

if (DEBUG)
{
	$configuration = [
	    'settings' => [
	        'displayErrorDetails' => true,
	    ],
	];
} else
	$configuration = [];

$container = new \Slim\Container($configuration);

$loader = new Twig_Loader_Filesystem([__DIR__.'/views/', $config['path'].'data/']);
$twig = new Twig_Environment($loader, array(
    'cache' => __DIR__.'/../cache/',
    'debug' => DEBUG,
    'auto_reload' => true,
));
$twig->addGlobal('DEBUG', DEBUG);
// $twig->addGlobal('CONFIG', $config);
$twig->addGlobal('URL', $config['url']);

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
    	global $twig;
        return $c['response']->withStatus(404)->write($twig->render('404.html'));
    };
};

global $slim;
$slim = new Slim\App($container);

DB::$user = $config['database']['user'];
DB::$password = $config['database']['password'];
DB::$dbName = $config['database']['db_name'];
DB::$encoding = 'utf8';

$twig->addFunction(new Twig_SimpleFunction('GET_URL', function ($name, $args = []) {
	global $slim;
	$path = $slim->getContainer()->get('router')->pathFor($name, $args);
	return ($config['url'].$path);
}));

if (DEBUG)
{
	$filter = new Twig_SimpleFilter('d', 'd');
	$twig->addFilter($filter);
	$filter = new Twig_SimpleFilter('dd', 'dd');
	$twig->addFilter($filter);
}
else
{
    $filter = new Twig_SimpleFilter('d', function ($void) {});
    $twig->addFilter($filter);
    $filter = new Twig_SimpleFilter('dd',  function ($void) {});
    $twig->addFilter($filter);
}

function domain_exists($email, $record = 'MX'){
	list($user, $domain) = split('@', $email);
	return checkdnsrr($domain, $record);
}

$db = new PDO('mysql:dbname='.$config['database']['db_name'].';host=localhost;charset=utf8mb4', $config['database']['user'], $config['database']['password']);
$auth = new \Delight\Auth\Auth($db, true);

if ($auth->isLoggedIn()) {
	$admin = new Admin($auth->getUserId());
	$twig->addGlobal('admin', $admin);
}
else
	$twig->addGlobal('admin', false);

Message::init($twig);

function genPassword($len = 8, $alternate = false) {
	if ($alternate)
    	$alphabet = 'abcdef0123456789';
    else
		$alphabet = 'abcdefghkmnpqrstuvwxyzABCDEFGHKMNPQRSTUVWXYZ123456789';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $len; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}