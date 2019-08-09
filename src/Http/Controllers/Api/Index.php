<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Controller;

class Index extends Controller
{
    /**
     * @return Response
     */
    public function notFound(): Response
    {
        $response = new Response();

        $response->setStatusCode(ResponseHeaders::HTTP_NOT_IMPLEMENTED);

        return $response;
    }
}
