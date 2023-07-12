<?php

namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Context\Context;

class NewsControllerSettings
{
    public function modify(array $params)
    {
        $settings = $params['originalSettings'];
        $settings['advancedSearch'] = $settings['advancedSearch'] ?? 0;
        $settings['searchCategory'] = $settings['searchCategory'] ?? '';
        $settings['disableOverrideDemand'] = $settings['disableOverrideDemand'] ?? 0;
        if (!is_null($settings['advancedSearch']) && $settings['advancedSearch']) {

            $context = GeneralUtility::makeInstance(Context::class);
            $languageid = $context->getPropertyFromAspect('language', 'id');

            $categoryStorage = $settings['advancedSearchCategoryPage'] ?? null;

            $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');

            if($settings['searchCategory']=='selected' && !empty($categoryStorage)) {
                $searchCategories = $queryBuilder
                ->select('*')
                ->from('sys_category')
                ->where($queryBuilder->expr()->eq('pid', $categoryStorage))
                ->andwhere($queryBuilder->expr()->eq('sys_language_uid', $languageid))
                ->orderBy('sorting')
                ->executeQuery()
                ->fetchAllAssociative();
            } else {
                $searchCategories = $queryBuilder
                ->select('*')
                ->from('sys_category')
                ->where($queryBuilder->expr()->eq('sys_language_uid', $languageid))
                ->orderBy('sorting')
                ->executeQuery()
                ->fetchAllAssociative();
            }

            if(!empty($searchCategories)) {
                $settings['searchCategories'] = $searchCategories;
            }
        }
        return $settings;
    }
}
