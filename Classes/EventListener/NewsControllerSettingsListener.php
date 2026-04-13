<?php
namespace NITSAN\NsNewsAdvancedsearch\EventListener;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use GeorgRinger\News\Event\NewsControllerOverrideSettingsEvent;

final class NewsControllerSettingsListener
{
    public function __invoke(NewsControllerOverrideSettingsEvent $event): void
    {
        $settings = $event->getSettings();
        $settings['advancedSearch'] ??= 0;
        $settings['searchCategory'] ??= '';
        $settings['disableOverrideDemand'] ??= 0;
        if (!empty($settings['advancedSearch'])) {
            $context = GeneralUtility::makeInstance(Context::class);
            $languageId = $context->getPropertyFromAspect('language', 'id');
            $categoryStorage = $settings['advancedSearchCategoryPage'] ?? null;
            try {
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('sys_category');

                $queryBuilder
                    ->select('*')
                    ->from('sys_category');

                if ($settings['searchCategory'] === 'selected' && !empty($categoryStorage)) {
                    $queryBuilder
                        ->where($queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($categoryStorage)))
                        ->andWhere($queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($languageId)));
                } else {
                    $queryBuilder
                        ->where($queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($languageId)));
                }
                $searchCategories = $queryBuilder
                    ->orderBy('sorting')
                    ->executeQuery()
                    ->fetchAllAssociative();
            } catch (Exception $e) {
                $event->setSettings($settings);
                return;
            }
            if (!empty($searchCategories)) {
                $settings['searchCategories'] = $searchCategories;
            }
        }
        $event->setSettings($settings);
    }
}