<?php

/*
 * This file is part of the "news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace NITSAN\NsNewsAdvancedsearch\Utility;

use GeorgRinger\News\Utility\ClassParser;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class ClassCacheManager
 */
class ClassCacheManager
{
    /**
     * Cache instance
     *
     * @var PhpFrontend
     */
    protected $classCache;

    /**
     * @var array
     */
    protected $constructorLines = [];

    /**
     * @param PhpFrontend $classCache
     */
    public function __construct(PhpFrontend $classCache = null)
    {
        if ($classCache === null) {
            $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
            if (!$cacheManager->hasCache('news')) {
                $cacheManager->setCacheConfigurations(
                    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']
                );
            }
            $this->classCache = $cacheManager->getCache('news');
        } else {
            $this->classCache = $classCache;
        }
    }

    public function reBuild()
    {
        $classPath = 'Classes/';

        if (!function_exists('token_get_all')) {
            throw new \Exception(
                LocalizationUtility::translate(
                    'error.token.get.all'
                )
            );
        }

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes'])) {
            return;
        }

        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes'] as $key => $extensionsWithThisClass) {

            $this->constructorLines = [];
            $extendingClassFound = false;

            $path = ExtensionManagementUtility::extPath('news') . $classPath . $key . '.php';
            if (!is_file($path)) {
                throw new \Exception('Given file "' . $path . '" does not exist');
            }

            $code = $this->parseSingleFile($path, true);

            // Get the files from all other extensions
            foreach (array_unique($extensionsWithThisClass) as $extensionKey) {

                $path = ExtensionManagementUtility::extPath($extensionKey) . $classPath . $key . '.php';

                if (is_file($path)) {
                    $extendingClassFound = true;
                    $code .= $this->parseSingleFile($path);
                }
            }
            if (!defined('LF')) {
                define('LF', "\n");
            }

            if (
                isset($this->constructorLines['code']) &&
                count($this->constructorLines['code']) &&
                isset($this->constructorLines['doc'])
            ) {
                $code .= LF . implode("\n", $this->constructorLines['doc']);
                $code .= LF . '    public function __construct(' .
                    implode(',', $this->constructorLines['parameters'] ?? []) . ')' . LF . '    {' . LF .
                    implode(LF, $this->constructorLines['code'] ?? []) . LF . '    }' . LF;
            }

            $code = $this->closeClassDefinition($code);

            // If an extending class is found, the file is written and
            // added to the autoloader info
            if ($extendingClassFound) {
                $cacheEntryIdentifier = 'tx_news_' . strtolower(str_replace('/', '_', $key));
                try {
                    $this->classCache->set($cacheEntryIdentifier, $code);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }
    }

    /**
     * Parse a single file and does some magic
     * - Remove the <?php tags
     * - Remove the class definition (if set)
     *
     * @param string $filePath path of the file
     * @param bool $baseClass If class definition should be removed
     * @return string path of the saved file
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    protected function parseSingleFile($filePath, $baseClass = false): string
    {

        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('File "%s" could not be found', $filePath));
        }
        $code = @file_get_contents($filePath);

        $classParser = GeneralUtility::makeInstance(ClassParser::class);
        $classParser->parse($filePath);
        $classParserInformation = $classParser->getFirstClass();

        $code = str_replace('<?php', '', $code);
        if (!defined('LF')) {
            define('LF', "\n");
        }
        if (!defined('CR')) {
            define('CR', "\n");
        }
        $codeInLines = explode(LF, str_replace(CR, '', $code));
        $offsetForInnerPart = 0;

        if ($baseClass) {
            $innerPart = $codeInLines;
        } else {
            $offsetForInnerPart = $classParserInformation['start'];
            if (isset($classParserInformation['eol'])) {
                $innerPart = array_slice(
                    $codeInLines,
                    $classParserInformation['start'],
                    $classParserInformation['eol'] - $classParserInformation['start'] - 1
                );
            } else {
                $innerPart = array_slice($codeInLines, $classParserInformation['start']);
            }
        }

        if (trim($innerPart[0]) === '{') {
            unset($innerPart[0]);
        }

        // unset the constructor and save it's lines

        if (isset($classParserInformation['functions']['__construct'])) {
            $constructorInfo = $classParserInformation['functions']['__construct'];

            $constructorInfo['inner_start'] = $constructorInfo['start'] - $offsetForInnerPart;
            $constructorInfo['inner_end'] = $constructorInfo['end'] - $offsetForInnerPart;

            if ($baseClass) {
                $this->constructorLines['doc'] = explode("\n", $constructorInfo['doc'] ?? '');

            } else {
                if (isset($this->constructorLines['doc'])) {
                    array_splice(
                        $this->constructorLines['doc'],
                        -1,
                        0,
                        array_filter(explode("\n", $constructorInfo['doc'] ?? ''), function ($value) {
                            return strpos($value, '@param') !== false;
                        })
                    );
                }
            }
            $codePart = false;
            for ($i = $constructorInfo['inner_start']; $i < $constructorInfo['inner_end']; $i++) {
                if ($codePart) {
                    $this->constructorLines['code'][] = $innerPart[$i];
                } elseif (trim($innerPart[$i]) === ') {' || trim($innerPart[$i]) === '{') {
                    $codePart = true;
                } elseif (trim($innerPart[$i]) !== ')' && $i >= $constructorInfo['inner_start']) {
                    $this->constructorLines['parameters'][] = LF . rtrim($innerPart[$i], ',');
                }
                unset($innerPart[$i]);
            }
            unset($innerPart[$constructorInfo['inner_start'] - 1]);
            unset($innerPart[$constructorInfo['inner_end']]);
        }

        $codePart = implode(LF, $innerPart);
        $closingBracket = strrpos($codePart, '}');
        $codePart = substr($codePart, 0, $closingBracket);

        return $this->getPartialInfo($filePath) . $codePart;
    }

    /**
     * @param string $filePath
     * @return string
     */

    protected function getPartialInfo($filePath): string
    {
        if (!defined('LF')) {
            define('LF', "\n");
        }
        return LF . '/*' . str_repeat('*', 70) . LF . "\t" .
        'this is partial from: ' . LF . "\t" .
            str_replace(Environment::getPublicPath(), '', $filePath) . LF . str_repeat(
                '*',
                70
            ) . '*/' . LF;
    }

    /**
     * @param string $code
     * @return string
     */
    protected function closeClassDefinition($code): string
    {
        if (!defined('LF')) {
            define('LF', "\n");
        }
        return $code . LF . '}';
    }
}
