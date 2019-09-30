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
    protected $page;
    protected $pageSize;
    protected $id;
    protected $category;
    protected $categoryType;
    protected $searchValue;
    protected $searchFields = [];
    protected $uri;
    protected $offset;

    protected $entityId;

    protected $mediator;

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

        $this->id = $this->params->id;
        $this->category = $this->params->category;
        $this->categoryType = $this->params->categoryType;

        $this->searchFields = !is_null($this->params->searchFields) ?
            explode(',', $this->params->searchFields) : null;

        $this->entityId = 2;
        $this->onInit();
    }

    /**
     * OnInit()
     *
     * @return void
     */
    public function onInit()
    { }

    /**
     * Search Key Algorithim
     *
     * @param string $search
     * @return void
     */
    private function _searchAlg(string $search)
    {

        $this->searchValue = '%' . str_replace(' ', '%', $search) . '%';
        // echo $this->searchValue;
    }

    // check for authentication and authorization if posssible
    private function authJWT(): bool
    {
        return true;
    }

    public function userLog(string $logType, int $userId = 0): bool
    {
        switch ($logType) {
            case 'loggedIn':
                $activity = 1;
                break;
            case 'loggedOut':
                $activity = 2;
                break;

            case 'logFail':
                $activity = 3;
                break;

            case 'logAccBlocked':
                $activity = 4;
                break;

            default:
                $activity = 3;
                break;
        }

        $data = [
            'remoteAddr' => $_SERVER['REMOTE_ADDR'],
            'requestUri' => $_SERVER['REQUEST_URI'],
            'requestMethod' => $_SERVER['REQUEST_METHOD'],
            'userId' => $userId,
            'activityId' => $activity,
        ];

        return (new UserLogModel())->logUser($data);
    }
}
