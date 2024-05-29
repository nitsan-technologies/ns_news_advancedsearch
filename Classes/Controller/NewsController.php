<?php
namespace NITSAN\NsNewsAdvancedsearch\Controller;

use GeorgRinger\News\Domain\Model\Dto\Search;
use GeorgRinger\News\Event\NewsSearchResultActionEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use GeorgRinger\News\Pagination\QueryResultPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;

/**
 * Controller of news records
 *
 */
class NewsController extends \GeorgRinger\News\Controller\NewsController
{

    /**
     * Displays the search result
     *
     * @param \GeorgRinger\News\Domain\Model\Dto\Search $search
     * @param array $overwriteDemand
     *
     * @return void
     */
    public function searchResultAction(
        Search $search = null,
        array $overwriteDemand = []
    ): void {

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        if ($this->settings['disableOverrideDemand'] != 1 && $overwriteDemand !== null) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }

        if (!is_null($search)) {
            $search->setFields($this->settings['search']['fields']);
            $search->setDateField($this->settings['dateField']);
            $search->setSplitSubjectWords((bool)$this->settings['search']['splitSearchWord']);
        }

        $demand->setSearch($search);

        $newsRecords = $this->newsRepository->findDemanded($demand);

        // pagination
        $paginationConfiguration = $this->settings['list']['paginate'] ?? [];
        $itemsPerPage = (int)(($paginationConfiguration['itemsPerPage'] ?? '') ?: 10);
        $maximumNumberOfLinks = (int)($paginationConfiguration['maximumNumberOfLinks'] ?? 0);

        $currentPage = $this->request->hasArgument('currentPage')
            ? (int)$this->request->getArgument('currentPage')
            : 1;

        $paginator = GeneralUtility::makeInstance(
            QueryResultPaginator::class,
            $newsRecords,
            $currentPage,
            $itemsPerPage,
            (int)($this->settings['limit'] ?? 0),
            (int)($this->settings['offset'] ?? 0)
        );

        $paginationClass = $paginationConfiguration['class'] ?? SimplePagination::class;
        if (class_exists(NumberedPagination::class) &&
            $paginationClass === NumberedPagination::class &&
            $maximumNumberOfLinks
        ) {
            $pagination = GeneralUtility::makeInstance(
                NumberedPagination::class,
                $paginator,
                $maximumNumberOfLinks
            );
        } elseif (class_exists($paginationClass)) {
            $pagination = GeneralUtility::makeInstance($paginationClass, $paginator);
        } else {
            $pagination = GeneralUtility::makeInstance(SimplePagination::class, $paginator);
        }

        $assignedValues = [
            'news' => $newsRecords,
            'overwriteDemand' => $overwriteDemand,
            'search' => $search,
            'demand' => $demand,
            'settings' => $this->settings,
            'pagination' => [
                'currentPage' => $currentPage,
                'paginator' => $paginator,
                'pagination' => $pagination,
            ]
        ];

        $event = $this->eventDispatcher->dispatch(
            new NewsSearchResultActionEvent($this, $assignedValues,$this->request)
        );
        $this->view->assignMultiple($event->getAssignedValues());
    }
}