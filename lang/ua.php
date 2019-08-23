<?php

function lang($str){
    $lang = array(
	"success" => "Ваш платіж успішно зараховано!"
    );
    if(isset($lang[$str]))
	return $lang[$str];
    else
	return $str;	
}
