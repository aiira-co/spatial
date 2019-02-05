<?php

namespace Presentation\IdentityApi\Controllers;

// use Api\Controller\BaseController;

/**
 * HubtelcallbackController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class HubtelcallbackController extends BaseController
{
    private $table = 'UserTransaction';
    // method called to handle a GET request

    public function httpGet(int ...$id): ?array
    {
        return ['error'=>'Access Denied!'];
    }


    // method called to handle a POST request
    public function httpPost(array $form)
    {
        // update the respose

        $update = DB::table($this->table)
          ->where('transactionId', $form['Data']['TransactionId'])
          ->update([
              'responseCode'=>$form['ResponseCode']
              ]);

        if (!$update) {
            return ['result'=>false, 'message'=>'could not make firsdt update','form'=>$form];
        }
        // else{
        //     return ['sql'=>DB::$sql,'transactionId'=>$form['Data']['TransactionId'], 'responseCode'=>$form['ResponseCode']];
        // }


        if ($form['ResponseCode'] === '0000') {
            // subscribe
            $getTransSub = DB::table($this->table)
                                    ->where('transactionId', $form['Data']['TransactionId'])
                                    ->single();

            if (!empty($getTransSub)) {
                // Do Subscription
                $data =[
                  'userId'=>$getTransSub->clientReference,
                  'subscriptionId'=>$getTransSub->subscriptionId,
                  'activated'=>true,
                  'date'=>strtotime(date('Y-m-d h:i:s'))
              ];
                return ['result'=>DB::table('UserSubscription')->add($data)];
            } else {
                return ['result'=>false,'message'=>'payment made but couldnt subscribe','form'=>$form];
            }
        } else {
        }
        // code here
        return ['result'=>false, 'message'=>'payment failed'];
    }

    // method called to handle a PUT request
    public function httpPut(array $form, int $id)
    {
        // code here
        return ['error'=>'Access Denied!'];
    }
}
