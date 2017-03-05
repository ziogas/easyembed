<?php

/*
 * Sample test controller
 */
class test_controller
{
    public function __construct()
    {
        //$this->model = EE::model('test');
    }

    /*
     * This private method is just a way to always include layout and could be completely avoided.
     */
    private function load_view($page)
    {
        EE::set('__page', $page);
        return EE::view('layout');
    }

    public function foo_action($path)
    {
        $id = (isset($path [ 0 ])) ? $path [ 0 ] : null;

        EE::set('id', $id);
        $this->load_view('foo');
    }

    public function index_action($path)
    {
        $this->load_view('index');
    }

    public function custom_bar_action($path)
    {
        $id = (isset($path [ 0 ])) ? $path [ 0 ] : null;

        EE::set('id', $id);
        $this->load_view('bar');
    }
}
