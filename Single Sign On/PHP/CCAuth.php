<?php
/**
 * Created by PhpStorm.
 * User: benjaminsmiley-andrews
 * Date: 28/09/2014
 * Time: 12:31
 */


class CCAuth {

    private $uid;
    private $api_key;
    private $domain;

    public function CCAuth ($uid, $api_key, $domain) {
        $this->uid = $uid;
        $this->api_key = $api_key;
        $this->domain = $domain;
    }

    function getToken () {

        if(!isset($this->uid) || !isset($this->api_key) || !isset($this->domain)) {
            return null;
        }

        $fields = array(
            'uid' => urlencode($this->uid),
            'api_key' => urlencode($this->api_key),
            'domain' => urlencode($this->domain),
        );

        // Create a URL string
        $fields_string = "";

        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';

        // Make a cURL request to the authentication API
        $url = null;
        if($_SERVER['SERVER_NAME'] == 'ccwp') {
            $url = 'http://ccwp/wp-content/plugins/chatcat-api/cc-auth-api.php';
        }
        else {
            $url = $protocol . 'chatcat.io/wp-content/plugins/chatcat-api/cc-auth-api.php';
        }

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        return $result;

    }

    /**
     * @param string $name - The user's name
     * @param string $status - User's public status
     * @param string $gender - "M" or "F"
     * @param string $yearOfBirth - in YYYY format
     * @param string $city - City or residence
     * @param string $countryCode - i.e. GB, US
     * @param string $imageURL - i.e. A url to the user's profile image - must be JPEG or PNG
     * @param array  $options [Optional] - Extra parameters to be passed back to chat client
     *                   - 'profileHTML': must contains HTML that will be displayed instead of the default user popup
     */
    function authUser($name, $status, $gender, $yearOfBirth, $city, $countryCode, $imageURL, $options = null) {

        // First get the user's token - if that's valid then return the user's information
        // plus the token to the client
        $token = json_decode($this->getToken());

        if($token && !isset($token->error)) {

            $result = array(
                'name' => $name,
                'status' => $status,
                'gender' => $gender,
                'yearOfBirth' => $yearOfBirth,
                'city' => $city,
                'countryCode' => $countryCode,
                'token' => $token->token,
                'imageURL' => $imageURL
            );

            if(isset($options)) {
                $result = array_merge($result, $options);
            }

            echo json_encode($result);

        }
        else {
            echo json_encode(array('error' => "Login error - please contact administrator"));
        }

    }

    static function logout () {
        echo json_encode(array('error' => "Not logged in"));
    }

} 