<?php

namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
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
            $actionRequest['category'] ??= '0';
            $actionRequest['teaser'] ??= '';
            $actionRequest['title'] ??= '';
            $categoryLogic = $request->getQueryParams()['tx_news_pi1']['categoryFilter'] ?? 'and';
            if ($actionRequest['category'] || $actionRequest['teaser'] || $actionRequest['title']) {
                // Filter Categories
                if ($actionRequest['category']) {
                    $searchCategories = $actionRequest['category'];
                    if($categoryLogic == 'or'){
                        // Group Uids by their parentId 
                        $groupByParent = [];
                        foreach ($searchCategories as $catUid) {
                            if($catUid == 0) continue;
                            $parentId = $this->getParentCategoryId((int)$catUid);
                            $groupByParent[$parentId][] = $query->contains('categories',$catUid);
                        }
                        
                        // For each parent group: OR among siblings; all groups are AND together
                        foreach ($groupByParent as $groupConstrain) {
                            $constraints[] = count($groupConstrain) > 1
                                ? $query->logicalOr(...$groupConstrain)
                                : $groupConstrain[0];
                        }
                    }else{
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
    /*
        @param int $uid
        Return parent id of subcategories
    */
    protected function getParentCategoryId(int $uid): int
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_category');

        $row = $connection->select(
            ['parent'],
            'sys_category',
            ['uid' => $uid]
        )->fetchAssociative();

        return (int)($row['parent'] ?? 0);
    }
}
