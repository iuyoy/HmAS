<?php

class UserAction extends BaseAction{

    public function signup($request, $response, $args) {
        $requests = array_change_key_case($request->getParsedBody(), CASE_LOWER);
        $requires = array('mail' => 'checkEmail', );
        $optional = array('deviceid' => 'checkString', );

        $ret = $this->check($requests, $requires, $optional);
        if($ret['Status'] === 1) {
            $requires = $ret['Result']['Requires'];
            $optional = $ret['Result']['Optional'];
            $ret = registerNewUser($this->ci->db, $requires['mail']);
            if($ret['Status'] === 1) {
                $id = $ret['Result']['ID'];
                $password = $ret['Result']['Password'];
                $token = $ret['Result']['Token'];
                $ret = array('Status' => 1, 'Password' => $password, 'Token' => $token );
                if(array_key_exists('deviceid', $optional)) {
                    $bindret = bindDevice($this->ci->db, $optional['deviceid'], $id);
                    if($bindret['Status'] === 0){
                        $ret['Status'] = 2;
                        $ret['Result'] = 'Fail to bind device with this account.';
                    }
                }
            }
        }
        $response->withJson($ret);
        return $response;
    }

    public function auth($request, $response, $args) {
        $requests = array_change_key_case($request->getParsedBody(), CASE_LOWER);
        $requires = array('mail' => 'checkEmail', 'password' => 'checkString');

        $ret = $this->check($requests, $requires, array());
        if($ret['Status'] === 1) {
            $requires = $ret['Result']['Requires'];
            $ret = getUserByMail($this->ci->db, $requires['mail']);
            if($ret['Status'] === 1) {
                $id = $ret['Result']['ID'];
                $password_hashed = $ret['Result']['Password'];
                $salt = $ret['Result']['Salt'];
                $ret = checkPassword($requires['password'], $salt, $password_hashed);
                if($ret === true) {
                    $token = generateToken($id, $password_hashed);
                    $ret = addNewToken($this->ci->db, $id, $token);
                    if($ret['Status'] === 1) {
                        $ret = array( 'Status' => 1, 'Token' => $token );
                    }
                }else
                    $ret = array( 'Status' => 0, 'Result' => 'Password or mail is wrong.' );
            }else
                $ret = array( 'Status' => 0, 'Result' => 'Password or mail is wrong.' );
        }
        $response->withJson($ret);
        return $response;
    }

    public function bind($request, $response, $args) {
        $requests = array_change_key_case($request->getParsedBody(), CASE_LOWER);
        $requires = array('token' => 'checkString', 'deviceid' => 'checkString');

        $ret = $this->check($requests, $requires, array());
        if($ret['Status'] === 1) {
            $requires = $ret['Result']['Requires'];
            $ret = getUserIDByToken($this->ci->db, $requires['token']);
            if($ret['Status'] === 1) {
                $userid = (int) $ret['Result']['UserID'];
                $ret = bindDevice($this->ci->db, $requires['deviceid'], $userid);
                if($ret['Status'] === 1)
                    $ret = array('Status' => 1);
            }
        }
        $response->withJson($ret);
        return $response;
    }

    public function update($request, $response, $args) {
        $requests = array_change_key_case($request->getParsedBody(), CASE_LOWER);
        $requires = array('token' => 'checkString');
        $optional = array(
            'name' => 'checkString',
            'age' => 'checkInt',
            'avatar' => 'checkString',
            'weight' => 'checkDouble',
            'sportiness' => 'checkInt', );
        $ret = $this->check($requests, $requires, $optional);
        if($ret['Status'] === 1) {
            $requires = $ret['Result']['Requires'];
            $optional = $ret['Result']['Optional'];
            $ret = getUserIDByToken($this->ci->db, $requires['token']);
            if($ret['Status'] === 1) {
                if(count($optional) > 0 ){
                    $userid = (int) $ret['Result']['UserID'];
                    $ret1 = array('Status' => 1);
                    $ret2 = array('Status' => 1);

                    $optional_update = array();
                    foreach (array('name', 'age', 'avatar') as $key => $value)
                        if(array_key_exists($value, $optional))
                            $optional_update[$value] = $optional[$value];
                    if(count($optional_update) > 0)
                        $ret1 = updateAccountInfo($this->ci->db, $userid, $optional_update);

                    $optional_insert = array();
                    foreach (array('weight', 'sportiness') as $key => $value)
                        if(array_key_exists($value, $optional))
                            $optional_insert[$value] = $optional[$value];

                    if(count($optional_insert) > 0)
                        $ret2 = updateUserInfo($this->ci->db, $userid, $optional_insert);
                    if($ret1['Status'] === 1 && $ret2['Status'] === 1){
                        $ret = array( 'Status' => 1);
                    }elseif ($ret1['Status'] === 1 || $ret2['Status'] === 1) {
                        $ret = array( 'Status' => 2, 'Result' => 'Some things are updated successfully, but the others are failed.');
                    }else{
                        $ret = array( 'Status' => 0, 'Result' => 'Fail to update user information.');
                    }
                }else{
                    $ret = array( 'Status' => 0, 'Result' => 'Set or change nothing.' );
                }
            }
        }
        $response->withJson($ret);
        return $response;
    }
}

?>
