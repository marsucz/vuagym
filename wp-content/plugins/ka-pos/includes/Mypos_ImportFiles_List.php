<?php

/**
 * Description of KiotViet_ManualSyncWeb_List
 *
 * @author dmtuan
 */

require_once('DbModel.php');
require_once('kiotviet_api.php');

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Mypos_ImportFiles_List extends WP_List_Table {

    private $dbModel;
    private $show_products_per_page = 10;

    function __construct($show_products = 10) {
        $args = array();
        parent::__construct($args);
        $this->dbModel = new DbModel();
        $this->show_products_per_page = $show_products;
    }
    
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $perPage = $this->show_products_per_page;
        $currentPage = $this->get_pagenum();
        
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_dir = $upload_dir . '/import-files/';
        if (!is_dir($upload_dir)) {
            mkdir( $upload_dir, 0700 );
        }
        $file_list = array_diff(scandir($upload_dir), array('.', '..'));
        
        $totalItems = count($file_list);
        
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $offset = ($currentPage - 1) * $perPage;
        $this->items = array_slice($file_list, $offset, $perPage); 
    }

    public function single_row($item) {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    public function get_columns() {
        $columns = array(
            'filename' => 'Tên File',
            'options' => 'Chức Năng',
        );
        return $columns;
    }

    public function get_hidden_columns() {
        return array('id');
    }

    public function get_sortable_columns() {
        return array();
    }

    public function column_default($item, $column_name) {
        $r = '';

        switch ($column_name) {
            case 'filename':
                $r = $item;
                break;
            case 'options':
                $r.= '  <button type="button" class="btn btn-mypos btn-success" title="Lấy thông tin file" onclick="getImportFile(this,\''. $item .'\');"><i class="fa fa-tasks"></i>  Xem chi tiết</button>';
                $r.= '  <button type="button" class="btn btn-mypos btn-danger" title="Xóa File Này" onclick="deleteImportFile(this,\''. $item .'\');"><i class="fa fa-tasks"></i>  Xóa</button>';
                break;
            default:
                return print_r($item, true);
        }

        return $r;
    }

}
