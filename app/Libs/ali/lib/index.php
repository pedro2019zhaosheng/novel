<?php

switch ($_REQUEST['act']) {
    case "alipay":
    $detail_data = '';
    $items = Withdrawals::factory()->getWithdrawals();
    if (empty($items)) {
        echo "暂无提现申请";
    }

    $batch_fee = 0;
    $batch_num = 0;
    foreach ($items as $key => $value) {
        $mid = $value['mid'];
        $aliuser = Member::factory()->getAliuser($mid);
        if ($aliuser) {
           $detail_data .= "00$key^{$aliuser['alipay']}^{$aliuser['aliname']}^{$value['pamount']}^{$value['pamount']}|"; 
           $batch_fee += $value['pamount'];
           $batch_num++;
        }
    }

    require_once("alipay.config.php");
    require_once("lib/alipay_submit.class.php");

    $batch_no = date("Ymd") . (time() % 86400);
    $notify_url = "http://127.0.0.1/notify_url.php";
    $pay_date = date("Ymd");
    $parameter = array(
            "service" => "batch_trans_notify",
            "partner" => trim($alipay_config['partner']),
            "notify_url"    => $notify_url,
            "email" => $alipay_config['email'],
            "account_name"  => $alipay_config['account_name'],
            "pay_date"  => $pay_date,
            "batch_no"  => $batch_no,
            "batch_fee" => $batch_fee,
            "batch_num" => $batch_num,
            "detail_data"   => $detail_data,
            "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
    );

    //建立请求
    $alipaySubmit = new AlipaySubmit($alipay_config);
    $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
    echo $html_text;
    break;
}