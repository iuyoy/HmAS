<?php

class SensorAction extends BaseAction{

    public function collectData($request, $response, $args) {
        $requests = array_change_key_case($request->getParsedBody(), CASE_LOWER);
        $requires = array('token' => 'checkString', 'timestamp' => 'checkTime');
        $optional = array(
            'steps' => 'checkInt',
            'avgbpm' => 'checkInt',
            'minbpm' => 'checkInt',
            'maxbpm' => 'checkInt',
            'avglightlevel' => 'checkInt',
            'activity' => 'checkInt',
            'sleepseconds' => 'checkInt',
            'positionlat' => 'checkDouble',
            'positionlon' => 'checkDouble',
            'altitude' => 'checkDouble',
            'acc_x' => 'checkInt',
            'acc_y' => 'checkInt',
            'acc_z' => 'checkInt',
         );

        $ret = $this->check($requests, $requires, $optional);
        if($ret['Status'] === 1) {
            $requires = $ret['Result']['Requires'];
            $optional = $ret['Result']['Optional'];
            if(count($optional) > 0) {
                $ret = getUserIDByToken($this->ci->db, $requires['token']);
                if($ret['Status'] === 1) {
                    $userid = (int) $ret['Result']['UserID'];
                    $ret = addSensorData($this->ci->db, $userid, $requires['timestamp'], $optional);
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
