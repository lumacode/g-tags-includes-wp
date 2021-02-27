<?php 
/* 
Plugin Name: G-Tag Include 
Plugin URI:
Description: Agrega las etiquetas de Google Analytics y Adwords al head de forma simple y rápida.  
Version: 1.0.0
Author: Luis Albanese
Author URI: https://luisalbanese.com.ar
License: GPL
*/

if(!defined('ABSPATH')) die();

//Search tags in db

function gtag_verifyTagsDB(){
  global $wpdb;

  $tags = $wpdb->prefix . 'google_tags';
  $charset_collate = $wpdb->get_charset_collate();
  $result = $wpdb->get_results("SELECT * FROM $tags WHERE id = 1");

  return $result;

}

//Hoooks activate and desactivate

function gtag_activate()
{
      global $wpdb;
      
      $tags = $wpdb->prefix . 'google_tags';
      $charset_collate = $wpdb->get_charset_collate();
      $query = "CREATE TABLE IF NOT EXISTS $tags (
              id int(255) NOT NULL AUTO_INCREMENT,
              tag text,
              type varchar(255),
              UNIQUE (id)
              ) $charset_collate;";
      include_once ABSPATH . 'wp-admin/includes/upgrade.php';
      dbDelta($query);

          //Conditional insert

          if(count(gtag_verifyTagsDB()) < 1)
          {
            $wpdb->insert($tags, array('tag' => '','type' => 'Global'));
          }
}

register_activation_hook(__FILE__, 'gtag_activate');

//Custom item menu 
  
function gtag_create_menu()
{
    add_menu_page(
    'Agregar Google Tags - Analytics y/o Adwords', //Title page
    'G-Tags Include', //Title menu
    'manage_options', //Capability
    'gtags_menu', //slug
    'gtag_template', //function name
    '', //icon
    '26' //position
    );
  }

add_action('admin_menu', 'gtag_create_menu');

//Scripts admin panel 

function gtag_load_plugins()
{

  $plugin_url = plugin_dir_url(__FILE__);

  wp_enqueue_style('styles_gtags', $plugin_url . 'css/style.css');
  wp_enqueue_script('custom_gtag', $plugin_url . 'js/main.js', array('jquery'), '1.0.0', true);

}

add_action('admin_enqueue_scripts', 'gtag_load_plugins');


//Update tags in db

function gtag_insert_tags()
{

  if(isset($_POST['g_tag'])){

    global $wpdb;
    $tags = $wpdb->prefix . 'google_tags';
    $charset_collate = $wpdb->get_charset_collate();
    
        $wpdb->update($tags, 
        array('tag' => $_POST['g_tag'],'type' => 'Global'), 
        array('type'=> 'Global'));
  }

}

//Template

function gtag_template()
{

  $g_tag = gtag_verifyTagsDB()[0]->tag;
  $plugin_url = plugin_dir_url(__FILE__);

    ?>

    <div class='container-gtag'> 

        <h2 class='text-primary'> Google tags plugin <img src='<?=esc_attr($plugin_url)?>connect.png' alt='icon connect' /> </h2>
        <p>
          Agregue las etiquetas de seguimiento de Google Analytics y/o conversiones de Google Ads en la etiqueta head del sitio.
        </p>
          <hr>
        <form method='POST' action='?page=gtags_menu' id='formTags'>
          <label for='analy'>Agregar Google tags:</label> <br>
          <textarea id='analy' class='mt-gtag text-area-gtag' name='g_tag'><?=esc_html($g_tag)?></textarea> <br>
            <div class='alert-success-gtag'> 
              Las etiquetas han sido agregadas correctamente 
            </div>
          <button class='mt-gtag btn-gtag' type='submit'>Guardar</button>
        </form>

          <p class='by-gtag'>
            Desarrollado por <a href='https://luisalbanese.com.ar' target='_blank'>Luis Albanese</a>
          </p>
    </div>
    
    <?php
}

//Listening and run actions 

gtag_insert_tags();

function gtag_global()
{
  echo gtag_verifyTagsDB()[0]->tag;
}

add_action('wp_head', 'gtag_global');



