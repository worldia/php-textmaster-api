<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Api;

use Textmaster\Api\User\Callback;

/**
 * Users Api.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class User extends AbstractApi
{
    /**
     * Get information about current user.
     *
     * @link https://fr.textmaster.com/documentation#users-get-information-about-myself
     *
     * @return array
     */
    public function me()
    {
        return $this->get('clients/users/me');
    }

    /**
     * Callbacks Api.
     *
     * @return Callback
     */
    public function callbacks()
    {
        return new Callback($this->client);
    }
}
