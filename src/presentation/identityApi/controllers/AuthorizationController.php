<?php
namespace Presentation\IdentityApi\Controllers;

// use Api\Controller\BaseController;
use Presentation\IdentityApi\Models\AuthenticationModel;
use Presentation\IdentityApi\Models\UserModel;

/**
 * AuthorizationController Class exists in the Api\Controllers namespace
 * A Controller represets the individual URIs client apps access to interact with data
 * URI:  https://api.com/values
 *
 * @category Controller
 */
class AuthorizationController extends BaseController
{

    /**
     * Method To init
     *
     * @return void
     */
    public function onInit()
    {
        $this->_authenticationModel = new AuthenticationModel;
        $this->_userModel = new UserModel;
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
        if (isset($this->params->email)) {
            $result = $this->_personExists('email', $this->params->email);
        } elseif (isset($this->params->username)) {
            $result = $this->_personExists('username', $this->params->username);
        }

        return ['notify' => $result];
    }

    /**
     * Checks if email/ username
     * already exists in the DB
     *
     * @param string $string
     * @param [type] $field
     * @return boolean
     */
    private function _personExists(string $field, string $string): bool
    {
        if ($this->_userModel->countPerson($field, $string)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Login Requests
     *
     * @param array $form
     * @return array|null
     */
    public function httpPost(array $form): ?array
    {
        switch ($this->params->category) {
            case 'autoauth':
                return $this->_authenticationModel
                    ->autoLogin(base64_decode($form));
                break;
            case 'social':
                return $this->_socialLogin($form);
                break;
            default:
                return $this->_authenticationModel
                    ->login($form['email'], $form['password']);
                break;
        }
    }

    private function _socialLogin(array $form): ?array
    {
        if ($this->_verifySocialToken($form['provider'], $form)) {
            // check if email _personExists
            if ($this->_personExists('email', $form['email'])) { // login User.
                return $this->_authenticationModel
                    ->socialEmailLogin($form['email'], $form['provider']);
            } else {
                // signup user then login
                $username = explode('@', $form['email'])[0];
                $data = [
                    'surname' => $form['name'],
                    'othername' => '',
                    'username' => $username,
                    'hashed' => password_hash($username . '123', PASSWORD_DEFAULT),
                    'emailVerified' => false,
                    'activated' => true,
                    'phoneVerified' => false,
                    'email' => $form['email'],
                    'image' => $form['image'],
                    'provider' => $form['provider'],
                ];

                $accountClass = new \Api\Controllers\AccountController();
                return $accountClass->_signup($data);

            }

        }

        return [
            'result' => false,
            'message' => 'Verification Failed!!',
        ];
    }

    /**
     * Verify Social Login Tokens
     *
     * @param string $provider
     * @param array $token
     * @return boolean
     */
    private function _verifySocialToken(string $provider, array $form): bool
    {
        switch ($provider) {

            case 'google':
                // Get $id_token via HTTPS POST.
                $CLIENT_ID = '774529671435-pfq7bebm3l2et6vpo980aekpsbseps3p.apps.googleusercontent.com';
                $client = new \Google_Client(['client_id' => $CLIENT_ID]); // Specify the CLIENT_ID of the app that accesses the backend
                $payload = $client->verifyIdToken($form['idToken']);

                if ($payload) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'facebook':
                # code...
                return true;
                break;
            case 'twitter':
                # code...
                break;

            default:
                return false;
                break;
        }

    }

}

// check wether its google, facebook or twitter
// verify the log to my appId,
// check if email already exists in db,
// if yes - login and return token
// else, signup user and user
// explode('@',email).'123' as default password
//     googleSignIn Data
// {
//     email: "koathecedi@gmail.com"
//     id: "107684393104945235445"
//     idToken: "eyJhbGciOiJSUzI1NiIsImtpZCI6ImJlODliYjdiYTliZTEwODkxNDg4MzQ0YTU1NDhiOGE0ZGI0N2RlMGUifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiYXpwIjoiNzc0NTI5NjcxNDM1LXR0aHUxYWFzZHNqMW8yMWY2M2M4YmkzMWlqZ2s0bTJpLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXVkIjoiNzc0NTI5NjcxNDM1LXR0aHUxYWFzZHNqMW8yMWY2M2M4YmkzMWlqZ2s0bTJpLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwic3ViIjoiMTA3Njg0MzkzMTA0OTQ1MjM1NDQ1IiwiZW1haWwiOiJrb2F0aGVjZWRpQGdtYWlsLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjp0cnVlLCJhdF9oYXNoIjoidHBBSUV6RXg0dEpaS1c5bHE4dEYtdyIsIm5hbWUiOiJPd3VzdS1BZnJpeWllIEtvZmkiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDUuZ29vZ2xldXNlcmNvbnRlbnQuY29tLy00c3B0V2FPOTBQZy9BQUFBQUFBQUFBSS9BQUFBQUFBQUFNTS9BQWlzejU5VFBpdy9zOTYtYy9waG90by5qcGciLCJnaXZlbl9uYW1lIjoiT3d1c3UtQWZyaXlpZSIsImZhbWlseV9uYW1lIjoiS29maSIsImxvY2FsZSI6ImVuIiwiaWF0IjoxNTM3NjI2MzI1LCJleHAiOjE1Mzc2Mjk5MjUsImp0aSI6IjNhMDA3MWExODgwODQ4YzI1MWJiZGEyOTk3ZDliNTM0NjU5ZTdhNGYifQ.JNc-_2ZQiJVQi64PrHsyaupAPvJsIFysWN7VDvgbmJQhcWGvhtaZ0j2uZ0lcxX4AJbmUwMgQW5qEt6TP4E9x4d0ndsCJtPw1ROG5YjoPDK1RHNp0Lp1aouUB8rTPGp2OEbIqu8zBK_C4MuRWiL1NgwNPNB5OTDNVT_IMWwY4D_rN_9olaorNcqgCZ62AR-Umi6eVZZMAxqKVTQsN3gdF5Nwtji5QaTfcVB4gM1vYyQyMxnxuncLBDLTfecUfRuMLL8yMVfdDKEQ5wVSPXV6qJ7MZIc6aLa1M3ioNNxxtTc9hqz1GlzGm0dGOpUEzWm3pWjGn3utwK2FZgAKMmAK5hw"
//     image: "https://lh5.googleusercontent.com/-4sptWaO90Pg/AAAAAAAAAAI/AAAAAAAAAMM/AAisz59TPiw/s96-c/photo.jpg"
//     name: "Owusu-Afriyie Kofi"
//     provider: "google"
//     token: "ya29.GlsgBrunAJg5VXlMoPP-yMFlr4bLEGv6z2m9MB3qxy7lOCUIY5sy20C_jc9jK7JSSmy6bHA4SQbUNanOIVk95mNW4YUq1f7__jnQlLeX_wlW8y3qL1TNI6lzNXx-"

// }

//     facebook sign in data :  SocialUser {
//         id: "2437458356272073",
//         name: "Kofi Owusu-Afriyie Hashem",
//         email: "koathecedi@gmail.com",
//         image: "https://graph.facebook.com/2437458356272073/picture?type=normal",
//         provider:"facebook"
//         token: "EAAJZCXmSDJv8BACrMdlercSzYFq3Y1KibZCLEonvIDgVbxg6Y…Dd3UeYENQ9ihEjBKcyBgdO06pr8vwVypeMyrKZBW7GuQwZDZD",
// }
