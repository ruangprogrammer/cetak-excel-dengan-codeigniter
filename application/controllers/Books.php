<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Books extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Books_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        //echo "test"; exit();

        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'books/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'books/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'books/index.html';
            $config['first_url'] = base_url() . 'books/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Books_model->total_rows($q);
        $books = $this->Books_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'books_data' => $books,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('books/books_list', $data);
    }


    public function excel()
    {  //echo "test"; exit();
        $this->load->helper('exportexcel');
        $namaFile = "books.xls";
        $judul = "books";
        $tablehead = 0;
        $tablebody = 1;
        $nourut = 1;
        //penulisan header
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $namaFile . "");
        header("Content-Transfer-Encoding: binary ");

        xlsBOF();

        $kolomhead = 0;
        xlsWriteLabel($tablehead, $kolomhead++, "No");
        xlsWriteLabel($tablehead, $kolomhead++, "Name");
        xlsWriteLabel($tablehead, $kolomhead++, "Author");
        xlsWriteLabel($tablehead, $kolomhead++, "Isbn");

        foreach ($this->Books_model->get_all() as $data) {
            $kolombody = 0;

            //ubah xlsWriteLabel menjadi xlsWriteNumber untuk kolom numeric
            xlsWriteNumber($tablebody, $kolombody++, $nourut);
            xlsWriteLabel($tablebody, $kolombody++, $data->name);
            xlsWriteLabel($tablebody, $kolombody++, $data->author);
            xlsWriteLabel($tablebody, $kolombody++, $data->isbn);

            $tablebody++;
            $nourut++;
        }

        xlsEOF();
        exit();
    }

}
