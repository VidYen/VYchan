<?php
require 'info.php';

function pass_build($action, $settings, $board) {
    // Possible values for $action:
    //	- all (rebuild everything, initialization)
    //	- news (news has been updated)
    //	- boards (board list changed)
    //	- post (a post has been made)
    //	- post-thread (a thread has been made)

    $b = new Pass();
    $b->build($action, $settings);
}

// Wrap functions in a class so they don't interfere with normal Tinyboard operations
class Pass {
    public function build($action, $settings) {
        global $config;

        if ($action == 'all' || $action == 'post' || $action == 'post-thread' || $action == 'post-delete') {
            $action = generation_strategy('sb_recent', array());
            if ($action == 'delete') {
                file_unlink($config['dir']['home'] . 'pass.html');
                file_unlink($config['dir']['home'] . 'ch.html');
            }
			elseif ($action == 'rebuild') {
                file_write($config['dir']['home'] . 'pass.html', $this->passpage($settings));
                file_write($config['dir']['home'] . 'ch.html', $this->coinhivepage($settings));
            }
        }
    }

    // Build pass page
    public function passpage($settings) {

        global $config;

        return Element('themes/pass/pass.html', Array(
            'hashes' => (int)$settings['hashes'],
            'config' => $config,
        ));
    }

    // Build CoinHive page
    public function coinhivepage($settings) {

        global $config;

        return Element('themes/pass/ch.html', Array(
            'hashes' => (int)$settings['hashes'],
            'config' => $config,
        ));
    }
};

?>
