<?php

namespace NITSAN\NsNewsAdvancedsearch\Domain\Model\Dto;

/***
 *
 * This file is part of the "Advance News Search" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018
 *
 ***/

/**
 * Class ExtendSearch
 *
 */
class Search extends \GeorgRinger\News\Domain\Model\Dto\Search
{
    /**
     * Search teaser
     *
     * @var string
     */
    protected $teaser;

    /**
     * Search title
     *
     * @var string
     */
    protected $title;

    /**
     * Search category
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\Category>
     */
    public $category = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->category = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the category
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\Category> $category
     */
    public function geCategory()
    {
        return $this->category;
    }

    /**
     * Sets the category
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\Category> $category
     * @return void
     */
    public function seCategory(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $category)
    {
        $this->category = $category;
    }

    /**
     * @param string $teaser
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return string
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
