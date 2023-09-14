<?php
namespace Tnq\Routes\Response;

use Pimple;
use Symfony\Component\HttpFoundation\Cookie; //FIXME
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

class ResponseHandlerComponent {

    private $context;
    private $data;
    private $status;
    private $headers;
    private $requestType;
    private $serializer;
    private $responseFactory;

    public function __construct(
        Serializer $serializer, $contentType, $cors = []
    ) {
        $this->serializer = $serializer;
        $this->headers['Content-Type'] = $contentType;
        $this->cors = $cors;
    }

    public function generateResponse($responseData) {
        $result = [];
        $contentType = $this->headers['Content-Type'];
        $responseExecutors = [
            'application/json' => 'generateJsonResponse',
            'application/xml' => 'generateXmlResponse',
            'text/html' => 'generateJsonResponse'
        ];
        if (empty($responseExecutors[$contentType])) {
            die("Content-Type '". $contentType ."'' not supported");
        }
        if (is_callable([$this, $responseExecutors[$contentType]])) {
            $result = call_user_func(
                [$this, $responseExecutors[$contentType]], $responseData
            );
        }

        return $result;
    }

    private function generateDataResponse() {
        return $this->data;
    }

    private function generateXmlResponse($result) {
        $serializer = $this->serializer;
        return $serializer->serialize($result, 'xml');
    }

    private function generateJsonResponse($result) {
        if (json_encode($result) === false) {
            throw new \Exception(
                'Response Contains Json Parse Error States: '.json_last_error_msg()
            );
        }

        return json_encode($result);
    }

    private function sendCliResponse($data) {
        return $data;
    }

    public function setCrosHeaders(Response $response) {
        $corsValues = $this->crosOriginHandler();
        $allowedOrigin = $this->getAllowedOrigin(
            $corsValues['origin']
        );
        $headers = $response->headers;
        $headers->set(
            'Access-Control-Allow-Origin', $allowedOrigin
        );
        $headers->set(
            'Access-Control-Allow-Headers', $corsValues['accept_headers']
        );
        $headers->set(
            'Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE'
        );
        $headers->set(
            'Access-Control-Allow-Credentials', $corsValues['allow_credentials']
        );
    }

    public function createResponse($data, $status=200) {
        $response = new Response(null , $status);
        $this->setCrosHeaders($response);
        $httpResponse = new HttpResponse($response, $this);
        return $httpResponse->generateResponse($data);
    }

    public function createTextResponse($data, $status=200) {
        $response = new Response($data, $status);
        $this->setCrosHeaders($response);
        return $response;
    }

    private function crosOriginHandler() {
        $keyExists = array_key_exists('cors', $this->cors);
        if ($keyExists === false) {
            return [
                'origin' => ['*'],
                'allow_credentials' => 'false',
                'accept_headers' => 'content-type, x-requested-with',
                'accept_methods' => 'GET, POST, OPTIONS, PUT, DELETE'
            ];
        }

        return $this->cors['cors'];
    }

    private function getOriginHeader() {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            return $_SERVER['HTTP_ORIGIN'];
        }
        return null;
    }

    private function getAllowedOrigin(array $allowedOriginList) {
        $originHeader = $this->getOriginHeader();
        if (empty($originHeader)) {
            return null;
        }
        $matchedKey = array_search($originHeader, $allowedOriginList);
        if ($matchedKey >= 0) {
            $matchedOrigin = $allowedOriginList[$matchedKey];
            return $matchedOrigin;
        }
        return null;
    }

    public function getDefaultContentType() {
        return $this->headers['Content-Type'];
    }
}
