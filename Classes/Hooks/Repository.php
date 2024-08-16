<?php

namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\Typo3Mode;

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
        // Get the current request
        $request = GeneralUtility::makeInstance(ServerRequest::class);
       

        // Extract 'search' parameters from the GET request
        $actionRequest = $request->getQueryParams()['tx_news_pi1']['search'] ?? null;
       
        if(isset($actionRequest)) {
            $actionRequest['category'] = $actionRequest['category'] ?? '0';
            $actionRequest['teaser'] = $actionRequest['teaser'] ?? '';
            $actionRequest['title'] = $actionRequest['title'] ?? '';
            
            if($actionRequest['category'] || $actionRequest['teaser'] || $actionRequest['title']) {
                // Filter Categories
                if($actionRequest['category']) {
                    $constCategory = [];
                    $searchCategories = $actionRequest['category'];
                    foreach ($searchCategories as $categories) {
                        if($categories == '0') {
                            $constCategory[] = $query->greaterThan('categories', 0);
                        } else {
                            $constCategory[] = $query->contains('categories', $categories);
                        }
                    }
                    $constraints[] = $query->logicalOr(...array_values($constCategory));
                }
                
                // Filter Teaser Text
                if($actionRequest['teaser']) {
                    $constraints[] = $query->like('teaser', '%' . $actionRequest['teaser'] . '%');
                }

                // Filter Title Text
                if($actionRequest['title']) {
                  $result =  $constraints[] = $query->like('title', '%' . $actionRequest['title'] . '%');
                  \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($result, __FILE__.' Line '.__LINE__);die;
                }
                
            }
        }
    }
}
