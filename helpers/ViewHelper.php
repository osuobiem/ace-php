<?php

    class ViewHelper {

      public static $title;
      public static $values = array();

      public static function title() {
        echo ' - '.self::$title;
      }

      public static function report($situation) {
        $situation == 'bad' ? $col = 'red' : $col = 'green';
        $situation == 'bad' ? $key = 'error' : $key = 'message';

        if(isset($_SESSION[$key]) && strlen($_SESSION[$key]) > 0) {
          echo '<center><h5 style="background-color: '.$col.'; border-radius: 8px; padding-top: 3px; padding-bottom: 3px; width: 75%; color: white; font-size: .89em;">'.$_SESSION[$key].'</h5></center>';
          $_SESSION[$key] = '';
        }
      }

      public static function regComp() {
        $_SESSION['name'] = $_POST['name'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['phone'] = $_POST['phone'];
        
        if(trim($_POST['password']) != trim($_POST['c-password'])) {
          $_SESSION['error'] = 'Passwords do not match!';
          header('Location: /vicinvent/company-register');
        }
        else {
          if(strlen($_POST['password']) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters!';
            header('Location: /vicinvent/company-register');
          }
          else {
            $response = CompanyController::create($_POST);

            if($response['status']) {
              $_SESSION['admin'] = $response['data'];
              $_SESSION['message'] = 'Registration Successful';
              header('Location: /vicinvent/admin-dashboard');
            }
            else {
              $_SESSION['error'] = $response['data']['message'];
              header('Location: /vicinvent/company-register');
            }
          }
        }
      }

      public static function addProd() {

        $admin = AdminController::getAdmin($_SESSION['admin']);
        $company_id = $admin['company_id'];

        $_POST['company_id'] = $company_id;

        $response = ProductController::create($_POST);

        if($response['status']) {
          $_SESSION['message'] = 'Product Added Successfully';
          header('Location: /vicinvent/admin-dashboard');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/add-product');
        }
      }

      public static function delProd($request) {
        $product_id = base64_decode($request[0]);
        
        $response = ProductController::delete($product_id);

        if($response['status']) {
          $_SESSION['message'] = 'Product Deleted Successfully';
          header('Location: /vicinvent/admin-dashboard');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/add-product');
        }
      }

      public static function oldData($key) {
        if(isset($_SESSION[$key])) {
          echo $_SESSION[$key];
        }
      }

      public static function upProd() {
        $product_id = base64_decode($_POST['p']);

        $_POST['id'] = $product_id;

        $response = ProductController::update($_POST);

        if($response['status']) {
          $_SESSION['message'] = 'Product Updated Successfully';
          header('Location: /vicinvent/admin-dashboard');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/update-product/'.$_POST['p']);
        }
      }

      public static function adminLogin() {
        $_POST['email'] = trim($_POST['email']);
        $_POST['passowrd'] = trim($_POST['password']);

        $response = AdminController::login($_POST);

        
        if($response['status']) {
          $_SESSION['admin'] = $response['data']['id'];
          $_SESSION['message'] = 'You are logged in';
          header('Location: /vicinvent/admin-dashboard');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/admin-login');
        }        
      }

      public function adminLogout() {
        unset($_SESSION['admin']);
        header('Location: /vicinvent/admin-login');
      }

      public function userLogout() {
        unset($_SESSION['user']);
        header('Location: /vicinvent/user-login');
      }

      public static function extends($page) {
        return require_once(__DIR__.'/../views/'.$page.'.php');
      }

      public static function get($key) {
        $split_key = explode('.', $key);
        if(array_key_exists(1, $split_key)) {
          echo self::$values[$split_key[0]][$split_key[1]];
        }
        else {
          if(gettype(self::$values[$key]) == 'array') {
            return self::$values[$key];
          }
          else {
            echo self::$values[$key];
          }
        }
      }
      
      public static function upAdmin() {
        $admin_id = base64_decode($_POST['p']);

        $_POST['id'] = $admin_id;

        $response = AdminController::update($_POST);

        if($response['status']) {
          $_SESSION['message'] = 'Profile Updated Successfully';
          header('Location: /vicinvent/admin-dashboard');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/update-admin');
        }
      }

      public static function addAdmin() {
        $_SESSION['firstname'] = $_POST['firstname'];
        $_SESSION['lastname'] = $_POST['lastname'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['phone'] = $_POST['phone'];
        
        if(trim($_POST['password']) != trim($_POST['c-password'])) {
          $_SESSION['error'] = 'Passwords do not match!';
          header('Location: /vicinvent/add-admin');
        }
        else {
          if(strlen($_POST['password']) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters!';
            header('Location: /vicinvent/add-admin');
          }
          else {
            $_POST['admin_type'] = 'normal';
            $_POST['company_id'] = base64_decode($_POST['c']);
            $response = AdminController::register($_POST);

            if($response['status']) {
              $_SESSION['message'] = 'Admin Added Successfully';
              header('Location: /vicinvent/admin-dashboard');
            }
            else {
              $_SESSION['error'] = $response['data']['message'];
              header('Location: /vicinvent/add-admin');
            }
          }
        }
      }

      public static function addUser() {
        $_SESSION['firstname'] = $_POST['firstname'];
        $_SESSION['lastname'] = $_POST['lastname'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['phone'] = $_POST['phone'];
        
        if(trim($_POST['password']) != trim($_POST['c-password'])) {
          $_SESSION['error'] = 'Passwords do not match!';
          header('Location: /vicinvent/add-user');
        }
        else {
          if(strlen($_POST['password']) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters!';
            header('Location: /vicinvent/add-user');
          }
          else {
            $_POST['company_id'] = base64_decode($_POST['c']);
            $response = UserController::register($_POST);

            if($response['status']) {
              $_SESSION['message'] = 'User Added Successfully';
              header('Location: /vicinvent/admin-dashboard');
            }
            else {
              $_SESSION['error'] = $response['data']['message'];
              header('Location: /vicinvent/add-user');
            }
          }
        }
      }

      public static function userLogin() {
        $_POST['email'] = trim($_POST['email']);
        $_POST['passowrd'] = trim($_POST['password']);

        $response = UserController::login($_POST);

        
        if($response['status']) {
          $_SESSION['user'] = $response['data']['id'];
          $_SESSION['message'] = 'You are logged in';
          header('Location: /vicinvent/sell-product');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/user-login');
        }        
      }

      public static function upUser() {
        $user_id = base64_decode($_POST['p']);

        $_POST['id'] = $user_id;

        $response = UserController::update($_POST);

        if($response['status']) {
          $_SESSION['message'] = 'Profile Updated Successfully';
          header('Location: /vicinvent/sell-product');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/update-user');
        }
      }

      public static function delAdmin($request) {
        $id = $_SESSION['admin'];
        $admin_id = base64_decode($request[0]);
        
        $admin = AdminController::getAdmin($id);
        if($admin['admin_type'] == 'super') {
          $response = AdminController::delete($admin_id);

          if($response['status']) {
            $_SESSION['message'] = $response['data']['message'];
            if($id == $admin_id) unset($_SESSION['admin']);
            header('Location: /vicinvent/all-admins');
          }
          else {
            $_SESSION['error'] = $response['data']['message'];
            header('Location: /vicinvent/all-admins');
          }
        }
        else {
          $_SESSION['error'] = 'You are not a Super Admin';
          header('Location: /vicinvent/all-admins');
        }
      }
      
      public static function delUser($request) {
        $user_id = base64_decode($request[0]);
        
        $response = UserController::delete($user_id);

        if($response['status']) {
          $_SESSION['message'] = $response['data']['message'];
          header('Location: /vicinvent/all-users');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/all-users');
        }
      }

      public static function sellProduct() {
        $product = explode('|', $_POST['product']);
        $product_id = base64_decode($product[count($product) - 1]);
        
        $response  = ProductController::sellProduct($product_id, $_POST['quantity']);

        if($response['status']) {
          $_SESSION['message'] = $response['data']['message'];
          header('Location: /vicinvent/sell-product');
        }
        else {
          $_SESSION['error'] = $response['data']['message'];
          header('Location: /vicinvent/sell-product');
        }
      }

      public static function with($values) {
        self::$values = $values;
      }

    }