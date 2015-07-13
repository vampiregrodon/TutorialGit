<?php

ini_set("display_errors", 0);

include_once 'Config.php';
include_once 'LibMySQL.php';

function runSQL($sql) {
    $rs = MySQLQuery_($sql);
    $rows = array();
    if ($rs) {
        while ($row = mysql_fetch_object($rs)) {
            if($row->revenue_gross == "" || $row->revenue_gross == null){
                $row->revenue_gross = 0;
            }
            if($row->revenue_net == "" || $row->revenue_net == null){
                $row->revenue_net = 0;
            }
            $rows[] = $row;

            
        }
        @mysql_free_result($rs);
    }
    return $rows;
}

function runSQL_sms($sql,$database_name) {
    $rs = MySQLQuery_all($sql,$database_name);
    $rows = array();
    if ($rs) {
        while ($row = mysql_fetch_object($rs)) {
            if($row->revenue_gross == "" || $row->revenue_gross == null){
                $row->revenue_gross = 0;
            }
            if($row->revenue_net == "" || $row->revenue_net == null){
                $row->revenue_net = 0;
            }
            $rows[] = $row;

            
        }
        @mysql_free_result($rs);
    }
    return $rows;
}

function savefile($content, $name_file_log) {
    $path_log_file = "Logs";
    $file = $path_log_file . "/" . "API_SO6Payment_" . $name_file_log . ".json";
    $write_result = file_put_contents($file, $content, LOCK_EX);
}


function getRevenue($date_to,$gameID) {
    if($gameID == "CARO"){
        $database_name = "gametrans_MPCARO";
    }elseif($gameID == "10HA7"){
        $database_name = "gametrans_VC";
    }else{
        $database_name = "gametrans_$gameID";
    }

    $param = $date_to;
    $table = date("Ymd", strtotime($param));
    $table_sms = $gameID."_sms_" . $table;
    $table_sms_user = "sms_user_" . $table;
    $table_mcard =  "card123_verify_" . $table;
    $table_mcard_user =  "card123_user_" . $table;
    $table_zcard =  "cardzing_verify_" . $table;
    $table_zcard_user =  "cardzing_user_" . $table;
    $table_atm =  "atm_queryorder_" . $table;
    $table_atm_user =  "atm_createorder_" . $table;
    //////////////////////// ATM ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $list = array();    

    $sql_atm = "SELECT COUNT(t1.mTransactionID) as qty,SUM(t1.totalAmount) as revenue_gross,SUM(t1.opAmount) as revenue_net FROM $table_atm t1, $table_atm_user t2 WHERE t1.mTransactionID = t2.mTransactionID 
AND t1.transactionStatus = '1' AND t2.gameID = '$gameID' ";
        $row_atm = runSQL($sql_atm);
        

    $sql_zcard = "SELECT COUNT(t1.mTransactionID) as qty,SUM(t1.cardvalue) as revenue_gross,SUM(t1.cardvalue) as revenue_net FROM $table_zcard t1 WHERE t1.verify = '1' AND t1.gameID = '$gameID' ";
        $row_zcard = runSQL($sql_zcard);
        


    $sql_mcard = "SELECT COUNT(t1.mTransactionID) as qty,SUM(t1.grossAmount) as revenue_gross,SUM(t1.netAmount) as revenue_net FROM $table_mcard t1 WHERE t1.groupResponseCode = '1' AND t1.gameID = '$gameID' ";
        $row_mcard = runSQL($sql_mcard);
        

    $sql_sms = "SELECT COUNT(t1.requestid) as qty,SUM(t1.money) as revenue_gross,SUM(t1.net_money) as revenue_net FROM $table_sms t1 
 ";
 
        $row_sms = runSQL_sms($sql_sms,$database_name);
           
        
      
    $list['atm'] =  $row_atm;
    $list['zcard'] =  $row_zcard;
    $list['mcard'] =  $row_mcard;
    $list['sms'] =  $row_sms;
    
    // $result = json_encode($list);
    // savefile($result, $name_file_log); 
    return $list;

}


// $min = $argv[1];

$time = time() - 86400;
$name_file_log = date("Ymd", $time);

$date_to = date("Y-m-d", $time);


$game_id = array(
            "FH",
            "SGMB",
            "FARM",
            "MPCOTUONG",
            "CARO",
            "MPPOKER",
            "MPTALA",
            "MPBINH",
            "MPTIENLEN",
            "MPXITO",
            "ZINZIN",
            "THOILOAN",
            "LM",
            "10HA7",
            "CANDYRUN",
            "VLMB",
            "ZB",
            "CUUTOC",
            "FARMWEB",
            "WC2014",
            "ICA",
        );

$re = array();
foreach ($game_id as $value) {
    $re[$value] = getRevenue($date_to,$value);
}

$re = json_encode($re);
savefile($re, $name_file_log);
die;



// for ($i = 1; $i <= 30; $i++) {
//    $date_to = "2015-06-" . sprintf("%02d", $i);
//    $name_file_log = "201506" . sprintf("%02d", $i); ;
//    getRevenue($date_to,$name_file_log);
// }
?>
