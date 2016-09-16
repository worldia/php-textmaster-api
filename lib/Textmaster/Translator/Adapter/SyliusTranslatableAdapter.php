<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Translator\Adapter;

class SyliusTranslatableAdapter extends AbstractDoctrineAdapter
{
    protected $interface = 'Sylius\Component\Resource\Model\TranslatableInterface';

    /**
     * {@inheritdoc}
     */
    protected function getPropertyHolder($subject, $language, $activity = null)
    {
        return $subject->translate($language);
    }
}
