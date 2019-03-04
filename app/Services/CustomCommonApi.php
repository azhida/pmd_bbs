<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

// 自定义 常用第三方平台api（只针对 短小api，强大的api不宜写在此处）
class CustomCommonApi {
    // 短信宝--短信平台
    public function sendDXBSms($phone = '', $sms_content = '')
    {
        $user = env('DXB_USER', ''); // 短信平台帐号
        $pass = env('DXB_PASS', ''); // 短信平台密码
        if (!$phone) {
            return ['code' => 1, 'msg' => '手机号码必填'];
        }
        if (!$sms_content) {
            return ['code' => 1, 'msg' => '短信内容必填'];
        }
        if (!$user) {
            return ['code' => 1, 'msg' => '短信系统账号不存在'];
        }
        if (!$pass) {
            return ['code' => 1, 'msg' => '短信系统密码不存在'];
        }

        $statusStr = [
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        ];
        $smsapi = "http://api.smsbao.com/";
        $sendurl = $smsapi."sms?u=".$user."&p=".md5($pass)."&m=".$phone."&c=".urlencode($sms_content);
        $result = file_get_contents($sendurl);
        // 记录已请求发送的记录
        Log::error('短信宝发送短信： $phone = ' . $phone . ' , $sms_content = ' . $sms_content . ' ; 发送结果：msg = ' . $statusStr[$result]  . ' , send_result_code = ' . $result);
        return ['code' => 0, 'msg' => $statusStr[$result], 'send_result_code' => $result];
    }

    /**
     * 阿里云的api：银行卡实名认证（银行卡二、三、四元素实名认证【无缓存、纯实时】）
     * 验证银行卡和开户名是否匹配
     * @param string $bank_name 银行卡开户姓名，必填
     * @param string $bank_no 银行卡号，必填
     * @param string $cert_id 身份证号码，可选
     * @param string $phone_num 预留手机号码，可选
     * @return array
     */
    function aliCloudApiVerifyBank($bank_name = '', $bank_no = '', $cert_id = '', $phone_num = ''){
        $params = [
            'bank_name' => $bank_name,
            'bank_no' => $bank_no,
            'cert_id' => $cert_id,
            'phone_num' => $phone_num,
        ];
        Log::error('阿里云银行卡实名认证 -- 请求参数：$params = ' . json_encode($params));

        if (!$bank_name || !$bank_no) return ['code' => 1, 'msg' => '开卡姓名和卡号必填'];
        $apiurl = "http://lundroid.market.alicloudapi.com/lianzhuo/verifi";
        $appcode = env('ALICLOUDAPI_VERIFYBANK_APPCODE'); // 正式
        $headers = [];
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $apiurl .= "?acct_name=" . urlencode($bank_name) . "&acct_pan=" . $bank_no;
        if ($cert_id) $apiurl .= '&cert_id=' . $cert_id;
        if ($phone_num) $apiurl .= '&phone_num=' . $phone_num;

        // curl请求 -- start
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_URL, $apiurl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) {
            $body =  "cURL Error #:" . $err;
        } else {
            if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == '200') {
                $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $headerSize);
                $body = substr($response, $headerSize);
            }
        }
        curl_close($curl);
        // curl请求 -- end

        $res = json_decode($body, true);

        Log::error('阿里云银行卡实名认证 -- 返回结果：$res = ' . json_encode($res));

        if($res['resp']['code'] != 0){
            return ['code' => 1, 'msg' => $res['resp']['desc']];
        }else if(!isset($res)) {
            return ['code' => 1, 'msg' => '银行卡填写有问题'];
        }else{
            return ['code' => 0, 'msg' => '验证通过', 'data' => $res['data']];
        }
    }

}




