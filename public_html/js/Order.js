class Order {
    table;
    add_button;
    constructor(table) {
        this.qr_code_id = "";
        this.table = table;
        this.month_name = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
    }

    total_order_count() {
        let fotm_data = new FormData();
        fotm_data.append('total_order_count', 'total_order_count');
        fetch('php/controller/c_order.php', {
            method: "POST",
            body: fotm_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            console.log(response_data.data);

            if (response_data.error) {

                new Notification().create_notification(response_data.error, "error");
            } else {

                document.getElementById('total_cancelled_count').innerHTML = response_data.data[0].total_cancelled_count;
                document.getElementById('total_completed_count').innerHTML = response_data.data[0].total_completed_count;
                document.getElementById('total_unclaimed_count').innerHTML = response_data.data[0].total_unclaimed_count;
                document.getElementById('total_preparing_count').innerHTML = response_data.data[0].total_preparing_count;
                document.getElementById('total_placed_count').innerHTML = response_data.data[0].total_placed_count;
                document.getElementById('total_order_count').innerHTML = response_data.data[0].total_order_count;
            }
        });
    }

    customer_order() {
        /*  new Order().close_order_details(); */
        //gets the value of selected radio button which is used to filter the items in the order table
        document.querySelectorAll('input[name="category"]')[0].checked = "checked";

        document.querySelectorAll('input[name="category"]')[0].parentElement.parentElement.id =
            "active-sort";
        document.querySelectorAll('input[name="category"]').forEach((radio) => {
            radio.addEventListener('change', function () {
                // removes styling of the unselected radio button
                document.querySelectorAll('input[name="category"]').forEach(function (
                    radio) {
                    radio.parentElement.parentElement.setAttribute("id", "");
                });
                (document.querySelector('input[name="category"]:checked').parentElement
                    .parentElement).id = "active-sort";
                new Order().display_order(document.querySelector('input[name="category"]:checked')
                    .value);
            });
            new Order().display_order(document.querySelector('input[name="category"]:checked').value);
        });
    }

    staff_order() {
        new Order().close_del_notif();
        document.getElementById("order_modal").style.display = "none";
        document.getElementById("preview").style.display = "none";


        let scanner = new Instascan.Scanner({
            video: document.getElementById('preview')
        });
        /* returns the QR code scanned by the  scanner */
        scanner.addListener('scan', function (content) {
            console.log(content);
            new Order().order_fetch_info(content, "qr");
        });

        /* gets device camera */
        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function (e) {
            console.log(e);
            new Notification().create_notification("Cannot access camera. Please refresh the page", "error");
        });
    }

    /* -------------------- order.php */
    display_order(category) {
        let form_data = new FormData();
        if (category == "Completed") {
            form_data.append('display_completed_order', 'display_completed_order');
        } else {
            form_data.append('display_order', 'display_order');
        }
        form_data.append('category', category);
        fetch('php/controller/c_order.php', {
            method: "POST",
            body: form_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            let order_list = "";
            /* console.log(response_data.data[0].total_order_price); */
            console.log(response_data);

            if (response_data.error) {
                let no_order = `
                <div class="no-orders-container">
                    <span class="no-order-message">Looks like you haven't made an order yet.</span>
                    <a href="menu.php">Order Now</a>
                </div>
                `;
                document.getElementById("order_list").innerHTML = no_order;
            } else {

                let status_class = "";
                let order_status = "";
                response_data.data.map(function (order) {
                    order_status = new Order().get_status_text(order.status);
                    status_class = new Order().get_status_style(order.status);

                    let show_date = new Order().get_date_format(order.date);
                    let fetch_time = new Order().get_time_format(order.time);
                    order_list += `
                    <div class="order-item">
                    <div class="order-details-row">
                        <div class="order-no-container">
                            <span>Order No.</span>
                            <span class="order-number">${(order.order_id).toString().padStart(10, '0')}</span>
                        </div>
                        <div class="order-date-container">`;
                    if (category == "Completed" || category == "details-completed") {
                        order_list += `      <span>${`${show_date}`}</span>`;
                    } else {
                        order_list += `      <span>${`${show_date} | ${fetch_time}`}</span>`;
                    }

                    order_list += `  </div>
                    </div>
                    <div class="order-details-row">
                        <div class="quantity-container">
                            <span>Quantity: ${order.total_quantity}</span>
                    </div>
                        <div class="amount-container">
                            <span>Amount:</span>
                            <span class="total-amt">PHP ${parseFloat(order.total_price).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</span>
                        </div>
                    </div>
                    <div class="order-details-row">
                        <div class="details-btn-container">
                            <button class="details-btn" onclick="new Order().display_details(${order.order_id},'${category}','${order.total_price}');" >Details</button>
                        </div>
                        <div class="status-container"> 
                            <span class="order-status ${status_class}">${(order_status)}</span>
                        </div>
                    </div>
                </div>
            `;
                });
                document.getElementById("order_list").innerHTML = order_list;
            }

        });
    }

    display_details(order_id, category, price) {
        let form_data = new FormData();
        form_data.append('display_details', 'display_details');
        form_data.append('order_id', order_id);
        form_data.append('category', category);

        fetch('php/controller/c_order.php', {
            method: "POST",
            body: form_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            console.log(response_data);
            let single_order = response_data.data[0];
            new Order().open_order_details();
            let order_details_list = "";

            let status_class = "";
            let order_status = "";

            order_status = new Order().get_status_text(single_order.status);
            status_class = new Order().get_status_style(single_order.status);

            let show_date = new Order().get_date_format(single_order.date);
            let fetch_time = new Order().get_time_format(single_order.time);

            let qr_image = "";

            if (single_order.qr_image == null) {
                qr_image = ` 
        <img src='img/no-qr.jpg' width='100px' height='100px' ></img>
    `;
            } else {
                qr_image = ` <a  href='https://res.cloudinary.com/dhzn9musm/image/upload/${single_order.qr_image}' target="_blank" >
            <img src='https://res.cloudinary.com/dhzn9musm/image/upload/${single_order.qr_image}' width='100px' height='100px' ></img>
        </a>`;
            }
            order_details_list += `
                <div class="content-container" >
                <div class="closing-bar">
                    <button class="close-btn" onclick="new Order().close_order_details();">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="mod-top">
                    <div class="header-col with-details">
                        <span class="order-number-label">Order No.:</span>
                        <span class="order-number">${(single_order.order_id).toString().padStart(10, '0')}</span><br>
                        `;
            if (category == "Completed" || category == "details-completed") {
                order_details_list += `<span class="label">Date Claimed:</span><span class="order-date value">${` ${`${show_date}`}`}</span><br>`;
            } else {
                order_details_list += `<span class="label">Order Date:</span><span class="order-date value">${` ${`${show_date} | ${fetch_time}`}`}</span><br>`;
            }
            order_details_list += ` <span class="label">Status:</span>
                        <span class="status ${status_class} status-value"> ${order_status}</span>
                    </div>
                    <div class="header-col">
                        ${qr_image}
                    </div>
                </div>
                <div class="items-list" style="min-height:215px;max-height:215px;">
                `;
            response_data.data.map(function (order) {
                order_details_list += `
                    <!-- ORDER ITEMS APPEND HERE -->
                    <div class="item">
                    <div class="item-img-container">
                    <img src='https://res.cloudinary.com/dhzn9musm/image/upload/${order.image_list}' alt="${order.menu_name_list}" ></img>
                    </div>
                    <div class="item-details">
                        <div class="quantity-con">
                        <span><span class="modal-quantity">${order.quantity_list}</span>x</span>
                        </div>
                        <div class="item-name-con">
                        <span class="item-name">${order.menu_name_list}</span>
                        </div>
                        <div class="item-price-con">
                        <span class="item-price">PHP ${parseFloat(order.discounted_price).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",").replace(".00", "")}</span>
                        </div>
                    </div>
                </div>
               `;

            });
            order_details_list += `
                 </div>
                <div class="mod-footer">
                    <div class="footer-col">
                        <span>Subtotal: </span>
                    </div>
                    <div class="footer-col">
                        <span class="sub-total">PHP ${parseFloat(single_order.total_price).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</span>
                    </div>
                </div>
                <div class="cancel-bar">`;
            if (single_order.status == "Placed") {
                order_details_list += `<button type="button" class="btn btn-danger cancel-btn" name='delete_order'  onclick="new Order().delete_order(${single_order.order_id});">Cancel Order</button>`;
            } else if (single_order.status == null || single_order.status == "Completed" || single_order.status == "Cancelled") {
                order_details_list += `<button type="button" class="btn btn-danger cancel-btn" name='delete_order' style="visibility:hidden;"></button>`;
            } else {
                order_details_list += `<button type="button" class="btn btn-danger cancel-btn" name='delete_order'  onclick="new Order().delete_order(${single_order.order_id});" disabled>Cancel Order</button>`;
            }
            order_details_list += `</div>
            </div>
                `;
            document.getElementById("order_details_list").innerHTML = order_details_list;

        });
    }

    delete_order(order_id) {
        if (confirm("Are you sure you want to cancel your order?")) {
            let fotm_data = new FormData();
            fotm_data.append('delete_order', 'delete_order');
            fotm_data.append('order_id', order_id);
            fetch('php/controller/c_order.php', {
                method: "POST",
                body: fotm_data
            }).then(function (response) {
                return response.json();
            }).then(function (response_data) {
                console.log(response_data);
                if (response_data.success) {
                  
                    new Order().close_order_details();
                    new Notification().notification(); 
                    new Notification().create_notification(response_data.success, "success");
  
                } else if (response_data.error) {
                    new Notification().create_notification(response_data.error, "error");
                }
                new Order().display_order(document.querySelector('input[name="category"]:checked').value);
            });
        }
    }

    get_date_format(date) {
        let show_date = "";
        let fetch_month = (date).substr(5, 2);
        let today = new Date();
        let fetch_date = new Date(date.substr(0, 10));

        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        if (fetch_date.toLocaleDateString() == today.toLocaleDateString()) {
            show_date = 'Today'
        } else if (fetch_date.toLocaleDateString() == yesterday.toLocaleDateString()) {
            show_date = 'Yesterday'
        } else {
            show_date = `${new Order().month_name[fetch_month-1]} ${(date).substr(8,2)}, ${(date).substr(0,4)}`;
        }
        return show_date;
    }

    get_time_format(time) {
        let fetch_time = new Date("2020-10-10 " + time).toLocaleTimeString('en-US', {
            hour12: true,
            hour: 'numeric',
            minute: 'numeric'
        });
        return fetch_time;
    }


    /* -------------------- STAFF -------------------- */
    /* -------------------- edit-order.php */
    claim_order() {
        let form_data = new FormData();
        form_data.append('claim_order', 'claim_order');
        form_data.append('identifier', document.getElementById("to_claim_order_id").value);
        form_data.append('type', document.getElementById("to_claim_type").value);

        fetch('php/controller/c_order.php', {
            method: "POST",
            body: form_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            console.log(response_data);
            new Order().qr_close_modal();
            if (response_data.success) {
                table.update();
                new Order().total_order_count();
                dataRemoved();
                new Notification().create_notification(response_data.success, "success");

            } else if (response_data.error) {
                new Notification().create_notification(response_data.error, "error");
            }
        });
    }

    /* gets and displays the order information of to claim orders*/
    order_fetch_info(identifier, type) {
        //plays beep audio wehn a qr is scanned
        if (type == "qr") {
            new Audio('sound/beep.mp3').play();;
        }
        let form_data = new FormData();
        form_data.append('identifier', identifier);
        form_data.append('type', type);
        form_data.append('order_fetch_info', 'order_fetch_info');
        fetch('php/controller/c_order.php', {
            method: "POST",
            body: form_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            console.log(response_data);
            if (response_data.error) {
                new Notification().create_notification(response_data.error, "error");
            } else {
                let to_claim_info = "";
                let to_claim_price = "";
                let to_claim_order = "";
                let order = response_data.data[0];
                let images = (order.image_list).split(', ');
                let orders = (order.menu_name_list).split(',');
                let prices = (order.price_list).split(',');
                let quantity = (order.quantity_list).split(',');
                let discount = (order.discount_list).split(',');

                     document.getElementById("to_claim_order_id").value = order.order_id;
               document.getElementById("to_claim_type").value = type;
               let show_date = new Order().get_date_format(order.date);
                to_claim_info += ` 
                
                <div class="content-container">
            <div class="closing-bar">`;
            if (type == "delete") {
                to_claim_info += `<button class="close-btn" onclick="new Order().close_del_notif();">`;
                } else {
                    to_claim_info += `<button class="close-btn" onclick="new Order().qr_close_modal();">`;
                }
           
            to_claim_info += `<i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="mod-top">
                <div class="header-col with-details">
                    <span class="order-number-label">Order No.:</span>
                    <span class="order-number">${(order.order_id).toString().padStart(10, '0')}</span><br>

                    <span class="label">Name:</span>
                    <span class="order-date value">${order.firstname} ${order.lastname}</span><br>

                    <span class="label">Order Date:</span>
                    <span class="status pickup-stat value">${show_date}</span>
                </div>
                <div class="header-col">
                   
                </div>
            </div>
            <div class="items-list" style="min-height:215px;max-height:215px;">
               `;
        
               for (let i = 0; i < prices.length; i++) {
               to_claim_info += `  
                <div class="item">
                    <div class="item-img-container">
                        <img src="https://res.cloudinary.com/dhzn9musm/image/upload/${images[i]}" alt="${orders[i]}">
                    </div>
                    <div class="item-details">
                        <div class="quantity-con">
                            <span><span class="modal-quantity">${quantity[i]}</span>x</span>
                        </div>
                        <div class="item-name-con">
                            <span class="item-name">${orders[i]}</span>
                        </div>
                        <div class="item-price-con">
                            <span class="item-price">PHP ${parseFloat(prices[i]).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",").replace(".00", "")}</span>
                        </div>
                    </div>
                </div>`
                ;
               }
                to_claim_info += `  </div>
            <div class="mod-footer">
                <div class="footer-col">
                    <span>Subtotal: </span>
                </div>
                <div class="footer-col">
                    <span class="sub-total">PHP ${parseFloat(order.total_price).toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</span>
                </div>
            </div>
            <div class="cancel-bar">`;
            if (type == "delete") {

            to_claim_info += ` 
            <form method="post" id="del_notif_form" class="mb-3" style="margin-top:-10px">
                              <label for="del_notif" style="font-size:1.6em">Reason</label>
                              <input list="del_list" name="del_notif" id="del_notif" class="form-control input-reason" />
                              <datalist id="del_list">
                                  <option>Item Unavailable</option>
                              </datalist>
                              <span class="input_error" id="del_notif_error"></span>
                          </form>
            <button class="btn btn-danger cancel-btn" onclick="new Order().staff_delete_order();">Cancel Order</button>`;
            } else {
                to_claim_info += `  <button class="btn btn-success cancel-btn" onclick=" new Order().claim_order();">Claim Order</button>`;
            }
            to_claim_info += `  </div>
        </div>
                `;
                if (type == "delete") {
                    document.getElementById("delete-details-modal").style.display = "block";
                    document.getElementById('delete-details-modal').innerHTML = to_claim_info;
                    document.querySelector('body').style.overflow = 'hidden';
              
                } else {
                    document.getElementById('claim-details-modal').innerHTML = to_claim_info;
                    document.getElementById("claim-details-modal").style.display = "block";
                    document.querySelector('body').style.overflow = 'hidden';
                }
            }
        });


    }


    toggle_camera() {
        if (document.getElementById("preview").style.display == "none") {
            document.getElementById("preview").style.display = "block";
        } else {
            document.getElementById("preview").style.display = "none";
        }
    }

    qr_close_modal() {
        this.qr_code_id = "";
        document.getElementById("claim-details-modal").style.display = "none";
        document.querySelector('body').style.overflow = 'visible';
    }


    del_notif(order_id, user_id) {
        document.getElementById("del_notif_order_id").value = order_id;
        document.getElementById("del_notif_user_id").value = user_id;
        document.querySelector('body').style.overflow = 'hidden';
        new Order().order_fetch_info(order_id, "delete");
    }
    close_del_notif() {
        document.getElementById('delete-details-modal').style.display = 'none';
        document.getElementById("del_notif_order_id").style.display = "none";
        document.getElementById("to_claim_order_id").style.display = "none";
        document.getElementById("to_claim_type").style.display = "none";
        document.getElementById("del_notif_order_id").value = "";
        // document.getElementById("del_notif").value = "";

        document.getElementById("del_notif_user_id").style.display = "none";
        document.getElementById("del_notif_user_id").value = "";
        // document.getElementById("del_notif").selectedIndex = 0;
        document.querySelector('body').style.overflow = 'visible';
    }


    /* -------------------- STAFF -------------------- */
    /* -------------------- edit-order.php */
    action_order_button() {
        let form_data = new FormData(document.getElementById('order_form'));
        form_data.append('action_order', 'Update')
        fetch('php/controller/c_order.php', {
            method: "POST",
            body: form_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            console.log(response_data);
            if (response_data.success) {
                new Notification().create_notification(response_data.success, "success");
                table.update();
                new Order().total_order_count();
            } else if (response_data.error) {
                new Notification().create_notification(response_data.error, "error");
            }
        });
    }

    fetch_selected_order(order_id, type) {
        let form_data = new FormData();
        form_data.append('order_id', order_id);
        form_data.append('fetch_selected_order', 'fetch_selected_order');
        fetch('php/controller/c_order.php', {
            method: "POST",
            body: form_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            console.log(response_data);
            document.getElementById('order_id').value = response_data.order_id;
            if (type == "new") {
                document.getElementById('date').value = document.getElementById(response_data.order_id + 'new-date').value;
                document.getElementById('time').value = document.getElementById(response_data.order_id + 'new-time').value;
                document.getElementById('status').value = document.getElementById(response_data.order_id + 'new-status').value;
            } else {
                document.getElementById('date').value = document.getElementById(response_data.order_id + 'filter-new-date').value;
                document.getElementById('time').value = document.getElementById(response_data.order_id + 'filter-new-time').value;
                document.getElementById('status').value = document.getElementById(response_data.order_id + 'filter-new-status').value;
            }
            new Order().action_order_button();
        });
    }

    staff_delete_order() {
        let form_data = new FormData(document.getElementById('del_notif_form'));
        form_data.append('action_order', 'delete');
        form_data.append('order_id', document.getElementById('del_notif_order_id').value);
        form_data.append('user_id', document.getElementById('del_notif_user_id').value);
        fetch('php/controller/c_order.php', {
            method: "POST",
            body: form_data
        }).then(function (response) {
            return response.json();
        }).then(function (response_data) {
            if (response_data.success) {
                new Notification().create_notification(response_data.success, "success");
                dataRemoved();
                table.update();
                new Order().total_order_count();
                new Order().close_del_notif();

            } else if (response_data.error) {
                new Notification().create_notification(response_data.error, "error");
            }
            new Order().show_error(response_data.del_notif_error, 'del_notif_error');
        });
    }

    /* -------------------- */
    open_order_details() {
        document.getElementById("order_details_list").style.display = "block";
        document.querySelector('body').style.overflow = 'hidden';
    }
    close_order_details() {
        document.getElementById("order_details_list").style.display = "none";
        document.querySelector('body').style.overflow = 'visible';
    }

    /* determines the color of status(text) */
    get_status_style(status) {
        if (status == "Placed" || status == "Confirmed") {
            return "pending-stat";
        } else if (status == "Preparing") {
            return "preparing-stat";
        } else if (status == "Ready") {
            return "pickup-stat";
        } else if (status == "Cancelled") {
            return "cancelled-stat";
        } else {
            return "complete-stat";
        }
    }

    get_status_text(status) {
        if (status == null) {
            return "Completed";
        } else {
            return status;
        }
    }

    /* displays or removes error messages */
    show_error(error, element) {
        error ? document.getElementById(element).innerHTML = error : document.getElementById(element).innerHTML = '';
        if (error) {
            document.getElementById(element.replace('_error', '')).style.border = "red solid 1px";
        } else {
            document.getElementById(element.replace('_error', '')).style.border = "none";
        }
    }

    /* scroll to the position of the input field with an error */
    scroll_to(element) {
        if (element == "top") {
            document.getElementById("order_modal").scrollTo({
                top: 0,
                left: 0,
                behavior: "smooth"
            });
        } else {
            document.getElementById("order_modal").scrollTo({
                top: (document.getElementById(element).offsetTop) - 250,
                left: 0,
                behavior: "smooth"
            });
        }
    }
}