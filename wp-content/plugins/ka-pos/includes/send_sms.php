<?php

require_once('vendor/autoload.php');
use Plivo\RestClient;

function send_single_sms_page() {
    
    load_assets_page_options();
    
    echo '<div class="wrap"><div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Gửi SMS tới SĐT bất kỳ
                        </div>
                        <div class="panel-body">';
    
    if (isset($_POST['sms-content'])) {
        
        try {
            
            $client = new RestClient("MAMDDLZJM4MZQ1N2IZMJ", "ODExNWNhMTU1MDYzNTdmMGQwYjk5OTEwODUwMDk0");

            $message_created = $client->messages->create(
                '+84965359181',
                array($_POST['phone-number']),
                $_POST['sms-content']
            );

        } catch (Exception $ex) {
            echo '<div class="alert alert-danger">
                            <strong> Có lỗi xảy ra: ' . $ex->getMessage() . '
                            </strong>
                </div>';
        } finally {
            echo '<div class="alert alert-success">
                            <strong> Đã gửi tin nhắn thành công!
                            </strong>
                </div>';
        }
        
    }

                            echo '<div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Nhập SĐT kèm mã quốc gia (+84)</label>
                                                <input class="form-control" type="number" id="phone-number" name="phone-number" value="84978126486" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Nội dung tin nhắn:</label>
                                                <input class="form-control" type="text" id="sms-content" name="sms-content" value="" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Gửi SMS</button>
                                            <button type="reset" class="btn btn-default">Nhập Lại</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>';
}