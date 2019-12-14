<?php
namespace NITSAN\NsNewsAdvancedsearch\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlexFormHook
{
  // For 7x
  /**
  * @param array $dataStructArray
  * @param array $conf
  * @param array $row
  * @param string $table
  */
  public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, $row, $table)
  {
     if ($table === 'tt_content' && $row['CType'] === 'list' && $row['list_type'] === 'news_pi1') {
         $dataStructArray['sheets']['extraEntry'] = 'typo3conf/ext/ns_news_advancedsearch/Configuration/FlexForm/NewsSearch.xml';
     }
  }
   // For 8x & 9x
   /**
   * @param array $dataStructure
   * @param array $identifier
   * @return array
   */
   public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier): array
   {
     if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content' && $identifier['dataStructureKey'] === 'news_pi1,list') {
        $getVars = GeneralUtility::_GET('edit');
        if (is_array($getVars['tt_content'])) {
            $item = array_keys($getVars['tt_content']);
            $row = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('tt_content', (int)$item[0]);
            $ffXml = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($row['pi_flexform']);
            $file = PATH_site . 'typo3conf/ext/ns_news_advancedsearch/Configuration/FlexForm/NewsSearch.xml';
            $content = file_get_contents($file);
            if ($content) {
                $dataStructure['sheets']['extraEntry'] = GeneralUtility::xml2array($content);
            }
        } 
     }
     return $dataStructure;
   }
}