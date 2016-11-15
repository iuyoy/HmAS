<?php

function registerNewUser($db, $mail){
    $is_user_existed = getUserByMail($db, $mail);
    if($is_user_existed['Status'] === 0){
        $password_plain = generatePassword();
        $salt = generateSalt();
        $password_hashed = hashPassword($password_plain, $salt);
        $time = date("Y-m-d H:i:s");
        $sql = 'INSERT INTO `user`(`mail`, `password`, `salt`, `signupat`, `lastlogin`) VALUES (?, ?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->execute([$mail, $password_hashed, $salt, $time, $time]);
        $id = $db->lastInsertId();
        if($id){
            $token = generateToken($id, $password_hashed);
            $ret = addNewToken($db, $id, $token);
            if($ret['Status'] === 1)
                return array('Status' => 1, 'Result' => array('ID' => $id, 'Password' => $password_plain, 'Token' => $token));
        }
        return array('Status' => 0, 'Result' => 'Database Error.');
    }
    return array('Status' => 0, 'Result' => 'The email has been used.');

}

function addSensorData($db, $userid, $timestamp, $array) {
    return addData($db, "sensor_data", $userid, $timestamp, $array);
}

function addHappinessData($db, $userid, $timestamp, $array) {
    return addData($db, "happiness_data", $userid, $timestamp, $array);
}

function addData($db, $database, $userid, $timestamp, $array) {
    $sql_1 = "INSERT INTO `$database`(`userid`, `timestamp`";
    $sql_2 = ') VALUES(?, ?';
    $parameters = array($userid, $timestamp);
    foreach ($array as $key => $value) {
        $sql_1 .= ", `$key`";
        $sql_2 .= ', ?';
        $parameters[] = $value;
    }
    $sql = $sql_1.$sql_2.')';
    // echo $sql;
    $stmt = $db->prepare($sql);
    $stmt->execute($parameters);
    $id = $db->lastInsertId();
    if($id){
        return array('Status' => 1, 'Result' => array('ID' => $id));
    }
    return array('Status' => 0, 'Result' => 'Database Error.');
}

function updateAccountInfo($db, $userid ,$array) {
    $sql_1 = 'UPDATE  `user` SET ';
    $sql_2 = ' WHERE `id` = ?';
    $parameters = array();
    foreach ($array as $key => $value) {
        $sql_1 .= "`$key` = ?, ";
        $parameters[] = $value;
    }
    $parameters[] = $userid;
    $sql = substr($sql_1, 0, -2).$sql_2;
    // echo $sql;
    $stmt = $db->prepare($sql);
    $ret = $stmt->execute($parameters);
    if($ret){
        return array('Status' => 1);
    }
    return array('Status' => 0, 'Result' => 'Database Error.');
}

function updateUserInfo($db, $userid ,$array) {
    $ret = getUserInfo($db, $userid);
    if($ret['Status'] === 1)
        foreach ($ret['Result'] as $key => $value)
            if(!array_key_exists($key, $array))
                $array[$key] = $value;

    $sql_1 = 'INSERT INTO `user_info`(`userid`,`updatetime`  ';
    $sql_2 = ') VALUES(?, ?';
    $parameters = array($userid, date("Y-m-d H:i:s"));
    foreach ($array as $key => $value) {
        $sql_1 .= ", `$key`";
        $sql_2 .= ', ?';
        $parameters[] = $value;
    }
    $sql = $sql_1.$sql_2.')';
    $stmt = $db->prepare($sql);
    $stmt->execute($parameters);
    $id = $db->lastInsertId();
    if($id){
        return array('Status' => 1, 'Result' => array('ID' => $id));
    }
    return array('Status' => 0, 'Result' => 'Database Error.');
}

function getUserInfo($db, $userid){
    $sql = "SELECT `weight`,`sportiness` FROM `user_info` WHERE `userid` = ? ORDER BY `id` DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute([$userid]);
    $ret = $stmt->fetch();
    if($ret !== false){
        return array('Status' => 1, 'Result' => array('weight' => $ret['weight'], 'sportiness' => $ret['sportiness']));
    }
    return array('Status' => 0, 'Result' => 'Mail or password is wrong.');
}
// function NewToken()

function addNewToken($db, $userid, $token){
    $sql = 'INSERT INTO `auth`(`userid`, `token`, `created`, `isdestroy`) VALUES (?, ?, ?, ?)';
    $stmt = $db->prepare($sql);
    $stmt->execute([$userid, $token, date("Y-m-d H:i:s"), 0]);
    $id = $db->lastInsertId();
    if($id){
        return  array('Status' => 1, 'Result' => array('ID' => $id));
    }
    return array('Status' => 0, 'Result' => 'Database Error.');
}

function bindDevice($db, $deviceid, $userid){
    $isBound = isDeviceBindwithUser($db, $deviceid, $userid);
    if($isBound['Status'] === 0)
    {
        $sql = 'INSERT INTO `device`(deviceid, userid) VALUES(?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->execute([$deviceid, $userid]);
        $id = $db->lastInsertId();
        if($id){
            return  array('Status' => 1, 'Result' => array('ID' => $id));
        }
        return array('Status' => 0, 'Result' => 'Database Error.');
    }
    return array('Status' => 0, 'Result' => 'The device has been bound with this acount.');
}

function getUserIDByToken($db, $token){
    $sql = 'SELECT `userid` FROM `auth` WHERE `token` = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$token]);
    $ret = $stmt->fetch();
    if($ret !== false){
        return array('Status' => 1, 'Result' => array('UserID' => $ret['userid']));
    }
    return array('Status' => 0, 'Result' => 'Token is wrong.');
}

function getUserByMail($db, $mail){
    $sql = 'SELECT `id`, `password` ,`salt` FROM `user` WHERE `mail` = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$mail]);
    $ret = $stmt->fetch();
    if($ret !== false){
        return array('Status' => 1, 'Result' => array('ID' => $ret['id'], 'Password' => $ret['password'], 'Salt' => $ret['salt']));
    }
    return array('Status' => 0, 'Result' => 'Mail or password is wrong.');
}

function isDeviceBindwithUser($db, $deviceid, $userid){
    $sql = 'SELECT `id`, `deviceid` ,`userid` FROM `device` WHERE `deviceid` = ? AND `userid` = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute([$deviceid, $userid]);
    $ret = $stmt->fetch();
    if($ret !== false){
        return array('Status' => 1, 'Result' => array('ID' => $ret['id'], 'DeviceID' => $ret['deviceid'], 'UserID' => $ret['userid']));
    }
    return array('Status' => 0, 'Result' => 'The device isn\'t bound with this account.');
}
?>
