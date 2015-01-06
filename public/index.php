<?php

require_once __DIR__ . '/../bootstrap.php';

use Silex\Application;
use AwinHaproxyLogParser\Controller\Index as IndexController;
use AwinHaproxyLogParser\Controller\Parse as ParseController;
use AwinHaproxyLogParser\Controller\StaticAssets as StaticAssetsController;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Response;

$application = new Application();

$twigServiceProvider = new TwigServiceProvider();
$application->register($twigServiceProvider, array('twig.path' => APPLICATION_ROOT_DIR . '/view/'));
$twigEnvironment = $application['twig'];

$application->get(
    'static',
    function (Request $request) {
        $controller = new StaticAssetsController();
        return $controller->serveAction($request);
    }
);

$application->get(
    '/',
    function () use ($twigEnvironment) {
        $controller = new IndexController();
        return $controller->indexAction($twigEnvironment);
    }
);

$application->post(
    '/parse/',
    function (Request $request) {
        $controller = new ParseController();
        return $controller->parseAction($request);
    }
);

$application->error(
    function (Exception $e, $code) {
        $content = var_export(array($e->getMessage(), $e->getTrace()), true);
        return new Response($content);
    }
);

$application->run();
