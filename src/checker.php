<?php

function checkEmail($email) {
    $pattern = "^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.([a-z\\.]+))\b^";
    return preg_match($pattern, $email) ? $email : false;
}

function checkPassword($password_input, $salt, $password_hashed) {
    return hashPassword($password_input, $salt) === $password_hashed ? true : false;
}

function checkTime($time){
    $ret = strtotime($time);
    if($ret !== false)
        $time =$ret;
    $ret = date("Y-m-d H:i:s", $time);
    return $ret;
}

// General checker or converter
function checkString($smt) {
    return (string) $smt;
}

function checkInt($smt) {
    return (int) $smt;
}

function checkDouble($smt) {
    return (Double) $smt;
}
?>
