<?php

class Dashboard_admin extends CI_Controller
{

    // sintax  ini digunakan untuk memblokir Akses yg akan masuk ke web tanpa login(kick Akses yg mencoba nakal!!)
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('id_role') != '2') {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					  <strong>Anda Belum Login, Silahkan Login Terlebih dahulukkk!!!.
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					    <span aria-hidden="true">&times;</span>
					  </button>
					</div>');
            redirect('auth/login');
        }
    }


    public function index()
    {
        $data['tbl_user'] = $this->db->get_where('tbl_user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $data['admin'] = $this->Model_admin->tampil_data()->result();
        $this->load->view('templates_a/Header', $data);
        $this->load->view('templates_a/Sidebar', $data);
        // $this->load->view('templates_a/Topbar', $data);
        $this->load->view('admin/Dashboard_admin', $data);
        $this->load->view('templates_a/Footer');
    }

    public function data_donatur()
    {
        $data['title'] = 'Data Donatur';
        $data['tbl_user'] = $this->db->get_where('tbl_user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $data['donatur'] = $this->Model_donatur->tampil_data()->result();
        $this->load->view('templates_a/Header', $data);
        $this->load->view('templates_a/Sidebar', $data);
        // $this->load->view('templates_a/Topbar', $data);
        $this->load->view('admin/D_donatur', $data);
        $this->load->view('templates_a/Footer');
    }

    public function tambah_donatur()
    {
        $nama       = $this->input->post('nama');
        $alamat     = $this->input->post('alamat');
        $no_wa      = $this->input->post('no_wa');
        $email      = $this->input->post('email');
        $password   = $this->input->post('password');
        $gambar     =  $_FILES['gambar']['name'];

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './uploads';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('gambar')) {
                echo "Gambar Gagal Diupload!";
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }

        $this->load->library('ciqrcode'); //pemanggilan library QR CODE

        $config['cacheable']    = true; //boolean, the default is true
        $config['cachedir']     = './assets/'; //string, the default is application/cache/
        $config['errorlog']     = './assets/'; //string, the default is application/logs/
        $config['imagedir']     = './assets/images/'; //direktori penyimpanan qr code
        $config['quality']      = true; //boolean, the default is true
        $config['size']         = '1024'; //interger, the default is 1024
        $config['black']        = array(224, 255, 255); // array, default is array(255,255,255)
        $config['white']        = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);

        $image_name = $email . '.png'; //buat name dari qr code sesuai dengan nim

        $params['data'] = $email; //data yang akan di jadikan QR CODE
        $params['level'] = 'H'; //H=High
        $params['size'] = 10;
        $params['savename'] = FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
        $this->ciqrcode->generate($params);

        $data_user = array(
            'nama'       => $nama,
            'email'      => $email,
            'password'   => $password,
            'id_role'      => 3,
            'is_active' => 1,
            'date_created' => date("Y-m-d"),
            'gambar'       => $gambar,
        );

        $this->db->insert('tbl_user', $data_user);

        $user = $this->Model_admin->getuser()->result();
        $id_user = $user[0]->id;

        $data = array(
            'nama'       => $nama,
            'alamat'     => $alamat,
            'no_wa'      => $no_wa,
            'email'      => $email,
            'password'   => $password,
            'gambar'     => $gambar,
            'qr_code'    => $image_name,
            'id_user'    => $id_user,
        );

        $this->db->insert('tbl_donatur', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Tambah Customer Success!!</div>');
        redirect('operator/Dashboard_admin/data_donatur');
    }


    public function detail_donatur($id)
    {
        $data['title'] = 'Detail Donatur';
        $data['tbl_user'] = $this->db->get_where('tbl_user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $where = array('id_dnt' => $id);
        $data['donatur'] = $this->Model_donatur->detail_donatur($id);
        $this->load->view('templates_a/Header', $data);
        $this->load->view('templates_a/Sidebar', $data);
        // $this->load->view('templates_a/Topbar', $data);
        $this->load->view('admin/Detail_donatur', $data);
        $this->load->view('templates_a/Footer');
    }

    public function edit_donatur($id)
    {
        $data['title'] = 'Detail Donatur';
        $data['tbl_user'] = $this->db->get_where('tbl_user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $where = $id;
        $data['donatur'] = $this->Model_donatur->edit_donatur($where);

        $this->load->view('templates_a/Header', $data);
        $this->load->view('templates_a/Sidebar', $data);
        // $this->load->view('templates_a/Topbar', $data);
        $this->load->view('admin/Edit_donatur', $data);
        $this->load->view('templates_a/Footer');
    }

    public function update_donator()
    {
        $id            = $this->input->post('id_dnt');
        $nama          = $this->input->post('nama');
        $email         = $this->input->post('email');
        $alamat        = $this->input->post('alamat');
        $no_wa         = $this->input->post('no_wa');
        $password      = $this->input->post('password');
        $id_user       = $this->input->post('id_user');

        $gambar        =  $_FILES['gambar']['name'];
        if ($gambar = '') {
        } else {
            $config['upload_path']   = './uploads';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('gambar')) {
                echo "Logo Gagal Diupload!";
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }

        $data_user = array(
            'nama'       => $nama,
            'email'      => $email,
            'password'   => $password,
            'gambar'       => $gambar,
        );


        $data = array(
            'nama'         => $nama,
            'alamat'       => $alamat,
            'no_wa'        => $no_wa,
            'password'     => $password,
            'gambar'       => $gambar,
            'email'        => $email
        );

        $this->db->where('id', $id_user);
        $this->db->update('tbl_user', $data_user);

        $this->db->where('id_dnt', $id);

        if ($this->db->update('tbl_donatur', $data)) {
            // if ($this->Model_donatur->update_donatur($where, $data, 'tbl_donatur')) {
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Update Donatur Success!!</div>');
            redirect('operator/Dashboard_admin/data_donatur');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Update Donatur Gagal!!</div>');
            redirect('operator/Dashboard_admin/data_donatur');
        }
    }

    public function hapus_donatur($id)
    {
        $where = array('id_dnt' => $id);
        $this->Model_donatur->hapus_donatur($where, 'tbl_donatur');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Delete Donatur Success!!</div>');
        redirect('operator/Dashboard_admin/data_donatur');
    }



    public function data_admin()
    {
        $data['title'] = 'Data Admin';
        $data['tbl_user'] = $this->db->get_where('tbl_user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $data['admin'] = $this->Model_admin->tampil_data()->result();
        $this->load->view('templates_a/Header', $data);
        $this->load->view('templates_a/Sidebar', $data);
        // $this->load->view('templates_a/Topbar', $data);
        $this->load->view('admin/D_admin', $data);
        $this->load->view('templates_a/Footer');
    }

    public function tambah_admin()
    {
        $nama       = $this->input->post('nama');
        $alamat     = $this->input->post('alamat');
        $no_wa      = $this->input->post('no_wa');
        $email      = $this->input->post('email');
        $password   = $this->input->post('password');
        $gambar     =  $_FILES['gambar']['name'];

        if ($gambar = '') {
        } else {
            $config['upload_path'] = './uploads';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('gambar')) {
                echo "Gambar Gagal Diupload!";
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }

        $data = array(
            'nama'       => $nama,
            'alamat'     => $alamat,
            'no_wa'      => $no_wa,
            'email'      => $email,
            'password'   => $password,
            'gambar'     => $gambar,
        );

        $this->Model_admin->tambah_admin($data, 'tbl_admin');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Tambah Customer Success!!</div>');
        redirect('operator/Dashboard_admin/data_admin');
    }


    public function detail_admin($id)
    {
        $data['title'] = 'Detail Admin';
        $data['tbl_user'] = $this->db->get_where('tbl_user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $where = array('id_adm' => $id);
        $data['admin'] = $this->Model_admin->detail_admin($id);
        $this->load->view('templates_a/Header', $data);
        $this->load->view('templates_a/Sidebar', $data);
        // $this->load->view('templates_a/Topbar', $data);
        $this->load->view('admin/Detail_admin', $data);
        $this->load->view('templates_a/Footer');
    }

    public function edit_admin($id)
    {
        $data['title'] = 'Edit admin';
        $data['tbl_user'] = $this->db->get_where('tbl_user', ['email' =>
        $this->session->userdata('email')])->row_array();

        $where = array('id_adm' => $id);
        $data['admin'] = $this->Model_admin->edit_admin($where, 'tbl_admin')->result();
        $this->load->view('templates_a/Header', $data);
        $this->load->view('templates_a/Sidebar', $data);
        // $this->load->view('templates_a/Topbar', $data);
        $this->load->view('admin/Edit_admin', $data);
        $this->load->view('templates_a/Footer');
    }

    public function update_admin()
    {
        $id            = $this->input->post('id_adm');
        $nama          = $this->input->post('nama');
        $email         = $this->input->post('email');
        $alamat        = $this->input->post('alamat');
        $no_wa         = $this->input->post('no_wa');
        $password      = $this->input->post('password');

        $gambar        =  $_FILES['gambar']['name'];
        if ($gambar = '') {
        } else {
            $config['upload_path']   = './uploads';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('gambar')) {
                echo "Logo Gagal Diupload!";
            } else {
                $gambar = $this->upload->data('file_name');
            }
        }


        $data = array(
            'nama'         => $nama,
            'alamat'       => $alamat,
            'no_wa'        => $no_wa,
            'password'     => $password,
            'gambar'       => $gambar,
            'email'        => $email
        );

        $where = array(
            'id_adm' => $id
        );

        if ($this->Model_admin->update_admin($where, $data, 'tbl_admin')) {
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Update Donatur Success!!</div>');
            redirect('operator/Dashboard_admin/data_admin');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Update Donatur Gagal!!</div>');
            redirect('operator/Dashboard_admin/data_admin');
        }
    }

    public function hapus_admin($id)
    {
        $where = array('id_adm' => $id);
        $this->Model_admin->hapus_admin($where, 'tbl_admin');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Delete Donatur Success!!</div>');
        redirect('operator/Dashboard_admin/data_admin');
    }
}
