<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Jk extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('master/m_jk');
    }

    public function index()
    {
        $this->load->admin('admin/master/jk/list');
    }

    public function create()
    {
        $this->load->view('admin/master/jk/form', ['id' => '', 'action' => 'create']);
    }

    public function read($id)
    {
        $this->load->view('admin/master/jk/read', ['id' => $id, 'action' => 'read']);
    }

    public function update($id)
    {
        $this->load->view('admin/master/jk/form', ['id' => $id, 'action' => 'update']);
    }

    public function delete($id)
    {
        header('Content-Type: application/json');
        echo json_encode(["data"=>$this->m_jk->delete($id)]);
    }

    public function json()
    {
        header('Content-Type: application/json');
        echo $this->m_jk->json();
    }
}
