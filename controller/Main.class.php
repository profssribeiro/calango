<?php

class Main{
	
	public function show(){
		return Html::load(App::$key.".html");
	}

}