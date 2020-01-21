<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Constant\Env;
use Opulence\Environments\Environment;
use Opulence\Http\Responses\JsonResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Controller;

class Editor extends Controller
{
    /**
     * @return Response
     */
    public function fileUpload(): Response
    {
        $actualClientId = explode(' ', $this->request->getHeaders()->get('authorization'))[1];

        $expectedClientId = Environment::getVar(Env::UPLOAD_CLIENT_ID);
        if ($actualClientId != $expectedClientId) {
            return $this->sendJson(ResponseHeaders::HTTP_FORBIDDEN);
        }

        $image = $this->request->getFiles()->get('image');
        if ($image->hasErrors()) {
            return $this->sendJson(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR);
        }

        $pathinfo  = pathinfo($image->getTempFilename());
        $basePath  = mb_substr(basename($image->getPathname()), 3);
        $extension = $pathinfo['extension'] ?? '';
        $filename  = sprintf('%s.%s', $basePath, $extension);

        $path = Environment::getVar(Env::DIR_UPLOAD);
        $url  = Environment::getVar(Env::UPLOAD_BASE_URL) . '/' . $filename;

        $image->move($path, $filename);

        return $this->sendJson(ResponseHeaders::HTTP_OK, ['url' => $url]);
    }

    /**
     * @param int        $status
     * @param array|null $data
     *
     * @return Response
     */
    protected function sendJson($status = ResponseHeaders::HTTP_OK, array $data = null): Response
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
