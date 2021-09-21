<?php

namespace hamburgscleanest\GuzzleAdvancedThrottle\Cache;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class CachedResponse
{
    private array $_headers;
    private string $_body;
    private string $_protocol;
    private int $_statusCode;
    private string $_reason;

    public function __construct(ResponseInterface $response)
    {
        $this->_headers = $response->getHeaders();
        $this->_body = (string) $response->getBody();
        $this->_protocol = $response->getProtocolVersion();
        $this->_statusCode = $response->getStatusCode();
        $this->_reason = $response->getReasonPhrase();
    }

    public function getResponse(): Response
    {
        return new Response(
            $this->_statusCode,
            $this->_headers,
            $this->_body,
            $this->_protocol,
            $this->_reason
        );
    }
}
