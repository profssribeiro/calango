<?php

class Datatable{
	public function show(){
		return Html::load(App::$key.".html");
	}
	public function get_users(){
		$tb_usuario = new Model("user");
		return $tb_usuario->get_json();
	}
	public function insert_user(){
		return json_encode(array('success'=>true));
	}
	public function update_user(){
		return json_encode(array('success'=>true));
	}
	public function remove_user(){
		return json_encode(array('success'=>true));
	}
}