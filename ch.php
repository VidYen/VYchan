<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'inc/functions.php';

    global $config;

    //set error to captcha error
    $error = 1;
    session_start();

    if(isset($_SESSION['captcha']) && $_SESSION['captcha'] == true){
        $error = 0;
    };

    $query = prepare("SELECT `value` FROM `theme_settings` WHERE `theme` = 'pass' AND `name` = 'ch_private'");
    $query->execute();
    $private = $query->fetchColumn();

    if(isset($_POST['type']) && $_POST['type'] == 'recaptcha'){
        $post_data = http_build_query(
            array(
                'secret' => $config['recaptcha_private'],
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        );
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data,
                'timeout' => 10,
            )
        );
        $context  = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);

        if ($result->success) {
            //set to no error
            $_SESSION['captcha'] = true;
            $error = 0;
        }
    }

    if($error == 0 && isset($_POST['type']) && $_POST['type'] == 'coinhive'
        && isset($_SESSION['captcha']) && $_SESSION['captcha'] == true){
        $post_data = [
            'secret' => $private, // <- Your secret key
            'token' => $_POST['coinhive-captcha-token'],
            'hashes' => 256
        ];

        $post_context = stream_context_create([
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($post_data)
            ]
        ]);

        $url = 'https://api.coinhive.com/token/verify';
        $response = json_decode(file_get_contents($url, false, $post_context));

        if ($response && $response->success) {
                try {
                    $pass_key = bin2hex(random_bytes(10));
                    $uuid = bin2hex(random_bytes(10));
                } catch(Exception $exception){
                    die($exception->getMessage());
                }
                $time = time()+21600;
                setcookie('pass', $pass_key, $time);
                setcookie('uuid', $uuid, $time);

                $hash = password_hash($pass_key, PASSWORD_DEFAULT);

                $query = prepare("INSERT INTO pass(`uuid`, `pass_key`, `ends_at`) VALUES(:uuid, :pass_key, :ends_at)");
                $query->execute(array(':uuid' => $uuid, ':pass_key' => $hash, ':ends_at' => $time));
                $count = $query->rowCount();

                $_SESSION['captcha'] = false;

                echo Element( '/themes/pass/ch_info.html', array(
                    'config' => $config,
                    'uuid' => $uuid,
                    'pass_key' => $pass_key,
                ));
                die();
            } else {
                $error = 4;
            }
    }


    if (isset($_GET['invalidate'])) {
        unset($_COOKIE['pass']);
        unset($_COOKIE['uuid']);
        setcookie('pass', null, -1, '/');
        setcookie('uuid', null, -1, '/');
    }

    $query = prepare("SELECT `value` FROM `theme_settings` WHERE `theme` = 'pass' AND `name` = 'hashes'");
    $query->execute();
    $hashes = $query->fetchColumn();

    $query = prepare("SELECT `value` FROM `theme_settings` WHERE `theme` = 'pass' AND `name` = 'ch_public'");
    $query->execute();
    $public = $query->fetchColumn();

    echo Element( '/themes/pass/miner.html', array(
        'hashes' => $hashes,
        'config' => $config,
        'error' => $error,
        'public' => $public,
    ));