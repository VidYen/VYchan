<?php
include_once 'inc/functions.php';
include_once 'inc/lib/cp/coinpayments.inc.php';

global $config;

if(isset($_POST['type']) && $_POST['type'] == 'existing'){
    $query = prepare("SELECT `pass_key`, `ends_at`, `logged_in` FROM pass WHERE uuid=:uuid");
    $query->execute(array(':uuid' => $_POST['uuid']));
    $columns = $query->fetchAll();

    $hash = $columns[0]['pass_key'];
    $time = $columns[0]['ends_at'];
    $logged_in = $columns[0]['logged_in'];

    $error = 0;
    if ((password_verify($_POST['pass'], $hash) && time() < $time) && $logged_in == 0) {
        setcookie('pass', $_POST['pass'], $time);
        setcookie('uuid', $_POST['uuid'], $time);

        $query = prepare("UPDATE pass SET `logged_in` = 1 WHERE `uuid` = :uuid");
        $query->execute(array(':uuid' => $_POST['uuid']));
        $error = 1;
    }

    if($logged_in == 1){
        $error = 2;
    }

    echo Element( '/themes/pass/login.html', array(
        'error' => $error,
        'config' => $config,
    ));

}

$query = prepare("SELECT `value` FROM theme_settings WHERE `name` = 'cp_public'");
$query->execute();
$cp_public = $query->fetchColumn();

$query = prepare("SELECT `value` FROM theme_settings WHERE `name` = 'cp_private'");
$query->execute();
$cp_private = $query->fetchColumn();

if(empty($address)){
    $address = "No address found.";
}

if (isset($_GET['a']) && !isset($_GET['7d']) && !isset($_GET['24h'])) {

    $received = false;

    if(!isset($_GET['uuid'])){
        if(!isset($_POST['email']) || !isset($_POST['crypto']) || !($_POST['crypto'] == 'BTC' || $_POST['crypto'] == 'LTC' || $_POST['crypto'] == 'DOGE')){
            echo Element( '/themes/pass/getaddress.html', array(
                'config' => $config,
                'type' => 'a',
            ));
        } else {
            $cps = new CoinPaymentsAPI();
            $cps->Setup($cp_private, $cp_public);

            $result = $cps->CreateTransactionSimple(3.50, 'USD', $_POST['crypto'], '', 'ipn_url', $_POST['email']);
            if(!isset($result['result']['txn_id']))
                die('Error, check coinpayment settings.');


            if($_POST['crypto'] == 'DOGE'){
                $result['result']['amount'] = number_format((float)$result['result']['amount'], 2, '.', '');
            }

            $query = prepare("INSERT INTO crypto_pass(`uuid`, `crypto_type`, `address`, `crypto_amount`) VALUES(:uuid, :crypto_type, :address, :crypto_amount)");
            $query->execute(array(':uuid' => $result['result']['txn_id'], ':crypto_type' => $_POST['crypto'],':address' => $result['result']['address'], ':crypto_amount' => number_format((float)$result['result']['amount'], 7, '.', '')));

            header("Location: /buy.php?a&uuid=" . $result['result']['txn_id']);
        }
        die();
    } else {
        $uuid = $_GET['uuid'];

        $query = prepare("SELECT `address`, `crypto_amount`, `crypto_type` FROM crypto_pass WHERE uuid=:uuid");
        $query->execute(array(':uuid' => $uuid));
        $rows = $query->fetchAll();
        if(!isset($rows[0][0]))
            die('Error processing order.');
        $address = $rows[0][0];
        $amount = floatval($rows[0][1]);
        $crypto_type = $rows[0][2];

        $ch = curl_init("https://chain.so/api/v2/get_address_received/" . $crypto_type . "/" . $address);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=json_decode(curl_exec($ch));
        curl_close($ch);

        $balance = 0;
        $unconfirmed = 0;
        if(isset($result->data->confirmed_received_value)){
            $balance = $result->data->confirmed_received_value;
            $unconfirmed = $result->data->unconfirmed_received_value;
        } else {
            die("Error occurred, check back in a few minutes.");
        }

        if($balance >= $amount){
            $received = true;
        }

        if($unconfirmed >= $amount){
            $unconfirmed = true;
        } else {
            $unconfirmed = false;
        }

        $query = prepare("SELECT `file_location` FROM advertisement WHERE uuid=:uuid");
        $query->execute(array(':uuid' => $uuid));
        $count = $query->rowCount();

        //create for the first time
        $file_location = '';
        if($received == true && $count == 0){

            $time = time() + 86400;

            $query = prepare("INSERT INTO advertisement(`uuid`, `file_location`, `ad_link`, `ends_at`) VALUES(:uuid, '', '', :ends_at)");
            $query->execute(array(':uuid' => $uuid,':ends_at' => $time));
        }

        $error = '';
        $file_location = $query->fetchColumn();

        if($received == true && $count == 1 && $file_location == '' && isset($_FILES['file']) && $_FILES['file']['size'] > 0){

            if(!getimagesize($_FILES["file"]["tmp_name"])) {
                $error = "File is not an image.";
            }

            list($width, $height) = getimagesize($_FILES["file"]["tmp_name"]);

            if($width != 728 && $height != 90){
                $error = "Resolution not 728x90.";
            }

            if ($_FILES["file"]["size"] > 1000000) {
                $error = "Image is more than one megabyte.";
            }

            $center = md5(random_bytes(100));
            $fileType = strtolower(basename(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION)));

            if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" ) {
                $error = "Unsupported file type";
            }

            if(substr($_POST['link'], 0, 4 ) != 'http'){
                $error = "Not a link.";
            }

            if(empty($error)){
                $file_location = 'ads/' . $center . '.' . strtolower(basename(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION)));
                if (!move_uploaded_file($_FILES["file"]["tmp_name"], $file_location)) {
                    $error = "Image not uploaded.";
                }
            }

            if(empty($error)){
                $file_location = 'ads/' . $center . '.' . strtolower(basename(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION)));
                $query = prepare("UPDATE advertisement SET `file_location` = :file_location, `ad_link` = :ad_link WHERE `uuid` = :uuid");
                $query->execute(array(':uuid' => $uuid, ':file_location' => $file_location, ':ad_link' => $_POST['link']));
                $count = $query->rowCount();
                $file_location = '';

                if($count != 1){
                    $error = "Image not uploaded.";
                } else {
                    $error = "Advertisement is now live for 24 hours.";
                }
            }
        } else {
            if($file_location != ''){
                $error = "Advertisement is now live for 24 hours.";
            }
        }

    }

    echo Element( '/themes/pass/advertisement.html', array(
        'config' => $config,
        'address' => $address,
        'received' => $received,
        'uuid' => $uuid,
        'file_location' => $file_location,
        'error' => $error,
        'unconfirmed' => $unconfirmed,
        'amount' => $amount,
        'type' => $crypto_type,
    ));
}

//buy a 7 day pass
if (!isset($_GET['a']) && isset($_GET['7d']) && !isset($_GET['24h'])) {

    $received = false;
    $pass_key = '';
    if(!isset($_GET['uuid'])){
        if(!isset($_POST['email']) || !isset($_POST['crypto']) || !($_POST['crypto'] == 'BTC' || $_POST['crypto'] == 'LTC' || $_POST['crypto'] == 'DOGE')){
            echo Element( '/themes/pass/getaddress.html', array(
                'config' => $config,
                'type' => 'a',
            ));
        } else {
            $cps = new CoinPaymentsAPI();
            $cps->Setup($cp_private, $cp_public);

            $result = $cps->CreateTransactionSimple(3.50, 'USD', $_POST['crypto'], '', 'ipn_url', $_POST['email']);
            if(!isset($result['result']['txn_id']))
                die('Error, check coinpayment settings.');

            $query = prepare("INSERT INTO crypto_pass(`uuid`, `crypto_type`, `address`, `crypto_amount`) VALUES(:uuid, :crypto_type, :address, :crypto_amount)");
            $query->execute(array(':uuid' => $result['result']['txn_id'], ':crypto_type' => $_POST['crypto'],':address' => $result['result']['address'], ':crypto_amount' => $result['result']['amount']));

            header("Location: /buy.php?a&uuid=" . $result['result']['txn_id']);
        }
        die();
    } else {
        $uuid = $_GET['uuid'];

        $query = prepare("SELECT `address`, `crypto_amount`, `crypto_type` FROM crypto_pass WHERE uuid=:uuid");
        $query->execute(array(':uuid' => $uuid));
        $rows = $query->fetchAll();
        if(!isset($rows[0][0]))
            die('Error processing order.');
        $address = $rows[0][0];
        $amount = floatval($rows[0][1]);
        $crypto_type = $rows[0][2];

        $ch = curl_init("https://chain.so/api/v2/get_address_received/" . $crypto_type . "/" . $address);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=json_decode(curl_exec($ch));
        curl_close($ch);

        $balance = 0;
        $unconfirmed = 0;
        if(isset($result->data->confirmed_received_value)){
            $balance = $result->data->confirmed_received_value;
            $unconfirmed = $result->data->unconfirmed_received_value;
        } else {
            die("Error occurred, check back in a few minutes.");
        }

        if($balance >= $amount){
            $received = true;
        }

        if($unconfirmed >= $amount){
            $unconfirmed = true;
        } else {
            $unconfirmed = false;
        }

        $query = prepare("SELECT `pass_key` FROM pass WHERE uuid=:uuid");
        $query->execute(array(':uuid' => $uuid));
        $count = $query->rowCount();

        if($received == true && $count == 0){
            $pass_key = bin2hex(random_bytes(10));

            $time = time()+604800;
            setcookie('pass', $pass_key, $time);
            setcookie('uuid', $uuid, $time);

            $hash = password_hash($pass_key, PASSWORD_DEFAULT);

            $query = prepare("INSERT INTO pass(`uuid`, `pass_key`, `ends_at`) VALUES(:uuid, :pass_key, :ends_at)");
            $query->execute(array(':uuid' => $uuid, ':pass_key' => $hash, ':ends_at' => $time));
        }
    }

    echo Element( '/themes/pass/pass_7d.html', array(
        'config' => $config,
        'address' => $address,
        'received' => $received,
        'uuid' => $uuid,
        'pass_key' => $pass_key,
        'unconfirmed' => $unconfirmed,
        'amount' => $amount,
        'type' => $crypto_type,
    ));
}

//buy a 24h pass
if (!isset($_GET['a']) && !isset($_GET['7d']) && isset($_GET['24h'])) {
    $received = false;
    $pass_key = '';

    if(!isset($_GET['uuid'])){
        if(!isset($_POST['email']) || !isset($_POST['crypto']) || !($_POST['crypto'] == 'BTC' || $_POST['crypto'] == 'LTC' || $_POST['crypto'] == 'DOGE')){
            echo Element( '/themes/pass/getaddress.html', array(
                'config' => $config,
                'type' => 'a',
            ));
        } else {
            $cps = new CoinPaymentsAPI();
            $cps->Setup($cp_private, $cp_public);

            $result = $cps->CreateTransactionSimple(0.50, 'USD', $_POST['crypto'], '', 'ipn_url', $_POST['email']);
            if(!isset($result['result']['txn_id']))
                die('Error, check coinpayment settings.');

            $query = prepare("INSERT INTO crypto_pass(`uuid`, `crypto_type`, `address`, `crypto_amount`) VALUES(:uuid, :crypto_type, :address, :crypto_amount)");
            $query->execute(array(':uuid' => $result['result']['txn_id'], ':crypto_type' => $_POST['crypto'],':address' => $result['result']['address'], ':crypto_amount' => $result['result']['amount']));

            header("Location: /buy.php?a&uuid=" . $result['result']['txn_id']);
        }
        die();
    } else {
        $uuid = $_GET['uuid'];

        $query = prepare("SELECT `address`, `crypto_amount`, `crypto_type` FROM crypto_pass WHERE uuid=:uuid");
        $query->execute(array(':uuid' => $uuid));
        $rows = $query->fetchAll();
        if(!isset($rows[0][0]))
            die('Error processing order.');
        $address = $rows[0][0];
        $amount = floatval($rows[0][1]);
        $crypto_type = $rows[0][2];

        $ch = curl_init("https://chain.so/api/v2/get_address_received/" . $crypto_type . "/" . $address);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result=json_decode(curl_exec($ch));
        curl_close($ch);

        $balance = 0;
        $unconfirmed = 0;
        if(isset($result->data->confirmed_received_value)){
            $balance = $result->data->confirmed_received_value;
            $unconfirmed = $result->data->unconfirmed_received_value;
        } else {
            die("Error occurred, check back in a few minutes.");
        }

        if($balance >= $amount){
            $received = true;
        }

        if($unconfirmed >= $amount){
            $unconfirmed = true;
        } else {
            $unconfirmed = false;
        }

        $query = prepare("SELECT `pass_key` FROM pass WHERE uuid=:uuid");
        $query->execute(array(':uuid' => $uuid));
        $count = $query->rowCount();

        if($received == true && $count == 0){
            $pass_key = bin2hex(random_bytes(10));

            $time = time()+86400;
            setcookie('pass', $pass_key, $time);
            setcookie('uuid', $uuid, $time);

            $hash = password_hash($pass_key, PASSWORD_DEFAULT);

            $query = prepare("INSERT INTO pass(`uuid`, `pass_key`, `ends_at`) VALUES(:uuid, :pass_key, :ends_at)");
            $query->execute(array(':uuid' => $uuid, ':pass_key' => $hash, ':ends_at' => $time));
        }
    }

    echo Element( '/themes/pass/pass_24h.html', array(
        'config' => $config,
        'address' => $address,
        'received' => $received,
        'uuid' => $uuid,
        'pass_key' => $pass_key,
        'unconfirmed' => $unconfirmed,
        'amount' => $amount,
        'type' => $crypto_type,
    ));
}

