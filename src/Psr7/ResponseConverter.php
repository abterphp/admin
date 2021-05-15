<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Psr7;

use Opulence\Http\Responses\Response as OpulenceResponse;
use Psr\Http\Message\ResponseInterface;

class ResponseConverter
{
    /**
     * @param ResponseInterface $psrResponse
     *
     * @return OpulenceResponse
     */
    public function fromPsr(ResponseInterface $psrResponse): OpulenceResponse
    {
        $content     = $psrResponse->getBody();
        $statusCode  = $psrResponse->getStatusCode();
        $headers     = $psrResponse->getHeaders();

        return new OpulenceResponse($content, $statusCode, $headers);
    }
}
