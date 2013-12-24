<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of booking
 *
 * @author Syiewa
 */
Class Booking extends Frontend_Controller {

    var $template = 'web/template';

    public function __construct() {
        parent::__construct();
        $this->load->library('cart');
        $this->load->model('m_page');
        $this->load->model('m_promo');
        $this->load->model('m_room');
    }

    public function index() {
//        $this->session->sess_destroy();
        if ($this->cart->contents()){
            $this->cart->destroy();
        }
        if ($this->input->post('check')) {
            $to = $this->input->post('to');
            $from = $this->input->post('from');
            $tgl = array(
                'from' => $from,
                'to' => $to
            );
            $this->session->set_userdata($tgl);
//            $id = $this->input->post('idclass');
//            $rooms = $this->m_room->get_room($id);
//            if (!empty($rooms)) {
//                $data = array(
//                    'id' => $id,
//                    'qty' => $this->IntervalDays($from, $to),
//                    'price' => $rooms[0]->net,
//                    'name' => $rooms[0]->title,
//                    'from' => $from,
//                    'to' => $to
//                );
//                $this->cart->insert($data);
//            } else {
//                $this->data['keterangan'] = $this->m_kelas->get($id);
//                $this->data['keterangan']->to = $to;
//                $this->data['keterangan']->from = $from;
//            }
        }

        $this->data['rooms'] = $this->m_room->get_allroom();
        foreach ($this->data['rooms'] as $k => $v) {
            $gmbr = $this->m_kelas->get_gambardefault();
            foreach ($gmbr as $g => $s) {
                if ($this->data['rooms'][$k]->idclass == $gmbr[$g]->idclass) {
                    $this->data['rooms'][$k]->thumb = $gmbr[$g]->thumb;
                    $this->data['rooms'][$k]->image = $gmbr[$g]->image;
                }
            }
        }
        $this->data['content'] = 'web/booking/index';
        $this->load->view($this->template, $this->data);
    }

    public function order($id = null) {
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $tgl = array(
            'from' => $from,
            'to' => $to
        );
        $this->session->set_userdata($tgl);
        $id = $this->input->post('idclass');
        foreach ($id as $i) {
            if ($this->input->post('check' . $i)) {
                $id = $i;
            }
        }
        if ($from == '' AND $to == '') {
            $this->session->set_flashdata('error', 'Silahkan pilih tgl');
            redirect('booking');
        }
        $this->data['rooms'] = $this->m_room->get_room($id);
        if (!empty($this->data['rooms'])) {
            $data = array(
                'id' => $id,
                'qty' => $this->IntervalDays($from, $to) + 1,
                'price' => $this->data['rooms'][0]->net,
                'name' => $this->data['rooms'][0]->title,
            );
            $this->cart->insert($data);
        }
        $this->data['content'] = 'web/booking/booking';
        $this->load->view($this->template, $this->data);
    }

    function delete($rowId) {
        $data = array('rowid' => $rowId, 'qty' => 0);

        $this->cart->update($data);
        $this->session->set_flashdata('success', 'Item deleted');
    }

    function update() {
        $data = $this->input->post('approve');
        if ($data) {
            for ($i = 0; $i <= sizeof($data); $i++) {
                $this->delete($data[$i]);
            }
            redirect('booking/index');
        } else {
            $this->cart->update($_POST);
            $this->session->set_flashdata('success', 'Shopping cart updated');
            redirect('booking/index');
        }
    }

    private function IntervalDays($CheckIn, $CheckOut) {
        $CheckInX = explode("/", $CheckIn);
        $CheckOutX = explode("/", $CheckOut);
        $date1 = mktime(0, 0, 0, $CheckInX[0], $CheckInX[1], $CheckInX[2]);
        $date2 = mktime(0, 0, 0, $CheckOutX[0], $CheckOutX[1], $CheckOutX[2]);
        $interval = ($date2 - $date1) / (3600 * 24);

// returns numberofdays
        return $interval;
    }

}

?>
