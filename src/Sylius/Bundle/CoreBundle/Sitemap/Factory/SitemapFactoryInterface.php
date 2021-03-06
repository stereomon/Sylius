<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Sitemap\Factory;

use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SitemapFactoryInterface
{
    /**
     * @return SitemapInterface
     */
    public function createNew();
}
