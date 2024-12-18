<?php

namespace App\Service\Response;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseProvider
{
    /**
     *
     */
    const SUCCESS = "Success";
    /**
     *
     */
    const FAILURE = "Failure";
    /**
     * @var array
     */
    private array $jsonData = array();


    /**
     * @return $this
     */
    public function builder(): static
    {
        $this->jsonData = array("meta" => array(), "content" => array());

        return $this;
    }

    public function status(string $status): static
    {
        $this->jsonData['meta']['status'] = $status;
        return $this;
    }

    public function httpCode(string $httpCode): static
    {
        $this->jsonData['meta']['httpCode'] = $httpCode;
        return $this;
    }

    public function errorMessage(array|string $errorMessage): static
    {
        $this->jsonData['meta']['errors'] = $errorMessage;

        return $this;
    }

    public function requestData(array|string $requestData): static
    {
        if (!is_string($requestData))
            $this->jsonData['meta']['requestData'] = $requestData;
        else
            $this->jsonData['meta']['requestData'] = json_decode($requestData);
        return $this;
    }

    public function addExtraToMeta(string $key, array|string $data): static
    {
        $this->jsonData['meta'][$key] = $data;
        return $this;
    }


    /**
     * @param $content
     * @return $this
     */
    public function content($content): static
    {

        // If it isn't a string, it isn't serialized.
        if (!is_string($content)) {
            $this->jsonData['content'] = $content;
        } else
            // to prevent double serialization
            $this->jsonData['content'] = json_decode($content);

        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function build(): JsonResponse
    {
        $this->jsonData['meta']['lastSyncDateAndTime'] = $this->getDateTime();

        $jsonResponse = new JsonResponse($this->jsonData);

        $jsonResponse->headers->set('Content-Type', 'application/json');
        $jsonResponse->setStatusCode($this->jsonData['meta']['httpCode']);

        return $jsonResponse;
    }


    /** Exception in Server */
    /**
     * @param $requestedData
     * @return JsonResponse
     */
    public function Error500($requestedData): JsonResponse
    {
        return $this->builder()
            ->status(JsonResponseProvider::FAILURE)
            ->httpCode(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->errorMessage(["A server error has occurred"])
            ->requestData($requestedData)
            ->build();
    }

    /** Problem with data ( validation errors ) */
    /**
     * @param string $errorMessage
     * @param $requestData
     * @return JsonResponse
     */
    public function error422(string $errorMessage, $requestData): JsonResponse
    {
        return $this
            ->builder()
            ->status(JsonResponseProvider::FAILURE)
            ->httpCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->errorMessage([$errorMessage])
            ->requestData($requestData)
            ->build();
    }

    /**
     * @param $errorMessage
     * @param $requestData
     * @return JsonResponse
     *
     * Data not in valid format ( json is malformed )
     */
    public function error400($errorMessage, $requestData): JsonResponse
    {

        return $this
            ->builder()
            ->status(JsonResponseProvider::FAILURE)
            ->httpCode(Response::HTTP_BAD_REQUEST)
            ->errorMessage([$errorMessage])
            ->requestData($requestData)
            ->build();
    }


    /**
     * @param array|string $requestedData
     * @param array|string $responseData
     * @return JsonResponse
     */
    public function success200(array|string $requestedData, array|string $responseData = []): JsonResponse
    {
        return $this
            ->builder()
            ->status(JsonResponseProvider::SUCCESS)
            ->httpCode(Response::HTTP_OK)
            ->requestData($requestedData)
            ->content($responseData)
            ->build();
    }

    /**
     * @param string $errorMessage
     * @param string $requestData
     * @return JsonResponse
     *
     * Tried to create a resource where it already exists
     */
    public function error409(string $errorMessage, string $requestData): JsonResponse
    {
        return $this
            ->builder()
            ->status(JsonResponseProvider::FAILURE)
            ->httpCode(Response::HTTP_CONFLICT)
            ->errorMessage([$errorMessage])
            ->requestData($requestData)
            ->build();

    }

    private function getDateTime(): string
    {
        $dateTime = new  DateTimeImmutable();
        return $dateTime->setTimezone(new DateTimeZone("UTC"))->format(DateTimeInterface::RFC850);
    }
}