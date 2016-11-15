<?php

class HappinessAction extends BaseAction{
    protected $ci;

    public function __construct(Slim\Container $ci) {
        $this->ci = $ci;
    }

    public function collectData($request, $response, $args) {
        $requests = array_change_key_case($request->getParsedBody(), CASE_LOWER);
        $requires = array('token' => 'checkString', 'timestamp' => 'checkTime');
        $optional = array(
            'happiness' => 'checkInt',
            'avgbpm' => 'checkInt',
            'whohaveyoubeenwith' => 'checkInt',
            'didyoudosports' => 'checkInt');

        $ret = $this->check($requests, $requires, $optional);
        if($ret['Status'] === 1) {
            $requires = $ret['Result']['Requires'];
            $optional = $ret['Result']['Optional'];
            if(count($optional) > 0) {
                $ret = getUserIDByToken($this->ci->db, $requires['token']);
                if($ret['Status'] === 1) {
                    $userid = (int) $ret['Result']['UserID'];
                    $ret = addHappinessData($this->ci->db, $userid, $requires['timestamp'], $optional);
                    if($ret['Status'] === 1) {
                        $ret = array('Status' => 1);
                    }
                }
            }else
                $ret = array('Status' => 0, 'Result' => 'Collect nothing.' );
        }

        $response->withJson($ret);
        return $response;
    }
}

?>
