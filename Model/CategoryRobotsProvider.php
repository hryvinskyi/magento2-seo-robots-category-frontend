<?php
/**
 * Copyright (c) 2025. All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoRobotsCategoryFrontend\Model;

use Hryvinskyi\SeoRobotsCategoryApi\Api\GetCategoryRobotsInterface;
use Hryvinskyi\SeoRobotsFrontend\Model\RobotsProviderInterface;
use Magento\Framework\App\HttpRequestInterface;
use Magento\Framework\Registry;

/**
 * Provides robots meta tags based on category settings
 */
class CategoryRobotsProvider implements RobotsProviderInterface
{
    /**
     * Default priority for category page robots
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
    public function getRobots(HttpRequestInterface $request): ?string
    {
        // Check if we're on a category page
        if ($request->getFullActionName() === 'catalog_category_view') {
            $category = $this->registry->registry('current_category');
            if ($category) {
                return $this->getCategoryRobots->execute($category);
            }
        }

        // Check if we're on a product page
        if ($request->getFullActionName() === 'catalog_product_view') {
            $product = $this->registry->registry('current_product');
            if ($product) {
                return $this->getCategoryRobots->executeForProduct($product);
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
