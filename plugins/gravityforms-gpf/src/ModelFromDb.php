<?php

namespace Greenpeacefrance\Gravityforms;


class ModelFromDb {


    private static $_instance = null;

	public $is_valid = false;
	public $tablename = "";

	public $data = [];


	private function __construct() {}


	public static function getInstance() {
        if ( self::$_instance == null ) {
            self::$_instance = new ModelFromDb();
        }

        return self::$_instance;
    }


	public function set($data) {
		foreach ($data as $key => $value) {
			$this->data[$key] = $value;
		}
	}
}