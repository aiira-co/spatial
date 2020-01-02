<?php

declare(strict_types=1);

namespace Common\Libraries;

use Spatial\Mediator\Mediator;
use Spatial\Router\ActivatedRoute;

/**
 * BaseController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class Controller
{
    protected ?int $page;
    protected ?int $pageSize;
    protected ?int $id;
    protected ?int $category;
    protected ?int $categoryType;
    protected ?string $searchValue;
    protected ?array $searchFields = [];
    protected ?array $orderBy = [];
    protected ?string $uri;
    protected ?int $offset;

    protected ?int $entityId;

    protected Mediator $mediator;
    protected ActivatedRoute $params;

    /**
     * Use constructor to Inject or instanciate dependecies
     */
    public function __construct()
    {
        $this->params = new ActivatedRoute();
        $this->mediator = new Mediator();

        $this->page = !is_null($this->params->page) ?
            (int) $this->params->page : 1;

        $this->pageSize = !is_null($this->params->pageSize) ?
            (int) $this->params->pageSize : 10;

        $this->offset = ($this->page - 1) * $this->pageSize;

        if (!is_null($this->params->searchValue)) {
            $this->_searchAlg($this->params->searchValue);
        } else {
            $this->searchValue = '%';
        }

        $this->id = (int) $this->params->id;
        $this->category = (int) $this->params->category;
        $this->categoryType = (int) $this->params->categoryType;

        $this->searchFields = !is_null($this->params->searchFields) ?
            explode(',', ($this->params->searchFields)) : null;

        $this->orderBy = $this->params->orderBy = !is_null($this->params->orderBy) ?
            explode(',', $this->params->orderBy) : null;

        $this->entityId = 2;
        $this->onInit();
    }

    /**
     * OnInit()
     *
     * @return void
     */
    public function onInit()
    {
    }

    /**
     * Search Key Algorithim
     *
     * @param string $search
     * @return void
     */
    private function _searchAlg(?string $search)
    {

        $this->searchValue = '%' . str_replace(' ', '%', $search) . '%';
        // echo $this->searchValue;
    }
}
