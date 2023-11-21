<?php
namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use GeorgRinger\News\Domain\Repository\NewsRepository;

class Repository {

    public function modify(array $params, $newsRepository) {
        $this->updateConstraints($params['demand'], $params['respectEnableFields'], $params['query'], $params['constraints']);
    }

    /**
     * @param \GeorgRinger\News\Domain\Model\Dto\NewsDemand $demand
     * @param bool $respectEnableFields
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param array $constraints
     */
    protected function updateConstraints($demand, $respectEnableFields, \TYPO3\CMS\Extbase\Persistence\QueryInterface $query, array &$constraints) {
        $actionRequest = GeneralUtility::_GET('tx_news_pi1')['search'] ?? null;
        if(isset($actionRequest)) {
            $actionRequest['category'] = $actionRequest['category'] ?? '0';
            $actionRequest['teaser'] = $actionRequest['teaser'] ?? '';
            $actionRequest['title'] = $actionRequest['title'] ?? '';
            if ($actionRequest['category'] || $actionRequest['teaser'] || $actionRequest['title']) {

                // Filter Categories
                if ($actionRequest['category']) {

                    $searchCategories = $actionRequest['category'];
                    foreach ($searchCategories as $categories) {
                        if ($categories == 0) {
                            $constCategory[] = $query->greaterThan('categories', 0);
                        } else {
                            $constCategory[] = $query->contains('categories', $categories);
                        }
                    }
                    $constraints[] = $query->logicalOr($constCategory);
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