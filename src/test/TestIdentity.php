<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/9/6
 * Time: 下午4:47
 */

require '../../vendor/autoload.php';

use cardinfo\api\IdentityInfo;

$identity = new IdentityInfo();
echo $identity->getGender('130503670401001') . PHP_EOL;
echo $identity->getGender('211303198902130018') . PHP_EOL;
echo $identity->getAge('211303198902130018') . PHP_EOL;
echo $identity->getAge('130503670401001') . PHP_EOL;
echo $identity->getBirthday('211303198902130018') . PHP_EOL;
echo $identity->getBirthday('130503670401001') . PHP_EOL;
echo $identity->getAddress('211303198902130018') . PHP_EOL;
echo $identity->getAddress('130503670401001') . PHP_EOL;