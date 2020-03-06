<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Pegawai extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('kepegawaian/m_pegawai');
    }

    public function index()
    {
        $this->load->admin('admin/kepegawaian/pegawai/list');
    }

    public function create()
    {
        $this->load->view('admin/kepegawaian/pegawai/form', ['id' => '', 'action' => 'create']);
    }

    public function read($id)
    {
        $this->load->view('admin/kepegawaian/pegawai/read', ['id' => $id, 'action' => 'read']);
    }

    public function update($id)
    {
        $this->load->view('admin/kepegawaian/pegawai/form', ['id' => $id, 'action' => 'update']);
    }

    public function delete($id)
    {
        header('Content-Type: application/json');
        echo json_encode(["data"=>$this->m_pegawai->delete($id)]);
    }

    public function json()
    {
        header('Content-Type: application/json');
        echo $this->m_pegawai->json();
    }
}
