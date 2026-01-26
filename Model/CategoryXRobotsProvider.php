<?php
/**
 * Copyright (c) 2026. All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoRobotsCategoryFrontend\Model;

use Hryvinskyi\SeoRobotsCategoryApi\Api\GetCategoryRobotsInterface;
use Hryvinskyi\SeoRobotsFrontend\Model\XRobotsProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

/**
 * Provides X-Robots-Tag HTTP header directives based on category settings
 */
class CategoryXRobotsProvider implements XRobotsProviderInterface
{
    /**
     * Default priority for category page X-Robots
     */
    private const DEFAULT_PRIORITY = 1000;

    /**
     * @param GetCategoryRobotsInterface $getCategoryRobots
     * @param Registry $registry
     * @param int $sortOrder
     * @param int $priority
     */
    public function __construct(
        private readonly GetCategoryRobotsInterface $getCategoryRobots,
        private readonly Registry $registry,
        private readonly int $sortOrder = 100,
        private readonly int $priority = self::DEFAULT_PRIORITY
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getDirectives(RequestInterface $request): ?array
    {
        // Check if we're on a category page
        if ($request->getFullActionName() === 'catalog_category_view') {
            $category = $this->registry->registry('current_category');
            if ($category) {
                $directives = $this->getCategoryRobots->executeXRobots($category);
                return !empty($directives) ? $directives : null;
            }
        }

        // Check if we're on a product page
        if ($request->getFullActionName() === 'catalog_product_view') {
            $product = $this->registry->registry('current_product');
            if ($product) {
                $directives = $this->getCategoryRobots->executeXRobotsForProduct($product);
                return !empty($directives) ? $directives : null;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
