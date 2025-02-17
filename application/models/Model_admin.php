<?php

class Model_admin extends CI_Model
{
    public function tampil_data()
    {
        return $this->db->get('tbl_admin');
    }

    public function getuser()
    {
        $this->db->select('*');

        $this->db->from('tbl_user');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query;
    }


    public function tambah_admin($data, $table)
    {
        $this->db->insert($table, $data);
    }

    public function detail_admin($id)
    {
        $result = $this->db->where('id_adm', $id)->get('tbl_admin');
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return false;
        }
    }

    public function edit_admin($where, $table)
    {
        // $this->db->from('tbl_donatur');
        // $this->db->join('tbl_user', 'tbl_donatur.id_user = tbl_user.id', 'left');
        // $this->db->where('tbl_donatur.id', $where);
        // return $this->db->get()->result();

        return $this->db->get_where($table, $where);
    }

    public function update_admin($where, $data, $table)
    {
        $this->db->where($where);
        $this->db->update($table, $data);
        return $this->db->affected_rows();
    }

    public function hapus_admin($where, $table)
    {
        $this->db->where($where);
        $this->db->delete($table);
    }
}
