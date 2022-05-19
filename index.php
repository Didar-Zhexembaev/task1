<?php
define('ROOT', __DIR__);
define('CONFIG_PATH', ROOT . '/config');

require_once './vendor/autoload.php';
require_once CONFIG_PATH . '/database.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel;

$locator = new FileLocator([CONFIG_PATH]);
$loader = new YamlFileLoader($locator);
$routes = $loader->load('routes.yml');

$context = new RequestContext();
$request = Request::createFromGlobals();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);
$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();

try {
	$matcher = $matcher->match($request->getPathInfo());
	$request->attributes->add($matcher);

	$controller = $controllerResolver->getController($request);
	$arguments = $argumentResolver->getArguments($request, $controller);

	call_user_func_array($controller, $arguments);
} catch (\Exception $e) {
	$response = new JsonResponse(
		['status' => 'error', 'code' => JsonResponse::HTTP_NOT_FOUND],
		JsonResponse::HTTP_NOT_FOUND
	);
	$response->send();
}