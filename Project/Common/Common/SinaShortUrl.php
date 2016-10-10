<?php
	/**
	 * Create by PhpStorm
	 * User: 0967
	 * Time: 2016-10-8 16:28
	 */
	header("Content-type:text/html; charset=utf-8");
	function getShortUrl($url){
		$result = file_get_contents("http://api.t.sina.com.cn/short_url/shorten.json?source=3271760578&url_long=$url");
		$result = json_decode($result);
		
		return $result[0]->url_short;
	}