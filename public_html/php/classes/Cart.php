<?php

require_once dirname(__FILE__) . "/DbConnection.php";

class Cart extends DbConnection
{
/* -------------------- cart  */
/* gets all the added-to-cart items of the logged-in customer */
public function display_cart($user_id)
{
    $result = $query = $this->connect()->prepare("SELECT m.*, c.* FROM cart c INNER JOIN menu m ON(c.menu_id = m.menu_id) WHERE c.user_id  = :user_id ORDER BY c.cart_id DESC");
    $query->execute([":user_id" => $user_id]);
    if ($query->rowCount() > 0) {
        $data = array();
        foreach ($result as $row) {
            $sub_array = array();
            $sub_array['cart_id'] = $row['cart_id'];
            $sub_array['user_id'] = $row['user_id'];
            $sub_array['menu_id'] = $row['menu_id'];
            $sub_array['quantity'] = $row['quantity'];
            $sub_array['name'] = $row['name'];
            $sub_array['description'] = $row['description'];
            $sub_array['discount'] = $row['discount'];
            $sub_array['price'] = $row['price'];
            $sub_array['image'] = $row['image'];
            $sub_array['discounted_price'] = ($row['price'] - ($row['price'] * (floatval($row['discount']) / 100)));
            $sub_array['availability'] = $row['availability'];
            $sub_array['total_price'] = $row['price'] * $row['quantity'];
            $sub_array['total_discounted_price'] = ($row['price'] - ($row['price'] * (floatval($row['discount']) / 100))) * $row['quantity'];
            $data[] = $sub_array;
        }
        $output = array("data" => $data);
    } else {
        $output['error'] = 'No Items Found';
    }
    echo json_encode($output);
}
   
    //gets how many items a customer has on its cart
    public function add_to_cart_count($user_id)
    {
        $query = $this->connect()->prepare("SELECT * FROM cart WHERE user_id = :user_id");
        $query->execute([":user_id" => $user_id]);
        if ($query->rowCount() > 0) {
            $output['cart_count'] = $query->rowCount();
      
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }

     //insert the customers selected item into the cart table
     public function add_to_cart($user_id, $menu_id)
     {
         $quantity = 1;
         /* checks if the selected item is already in the cart
         if true the quantity will be increased by 1
         else the item will be added to cart  */
         $query = $this->connect()->prepare("SELECT * FROM cart WHERE user_id = :user_id AND menu_id =:cart_menu_id");
         $query->execute([":user_id" => $user_id, ":cart_menu_id" => $menu_id]);
         if ($query->rowCount() > 0) {
             $fetch = $query->fetch(PDO::FETCH_ASSOC);
             $fetch_cart_id = $fetch['cart_id'];
             $fetch_quantity = $fetch['quantity'];
             $query = $this->connect()->prepare("UPDATE cart SET quantity= :quantity WHERE cart_id =:cart_id");
             $result = $query->execute([":quantity" => $fetch_quantity + $quantity, ":cart_id" => $fetch_cart_id]);
         } else {
             $query = $this->connect()->prepare("INSERT INTO cart (user_id, menu_id, quantity) VALUES( :user_id, :menu_id, :quantity)");
             $result = $query->execute([":user_id" => $user_id, ":menu_id" => $menu_id, ":quantity" => $quantity]);
         }
         if ($result) {
             $output['success'] = 'Added to cart succesfully';
         } else {
             $output['error'] = 'Something went wrong! Please try again later.';
         }
         echo json_encode($output);
     }
 

    //updates quantity value
    public function update_quantity($cart_id, $quantity)
    {
        $query = $this->connect()->prepare("UPDATE cart SET quantity=:quantity WHERE cart_id = :cart_id");
        $result = $query->execute([":quantity" => $quantity, ":cart_id" => $cart_id]);
        if ($result) {
            $output['success'] = 'Item Updated Successfully';
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }

    //deletes an item in the cart table
    public function delete_cart($cart_id)
    public function delete_cart($cart_id,$type)
    {
        $query = $this->connect()->prepare("DELETE FROM cart WHERE cart_id = :cart_id");
        $result = $query->execute([":cart_id" => $cart_id]);
        $sql = "DELETE FROM cart ";
        if($type == "empty") {
            $query = $this->connect()->prepare($sql);
            $result = $query->execute([]);
        } else {
            $sql .= "WHERE cart_id = :cart_id";
            $query = $this->connect()->prepare($sql);
            $result = $query->execute([":cart_id" => $cart_id]);
        }
        if ($result) {
            $output['success'] = 'Item Deleted Successfully';
        } else {
            $output['error'] = 'Something went wrong! Please try again later.';
        }
        echo json_encode($output);
    }

    //gets the discounted price of the checked items in the cart
    public function get_price($cart_id_list)
    public function get_price($cartlist, $type)
    {
      /*   $_SESSION['cart_id_list'] =$cart_id_list; */
        //split comma separated values into array
        $cart_id = explode(',', $cart_id_list);
        $cart_id = explode(',', $cartlist);
        $data = array();
        //get the price of selected item
        for ($i = 0; $i < count($cart_id); $i++) {

            $result = $query = $this->connect()->prepare("SELECT m.*, c.* FROM cart c INNER JOIN menu m ON(c.menu_id = m.menu_id) WHERE c.cart_id  = :cart_id");
            $query->execute([":cart_id" => $cart_id[$i]]);
            $total_price=0;
            foreach ($result as $row) {
                $sub_array = array();
                $sub_array['total_discounted_price'] = ($row['price'] - ($row['price'] * (floatval($row['discount']) / 100))) * $row['quantity'];
                $total_price=($row['price'] - ($row['price'] * (floatval($row['discount']) / 100))) * $row['quantity'];
                $sub_array['total_discounted_price'] =  $total_price;
                $data[] = $sub_array;
            }
        }
        $output = array("data" => $data);

        echo json_encode($output);
        if($type =="return") {
            return $total_price; 
    } else {
            echo json_encode($output); 
    }
    }
}
