<?php 
//  Scripts
add_action('wp_enqueue_scripts', 'assets');

function assets() {
   wp_enqueue_style('common-css', plugins_url( './css/common.css', __FILE__ ));
   
   
   wp_enqueue_script('fullcalendar', plugins_url( './src/libs/fullcalendar-6.1.11/dist/index.global.min.js', __FILE__ ), array('jquery'));
   wp_enqueue_script('common-js', plugins_url( './js/common.js', __FILE__ ));
 }



