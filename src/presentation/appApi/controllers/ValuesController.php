<?php
namespace Presentation\AppApi\Controllers;

/**
 * ValuesController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */

class ValuesController
{
    /**
     * Use constructor to Inject or instanciate dependecies
     */
    public function __construct()
    {
    }

    /**
     * The Method httpGet() called to handle a GET request
     * URI: POST: https://api.com/values
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpGet(int...$id): ?array
    {
        return [
            'hospital api',
            'value1',
            'value2',
        ];
    }

    /**
     * The Method httpPost() called to handle a POST request
     * This method requires a body(json) which is passed as the var array $form
     * URI: POST: https://api.com/values
     */
    public function httpPost(array $form)
    {
        $postId = null;

        // code here
        return ['success' => true, 'alert' => 'We have it at post', 'id' => $postId];
    }

    /**
     * The Method httpPut() called to handle a PUT request
     * This method requires a body(json) which is passed as the var array $form and
     * An id as part of the uri.
     * URI: POST: https://api.com/values/2 the number 2 in the uri is passed as int $id to the method
     */
    public function httpPut(array $form, int $id)
    {

        // code here
        return ['success' => true, 'alert' => 'We have it at put'];
    }

    /**
     * The Method httpDelete() called to handle a DELETE request
     * URI: POST: https://api.com/values/2 ,the number 2 in the uri is passed as int ...$id to the method
     */
    public function httpDelete(int $id)
    {
        // code here
        return ['id' => $id];
    }
}
