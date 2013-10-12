<?php

class HomeController extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = array();
        $data['auth'] = Auth::check();
        return $this->_display('home/home',$data);
    }

}
