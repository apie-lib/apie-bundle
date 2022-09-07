<?php
namespace Apie\Tests\ApieBundle\Concerns;

use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait ItValidatesOpenapi
{
    public function validateResponse(Request $request, Response $response)
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);
        $psrResponse = $psrHttpFactory->createResponse($response);
        $validatorBuilder = (new ValidatorBuilder)
            ->fromJsonFile(__DIR__ . '/../../fixtures/expected-openapi.json');
        $requestValidator = $validatorBuilder->getRequestValidator();
        $responseValidator = $validatorBuilder->getResponseValidator();
        if ($psrResponse->getStatusCode() < 300) {
            $requestValidator->validate($psrRequest);
        }

        $operation = new OperationAddress($request->getPathInfo(), strtolower($request->getMethod()));
        $responseValidator->validate($operation, $psrResponse);
    }
}
