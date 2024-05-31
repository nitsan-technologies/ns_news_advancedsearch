<?php

namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class NewsControllerSettings
{
    public function modify(array $params)
    {
        $settings = $params['originalSettings'];
        if (!is_null($settings['advancedSearch']) && $settings['advancedSearch']) {
            //@extensionScannerIgnoreLine
            if (version_compare(TYPO3_branch, '9.0', '<')) {
                $languageid = $GLOBALS['TSFE']->sys_language_uid;
                if (empty($languageid)) {
                    $languageid = 0;
                }
            } else {
                $languageAspect = GeneralUtility::makeInstance(
                    \TYPO3\CMS\Core\Context\Context::class
                )->getAspect('language');
                $languageid = $languageAspect->getId();
            }
            $categoryStorage = $settings['advancedSearchCategoryPage'];

            // Get Categories for TYPO3 7&8
            if (version_compare(TYPO3_branch, '9.0', '<')) {
                if($settings['searchCategory'] == 'selected' && !empty($categoryStorage)) {
                    //@extensionScannerIgnoreLine
                    $searchCategories = $GLOBALS['TYPO3_DB']
                        ->exec_SELECTgetRows(
                            '*',
                            'sys_category',
                            'deleted=0 AND hidden=0 AND sys_language_uid='.$languageid.
                            ' AND pid='.$categoryStorage.' ORDER BY sorting ASC'
                        );
                } else {
                    //@extensionScannerIgnoreLine
                    $searchCategories = $GLOBALS['TYPO3_DB']
                        ->exec_SELECTgetRows(
                            '*',
                            'sys_category',
                            'deleted=0 AND hidden=0 AND sys_language_uid='.$languageid.' ORDER BY sorting ASC'
                        );
                }
            } else {
                // Get Categories for TYPO3 9
                $queryBuilder = GeneralUtility::makeInstance(
                    \TYPO3\CMS\Core\Database\ConnectionPool::class
                )
                ->getQueryBuilderForTable('sys_category');

                $queryBuilder
                    ->select('*')
                    ->from('sys_category');

                if($settings['searchCategory'] == 'selected' && !empty($categoryStorage)) {
                    $queryBuilder->where($queryBuilder->expr()->eq('pid', $categoryStorage))
                                   ->andwhere($queryBuilder->expr()->eq('sys_language_uid', $languageid));
                } else {
                    $queryBuilder->where($queryBuilder->expr()->eq('sys_language_uid', $languageid));
                }
                $searchCategories = $queryBuilder->orderBy('sorting')
                    ->execute()
                    ->fetchAll();
            }

            if(!empty($searchCategories)) {
                $settings['searchCategories'] = $searchCategories;
            }
        }
        return $settings;
    }
}
