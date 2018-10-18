<?php

namespace Zipofar\Misc;

use Symfony\Component\HttpFoundation\Response;

trait ResponseBuilder
{
    /**
     * Response builder
     *
     * @param array $response       Payload data for response
     * @param integer $countRecords Count records from model
     * @return Response
     */
    protected function buildResponse($response, $countRecords)
    {
        $newResponse = [
            'meta' => [
                'number_of_records' => $countRecords,
            ],
            'payload' => $response,
        ];

        if (empty($response)) {
            $emptyPayload = new \stdClass();
            $newResponse['payload'] = $emptyPayload;
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $statusCode = Response::HTTP_OK;
        }

        $this->response->setStatusCode($statusCode);
        $this->response->setContent(json_encode($newResponse));
        $this->response->headers->set('content-type', 'application/json');

        return $this->response;
    }
}