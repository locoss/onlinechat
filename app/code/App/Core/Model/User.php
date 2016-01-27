<?php

namespace Chat\App\Core\Model;

use Chat\Framework\Db\DB as DB;

class User {

    protected $name = '', $gravatar = '';

    public function __construct(array $options) {

        foreach ($options as $k => $v) {
            if (isset($this->$k)) {
                $this->$k = $v;
            }
        }
    }

    public function save() {
        DB::query("
			INSERT INTO users (name, gravatar)
			VALUES (
				'" . DB::esc($this->name) . "',
				'" . DB::esc($this->gravatar) . "'
		)");

        return DB::getMySQLiObject();
    }

    public function update() {
        DB::query("
			INSERT INTO users (name, gravatar)
			VALUES (
				'" . DB::esc($this->name) . "',
				'" . DB::esc($this->gravatar) . "'
			) ON DUPLICATE KEY UPDATE last_activity = NOW()");
    }

}
