<?php

namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Psr\Http\Message\ServerRequestInterface;

class Repository
{
    public function modify(array $params)
    {
        $this->updateConstraints($params['query'], $params['constraints']);
    }

    /**
     * @param QueryInterface $query
     * @param array $constraints
     */
    protected function updateConstraints(QueryInterface $query, array &$constraints)
    {
        $request = $GLOBALS['TYPO3_REQUEST'];
        $actionRequest = $request->getQueryParams()['tx_news_pi1']['search'] ?? null;
        
        if (isset($actionRequest)) {
            $actionRequest['category'] = $actionRequest['category'] ?? '0';
            $actionRequest['teaser'] = $actionRequest['teaser'] ?? '';
            $actionRequest['title'] = $actionRequest['title'] ?? '';

            if ($actionRequest['category'] || $actionRequest['teaser'] || $actionRequest['title']) {
                // Filter Categories
                if ($actionRequest['category']) {
                    $constCategory = [];
                    $searchCategories = $actionRequest['category'];
                    foreach ($searchCategories as $categories) {
                        if ($categories == '0') {
                            $constCategory[] = $query->greaterThan('categories', 0);
                        } else {
                            $constCategory[] = $query->contains('categories', $categories);
                        }
                    }
                    $constraints[] = $query->logicalOr(...array_values($constCategory));
                }

                // Filter Teaser Text
                if ($actionRequest['teaser']) {
                    $constraints[] = $query->like('teaser', '%' . $actionRequest['teaser'] . '%');
                }

                // Filter Title Text
                if ($actionRequest['title']) {
                    $constraints[] = $query->like('title', '%' . $actionRequest['title'] . '%');
                }
            }
        }
    }
}
