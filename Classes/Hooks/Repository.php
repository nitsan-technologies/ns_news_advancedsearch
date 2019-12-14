<?php
namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use \GeorgRinger\News\Domain\Repository\NewsRepository;

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
        if($_REQUEST['tx_news_pi1']['search']['category'] || $_REQUEST['tx_news_pi1']['search']['teaser'] || $_REQUEST['tx_news_pi1']['search']['title']){                    
            
            // Filter Categories
            if($_REQUEST['tx_news_pi1']['search']['category']){
                $searchCategories = $_REQUEST['tx_news_pi1']['search']['category'];
                foreach ($searchCategories as $categories) {
                    if($categories==0){
                        $constCategory[]=$query->greaterThan('categories', 0);
                    } else {
                        $constCategory[]=$query->contains('categories', $categories);
                    }
                }
                $constraints[] =$query->logicalOr($constCategory);
            }

            // Filter Teaser Text
            if($_REQUEST['tx_news_pi1']['search']['teaser']){
                $constraints[] = $query->like('teaser', '%' . $_REQUEST['tx_news_pi1']['search']['teaser'] . '%');   
            }

            // Filter Title Text
            if($_REQUEST['tx_news_pi1']['search']['title']){
                $constraints[] = $query->like('title', '%' . $_REQUEST['tx_news_pi1']['search']['title'] . '%');
            }
        }
    }
}