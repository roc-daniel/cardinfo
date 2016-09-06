<?php
/**
 * 抓取身份证号对应的信息
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/9/6
 * Time: 上午11:22
 */
use cardinfo\lib\curl;

class Spider
{
    public static function main()
    {
        $info = require_once '../config/info.php';
        if (!isset($info['cities'])) {
            echo 'no cities';
            exit(0);
        }
        if (!isset($info['url'])) {
            echo 'no url';
            exit(0);
        }

        $cities = $info['cities'];
        $url = $info['url'];
        $count = count($cities);
        $contents = [];
        $sqlData = [];
        for($i = 0; $i < $count; $i++) {
            $now = date('Y-m-d H:i:s');
            $curl = new Curl();
            $city = iconv('UTF-8','GBK', $cities[$i]);
            $params = 'describe=' . urlencode($city) . '&Submit=%B2%E9%D1%AF';
            $data = $curl->post($url,$params);
            $data = iconv('GBK','UTF-8',$data);
            preg_match_all('/CopyCode\(1,(.*)\)/', $data, $out);
            $result = $out[1];
            $count = count($result);
            for ($j = 0; $j < $count; $j++) {
                $str = $result[$j];
                $tmp = explode(',', $str);
                if (!isset($contents[$tmp[0]])) {
                    $contents[$tmp[0]] = $str;
                    preg_match('/\'(.*)\'/',$tmp[0], $output0);
                    $code = $output0[1];
                    preg_match('/\'(.*)\'/',$tmp[1], $output1);
                    $address = $output1[1];
                    $tmpData = [
                        'id' => NULL,
                        'code' => $code,
                        'address' => $address,
                        'update_at' => $now,
                        'create_at' => $now,
                    ];
                    array_push($sqlData, $tmpData);
                }
            }
        }

        print_r($sqlData);

        $db = require_once '../config/db.php';
        $dbConfig = $db['db'];
        $medoo = new medoo([
            'database_type' => $dbConfig['type'],
            'database_name' => $dbConfig['name'],
            'server' => $dbConfig['server'],
            'port' => $dbConfig['port'],
            'username' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'charset' => $dbConfig['charset'],
            'prefix' => $dbConfig['prefix'],
        ]);

        $medoo->pdo->beginTransaction();
        $medoo->insert('identity_info', $sqlData);
        $medoo->pdo->commit();

        print_r($medoo->error());
    }
}

require '../../vendor/autoload.php';
Spider::main();