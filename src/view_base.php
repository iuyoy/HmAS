<?php
require "checker.php";
require "util.php";
require "dao.php";

class BaseAction{
    protected $ci;

    public function __construct(Slim\Container $ci) {
        $this->ci = $ci;
    }

    public function check($requests, $requires, $optional){
        $ret = requiredParametersFilter($requires, $requests);
        if($ret['Status'] === 1) {
            $requires = $ret['Result'];
            $ret = optionalParametersFilter($optional, $requests);
            //Check deviceid
            if($ret['Status'] === 1) {
                $optional = $ret['Result'];
                $ret = array('Status' => 1, 'Result' => array('Requires' => $requires, 'Optional' => $optional));
            }
        }
        return $ret;
    }
}
?>
