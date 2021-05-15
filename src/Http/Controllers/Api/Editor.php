<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Constant\Env;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\UploadException;
use Opulence\Http\Responses\JsonResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Controller;

class Editor extends Controller
{
    /**
     * @return Response
     * @throws UploadException
     */
    public function fileUpload(): Response
    {
        $authHeader     = $this->request->getHeaders()->get('authorization');
        $actualClientId = strpos($authHeader, ' ') ? explode(' ', $authHeader)[1] : '';

        $expectedClientId = Environment::getVar(Env::EDITOR_CLIENT_ID);
        if ($actualClientId != $expectedClientId) {
            return $this->sendJson(ResponseHeaders::HTTP_FORBIDDEN);
        }

        $image = $this->request->getFiles()->get('image');
        if ($image->hasErrors()) {
            return $this->sendJson(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }

        $pathInfo  = pathinfo($image->getTempFilename());
        $baseName  = mb_substr(basename($image->getPathname()), 3);
        $extension = $pathInfo['extension'] ?? '';
        $filename  = sprintf('%s.%s', $baseName, $extension);

        $basePath = Environment::getVar(Env::EDITOR_BASE_PATH);
        $path     = Environment::getVar(Env::DIR_MEDIA) . $basePath;
        $url      = sprintf(
            '%s/%s/%s',
            rtrim(Environment::getVar(Env::MEDIA_BASE_URL), DIRECTORY_SEPARATOR),
            trim($basePath, DIRECTORY_SEPARATOR),
            $filename
        );

        $image->move($path, $filename);

        return $this->sendJson(ResponseHeaders::HTTP_OK, ['url' => $url]);
    }

    /**
     * @param int        $status
     * @param array|null $data
     *
     * @return Response
     */
    protected function sendJson(int $status = ResponseHeaders::HTTP_OK, array $data = null): Response
    {
        $body = [
            'success' => $status == ResponseHeaders::HTTP_OK,
            'status'  => $status,
        ];

        if ($data) {
            $body['data'] = $data;
        }

        $response = new JsonResponse($body, $status);

        $response->send();

        return $response;
    }
}
