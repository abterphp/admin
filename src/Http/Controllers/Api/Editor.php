<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Config\Routes;
use AbterPhp\Framework\Constant\Env as FrameworkEnv;
use AbterPhp\Website\Constant\Env as WebsiteEnv;
use Opulence\Environments\Environment;
use Opulence\Http\Responses\JsonResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Controller;

class Editor extends Controller
{
    /** @var Routes */
    protected $routes;

    /**
     * Editor constructor.
     *
     * @param Routes $routes
     */
    public function __construct(Routes $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return Response
     */
    public function fileUpload(): Response
    {
        $actualClientId = explode(' ', $this->request->getHeaders()->get('authorization'))[1];

        $expectedClientId = Environment::getVar(FrameworkEnv::CRYPTO_CLIENT_ID);
        if ($actualClientId != $expectedClientId) {
            $response = new JsonResponse(
                [
                    'success' => false,
                    'status'  => ResponseHeaders::HTTP_FORBIDDEN,
                ],
                ResponseHeaders::HTTP_FORBIDDEN
            );
            $response->send();

            return $response;
        }

        $image = $this->request->getFiles()->get('image');
        if ($image->hasErrors()) {
            $response = new JsonResponse(
                [
                    'success' => false,
                    'status'  => ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR,
                ],
                ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR
            );
            $response->send();

            return $response;
        }

        $basePath  = mb_substr(basename($image->getPathname()), 3);
        $extension = pathinfo($image->getTempFilename())['extension'];
        $filename  = sprintf('%s.%s', $basePath, $extension);

        $path = sprintf('%s/editor-file-upload', Environment::getVar(FrameworkEnv::DIR_PUBLIC));
        $url  = Environment::getVar(WebsiteEnv::WEBSITE_BASE_URL) . 'editor-file-upload/' . $filename;

        $image->move($path, $filename);

        $response = new JsonResponse(
            [
                'data'    => ['url' => $url],
                'success' => true,
                'status'  => ResponseHeaders::HTTP_OK,
            ],
            ResponseHeaders::HTTP_OK
        );
        $response->send();

        return $response;
    }
}
