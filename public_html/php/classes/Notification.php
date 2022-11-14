<?php

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
require_once dirname(__FILE__) . "/DbConnection.php";
require_once dirname(__FILE__) . "/Email.php";

class Notification extends DbConnection
{
    public $app_id = "1497997";
    public $app_key = "56c2bebb33825a275ca8";
    public $app_secret = "293189e5cb681e1aea78";
    public $app_cluster = "ap1";

    public function pusher() {
        return new Pusher\Pusher($this->app_key, $this->app_secret, $this->app_id, array('cluster' => $this->app_cluster));
    }
    /* invoke when the staff deleted the order of a customer, or when the order is claimed */
    public function order_notif($user_id, $message)
    {
        $data['notification'] = array(
            'user_id' => $user_id,
            'message' => $message
        );
        $this->pusher()->trigger('snackwise', 'notif', $data);
    }

    /* updates the order table when a customer deletes an order */
    public function delete_order_notif()
    {
        $data['notification'] = array(
            'notif' => 'notif',
        );
        $this->pusher()->trigger('snackwise', 'notif', $data);
    }

   /*  display all the notifications intended for the customer */
    public function display_notification($user_id)
    {
        $result = $query = $this->connect()->prepare("SELECT notification_id, user_id, message, status FROM notification WHERE user_id =:user_id ORDER BY notification_id DESC");
        $query->execute([":user_id" => $user_id]);
        if ($result) {
            $data = array();
            foreach ($result as $row) {
                $sub_array = array();
                $sub_array['notification_id'] = $row['notification_id'];
                $sub_array['user_id'] = $row['user_id'];
                $sub_array['message'] = $row['message'];
                $sub_array['status'] = $row['status'];
                $data[] = $sub_array;
            }
            $output = array("data"=>$data);
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }

    /*  count all the unread notification of a customer */
    public function notification_count($user_id){
        $status = 'unread';
        $query = $this->connect()->prepare("SELECT * FROM notification WHERE user_id = :user_id AND status = :status");
        $query->execute([":user_id" => $user_id, ":status" => $status]);
        $output['notification_count'] = $query->rowCount();
        echo json_encode($output);
    }

    /* changes the status of notification from unread to read  */
    public function update_notification($user_id){
        $status = 'read';
        $query  = $this->connect()->prepare("UPDATE notification SET status = :status WHERE user_id = :user_id");
        $query->execute([':status' => $status, ':user_id' => $user_id]);
        echo json_encode("");
    }


    /* invoked when a customer submits a message from contact us  */
    public function send_email_message($name, $email,$subject,$message){
        $email_verification = new Email();
       if ($email_verification->sendEmail("Customer",$email, $subject, $message, "contact")) {
        $output['success'] = 'Message sent successfully';
    } else {
        $output['error'] = 'Something went wrong! Please try again later.';
    }
       echo json_encode($output);
    }
}
