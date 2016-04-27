<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\HttpClient\Message;

use Guzzle\Http\Message\Response;
use Textmaster\Exception\ApiLimitExceedException;

class ResponseMediator
{
    public static function getContent(Response $response)
    {
        $body = $response->getBody(true);
        if (strpos($response->getContentType(), 'application/json') === 0) {
            $content = json_decode($body, true);
            if (JSON_ERROR_NONE === json_last_error()) {
                return $content;
            }
        }

        return $body;
    }

    public static function getPagination(Response $response)
    {
        $header = (string) $response->getHeader('Link');

        if (empty($header)) {
            return;
        }

        $pagination = array();
        foreach (explode(',', $header) as $link) {
            preg_match('/<(.*)>; rel="(.*)"/i', trim($link, ','), $match);

            if (3 === count($match)) {
                $pagination[$match[2]] = $match[1];
            }
        }

        return $pagination;
    }

    public static function getApiLimit(Response $response)
    {
        $remainingCalls = (string) $response->getHeader('X-RateLimit-Remaining');

        if (null !== $remainingCalls && 1 > $remainingCalls) {
            throw new ApiLimitExceedException($remainingCalls);
        }

        return $remainingCalls;
    }
}
