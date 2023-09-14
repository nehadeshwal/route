<?php
namespace Tnq\Routes\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use Tnq\Routes\Response\ResponseHandlerComponent;
use Tnq\Routes\Logger\Logger;

class FrontController {
    private $app = null;
    private $context = null;

    public function __construct(
        $context, array $routes, ResponseHandlerComponent $responseHandler,
        $options = []
    ) {
        $this->app = new Application();
        $this->app['debug'] = true;
        $this->context = $context;
        $this->responseHandler = $responseHandler;
        $this->setupRoutes($routes, $options);
    }

    private function getError($message, $status = false) {
        $errorData = [];
        $errorData['data'] = " ";
        $errorData['message'] = $message;
        $errorData['status'] = $status;
        return $errorData;
    }

    public function setupRoutes(array $routes, array $options = []) {

        foreach ($routes as $route) {
            $s = $route['invoke'];
            $pos = strpos($s, '->');
            $className = trim(substr($s, 0, $pos));
            $method = trim(substr($s, $pos + strlen('->')));
            $this->app->match(
                $route['url'], function(Request $request
            ) use($className, $method) {
                return call_user_func(
                    [$this->context[$className], $method], $request
                );
            })->method($route['method']);
        }

        $this->app->match("{url}", function($url) {
            return $this->responseHandler->createTextResponse('OK', 200);
        })->assert('url', '.*')->method('OPTIONS');

        $this->app->error(function (\Exception $e, $code) use ($options) {
            $error = $e->getMessage();
            if (array_key_exists('errorLogger', $options) === true) {
                $logger = $options['errorLogger'];
                if ($logger instanceof Logger === false) {
                    throw new \Exception(
                        'logger object should implements the interface Tnq\Routes\Logger\Logger'
                    );
                }

                $logger->logException($e);
            }

            if ((array_key_exists('errorAsJson', $options) === true) &&
                ($options['errorAsJson'] === true)
            ) {
                $code = $e->getCode() ? $e->getCode() : 500;
                $response = $this->responseHandler->createResponse(
                    $this->getError($error),
                    $code
                );

                return $response;
            }

            $response = $this->responseHandler->createTextResponse($error, 500);
            $response->headers->set('Content-Type', 'text/html');
            //$response->headers->set('X-Status-Code', 500);

            return $response;
        });
    }

    public function getSilex() {
        return $this->app;
    }

    public function run() {
        $this->app->run();
    }
}
