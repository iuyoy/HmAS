<?php
function requiredParametersFilter($requires, $parameters) {
    $ret = array('Status' => 1, 'Result' => '');
    $filtered_parameters = array();
    foreach ($requires as $parameter_name => $check_function) {
        if(array_key_exists($parameter_name, $parameters)) {
            //Check the format of the parameter
            $checked_parameter = $check_function($parameters[$parameter_name]);
            if($checked_parameter !== false){
                $filtered_parameters[$parameter_name] = $checked_parameter;
            }else{
                $ret['Status'] = 0;
                $ret['Result'] = "The format of parameter $parameter_name is wrong.";
                break;
            }
        }else{
            $ret['Status'] = 0;
            $ret['Result'] = "Can't find paramter $parameter_name.";
            break;
        }
    }
    if ($ret['Status'] === 1) $ret['Result'] = $filtered_parameters;
    return $ret;
}

function optionalParametersFilter($optional, $parameters) {
    $ret = array('Status' => 1, 'Result' => '');
    $filtered_parameters = array();
    foreach ($optional as $parameter_name => $check_function) {
        if(array_key_exists($parameter_name, $parameters)) {
            //Check the format of the parameter
            $checked_parameter = $check_function($parameters[$parameter_name]);
            if($checked_parameter !== false){
                $filtered_parameters[$parameter_name] = $checked_parameter;
            }else{
                $ret['Status'] = 0;
                $ret['Result'] = "The format of parameter $parameter_name is wrong.";
                break;
            }
        }
    }
    if ($ret['Status'] === 1) $ret['Result'] = $filtered_parameters;
    return $ret;
}

// function

//Functions about password and random string.
function generateToken($id, $password) {
    return hash('md5', (string) time() . (string) $id . (string) $password);
}

function hashPassword($password, $salt) {
    return hash('sha512', $password.$salt);
}

function generatePassword() {
    $pass_list = '0123456789';
    return generateRandString($pass_list, 6);
}
function generateSalt() {
    $salt_list='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    return generateRandString($salt_list, 8);
}

function generateRandString($chara_list, $length) {
    $strlen = strlen($chara_list) - 1;
    $string = '';
    for($i=0; $i<$length; $i++)
        $string .= $chara_list[mt_rand(0, $strlen)];
    return $string;
}
?>
