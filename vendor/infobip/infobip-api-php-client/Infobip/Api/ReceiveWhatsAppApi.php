<?php
/**
 * ReceiveWhatsAppApi
 * PHP version 7.2
 *
 * @category Class
 * @package  Infobip
 * @author   Infobip Support
 * @link     https://www.infobip.com
 */

/**
 * Infobip Client API Libraries OpenAPI Specification
 *
 * OpenAPI specification containing public endpoints supported in client API libraries.
 *
 * Contact: support@infobip.com
 *
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * Do not edit the class manually.
 */

namespace Infobip\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use Infobip\ApiException;
use Infobip\Configuration;
use Infobip\HeaderSelector;
use Infobip\ObjectSerializer;

/**
 * ReceiveWhatsAppApi Class Doc Comment
 *
 * @category Class
 * @package  Infobip
 * @author   Infobip Support
 * @link     https://www.infobip.com
 */
class ReceiveWhatsAppApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
    * @param ClientInterface $client
    * @param Configuration   $config
    * @param HeaderSelector  $selector
    */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation downloadWhatsAppInboundMedia
     *
     * Download inbound media
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplFileObject
     */
    public function downloadWhatsAppInboundMedia($sender, $mediaId)
    {
        list($response) = $this->downloadWhatsAppInboundMediaWithHttpInfo($sender, $mediaId);
        return $response;
    }

    /**
     * Operation downloadWhatsAppInboundMediaWithHttpInfo
     *
     * Download inbound media
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplFileObject, HTTP status code, HTTP response headers (array of strings)
     */
    public function downloadWhatsAppInboundMediaWithHttpInfo($sender, $mediaId)
    {
        $request = $this->downloadWhatsAppInboundMediaRequest($sender, $mediaId);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
                return $this->downloadWhatsAppInboundMediaResponse($response, $request->getUri());
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }
        } catch (ApiException $e) {
            throw $this->downloadWhatsAppInboundMediaApiException($e);
        }
    }

    /**
     * Operation downloadWhatsAppInboundMediaAsync
     *
     * Download inbound media
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function downloadWhatsAppInboundMediaAsync($sender, $mediaId)
    {
        return $this->downloadWhatsAppInboundMediaAsyncWithHttpInfo($sender, $mediaId)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation downloadWhatsAppInboundMediaAsyncWithHttpInfo
     *
     * Download inbound media
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function downloadWhatsAppInboundMediaAsyncWithHttpInfo($sender, $mediaId)
    {
        $request = $this->downloadWhatsAppInboundMediaRequest($sender, $mediaId);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($request) {
                    return $this->downloadWhatsAppInboundMediaResponse($response, $request->getUri());
                },
                function ($exception) {
                    $statusCode = $exception->getCode();
                    $response = $exception->getResponse();
                    $e = new ApiException(
                        "[{$statusCode}] {$exception->getMessage()}",
                        $statusCode,
                        $response ? $response->getHeaders() : null,
                        $response ? (string) $response->getBody() : null
                    );
                    throw $this->downloadWhatsAppInboundMediaApiException($e);
                }
            );
    }

    /**
     * Create request for operation 'downloadWhatsAppInboundMedia'
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function downloadWhatsAppInboundMediaRequest($sender, $mediaId)
    {
        // verify the required parameter 'sender' is set
        if ($sender === null || (is_array($sender) && count($sender) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $sender when calling downloadWhatsAppInboundMedia'
            );
        }
        // verify the required parameter 'mediaId' is set
        if ($mediaId === null || (is_array($mediaId) && count($mediaId) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mediaId when calling downloadWhatsAppInboundMedia'
            );
        }

        $resourcePath = '/whatsapp/1/senders/{sender}/media/{mediaId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';



        // path params
        if ($sender !== null) {
            $resourcePath = str_replace(
                '{' . 'sender' . '}',
                ObjectSerializer::toPathValue($sender),
                $resourcePath
            );
        }
        // path params
        if ($mediaId !== null) {
            $resourcePath = str_replace(
                '{' . 'mediaId' . '}',
                ObjectSerializer::toPathValue($mediaId),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['*/*'],
            []
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($headers['Content-Type'] === 'multipart/form-data') {
                $boundary = '----'.hash('sha256', uniqid('', true));
                $headers['Content-Type'] .= '; boundary=' . $boundary;
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents, $boundary);
            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires HTTP basic authentication
        if (!empty($this->config->getUsername()) || !(empty($this->config->getPassword()))) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'GET',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create response for operation 'downloadWhatsAppInboundMedia'
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @param string $requestUri
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @return array of \SplFileObject|null, HTTP status code, HTTP response headers (array of strings)
     */
    protected function downloadWhatsAppInboundMediaResponse($response, $requestUri)
    {
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseHeaders = $response->getHeaders();

        if ($statusCode < 200 || $statusCode > 299) {
            throw new ApiException(
                sprintf('[%d] Error connecting to the API (%s)', $statusCode, $requestUri),
                $statusCode,
                $responseHeaders,
                $responseBody
            );
        }

        $responseObject = null;

        if ($statusCode === 200) {
            $type = '\SplFileObject';
            if ($type === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }
            $responseObject = ObjectSerializer::deserialize($content, $type, $responseHeaders);

            return [
                $responseObject,
                $statusCode,
                $responseHeaders
            ];
        }

        return [
            $responseObject,
            $statusCode,
            $responseHeaders
        ];
    }

    /**
     * Adapt given \Infobip\ApiException for operation 'downloadWhatsAppInboundMedia'
     *
     * @param \Infobip\ApiException $apiException
     *
     * @return \Infobip\ApiException
     */
    protected function downloadWhatsAppInboundMediaApiException($apiException)
    {
        $statusCode = $apiException->getCode();

        return $apiException;
    }

    /**
     * Operation getWhatsAppMediaMetadata
     *
     * Get media metadata
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return string|string|string
     */
    public function getWhatsAppMediaMetadata($sender, $mediaId)
    {
        list($response) = $this->getWhatsAppMediaMetadataWithHttpInfo($sender, $mediaId);
        return $response;
    }

    /**
     * Operation getWhatsAppMediaMetadataWithHttpInfo
     *
     * Get media metadata
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of string|string|string, HTTP status code, HTTP response headers (array of strings)
     */
    public function getWhatsAppMediaMetadataWithHttpInfo($sender, $mediaId)
    {
        $request = $this->getWhatsAppMediaMetadataRequest($sender, $mediaId);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
                return $this->getWhatsAppMediaMetadataResponse($response, $request->getUri());
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }
        } catch (ApiException $e) {
            throw $this->getWhatsAppMediaMetadataApiException($e);
        }
    }

    /**
     * Operation getWhatsAppMediaMetadataAsync
     *
     * Get media metadata
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getWhatsAppMediaMetadataAsync($sender, $mediaId)
    {
        return $this->getWhatsAppMediaMetadataAsyncWithHttpInfo($sender, $mediaId)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getWhatsAppMediaMetadataAsyncWithHttpInfo
     *
     * Get media metadata
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getWhatsAppMediaMetadataAsyncWithHttpInfo($sender, $mediaId)
    {
        $request = $this->getWhatsAppMediaMetadataRequest($sender, $mediaId);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($request) {
                    return $this->getWhatsAppMediaMetadataResponse($response, $request->getUri());
                },
                function ($exception) {
                    $statusCode = $exception->getCode();
                    $response = $exception->getResponse();
                    $e = new ApiException(
                        "[{$statusCode}] {$exception->getMessage()}",
                        $statusCode,
                        $response ? $response->getHeaders() : null,
                        $response ? (string) $response->getBody() : null
                    );
                    throw $this->getWhatsAppMediaMetadataApiException($e);
                }
            );
    }

    /**
     * Create request for operation 'getWhatsAppMediaMetadata'
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $mediaId ID of the media. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function getWhatsAppMediaMetadataRequest($sender, $mediaId)
    {
        // verify the required parameter 'sender' is set
        if ($sender === null || (is_array($sender) && count($sender) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $sender when calling getWhatsAppMediaMetadata'
            );
        }
        // verify the required parameter 'mediaId' is set
        if ($mediaId === null || (is_array($mediaId) && count($mediaId) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $mediaId when calling getWhatsAppMediaMetadata'
            );
        }

        $resourcePath = '/whatsapp/1/senders/{sender}/media/{mediaId}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';



        // path params
        if ($sender !== null) {
            $resourcePath = str_replace(
                '{' . 'sender' . '}',
                ObjectSerializer::toPathValue($sender),
                $resourcePath
            );
        }
        // path params
        if ($mediaId !== null) {
            $resourcePath = str_replace(
                '{' . 'mediaId' . '}',
                ObjectSerializer::toPathValue($mediaId),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['*/*'],
            []
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($headers['Content-Type'] === 'multipart/form-data') {
                $boundary = '----'.hash('sha256', uniqid('', true));
                $headers['Content-Type'] .= '; boundary=' . $boundary;
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents, $boundary);
            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires HTTP basic authentication
        if (!empty($this->config->getUsername()) || !(empty($this->config->getPassword()))) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'HEAD',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create response for operation 'getWhatsAppMediaMetadata'
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @param string $requestUri
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @return array of string|string|string|null, HTTP status code, HTTP response headers (array of strings)
     */
    protected function getWhatsAppMediaMetadataResponse($response, $requestUri)
    {
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseHeaders = $response->getHeaders();

        if ($statusCode < 200 || $statusCode > 299) {
            throw new ApiException(
                sprintf('[%d] Error connecting to the API (%s)', $statusCode, $requestUri),
                $statusCode,
                $responseHeaders,
                $responseBody
            );
        }

        $responseObject = null;

        if ($statusCode === 200) {
            $type = 'string';
            if ($type === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = (string) $responseBody;
            }
            $responseObject = ObjectSerializer::deserialize($content, $type, $responseHeaders);

            return [
                $responseObject,
                $statusCode,
                $responseHeaders
            ];
        }

        return [
            $responseObject,
            $statusCode,
            $responseHeaders
        ];
    }

    /**
     * Adapt given \Infobip\ApiException for operation 'getWhatsAppMediaMetadata'
     *
     * @param \Infobip\ApiException $apiException
     *
     * @return \Infobip\ApiException
     */
    protected function getWhatsAppMediaMetadataApiException($apiException)
    {
        $statusCode = $apiException->getCode();

        if ($statusCode === 404) {
            $data = ObjectSerializer::deserialize(
                $apiException->getResponseBody(),
                'string',
                $apiException->getResponseHeaders()
            );
            $apiException->setResponseObject($data);
            return $apiException;
        }
        if ($statusCode === 403) {
            $data = ObjectSerializer::deserialize(
                $apiException->getResponseBody(),
                'string',
                $apiException->getResponseHeaders()
            );
            $apiException->setResponseObject($data);
            return $apiException;
        }
        return $apiException;
    }

    /**
     * Operation markWhatsAppMessageAsRead
     *
     * Mark as read
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $messageId ID of the message to be marked as read. (required)
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return void
     */
    public function markWhatsAppMessageAsRead($sender, $messageId)
    {
        $this->markWhatsAppMessageAsReadWithHttpInfo($sender, $messageId);
    }

    /**
     * Operation markWhatsAppMessageAsReadWithHttpInfo
     *
     * Mark as read
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $messageId ID of the message to be marked as read. (required)
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of null, HTTP status code, HTTP response headers (array of strings)
     */
    public function markWhatsAppMessageAsReadWithHttpInfo($sender, $messageId)
    {
        $request = $this->markWhatsAppMessageAsReadRequest($sender, $messageId);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
                return $this->markWhatsAppMessageAsReadResponse($response, $request->getUri());
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            }
        } catch (ApiException $e) {
            throw $this->markWhatsAppMessageAsReadApiException($e);
        }
    }

    /**
     * Operation markWhatsAppMessageAsReadAsync
     *
     * Mark as read
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $messageId ID of the message to be marked as read. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function markWhatsAppMessageAsReadAsync($sender, $messageId)
    {
        return $this->markWhatsAppMessageAsReadAsyncWithHttpInfo($sender, $messageId)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation markWhatsAppMessageAsReadAsyncWithHttpInfo
     *
     * Mark as read
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $messageId ID of the message to be marked as read. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function markWhatsAppMessageAsReadAsyncWithHttpInfo($sender, $messageId)
    {
        $request = $this->markWhatsAppMessageAsReadRequest($sender, $messageId);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($request) {
                    return $this->markWhatsAppMessageAsReadResponse($response, $request->getUri());
                },
                function ($exception) {
                    $statusCode = $exception->getCode();
                    $response = $exception->getResponse();
                    $e = new ApiException(
                        "[{$statusCode}] {$exception->getMessage()}",
                        $statusCode,
                        $response ? $response->getHeaders() : null,
                        $response ? (string) $response->getBody() : null
                    );
                    throw $this->markWhatsAppMessageAsReadApiException($e);
                }
            );
    }

    /**
     * Create request for operation 'markWhatsAppMessageAsRead'
     *
     * @param  string $sender Registered WhatsApp sender number. Must be in international format. (required)
     * @param  string $messageId ID of the message to be marked as read. (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function markWhatsAppMessageAsReadRequest($sender, $messageId)
    {
        // verify the required parameter 'sender' is set
        if ($sender === null || (is_array($sender) && count($sender) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $sender when calling markWhatsAppMessageAsRead'
            );
        }
        // verify the required parameter 'messageId' is set
        if ($messageId === null || (is_array($messageId) && count($messageId) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $messageId when calling markWhatsAppMessageAsRead'
            );
        }

        $resourcePath = '/whatsapp/1/senders/{sender}/message/{messageId}/read';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';



        // path params
        if ($sender !== null) {
            $resourcePath = str_replace(
                '{' . 'sender' . '}',
                ObjectSerializer::toPathValue($sender),
                $resourcePath
            );
        }
        // path params
        if ($messageId !== null) {
            $resourcePath = str_replace(
                '{' . 'messageId' . '}',
                ObjectSerializer::toPathValue($messageId),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json'],
            []
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($headers['Content-Type'] === 'multipart/form-data') {
                $boundary = '----'.hash('sha256', uniqid('', true));
                $headers['Content-Type'] .= '; boundary=' . $boundary;
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents, $boundary);
            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\Query::build($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires HTTP basic authentication
        if (!empty($this->config->getUsername()) || !(empty($this->config->getPassword()))) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ":" . $this->config->getPassword());
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('Authorization');
        if ($apiKey !== null) {
            $headers['Authorization'] = $apiKey;
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\Query::build($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create response for operation 'markWhatsAppMessageAsRead'
     *
     * @param \GuzzleHttp\Psr7\Response $response
     * @param string $requestUri
     *
     * @throws \Infobip\ApiException on non-2xx response
     * @return array of , HTTP status code, HTTP response headers (array of strings)
     */
    protected function markWhatsAppMessageAsReadResponse($response, $requestUri)
    {
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody();
        $responseHeaders = $response->getHeaders();

        if ($statusCode < 200 || $statusCode > 299) {
            throw new ApiException(
                sprintf('[%d] Error connecting to the API (%s)', $statusCode, $requestUri),
                $statusCode,
                $responseHeaders,
                $responseBody
            );
        }

        $responseObject = null;


        return [
            $responseObject,
            $statusCode,
            $responseHeaders
        ];
    }

    /**
     * Adapt given \Infobip\ApiException for operation 'markWhatsAppMessageAsRead'
     *
     * @param \Infobip\ApiException $apiException
     *
     * @return \Infobip\ApiException
     */
    protected function markWhatsAppMessageAsReadApiException($apiException)
    {
        $statusCode = $apiException->getCode();

        if ($statusCode === 400) {
            $data = ObjectSerializer::deserialize(
                $apiException->getResponseBody(),
                '\Infobip\Model\WhatsAppMarkAsReadErrorResponse',
                $apiException->getResponseHeaders()
            );
            $apiException->setResponseObject($data);
            return $apiException;
        }
        return $apiException;
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}