<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Berbagiebook extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('forum/m_berbagi');
    }

    public function index()
    {
        $this->load->admin('admin/forum/berbagiebook/list');
    }

    public function create()
    {
        $this->load->view('admin/forum/berbagiebook/form', ['id' => '', 'action' => 'create']);
    }

    public function read($id)
    {
        $this->load->view('admin/forum/berbagiebook/read', ['id' => $id, 'action' => 'read']);
    }

    public function update($id)
    {
        $this->load->view('admin/forum/berbagiebook/form', ['id' => $id, 'action' => 'update']);
    }

    public function delete($id)
    {
        header('Content-Type: application/json');
        echo json_encode(["data"=>$this->m_berbagi->delete($id)]);
    }

    public function file($id)
    {
        $gambar = $this->m_berbagi->read(['id'=>$id,]);
        $attach = [];
        foreach ($gambar as $a => $b) {
            $attach[] = $b->attach;
        }
        $this->load->view('admin/forum/berbagiebook/file', ['id' => $id, 'action' => 'file', 'attach' => join(',',$attach),]);
    }

    public function json()
    {
        header('Content-Type: application/json');
        echo $this->m_berbagi->json();
    }
}
