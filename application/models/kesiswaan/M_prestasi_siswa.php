<?php

if (!defined('BASEPATH')) {
    exit('No direct script allowed');
}

class M_prestasi_siswa extends CI_Model
{
    public $table = 'app_prestasi_siswa';
    public $alias = 'mst';
    public $id = 'id';
    public $order = 'DESC';
    public $limit = 20;
    public $sql = "
                    select * from (
                        select
                        akk.*,
                        if(akk.is_active=1,'YES','NO') as is_active_name,
                        kls.nama_kelas, jr.jurusan, sm.semester, sw.nama_siswa,
                        ata.tahun,
                        muc.name as created_by_name,
                        muu.name as updated_by_name,
                        DATE_FORMAT(tanggal, '%d/%m/%Y') as tanggal_name,
                        DATE_FORMAT(akk.created_dt, '%d/%m/%Y %H:%i:%s') as created_dt_name,
                        DATE_FORMAT(akk.updated_dt, '%d/%m/%Y %H:%i:%s') as updated_dt_name,
                        ask.nm_skl as sekolah
                        from app_prestasi_siswa akk
                        left join app_user muc on muc.id = akk.created_by
                        left join app_user muu on muu.id = akk.updated_by
                        left join app_tahun_ajaran ata on ata.id = akk.id_tahun
                        left join app_kelas kls on kls.id = akk.id_kelas
                        left join app_jurusan jr on jr.id = akk.id_jurusan
                        left join app_semester sm on sm.id = akk.id_semester
                        left join app_siswa sw on sw.id_user = akk.id_siswa
                        left join app_skl ask on ask.id = akk.id_skl
                        where akk.is_active != 9 and ask.is_active = 1
                    ) as mst
    ";
    public $where = [];
    public $add_params = [];

    public $select = '
    id, jurusan, nama_kelas, semester, nama_siswa, tanggal, tanggal_name, prestasi, tahun,
    is_active, is_active_name,created_dt,created_by_name,updated_dt,updated_by_name,
    sekolah,created_dt_name,updated_dt_name
    ';

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
        $builder = $this->db->update($this->table, ['is_active' => STATUS_IS_DELETE], [$this->id => $id]);

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

    public function readdetail($params=[])
    {

        $limit = " ";
        if(isset($params['page'])){
            $start = ($params['page'] - 1) * PAGING;
            // $end = $start + PAGING;
            $limit = " limit $start," . PAGING;
        }

        

        $condition = " where akk.is_active = 1";
        if(isset($params['id'])){
            $condition .= " and akk.id = ". $params['id'] ." ";
        }
        if(isset($params['id_siswa']) && $params['id_siswa'] !=0){
            $condition .= " and akk.id_siswa = '". $params['id_siswa'] ."' ";
        }
        if(isset($params['id_skl'])){
            $condition .= " and akk.id_skl = '". $params['id_skl'] ."' ";
        }

        $like = " ";
        if(isset($params['like']) && !empty($params['like'])){
            $condition .= " 
                        and (
                            ata.tahun like '%".$params['like']."%' 
                            or sw.nama_siswa like '%".$params['like']."%' 
                            or akk.prestasi like '%".$params['like']."%' 
                            or kls.nama_kelas like '%".$params['like']."%' 
                            or sm.semester like '%".$params['like']."%' 
                            or DATE_FORMAT(akk.tanggal, '%d/%m/%Y') like '%".$params['like']."%'
                        ) ";
        }


        $sql = "
            select
            akk.*,
            if(akk.is_active=1,'YES','NO') as is_active_name,
            kls.nama_kelas, jr.jurusan, sm.semester, sw.nama_siswa,
            ata.tahun,
            muc.name as created_by_name,
            muu.name as updated_by_name,
            DATE_FORMAT(akk.tanggal, '%d/%m/%Y') as tanggal_name,
            DATE_FORMAT(akk.created_dt, '%d/%m/%Y %H:%i:%s') as created_dt_name,
            DATE_FORMAT(akk.updated_dt, '%d/%m/%Y %H:%i:%s') as updated_dt_name,
            ask.nm_skl as sekolah
            from app_prestasi_siswa akk
            left join app_user muc on muc.id = akk.created_by
            left join app_user muu on muu.id = akk.updated_by
            left join app_tahun_ajaran ata on ata.id = akk.id_tahun
            left join app_kelas kls on kls.id = akk.id_kelas
            left join app_jurusan jr on jr.id = akk.id_jurusan
            left join app_semester sm on sm.id = akk.id_semester
            left join app_siswa sw on sw.id_user = akk.id_siswa
            left join app_skl ask on ask.id = akk.id_skl
            $condition
            order by akk.id desc
            $limit
        ";
        // echo '<pre>';
        // var_dump($sql); die;
        $read = findAll($sql);

        return $read;
    }
}
