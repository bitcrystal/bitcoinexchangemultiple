<?php
require_once('coins.php');
$GLOBALS['cp0']=$my_coins->coins_names_prefix[0];
$GLOBALS['cp2']=$my_coins->coins_names_prefix[2];
$GLOBALS['cp1']=$my_coins->coins_names_prefix[1];
function security($value) {
   if(is_array($value)) {
      $value = array_map('security', $value);
   } else {
      if(!get_magic_quotes_gpc()) {
         $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
      } else {
         $value = htmlspecialchars(stripslashes($value), ENT_QUOTES, 'UTF-8');
      }
      $value = str_replace("\\", "\\\\", $value);
   }
   return $value;
}

function apikeygen() {
   $keygen_characters = "0011223344--__5566778899aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzzAABBCCDDEEF--__FGGHHIIJJKKLLMMNNOOPPQQRRSSTUUVVWWXXYYZZ";
   $keygen_key = "";
   $keygen_length = rand(40, 60);
   for($keygen_i = 0; $keygen_i < $keygen_length; $keygen_i++) {
      $keygen_key .= $keygen_characters[rand(0, strlen($keygen_characters) - 1)];
   }
   return $keygen_key;
}

function satoshitize($satoshitize) {
   return sprintf("%.8f", $satoshitize);
}

function satoshitrim($satoshitrim) {
   return rtrim(rtrim($satoshitrim, "0"), ".");
}

function userbalance($function_user,$function_coin) {
   if($function_coin==$GLOBALS['cp0']) {
      $function_query = mysql_query("SELECT coin1 FROM balances WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'");
      while($function_row = mysql_fetch_assoc($function_query)) { $function_return = $function_row['coin1']; }
   }
   if($function_coin==$GLOBALS['cp2']) {
      $function_query = mysql_query("SELECT coin2 FROM balances WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'");
      while($function_row = mysql_fetch_assoc($function_query)) { $function_return = $function_row['coin2']; }
   }
   if($function_coin==$GLOBALS['cp1']) {
      $function_query = mysql_query("SELECT coin3 FROM balances WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'");
      while($function_row = mysql_fetch_assoc($function_query)) { $function_return = $function_row['coin3']; }
   }
   return $function_return;
}

function buyrate($function_coin, $function_coin2) {
   $function_query = mysql_query("SELECT rate FROM buy_orderbook WHERE want='$function_coin' and processed='1' and trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."' AND trade_with = '$function_coin2' ORDER BY rate DESC LIMIT 1");
   while($function_row = mysql_fetch_assoc($function_query)) {
      $function_return = $function_row['rate'];
   }
   return $function_return;
}

function sellrate($function_coin,$function_coin2) {
   $function_query = mysql_query("SELECT rate FROM sell_orderbook WHERE want='$function_coin' and processed='1' and trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."' AND trade_with = '$function_coin2' ORDER BY rate ASC LIMIT 1");
   while($function_row = mysql_fetch_assoc($function_query)) {
      $function_return = $function_row['rate'];
   }
   return $function_return;
}

function plusfunds($function_user,$function_coin,$function_amount) {
   $function_user_balance = userbalance($function_user,$function_coin);
   $function_balance = $function_user_balance + $function_amount;
   $function_balance = satoshitrim(satoshitize($function_balance));
   if($function_coin==$GLOBALS['cp0']) { $sql = "UPDATE balances SET coin1='$function_balance' WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'"; }
   if($function_coin==$GLOBALS['cp2']) { $sql = "UPDATE balances SET coin2='$function_balance' WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'"; }
   if($function_coin==$GLOBALS['cp1']) { $sql = "UPDATE balances SET coin3='$function_balance' WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'"; }
   $result = mysql_query($sql);
   if($result) {
      $function_return = "success";
   } else {
      $function_return = "error";
   }
   return $function_return;
}

function minusfunds($function_user,$function_coin,$function_amount) {
   $function_user_balance = userbalance($function_user,$function_coin);
   $function_balance = $function_user_balance - $function_amount;
   $function_balance = satoshitrim(satoshitize($function_balance));
   if($function_coin==$GLOBALS['cp0']) { $sql = "UPDATE balances SET coin1='$function_balance' WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'"; }
   if($function_coin==$GLOBALS['cp2']) { $sql = "UPDATE balances SET coin2='$function_balance' WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'"; }
   if($function_coin==$GLOBALS['cp1']) { $sql = "UPDATE balances SET coin3='$function_balance' WHERE username='$function_user' AND trade_id = '".$GLOBALS['cp0']."_".$GLOBALS['cp1']."_".$GLOBALS['cp2']."'"; }
   $result = mysql_query($sql);
   if($result) {
      $function_return = "success";
   } else {
      $function_return = "error";
   }
   return $function_return;
}

function get_current_url()
{
	return 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}

function get_root_path($file)
{
	$str = dirname(__FILE__);
	$str = str_replace("ajax","",$str);
	$str = $str . '/' . $file;
	return $str;
}
?>