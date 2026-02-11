<?php

namespace NITSAN\NsNewsAdvancedsearch\EventListener;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class ModifyFlexformEvent
{
    public function __invoke(AfterFlexFormDataStructureParsedEvent $event): void
    {
        $dataStructure = $event->getDataStructure();
        $identifier = $event->getIdentifier();
        $versionNumber =  VersionNumberUtility::convertVersionStringToArray(VersionNumberUtility::getCurrentTypo3Version());
        if ($versionNumber['version_main'] <= 13) {
            $dataStructureKey = '*,news_newssearchform';
        } else {
            $dataStructureKey = 'news_newssearchform';
        }
        if ($identifier['type'] === 'tca'
            && $identifier['tableName'] === 'tt_content'
            && $identifier['dataStructureKey'] === $dataStructureKey
        ) {
            $file = GeneralUtility::getFileAbsFileName(
                'EXT:ns_news_advancedsearch/Configuration/FlexForm/NewsSearch.xml'
            );
            $content = file_get_contents($file);
            if ($content) {
                ArrayUtility::mergeRecursiveWithOverrule(
                    $dataStructure,
                    GeneralUtility::xml2array($content)
                );
                $dataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);
            }
        }
        $event->setDataStructure($dataStructure);
    }
}
