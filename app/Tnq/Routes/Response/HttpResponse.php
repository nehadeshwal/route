<?php
namespace Tnq\Routes\Response;

use Symfony\Component\HttpFoundation\Response;

class HttpResponse {
    private $headers;
    private $content;

    public function __construct(
        Response $response,
        ResponseHandlerComponent $responseHandler
    ) {
        $this->responseHandler = $responseHandler;
        $this->response = $response;
    }

    public function addHeader($name, $value) {
        $this->response->headers->set($name, $value);
    }

    public function setContent($content) {
        $this->response->setContent($content);
    }

    private function defaultResponse() {
        $defaultResponseData = [
            'status' => true,
            'message' => null,
            'data' => null
        ];

        return $defaultResponseData;
    }

    public function generateResponse(array $responseData) {
        $defaultResponseData = $this->defaultResponse();
        $filteredResponse = array_replace_recursive(
            $defaultResponseData, $responseData
        );

        $responsecontent  = $this->responseHandler->generateResponse(
            $filteredResponse
        );
        $this->response->setContent($responsecontent);
        $this->addHeader(
            'Content-Type',
            $this->responseHandler->getDefaultContentType()
        );

        return $this->response;
    }

    private function setHeaderCookies($response, $cookies) {
        foreach ($cookies as $cookie) {
            $response->headers->setCookie(new Cookie($cookie[0], $cookie[1]));
        }
    }

    private function clearCookies($response, $cookie) {
        $response->headers->clearCookie($cookie);
    }
}
