<?php
namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class NewsControllerSettings {

    public function modify(array $params) {
        $settings = $params['originalSettings'];

        $languageid = $GLOBALS['TSFE']->sys_language_uid;
        if(empty($languageid)){
          $languageid=0;
        }
        $categoryStorage = $settings['advancedSearchCategoryPage'];

        // Get Categories for TYPO3 7&8
        if (version_compare(TYPO3_branch, '9.0', '<')) {
            if($settings['searchCategory']=='selected' && !empty($categoryStorage)){
                $searchCategories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_category','deleted=0 AND hidden=0 AND sys_language_uid='.$languageid.' AND pid='.$categoryStorage.' ORDER BY sorting ASC');
            } else {
                $searchCategories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_category','deleted=0 AND hidden=0 AND sys_language_uid='.$languageid.' ORDER BY sorting ASC');
            }
        } else {
            // Get Categories for TYPO3 9
            $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');
            
            if($settings['searchCategory']=='selected' && !empty($categoryStorage)){
                $searchCategories = $queryBuilder
                               ->select('*')
                               ->from('sys_category')
                               ->where($queryBuilder->expr()->eq('pid', $categoryStorage))
                               ->andwhere($queryBuilder->expr()->eq('sys_language_uid', $languageid))
                               ->orderBy('sorting')
                               ->execute()
                               ->fetchAll();
            } else {
                $searchCategories = $queryBuilder
                               ->select('*')
                               ->from('sys_category')
                               ->where($queryBuilder->expr()->eq('sys_language_uid', $languageid))
                               ->orderBy('sorting')
                               ->execute()
                               ->fetchAll();   
            }
        }
        
        if(!empty($searchCategories)){
            $settings['searchCategories'] = $searchCategories;
        }

        return $settings;
    }
}