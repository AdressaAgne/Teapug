<?php

function dd(...$param){
	// @: Header will output a notice if there is an error already,
	// since we can not send the header after text has been sent.
	// So we remove all error logging from the header()
	@header('Content-type: application/json');
	die(print_r($param, true));
}

/*
	Hash string with PASSWORD_DEFAULT
 */
function bcrypt($str){
	return password_hash($str, PASSWORD_DEFAULT);
}

/*
	Return a View::make
 */
function view($file, $vars = null){
	return View::make($file, $vars);
}