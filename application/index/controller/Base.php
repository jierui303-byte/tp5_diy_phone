<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Loader;
use think\Session;

class Base extends Controller
{
    protected $current_action;
    protected $request;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->request = Request::instance();
    }
}
