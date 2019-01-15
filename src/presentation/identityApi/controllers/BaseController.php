<?php

namespace Presentation\IdentityApi\Controllers;

use Presentation\IdentityApi\Models\UserLogModel;
use Cqured\Router\ActivatedRoute;

/**
 * BaseController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class BaseController
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

    /**
     * Use constructor to Inject or instanciate dependecies
     */
    public function __construct()
    {
        $this->params = new ActivatedRoute;

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
        explode(',', $this->params->searchFields) :
        null;

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

        return (new UserLogModel)->logUser($data);
    }

    /**
     * The Method httpGet() called to handle a GET request
     * URI: POST: https://api.com/values
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id
     * to the methodUndocumented function
     *
     * @param integer ...$id
     * @return array|null
     */
    public function httpGet(int...$id): ?array
    {
        // --- use this if you are connected to the Databases ---
        // if (count($id)) {
        //     $users = Lynq\Entity\EntityModel::table('user')
        //                     ->where('id', $id[0])
        //                     ->single();
        // } else {
        //     $users = Lynq\Entity\EntityModel::table('users')->get();
        // }

        // return ['data'=>$users,'totalCount'=>count($users)];

        return ['value1000', 'value2'];
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var array $form
     * URI: POST: https://api.com/values
     *
     * @param array $form
     * @return array|null
     */
    public function httpPost(array $form): ?array
    {
        $postId = null;
        // --- use this if you are connected to the Databases ---
        // if (Lynq\Entity\EntityModel::table('values')->add($form)) {
        //     $alert = 'Succesfully saved';
        //      $postId = Lynq\Entity\EntityModel::$postId;
        // } else {
        //     $alert = 'Could not be saved. Please try again';

        // }

        // code here
        return ['success' => true, 'alert' => 'We have it at post', 'id' => $postId];
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var array $form and
     * An id as part of the uri.
     * URI: POST: https://api.com/values/2 the number 2 in
     * the uri is passed as int $id to the method
     *
     * @param array $form
     * @param integer $id
     * @return array|null
     */
    public function httpPut(array $form, int $id): ?array
    {

        // --- use this if you are connected to the Databases ---
        // if (Lynq\Entity\EntityModel::table('values')->where('id',$id)->update($form)) {
        //     $alert = 'Succesfully updated';
        //      $success = true;
        // } else {
        //     $alert = 'Could not be saved. Please try again';
        //      $success = false;

        // }

        // code here
        return ['success' => true, 'alert' => 'We have it at put'];
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: POST: https://api.com/values/2 ,the number 2 in
     * the uri is passed as int ...$id to the method
     *
     * @param integer $id
     * @return array|null
     */
    public function httpDelete(int $id): ?array
    {
        // code here
        return ['id' => $id];
    }
}
