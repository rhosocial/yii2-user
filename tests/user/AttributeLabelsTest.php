<?php

/**
 *  _   __ __ _____ _____ ___  ____  _____
 * | | / // // ___//_  _//   ||  __||_   _|
 * | |/ // /(__  )  / / / /| || |     | |
 * |___//_//____/  /_/ /_/ |_||_|     |_|
 * @link https://vistart.name/
 * @copyright Copyright (c) 2016 vistart
 * @license https://vistart.name/license/
 */

namespace rhosocial\user\tests\user;

use rhosocial\user\tests\TestCase;
use rhosocial\user\tests\data\User;

/**
 * Description of AttributeLabelsTest
 *
 * @author vistart <i@vistart.name>
 */
class AttributeLabelsTest extends TestCase {

    public function testGet() {
        $user = new User();
        $this->assertNotEmpty($user->attributeLabels());
    }

}
