<?php
namespace Tnq\Routes\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\HttpFoundation\Response;
use Tnq\Routes\Response\ResponseHandlerComponent;

class ResponseFactory {
    private $cors;

    public function __construct($cors = []) {
        $this->cors = $cors;
    }


    public function createHandler($type = 'application/json') {
        $encoders = [new JsonEncoder()];
        $normalizers = [new GetSetMethodNormalizer()];
        $serializer =  new Serializer($normalizers, $encoders);
        $responseHandler = new ResponseHandlerComponent(
            $serializer, $type, $this->cors
        );
        return $responseHandler;
    }
}
