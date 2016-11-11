<?php
class AuthAction{
    protected $ci;
    protected $path;
    protected $baseUri;
    protected $data;
    //Constructor
    public function __construct(Slim\Container $ci) {
        $this->ci = $ci;
    }

    public function signup($request, $response, $args) {
        $result = array('status' => 1);
        $gets = $request->getParsedBody();

    }

    public function auth($request, $response, $args) {
    }
}

?>
