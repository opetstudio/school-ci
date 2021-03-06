<?php

if (!defined('BASEPATH')) {
    exit('No direct script allowed');
}

class M_outlet extends CI_Model
{
    public $table = 'app_toko';
    public $alias = 'mst';
    public $id = 'id';
    public $order = 'DESC';
    public $limit = 20;
    public $sql = "
                        select * from (SELECT apt.*, 
                        ask.nm_skl,
                        if(apt.is_active=1,'YES','NO') as is_active_name,
                        muc.name as created_by_name,
                        muu.name as updated_by_name,
                        mup.name as pemilik,
                        DATE_FORMAT(apt.created_dt, '%d/%m/%Y %H:%i:%s') as created_dt_name,
                        DATE_FORMAT(apt.updated_dt, '%d/%m/%Y %H:%i:%s') as updated_dt_name
                        from app_toko apt
                        left join app_user muc on muc.id = apt.created_by
                        left join app_user muu on muu.id = apt.updated_by
                        left join app_user mup on mup.id = apt.id_user
                        left join app_skl ask on ask.id = apt.id_skl
                        where apt.is_active != 9 
                        ) as mst
    ";
    public $where = [];
    public $add_params = [];

    public $select = 'id,nm_skl,pemilik,keterangan,nama_toko,is_active_name,created_dt_name,created_by_name,updated_by,updated_by_name,updated_dt_name';

    public function __construct()
    {
        parent::__construct();
    }

    public function create($params = [])
    {
        $params = compareFields('create', $this->table, $params);
        $builder = $this->db->insert($this->table, $params);

        return $builder ? $this->db->insert_id() : false;
    }

    public function read($params = [])
    {
        // var_dump($this->sql); die;
        $read = readSql($this, $params);

        return $read;
    }

    public function update($params = [], $id)
    {
        $params = compareFields('update', $this->table, $params);
        $builder = $this->db->update($this->table, $params, [$this->id => $id]);

        return $builder ? $id : false;
    }

    public function delete($id)
    {
        //$builder = $this->db->update($this->table, ['is_active' => STATUS_IS_DELETE], [$this->id => $id]);
        $builder = $this->db->delete($this->table, [$this->id => $id]);

        return $builder ? $id : false;
    }

    public function json()
    {
        $this->datatables->select($this->select);
        $this->datatables->from('
            (
                '.$this->sql.'
            ) as mst
        ');
        //add this line for join
        $this->datatables->where('mst.is_active in(0,1)');
        $this->datatables->where('mst.id_skl in('.$_POST['id_skl'].')');

        return $this->datatables->generate();
    }

    public function getData($id_user="",$id_skl=""){
        if($id_user != ""){
            $this->db->where("id_user = $id_user and id_skl in($id_skl)");
        }
        $q=$this->db->query($this->sql)->result_array();
        return $q;
    }
}
