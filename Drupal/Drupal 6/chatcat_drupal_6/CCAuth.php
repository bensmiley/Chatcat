<?php
/*

LICENSE

Copyright 2015  Ben Smiley-Andrews  (email : ben@chatcatapp.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */

class CCAuth {

    private $uid;
    private $secret;
    private $api_key;
    private $domain;

    private $name;
    private $status;
    private $gender;
    private $yearOfBirth;
    private $city;
    private $countryCode;
    private $imageURL;
    private $options;

    public function CCAuth ($uid, $secret, $api_key, $domain) {
        $this->secret = $secret;
        $this->api_key = $api_key;
        $this->domain = $domain;
        $this->uid = $uid;
    }

    function getToken () {
    
        if(!isset($this->uid) || !isset($this->secret) || !isset($this->domain) || !isset($this->api_key)) {
            return null;
        }

        $fields = array(
            'uid' => urlencode($this->uid),
            'secret' => urlencode($this->secret),
            'domain' => urlencode($this->domain),
            'force' => 'true',
        );

        // Create a URL string
        $fields_string = "";

        foreach($fields as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        $fields_string = rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';

        // Make a cURL request to the authentication API
        //$url = null;
        //if($_SERVER['SERVER_NAME'] == 'ccwp') {
        //    $url = 'http://ccwp/wp-content/plugins/chatcat-api/cc-auth-api.php';
        //}
        //else {
            //$url = $protocol . 'chatcat.io/wp-content/plugins/chatcat-api/cc-auth-api.php';
            //$url = 'http://api.chatcatapp.com/cc-auth-api.php';
            $url = 'http://dev.chatcatapp.com/api/chat/token?' . $fields_string;
        //}

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
//         curl_setopt($ch,CURLOPT_POST, count($fields));
//         curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
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
    function setUserInfo ($name, $status, $gender, $yearOfBirth, $city, $countryCode, $imageURL, $options = null) {
        $this->name = $name;
        $this->status = $status;
        $this->gender = $gender;
        $this->yearOfBirth = $yearOfBirth;
        $this->city = $city;
        $this->countryCode = $countryCode;
        $this->imageURL = $imageURL;
        $this->options = $options;
    }

    function userInfoArray () {
        $info = array(
            'name' => $this->name,
            'status' => $this->status,
            'gender' => $this->gender,
            'yearOfBirth' => $this->yearOfBirth,
            'city' => $this->city,
            'countryCode' => $this->countryCode,
            'token' => $this->token->token,
            'imageURL' => $this->imageURL
        );
        if(isset($this->options)) {
            $info = array_merge($info, $this->options);
        }
        return $info;
    }

    /*
     * Respond to the client with the current user's token
     */
    function respondWithToken() {

        // First get the user's token - if that's valid then return the user's information
        // plus the token to the client
        $token = json_decode($this->getToken());

        if($token && !isset($token->error)) {
        
            $response = $this->userInfoArray();

            // Set the token
            $response['token'] = $token->token;
                        
            // Respond with the JSON
            echo json_encode($response);

        }
        else {
            echo json_encode(array('error' => "Login error - please contact administrator"));
        }

    }

    /**
     * Respond to the client with the current user ID
     */
    function respondWithUserID ($includeUserInfo=false) {

        $response = $this->userInfoArray();

        // Set the uid
        $response['uid'] = $this->api_key . ':' . $this->uid;

        echo json_encode($response);
    }

    static function logout () {
        echo json_encode(array('error' => "Not logged in"));
    }

} 