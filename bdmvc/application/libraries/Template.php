<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MY_Loader
 *
 * @author formation
 */

class Template {

  var $ci;

  function __construct(){
    $this->ci =& get_instance();
}

  public function load($view, $data = array(), $return = FALSE){

    $this->ci->load->view("template/header",$data,$return);
    $this->ci->load->view($view .'.php',$data,$return);
    $this->ci->load->view("template/footer",$data, $return);

  }

}



?>