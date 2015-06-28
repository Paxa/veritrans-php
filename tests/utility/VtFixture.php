<?php
class VtFixture {
	public static function read($filename){
		return file_get_contents(__DIR__ . '/fixture/' . $filename);
	}

	public static function build($filename, $arguments) {
		$json_string = self::read($filename);
		$json_object = json_decode($json_string, true);
		return array_replace_recursive($json_object, $arguments);
	}
}