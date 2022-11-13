<?php
/* session_start(); */

require_once dirname(__FILE__) . "/DbConnection.php";
require_once dirname(__FILE__) . "/Image.php";
require_once dirname(__FILE__) . "/../../vendor/phpqrcode/qrlib.php";
require_once dirname(__FILE__) . '/../classes/Validate.php';
require_once dirname(__FILE__) . '/../classes/Notification.php';


class Order extends DbConnection
{
    /* generates qr verification code */
    public function generate_qr_code()
    {
        $qr_code = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1, 6))), 1, 16);
        /* check if verification code already exist, if true it will regenerate again */
        $query = $this->connect()->prepare("SELECT * FROM orders WHERE qr_code = :qr_code");
        $query->execute([':qr_code' => $qr_code]);

        if ($query->rowCount() > 0) {
            $qr_code = $this->generate_qr_code();
        } else {
            return $qr_code;
        }
    }

    /* -------------------- */
    /* invoked when the items on the cart have been ordered */
    public function add_order($user_id, $cartlist, $date, $time)
    {
        $id = 0;
        $query = $this->connect()->prepare("SELECT order_id FROM orders ORDER BY order_id DESC LIMIT 1");
        $result = $query->execute();
        if ($query->rowCount() > 0) {
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            $fetch_order_id = $fetch['order_id'];
            $id = $fetch_order_id + 1;
        } else {
            $id = 1;
        }

        $status = "Placed";
        $qr_code = $this->generate_qr_code();
        
        /* generates a QR code image and placed it in a temporary folder,
        once it has been uploaded to cloud storage, the temporary file will be deleted */
        $PNG_TEMP_DIR = '../../img/temp/';
        $filename = $PNG_TEMP_DIR . $qr_code . '.png';
        QRcode::png($qr_code, $filename);
        $qr_directory =  $PNG_TEMP_DIR . $filename;
        $upload_image = new Image();
        $data = $upload_image->upload_image($qr_directory, $qr_code, "SnackWise/QR/");
        $image_link = "v" . $data['version'] . "/" . $data['public_id'];
        unlink($PNG_TEMP_DIR . $qr_code . '.png');

        $query = $this->connect()->prepare("INSERT INTO orders (order_id, user_id, date, time, qr_code, qr_image, status) VALUES( :order_id, :user_id, :date, :time, :qr_code, :qr_image, :status)");
        $result = $query->execute([":order_id" => $id, ":user_id" => $user_id, ":date" => $date, ":time" => $time, ":qr_code" => $qr_code, ":qr_image" => $image_link, ":status" => $status]);
        if ($result) {
            $cart_id = explode(',', $cartlist);
            for ($i = 0; $i < count($cart_id); $i++) {
                $query = $this->connect()->prepare("SELECT menu_id, quantity FROM cart where cart_id = :cart_id");
                $result = $query->execute([":cart_id" => $cart_id[$i]]);
                if ($query->rowCount() > 0) {
                    $fetch = $query->fetch(PDO::FETCH_ASSOC);
                    $fetch_menu_id = $fetch['menu_id'];
                    $fetch_quantity = $fetch['quantity'];

                    $query = $this->connect()->prepare("INSERT INTO orderlist (order_id, menu_id, quantity) VALUES( :order_id, :menu_id, :quantity)");
                    $result = $query->execute([":order_id" => $id, ":menu_id" => $fetch_menu_id, ":quantity" => $fetch_quantity]);
                }
            }
            $output['success'] = 'Order Successfully Placed';
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }

/* invoke when a QR Image is scanned,
it checks if the code is associated with an order, if true it will display the order */
    public function qr_fetch_info($qr_code_id)
    {
        $result=$query = $this->connect()->prepare("SELECT o.user_id,o.qr_image, u.firstname, u.lastname,CONCAT(u.firstname,' ', u.lastname) AS name,CONCAT(u.street,' ', u.barangay, ' ', u.municipality, ' ', u.province) AS address, o.order_id, o.date, o.time, o.qr_code,o.status,GROUP_CONCAT(m.menu_id SEPARATOR ',') AS menu_id_list, GROUP_CONCAT(m.name SEPARATOR ',') AS menu_name_list, GROUP_CONCAT(l.quantity SEPARATOR ',') AS quantity_list, GROUP_CONCAT(m.category SEPARATOR ',') AS category_list, GROUP_CONCAT(m.price SEPARATOR ',') AS price_list,GROUP_CONCAT(m.discount SEPARATOR ',') AS discount_list,GROUP_CONCAT(m.image SEPARATOR ',') AS image_list FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id  INNER JOIN menu m ON l.menu_id = m.menu_id WHERE qr_code = :qr_code_id GROUP BY l.order_id");
       $query->execute(["qr_code_id" => $qr_code_id]);
        if ($query->rowCount() > 0) {
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            $fetch_status = $fetch['status'];
            /* determines if an order is ready to be claimed or not */
            if ($fetch_status != "Pick-Up") {
                $data = array();

                    $sub_array = array();
                    $sub_array['user_id'] = $fetch['user_id'];
                    $sub_array['firstname'] = $fetch['firstname'];
                    $sub_array['lastname'] = $fetch['lastname'];
                    $sub_array['order_id'] = $fetch['order_id'];
                    $sub_array['date'] = $fetch['date'];
                    $sub_array['time'] = $fetch['time'];
                    $sub_array['qr_image'] = $fetch['qr_image'];
                    $sub_array['status'] = $fetch['status'];
                    $sub_array['menu_id_list'] = $fetch['menu_id_list'];
                    $sub_array['menu_name_list'] = $fetch['menu_name_list'];
                    $sub_array['discount_list'] = $fetch['discount_list'];
                    $sub_array['quantity_list'] = $fetch['quantity_list'];
                    $sub_array['category_list'] = $fetch['category_list'];
                    $sub_array['price_list'] = $fetch['price_list'];
                    $sub_array['image_list'] = $fetch['image_list'];
    
                    $data[] = $sub_array;
                
                $output = array("data"=>$data);
            } else {
                $output['error'] = 'Order is not ready yet';
            }
        } else {
            $output['error'] = 'Could not find order';
        }
        echo json_encode($output);
    }

/* -------------------- */
/* gets and display all the ordered items of the logged-in customer */
    public function display_order($user_id)
    {
        $result=$query = $this->connect()->prepare("SELECT o.user_id, u.firstname, u.lastname, o.order_id, o.date, o.time, o.qr_image,o.status,m.menu_id AS menu_id_list, m.name AS menu_name_list , l.quantity AS quantity_list, m.category AS category_list, m.price AS price_list, m.discount AS discount_list, m.image AS image_list FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id INNER JOIN menu m ON l.menu_id = m.menu_id WHERE u.user_id = :user_id");
         $query->execute(["user_id" => $user_id]);
        if ($query->rowCount() > 0) {
            $fetch = $query->fetch(PDO::FETCH_ASSOC);
            $fetch_status = $fetch['status'];
            if ($fetch_status != "Pick-Up") {
                $data = array();
                foreach ($result as $row) {
                    $sub_array = array();
                    $sub_array['user_id'] =    $row['user_id'];
                    $sub_array['firstname'] =     $row['firstname'];
                    $sub_array['lastname'] = $row['lastname'];
                    $sub_array['order_id'] = $row['order_id'];
                    $sub_array['date'] = $row['date'];
                    $sub_array['time'] = $row['time'];
                    $sub_array['qr_image'] = $row['qr_image'];
                    $sub_array['status'] = $row['status'];
                    $sub_array['menu_id_list'] = $row['menu_id_list'];
                    $sub_array['menu_name_list'] = $row['menu_name_list'];
                    $sub_array['quantity_list'] = $row['quantity_list'];
                    $sub_array['category_list'] = $row['category_list'];
                    $sub_array['price_list'] = $row['price_list'];
                    $sub_array['image_list'] = $row['image_list'];
                    $sub_array['total_discounted_price'] = ($row['price_list'] - ($row['price_list'] * (floatval($row['discount_list']) / 100))) * $row['quantity_list'];
                    $data[] = $sub_array;
                }
                $output = array("data"=>$data);
            } else {
                $output['error'] = 'Order is not ready yet';
            }
        } else {
            $output['error'] = 'Could not find order';
        }
        echo json_encode($output);
    }
/* gets and display all the claimed ordered items of the logged-in customer */
    public function display_completed_order($user_id)
    {
        $result=$query = $this->connect()->prepare("SELECT o.user_id, u.firstname, u.lastname, o.order_id, o.date,m.menu_id AS menu_id_list, m.name AS menu_name_list , l.quantity AS quantity_list, m.category AS category_list, m.price AS price_list, m.discount AS discount_list, m.image AS image_list FROM user u INNER JOIN transaction o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id INNER JOIN menu m ON l.menu_id = m.menu_id WHERE u.user_id = :user_id");
         $query->execute(["user_id" => $user_id]);
        if ($query->rowCount() > 0) {
                $data = array();
                foreach ($result as $row) {
                    $sub_array = array();
                    $sub_array['user_id'] =    $row['user_id'];
                    $sub_array['firstname'] =     $row['firstname'];
                    $sub_array['lastname'] = $row['lastname'];
                    $sub_array['order_id'] = $row['order_id'];
                    $sub_array['date'] = $row['date'];
                   
                    $sub_array['menu_id_list'] = $row['menu_id_list'];
                    $sub_array['menu_name_list'] = $row['menu_name_list'];
                    $sub_array['quantity_list'] = $row['quantity_list'];
                    $sub_array['category_list'] = $row['category_list'];
                    $sub_array['price_list'] = $row['price_list'];
                    $sub_array['image_list'] = $row['image_list'];
                    $sub_array['total_discounted_price'] = ($row['price_list'] - ($row['price_list'] * (floatval($row['discount_list']) / 100))) * $row['quantity_list'];
                    $data[] = $sub_array;
                }
                $output = array("data"=>$data);
        } else {
            $output['error'] = 'Could not find order';
        }
        echo json_encode($output);
    }

    /* invoked when the customer canceled its order, only items with 'placed' status can be canceled */
    public function delete_order($order_id)
    {
        $status = "placed";
        $query = $this->connect()->prepare("DELETE FROM orders WHERE order_id = :order_id AND status = :status");
        $result = $query->execute([":order_id" => $order_id,":status"=> $status]);
        if ($result) {
            $notification = new Notification();
            $notification->delete_order_notif();
            $output['success'] = 'Item Deleted Successfully';
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }



    /*  transfer claimed order from the orders table to the transaction table */
    public function claim_order($order_id, $user_id, $qr_code_id)
    {
        try {

            if($qr_code_id == "") {
            $query = $this->connect()->prepare("SELECT m.price AS price, m.discount AS discount, l.quantity AS quantity, u.user_id, o.order_id, CONCAT(u.firstname,' ', u.lastname) AS customer_name, GROUP_CONCAT(m.name SEPARATOR ',') AS menu_name, GROUP_CONCAT(m.price SEPARATOR ',') AS price_list, GROUP_CONCAT(l.quantity SEPARATOR ',') AS quantity_list, o.date, o.time, o.status FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id  INNER JOIN menu m ON l.menu_id = m.menu_id WHERE o.order_id = :order_id  GROUP BY l.order_id ORDER BY order_id DESC 
            ");
         /*    $query = $this->connect()->prepare("SELECT order_id, user_id FROM orders WHERE order_id = :order_id "); */
            $result = $query->execute(["order_id" => $order_id]);
            } else if($order_id == "") {
                /* $query = $this->connect()->prepare("SELECT order_id, user_id FROM orders WHERE qr_code = :qr_code_id "); */
                $query = $this->connect()->prepare("SELECT m.price AS price, m.discount AS discount, l.quantity AS quantity, u.user_id, o.order_id, CONCAT(u.firstname,' ', u.lastname) AS customer_name, GROUP_CONCAT(m.name SEPARATOR ',') AS menu_name, GROUP_CONCAT(m.price SEPARATOR ',') AS price_list, GROUP_CONCAT(l.quantity SEPARATOR ',') AS quantity_list, o.date, o.time, o.status FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id  INNER JOIN menu m ON l.menu_id = m.menu_id WHERE o.qr_code = :qr_code_id  GROUP BY l.order_id ORDER BY order_id DESC 
               ");




                $result = $query->execute(["qr_code_id" => $qr_code_id]);
            }
            if ($query->rowCount() > 0) {
                $fetch = $query->fetch(PDO::FETCH_ASSOC);
                $price=0;
                $price = ($fetch['price'] - ($fetch['price'] * (floatval($fetch['discount']) / 100))) * $fetch['quantity'];
                $date = date('Y-m-d H:i:s');

                $query = $this->connect()->prepare("INSERT INTO transaction (order_id, user_id, date, price) VALUES( :order_id, :user_id, :date, :price)");
                $result = $query->execute([":order_id" => $order_id, ":user_id" => $user_id, ":date" => $date, ":price" => $price]);
                $output['success'] = 'Order has been claimed';

                 $query = $this->connect()->prepare("DELETE FROM orders where order_id = :order_id");
                     $result = $query->execute([":order_id" => $order_id]);

                     $status = "unread";
                     $message = "Your order has been claimed";
         
                     $query = $this->connect()->prepare("INSERT INTO notification ( user_id, message, status) VALUES( :user_id, :message, :status)");
                     $result = $query->execute([":user_id" => $user_id, ":message" => $message, ":status" => $status]);
         
                     $notification = new Notification();
                     $notification->order_notif($user_id, $message);
         
            } else {
                $output['error'] = 'Something went wrong! Please try again later.';
            }
        } catch (Exception $e) {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }















/* --------------------admin */
 
public function admin_edit_order($order_id,  $date, $time, $status)
{
    if ($_POST['action_order'] == 'Update') {
        $query = $this->connect()->prepare("UPDATE orders SET date = :date, time = :time, status = :status WHERE order_id = :order_id");
        $result = $query->execute([':date' => $date,':time' => $time,':status' => $status,':order_id' => $order_id]);
        if ($result) {
            $output['success'] = 'Item Updated Successfully';
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }
}

public function admin_delete_order($order_id, $user_id, $del_notif)
{

        $query =$this->connect()->prepare("DELETE FROM orders WHERE order_id = :order_id");
        $result = $query->execute([":order_id" => $order_id]);
        if ($result) {
            $output['success'] = 'Order Deleted';
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }

        echo json_encode($output);
        

        $status = "unread";
        /*  $user_id = 1; */
        $query = $this->connect()->prepare("INSERT INTO notification ( user_id, message, status) VALUES( :user_id, :message, :status)");
        $result = $query->execute([":user_id" => $user_id, ":message" => $del_notif, ":status" => $status]);
    }

    public function fetch()
    {
        $order_id = $_POST["id"];
        $result = $query = $this->connect()->prepare("SELECT o.order_id, CONCAT(u.firstname,' ', u.lastname) AS customer_name, GROUP_CONCAT(m.name SEPARATOR ',') AS menu_name, GROUP_CONCAT(m.price SEPARATOR ',') AS price_list, GROUP_CONCAT(l.quantity SEPARATOR ',') AS quantity_list, o.date, o.time, o.status FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id  INNER JOIN menu m ON l.menu_id = m.menu_id WHERE o.order_id = :order_id GROUP BY l.order_id");
        $query->execute(["order_id" => $order_id]);

        $data = array();

        foreach ($result as $row) {


            $data['order_id'] = $row['order_id'];
            $data['customer_name'] = $row['customer_name'];
            $data['menu_name'] = $row['menu_name'];
            $data['price'] = $row['price_list'];
            $data['quantity'] = $row['quantity_list'];
            $data['total_price'] = "";
            $data['date'] = $row['date'];
            $data['time'] = $row['time'];
            $data['status'] = $row['status'];
        }

        echo json_encode($data);
    }


    public function fetch_top_five_data()
    {
        /* $query = "
	SELECT * FROM menu 
	ORDER BY menu_id DESC 
	LIMIT 5"; */

    $result=$query = $this->connect()->prepare("SELECT m.price AS price, m.discount AS discount, l.quantity AS quantity, u.user_id, o.order_id, CONCAT(u.firstname,' ', u.lastname) AS customer_name, GROUP_CONCAT(m.name SEPARATOR ',') AS menu_name, GROUP_CONCAT(m.price SEPARATOR ',') AS price_list, GROUP_CONCAT(l.quantity SEPARATOR ',') AS quantity_list, o.date, o.time, o.status FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id  INNER JOIN menu m ON l.menu_id = m.menu_id GROUP BY l.order_id ORDER BY date ASC 
        LIMIT 5");
         $query->execute();
$total_price = 0;
        $output = '';
        foreach ($result as $row) {
            $total_price += ($row['price'] - ($row['price'] * (floatval($row['discount']) / 100))) * $row['quantity'];
            $output .= '
           
		<tr>

		<td>' . $row["order_id"] . '</td>
		<td>' . $row["customer_name"] . '</td>
		<td>' . $row["menu_name"] . '</td>
		<td>' . $row["price_list"] . '</td>
		<td>' . $row["quantity_list"] . '</td>
		<td>' . $total_price  . '</td>
		<td>' . $row["date"] . '</td>
		<td>' . $row["time"] . '</td>
		<td>' . $row["status"] . '</td>
	
		
			<td><button type="button" onclick="new Order().fetch_data(' . $row["order_id"] . ')" class="btn btn-edit"><i class="fa-solid fa-pen"></i></button>&nbsp;
            <button type="button" onclick="new Order().del_notif(' . $row["order_id"] . ', ' . $row["user_id"] . ')" class="btn btn-delete"><i class="fa-solid fa-trash"></i></button>&nbsp;
            <button type="button" onclick="new Order().claim_order(' . $row["order_id"] . ', ' . $row["user_id"] . ')" class="btn btn-claim">Claim</button></td>
		</tr>
		';
        }
        return $output;
    }

    public function count_all_data()
    {
        $query = "SELECT o.order_id, CONCAT(u.firstname,' ', u.lastname) AS customer_name, GROUP_CONCAT(m.name SEPARATOR ',') AS menu_name, GROUP_CONCAT(m.price SEPARATOR ',') AS price_list, GROUP_CONCAT(l.quantity SEPARATOR ',') AS quantity_list, o.date, o.time, o.status FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id  INNER JOIN menu m ON l.menu_id = m.menu_id GROUP BY l.order_id";

        $query = $this->connect()->prepare($query);

        $query->execute();

        return $query->rowCount();
    }

    public function filter()
    {
        $startGET = filter_input(INPUT_GET, "start", FILTER_SANITIZE_NUMBER_INT);
        $start = $startGET ? intval($startGET) : 0;
        $lengthGET = filter_input(INPUT_GET, "length", FILTER_SANITIZE_NUMBER_INT);
        $length = $lengthGET ? intval($lengthGET) : 10;
        $searchQuery = filter_input(INPUT_GET, "searchQuery", FILTER_SANITIZE_STRING);
        $search = empty($searchQuery) || $searchQuery === "null" ? "" : $searchQuery;
        $sortColumnIndex = filter_input(INPUT_GET, "sortColumn", FILTER_SANITIZE_NUMBER_INT);
        $sortDirection = filter_input(INPUT_GET, "sortDirection", FILTER_SANITIZE_STRING);

        $column = array("o.order_id", "u.firstname", "u.lastname", "o.date", "o.time", "o.status");
        $sql = "SELECT m.price AS price, m.discount AS discount, l.quantity AS quantity, u.user_id, o.order_id, CONCAT(u.firstname,' ', u.lastname) AS customer_name, GROUP_CONCAT(m.name SEPARATOR ',') AS menu_name, GROUP_CONCAT(m.price SEPARATOR ',') AS price_list, GROUP_CONCAT(l.quantity SEPARATOR ',') AS quantity_list, o.date, o.time, o.status FROM user u INNER JOIN orders o ON u.user_id = o.user_id INNER JOIN orderlist l ON o.order_id = l.order_id  INNER JOIN menu m ON l.menu_id = m.menu_id";

        $sql .= '
        WHERE o.order_id LIKE "%' . $search . '%" 
        OR u.firstname LIKE "%' . $search . '%" 
        OR u.lastname LIKE "%' . $search . '%" 
        OR o.date LIKE "%' . $search . '%" 
        OR o.time LIKE "%' . $search . '%" 
        OR o.status LIKE "%' . $search . '%" 
            ';
        $sql .= ' GROUP BY l.order_id ';

        if ($sortColumnIndex != '') {
            $sql .= 'ORDER BY ' . $column[$sortColumnIndex] . ' ' . $sortDirection . ' ';
        } else {
            $sql .= 'ORDER BY date ASC ';
        }
        $sql1 = '';
        if ($length != -1) {
            $sql1 = 'LIMIT ' . $start . ', ' . $length;
        }
        $total_price = 0;
        $query = $this->connect()->prepare($sql);
        $query->execute();
        $number_filter_row = $query->rowCount();
        $result = $this->connect()->prepare($sql . $sql1);
        $result->execute();
        $data = array();
        foreach ($result as $row) {
            $total_price += ($row['price'] - ($row['price'] * (floatval($row['discount']) / 100))) * $row['quantity'];
            $sub_array = array();
            $sub_array[] = $row['order_id'];
            $sub_array[] = $row['customer_name'];
            $sub_array[] = $row['menu_name'];
            $sub_array[] = $row['price_list'];
            $sub_array[] = $row['quantity_list'];
            $sub_array[] = $total_price;
            $sub_array[] = $row['date'];
            $sub_array[] = $row['time'];
            $sub_array[] = $row['status'];
            $sub_array[] = '<button type="button" onclick="new Order().fetch_data(' . $row["order_id"] . ')" class="btn btn-edit"><i class="fa-solid fa-pen"></i></button>&nbsp;
            <button type="button" class="" onclick="new Order().del_notif(' . $row["order_id"] . ', ' . $row["user_id"] . ')" class="btn btn-delete"><i class="fa-solid fa-trash"></i></button>&nbsp;
            <button type="button" onclick="new Order().claim_order(' . $row["order_id"] . ', ' . $row["user_id"] . ')" class="btn btn-claim">Claim</button>';
            $data[] = $sub_array;
        }
        $output = array("recordsTotal"=>$this->count_all_data(),"recordsFiltered"=>$number_filter_row,"data"=>$data);
        echo json_encode($output);
    }
}
