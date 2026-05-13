<?php
add_action('wp_ajax_get_api_key', 'get_api_key');
add_action('wp_ajax_nopriv_get_api_key', 'get_api_key');

function get_api_key()
{
  $api_key = ['ck' => 'ck_ff8c3f21b9f859658ca398075ebe7bfa46e712c3', 'cs' => 'cs_94e2cbbc7e958839c06fabccd342bab0b2c92fa3'];
  wp_send_json($api_key);
  wp_die();
}