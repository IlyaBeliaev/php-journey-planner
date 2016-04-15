<?php

namespace LJN;

use Assertis\Util\ObjectList;

class Route extends ObjectList {

    public function accepts($object) {
        return $object instanceof Connection;
    }

}
