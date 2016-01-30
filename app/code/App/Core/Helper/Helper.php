<?php

namespace Chat\App\Core\Helper;

class Helper {

    public static function clean($value = "") {
        $value = trim($value);
        $value = stripslashes($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value);

        return $value;
    }

    public static function gravatarFromHash($hash, $size = 23) {
        return 'http://www.gravatar.com/avatar/' . $hash . '?size=' . $size . '&amp;default=' .
                urlencode('http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size=' . $size);
    }

}
