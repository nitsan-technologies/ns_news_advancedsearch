<?php

namespace NITSAN\NsNewsAdvancedsearch\EventListener;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;

class ModifyFlexformEvent
{
    public function __invoke(AfterFlexFormDataStructureParsedEvent $event): void
    {
        $dataStructure = $event->getDataStructure();
        $identifier = $event->getIdentifier();
        // $identifier['dataStructureKey'] depends on the selected plugin!
        if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content' && $identifier['dataStructureKey'] === '*,news_newssearchform') {
            $file = GeneralUtility::getFileAbsFileName('EXT:ns_news_advancedsearch/Configuration/FlexForm/NewsSearch.xml');
            $content = file_get_contents($file);
            if ($content) {
                ArrayUtility::mergeRecursiveWithOverrule($dataStructure, GeneralUtility::xml2array($content));
                $dataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);

            }
        }
        $event->setDataStructure($dataStructure);
    }
}
