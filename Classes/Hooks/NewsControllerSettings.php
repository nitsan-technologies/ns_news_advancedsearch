<?php

namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
        if (!is_null($settings['advancedSearch'])) {

            $context = GeneralUtility::makeInstance(Context::class);
            $languageid = $context->getPropertyFromAspect('language', 'id');

            $categoryStorage = $settings['advancedSearchCategoryPage'] ?? null;

            try {
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('sys_category');
                $queryBuilder
                    ->select('*')
                    ->from('sys_category');

                if($settings['searchCategory'] == 'selected' && !empty($categoryStorage)) {
                    $queryBuilder
                    ->where($queryBuilder->expr()->eq('pid', $categoryStorage))
                    ->andwhere($queryBuilder->expr()->eq('sys_language_uid', $languageid));

                } else {
                    $queryBuilder
                     ->where($queryBuilder->expr()->eq('sys_language_uid', $languageid));
                }

                $searchCategories = $queryBuilder->orderBy('sorting')
                    ->executeQuery()
                    ->fetchAllAssociative();
            } catch (Exception $e) {
                return false;
            }

            if(!empty($searchCategories)) {
                $settings['searchCategories'] = $searchCategories;
            }
        }
        return $settings;
    }
}
