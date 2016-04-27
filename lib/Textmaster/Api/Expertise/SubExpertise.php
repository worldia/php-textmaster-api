<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Api\Expertise;

use Textmaster\Api\AbstractApi;

/**
 * Subexpertises Api.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class SubExpertise extends AbstractApi
{
    /**
     * List all sub expertises.
     *
     * @link https://fr.textmaster.com/documentation#expertises-listing-sub-expertises
     *
     * @param string      $expertiseId
     * @param null|string $locale
     *
     * @return array
     */
    public function all($expertiseId, $locale = null)
    {
        $params = array();

        if (null !== $locale) {
            $params['locale'] = $locale;
        }

        return $this->get($this->getPath($expertiseId), $params);
    }

    /**
     * Show a sub expertise.
     *
     * @link https://fr.textmaster.com/documentation#expertises-get-a-sub-expertise
     *
     * @param string      $expertiseId
     * @param string      $subExpertiseId
     * @param null|string $locale
     *
     * @return array
     */
    public function show($expertiseId, $subExpertiseId, $locale = null)
    {
        $params = array();

        if (null !== $locale) {
            $params['locale'] = $locale;
        }

        return $this->get($this->getPath($expertiseId).'/'.rawurlencode($subExpertiseId), $params);
    }

    /**
     * Get api path.
     *
     * @param string $expertiseId
     *
     * @return string
     */
    protected function getPath($expertiseId)
    {
        return sprintf('public/expertises/%s/sub_expertises', rawurlencode($expertiseId));
    }
}
