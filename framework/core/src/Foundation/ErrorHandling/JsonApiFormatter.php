<?php

/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flarum\Foundation\ErrorHandling;

use Flarum\Api\JsonApiResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tobscure\JsonApi\Document;

/**
 * A formatter to render exceptions as valid {JSON:API} error object.
 *
 * See https://jsonapi.org/format/1.0/#errors.
 */
class JsonApiFormatter implements HttpFormatter
{
    public function format(HandledError $error, Request $request): Response
    {
        $document = new Document;

        if ($error->hasDetails()) {
            $document->setErrors($this->withDetails($error));
        } else {
            $document->setErrors($this->simple($error));
        }

        return new JsonApiResponse($document, $error->getStatusCode());
    }

    private function simple(HandledError $error): array
    {
        return [
            [
                'status' => (string) $error->getStatusCode(),
                'code' => $error->getType(),
            ]
        ];
    }

    private function withDetails(HandledError $error): array
    {
        $data = [
            'status' => (string) $error->getStatusCode(),
            'code' => $error->getType(),
        ];

        return array_map(
            function ($row) use ($data) {
                return array_merge($data, $row);
            },
            $error->getDetails()
        );
    }
}
