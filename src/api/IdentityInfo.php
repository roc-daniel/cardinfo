<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/9/6
 * Time: 下午3:57
 */

namespace cardinfo\api;

class IdentityInfo
{
    private $_dbInstance = NULL;
    private $_errorMsg = '未知';

    public function __construct()
    {
        if (empty($this->_dbInstance)) {
            $db = require_once '../config/db.php';
            $dbConfig = $db['db'];
            $this->_dbInstance = new \medoo([
                'database_type' => $dbConfig['type'],
                'database_name' => $dbConfig['name'],
                'server' => $dbConfig['server'],
                'port' => $dbConfig['port'],
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
                'charset' => $dbConfig['charset'],
                'prefix' => $dbConfig['prefix'],
            ]);
        }
    }

    /**
     * 根据身份证号获取性别
     * getGender
     * @param $identityId
     *
     * @return string
     */
    public function getGender($identityId)
    {
        if (empty($identityId)) {
            return $this->_errorMsg;
        }

        $data = $this->splitIdentityId($identityId);
        if (!is_array($data) || empty($data)) {
            return $this->_errorMsg;
        }

        if (0 == $data['gender'] % 2) {
            return '女';
        } else {
            return '男';
        }
    }

    /**
     * 根据身份证号获取年龄
     * getAge
     * @param $identityId
     *
     * @return int|string
     */
    public function getAge($identityId)
    {
        if (empty($identityId)) {
            return $this->_errorMsg;
        }

        $data = $this->splitIdentityId($identityId);
        if (!is_array($data) || empty($data)) {
            return $this->_errorMsg;
        }

        $thisYear = intval(date('Y'));
        $birthday = $data['birthday'];
        if (8 == strlen($birthday)) {
            $year = intval(substr($birthday,0,4));
        } else {
            $year = intval('19' . substr($birthday,0,2));
        }

        return $age = $thisYear - $year + 1;
    }

    /**
     * 根据身份证号获取生日,并提供给格式信息
     * getBirthday
     * @param        $identityId
     * @param string $format
     *
     * @return bool|string
     */
    public function getBirthday($identityId, $format = 'Y-m-d')
    {
        if (empty($identityId)) {
            return $this->_errorMsg;
        }

        $data = $this->splitIdentityId($identityId);
        if (!is_array($data) || empty($data)) {
            return $this->_errorMsg;
        }

        $birthday = $data['birthday'];
        if (8 == strlen($birthday)) {
            $birthday = date($format, strtotime($birthday));
        } else {
            $birthday = date($format, strtotime('19' . $birthday));
        }
        return $birthday;
    }

    /**
     * 根据身份证号获取户籍信息
     * getAddress
     * @param $identityId
     *
     * @return string
     */
    public function getAddress($identityId)
    {
        if (empty($identityId)) {
            return $this->_errorMsg;
        }

        $data = $this->splitIdentityId($identityId);
        if (!is_array($data) || empty($data)) {
            return $this->_errorMsg;
        }

        $code = $data['code'];
        $result = $this->_dbInstance->select('identity_info',['address'],['code[=]' => $code]);
        if (empty($result)) {
            return $this->_errorMsg;
        }
        $result = $result[0];

        return $result['address'];
    }

    /**
     * 切分身份证号
     * splitIdentityId
     * @param $identityId
     *
     * @return array
     */
    private function splitIdentityId($identityId)
    {
        // 判断是15位还是18位的身份证号
        if (18 == strlen($identityId)) {
            $result = [
                'code' => substr($identityId,0,6),
                'birthday' => substr($identityId,6,8),
                'gender' => substr($identityId,16,1),
            ];
        } else {
            $result = [
                'code' => substr($identityId,0,6),
                'birthday' => substr($identityId,6,6),
                'gender' => substr($identityId,-1,1),
            ];
        }

        return $result;
    }
}