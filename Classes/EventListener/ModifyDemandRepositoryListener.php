<?php
namespace NITSAN\NsNewsAdvancedsearch\EventListener;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use GeorgRinger\News\Event\ModifyDemandRepositoryEvent;

final class ModifyDemandRepositoryListener
{
    public function __invoke(ModifyDemandRepositoryEvent $event): void
    {
        $query = $event->getQuery();
        $constraints = $event->getConstraints();

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $actionRequest = $request->getQueryParams()['tx_news_pi1']['search'] ?? null;
        if ($actionRequest !== null) {
            $actionRequest['category'] ??= '0';
            $actionRequest['teaser'] ??= '';
            $actionRequest['title'] ??= '';
            $categoryLogic = $request->getQueryParams()['tx_news_pi1']['categoryFilter'] ?? 'and';
            if ($actionRequest['category'] || $actionRequest['teaser'] || $actionRequest['title']) {
                // Categories
                if (!empty($actionRequest['category'])) {
                    $searchCategories = $actionRequest['category'];
                    if($categoryLogic == 'or'){
                        $groupByParent = [];
                        foreach ($searchCategories as $catUid) {
                            if($catUid == 0) continue;
                            $parentId = $this->getParentCategoryId((int)$catUid);
                            $groupByParent[$parentId][] = $query->contains('categories',$catUid);
                        }
                        // For each parent group child A or child B, Then all groups are AND together
                        foreach ($groupByParent as $groupConstrain) {
                            $constraints[] = count($groupConstrain) > 1
                                ? $query->logicalOr(...$groupConstrain)
                                : $groupConstrain[0];
                        }
                    }else{
                        $constCategory = [];
                        foreach ($actionRequest['category'] as $category) {
                            if ($category === '0') {
                                $constCategory[] = $query->greaterThan('categories', 0);
                            } else {
                                $constCategory[] = $query->contains('categories', $category);
                            }
                        }
                        if ($constCategory !== []) {
                            $constraints[] = $query->logicalOr(...$constCategory);
                        }
                    }
                }
               
                // Teaser
                if ($actionRequest['teaser'] !== '') {
                    $constraints[] = $query->like('teaser', '%' . $actionRequest['teaser'] . '%');
                }
                // Title
                if ($actionRequest['title'] !== '') {
                    $constraints[] = $query->like('title', '%' . $actionRequest['title'] . '%');
                }
            }
        }
        // Write modified constraint array back into the event
        $event->setConstraints($constraints);
    }
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