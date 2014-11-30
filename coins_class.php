<?php
require_once 'jsonRPCClient.php';
require_once 'w_coins_settings.php';
class w_coins {
	private $my_w;
	public $coins_names;
	public $coins_names_prefix;
	private $coins_count;
	public $coins;
	private $enabled_coins;
	private $enabled_coins_count;
	private $is_enabled_coins;
	private $is_enabled_default_coins;
	private $current_trade_coin_names;
	private $current_trade_coin_names_prefix;
	private $current_trade_from_coin_prefix;
	private $current_trade_from_coin_name;
	private $current_trade_to_coin_prefix;
	private $current_trade_to_coin_name;
	public $trade_coins;
	private $instance_id;
	private $select_instance_id;
	private static $SINGLETON = NULL;
	
	private function __construct() {
		$instance_id = 0;
		$select_instance_id = 0;
		$this->my_w = new w_coins_settings();
		$this->coins = array();
		$this->coins_names = array();
		$this->coins_names_prefix = array();
		
		$this->coins["create_feebee_account"]=$this->my_w->coins["create_feebee_account"];
		$this->coins_names[0]=$this->my_w->coins["coin_name_1"];
		$this->coins_names[1]=$this->my_w->coins["coin_name_2"];
		$this->coins_names[2]=$this->my_w->coins["coin_name_3"];

		$this->coins_names_prefix[0]=$this->my_w->coins["coin_prefix_1"];
		$this->coins_names_prefix[1]=$this->my_w->coins["coin_prefix_2"];
		$this->coins_names_prefix[2]=$this->my_w->coins["coin_prefix_3"];
		$this->coins_count=count($this->coins_names);
		
		$this->coins[$this->coins_names[0]] = array();
		$this->coins[$this->coins_names[0]]["enabled"]=false;
		$this->coins[$this->coins_names[0]]["daemon"]=false;
		$this->coins[$this->coins_names[0]]["rpcsettings"]=array();
		$this->coins[$this->coins_names[0]]["fee"]=$this->my_w->coins["coin_fee_1"];
		$this->coins[$this->coins_names[0]]["FEEBEE"]=$this->my_w->coins["coin_feebee_1"];
		$this->coins[$this->coins_names[0]]["buy_fee"]=$this->my_w->coins["coin_buy_fee_1"];
		$this->coins[$this->coins_names[0]]["sell_fee"]=$this->my_w->coins["coin_sell_fee_1"];
		
		$this->coins[$this->coins_names[1]] = array();
		$this->coins[$this->coins_names[1]]["enabled"]=false;
		$this->coins[$this->coins_names[1]]["daemon"]=false;
		$this->coins[$this->coins_names[1]]["rpcsettings"]=array();
		$this->coins[$this->coins_names[1]]["fee"]=$this->my_w->coins["coin_fee_2"];
		$this->coins[$this->coins_names[1]]["FEEBEE"]=$this->my_w->coins["coin_feebee_2"];
		$this->coins[$this->coins_names[1]]["buy_fee"]=$this->my_w->coins["coin_buy_fee_2"];
		$this->coins[$this->coins_names[1]]["sell_fee"]=$this->my_w->coins["coin_sell_fee_2"];
		
		$this->coins[$this->coins_names[2]] = array();
		$this->coins[$this->coins_names[2]]["enabled"]=false;
		$this->coins[$this->coins_names[2]]["daemon"]=false;
		$this->coins[$this->coins_names[2]]["rpcsettings"]=array();
		$this->coins[$this->coins_names[2]]["fee"]=$this->my_w->coins["coin_fee_3"];
		$this->coins[$this->coins_names[2]]["FEEBEE"]=$this->my_w->coins["coin_feebee_3"];
		$this->coins[$this->coins_names[2]]["buy_fee"]=$this->my_w->coins["coin_buy_fee_3"];
		$this->coins[$this->coins_names[2]]["sell_fee"]=$this->my_w->coins["coin_sell_fee_3"];
		
		$coin0rpc = $this->my_w->coins[$this->coins_names[0]]["rpcsettings"];
		$coin1rpc = $this->my_w->coins[$this->coins_names[1]]["rpcsettings"];
		$coin2rpc = $this->my_w->coins[$this->coins_names[2]]["rpcsettings"];
		
		$this->coins[$this->coins_names[0]]["rpcsettings"]["user"]=$coin0rpc["user"];
		$this->coins[$this->coins_names[0]]["rpcsettings"]["pass"]=$coin0rpc["pass"];
		$this->coins[$this->coins_names[0]]["rpcsettings"]["host"]=$coin0rpc["host"];
		$this->coins[$this->coins_names[0]]["rpcsettings"]["port"]=$coin0rpc["port"];
		$this->coins[$this->coins_names[0]]["rpcsettings"]["walletpassphrase"]=$coin0rpc["walletpassphrase"];
		$this->coins[$this->coins_names[0]]["rpcsettings"]["walletpassphrase_timeout"]=$coin0rpc["walletpassphrase_timeout"];

		$this->coins[$this->coins_names[1]]["rpcsettings"]["user"]=$coin1rpc["user"];
		$this->coins[$this->coins_names[1]]["rpcsettings"]["pass"]=$coin1rpc["pass"];
		$this->coins[$this->coins_names[1]]["rpcsettings"]["host"]=$coin1rpc["host"];
		$this->coins[$this->coins_names[1]]["rpcsettings"]["port"]=$coin1rpc["port"];
		$this->coins[$this->coins_names[1]]["rpcsettings"]["walletpassphrase"]=$coin1rpc["walletpassphrase"];
		$this->coins[$this->coins_names[1]]["rpcsettings"]["walletpassphrase_timeout"]=$coin1rpc["walletpassphrase_timeout"];

		$this->coins[$this->coins_names[2]]["rpcsettings"]["user"]=$coin2rpc["user"];
		$this->coins[$this->coins_names[2]]["rpcsettings"]["pass"]=$coin2rpc["pass"];
		$this->coins[$this->coins_names[2]]["rpcsettings"]["host"]=$coin2rpc["host"];
		$this->coins[$this->coins_names[2]]["rpcsettings"]["port"]=$coin2rpc["port"];
		$this->coins[$this->coins_names[2]]["rpcsettings"]["walletpassphrase"]=$coin2rpc["walletpassphrase"];
		$this->coins[$this->coins_names[2]]["rpcsettings"]["walletpassphrase_timeout"]=$coin2rpc["walletpassphrase_timeout"];
		
		$this->enabled_coins=array();
		
		$this->enabled_coins[0]=$this->coins_names[0];
		$this->enabled_coins[1]=$this->coins_names[1];
		$this->enabled_coins[2]=$this->coins_names[2];

		$this->enabled_coins_count = count($this->enabled_coins);

		$this->is_enabled_coins=false;
		$this->is_enabled_default_coins=false;

		$this->current_trade_coin_names = array();
		
		$this->current_trade_coin_names[0]=$this->coins_names[0];
		$this->current_trade_coin_names[1]=$this->coins_names[1];

		$this->current_trade_coin_names_prefix = array();
		
		$this->current_trade_coin_names_prefix[0]=$this->coins_names_prefix[0];
		$this->current_trade_coin_names_prefix[1]=$this->coins_names_prefix[1];

		$this->current_trade_from_coin_prefix=$this->coins_names_prefix[0];
		$this->current_trade_from_coin_name=$this->coins_names[0];
		$this->current_trade_to_coin_prefix=$this->coins_names_prefix[1];
		$this->current_trade_to_coin_name=$this->coins_names[1];
		
		$this->trade_coins=array();
		$this->trade_coins["BTCRY"] = array();
		$this->trade_coins["BTCRY"]["BTC"]= $this->current_trade_from_coin_prefix;
		$this->trade_coins["BTCRY"]["BTCRYX"]= $this->current_trade_to_coin_prefix;
		$this->trade_coins["BTCRY"]["BTCS"]= $this->current_trade_from_coin_name;
		$this->trade_coins["BTCRY"]["BTCRYXS"]= $this->current_trade_to_coin_name;
		$this->trade_coins["BTCRYX"] = array();
		$this->trade_coins["BTCRYX"]["BTC"]= $this->current_trade_from_coin_prefix;
		$this->trade_coins["BTCRYX"]["BTCRYX"]= $this->current_trade_to_coin_prefix;
		$this->trade_coins["BTCRYX"]["BTCS"]= $this->current_trade_from_coin_name;
		$this->trade_coins["BTCRYX"]["BTCRYXS"]= $this->current_trade_to_coin_name;
		$this->enable_default_coins();
	}
	
	public function set_current_from_trade_coin_prefix_and_name($prefix, $name)
	{
		$this->current_trade_from_coin_prefix=$prefix;
		$this->current_trade_from_coin_name=$name;
		$this->trade_coins["BTCRY"]["BTC"]= $this->current_trade_from_coin_prefix;
		$this->trade_coins["BTCRY"]["BTCS"]= $this->current_trade_from_coin_name;
		$this->trade_coins["BTCRYX"]["BTC"]= $this->current_trade_from_coin_prefix;
		$this->trade_coins["BTCRYX"]["BTCS"]= $this->current_trade_from_coin_name;
	}

	public function set_current_to_trade_coin_prefix_and_name($prefix, $name)
	{
		$this->current_trade_to_coin_prefix=$prefix;
		$this->current_trade_to_coin_name=$name;
		$this->trade_coins["BTCRY"]["BTCRYX"]= $this->current_trade_to_coin_prefix;
		$this->trade_coins["BTCRY"]["BTCRYXS"]= $this->current_trade_to_coin_name;
		$this->trade_coins["BTCRYX"]["BTCRYX"]= $this->current_trade_to_coin_prefix;
		$this->trade_coins["BTCRYX"]["BTCRYXS"]= $this->current_trade_to_coin_name;
	}

	public function get_current_from_trade_coin_prefix_and_name(&$prefix, &$name)
	{
		$prefix=$this->current_from_trade_coin_prefix;
		$name=$this->current_from_trade_coin_name;
	}

	public function get_current_to_trade_coin_prefix_and_name(&$prefix, &$name)
	{
		$prefix=$this->current_to_trade_coin_prefix;
		$name=$this->current_to_trade_coin_name;
	}

	public function get_coins_prefix_of_name($name)
	{
		for($i=0; $i < $this->coins_count; $i++)
		{
			if($this->coins_names[$i]==$name)
			{
				return $this->coins_names_prefix[$i];
			}
		}
		return "unknown";
	}

	public function get_coins_name_of_prefix($prefix)
	{
		for($i=0; $i < $this->coins_count; $i++)
		{
			if($this->coins_names_prefix[$i]==$prefix)
			{
				return $this->coins_names[$i];
			}
		}
		return "unknown";
	}
	
	public function get_coins_walletpassphrase_of_name($name)
	{
		if($this->coins[$name]["rpcsettings"]["walletpassphrase"]=="")
			return "";
		return $this->coins[$name]["rpcsettings"]["walletpassphrase"];	
	}
	
	public function get_coins_walletpassphrase_timeout_of_name($name)
	{
		return $this->coins[$name]["rpcsettings"]["walletpassphrase_timeout"];
	}

	public function set_coins_daemon($name, $rpc_user, $rpc_pass, $rpc_host, $rpc_port)
	{
		if($this->coins[$name]["enabled"]==false)
		{
			return false;
		} else {
			if($this->coins[$name]["daemon"]==false)
			{
				$url = "http://".$rpc_user.":".$rpc_pass."@".$rpc_host.":".$rpc_port."/";
				$this->coins[$name]["daemon"]=new jsonRPCClient($url);
			}
			$walletpassphrase=$this->get_coins_walletpassphrase_of_name($name);
			if($walletpassphrase!="")
			{
				$walletpassphrase_timeout=$this->get_coins_walletpassphrase_timeout_of_name($name);
				$this->coins[$name]["daemon"]->walletpassphrase($walletpassphrase, $walletpassphrase_timeout);
			}
			return true;
		}	
	}

	public function get_coins_daemon_safe($name, $rpc_user, $rpc_pass, $rpc_host, $rpc_port)
	{
		$rv=set_coins_daemon($name, $rpc_user, $rpc_pass, $rpc_host, $rpc_port);
		if($rv==true)
		{
			$walletpassphrase=$this->get_coins_walletpassphrase_of_name($name);
			if($walletpassphrase!="")
			{
				$walletpassphrase_timeout=$this->get_coins_walletpassphrase_timeout_of_name($name);
				$this->coins[$name]["daemon"]->walletpassphrase($walletpassphrase, $walletpassphrase_timeout);
			}
			return $this->coins[$name]["daemon"];
		}
	}

	public function get_coins_daemon($name)
	{
		if($this->coins[$name]["enabled"] == false || $this->coins[$name]["daemon"] == false)
		{
			return false;
		} else {
			$walletpassphrase=$this->get_coins_walletpassphrase_of_name($name);
			if($walletpassphrase!="")
			{
				$walletpassphrase_timeout=$this->get_coins_walletpassphrase_timeout_of_name($name);
				$this->coins[$name]["daemon"]->walletpassphrase($walletpassphrase, $walletpassphrase_timeout);
			}
			return $this->coins[$name]["daemon"];
		}
	}

	public function get_coins_balance_old($name, $user_session)
	{
		if($this->coins[$name]["enabled"]==false)
			return "";
		return userbalance($user_session,$this->get_coins_prefix_of_name($name));
	}
	
	public function get_coins_balance($name, $account, $confirmations)
	{
		if($confirmations<=0)
		{
			return false;
		}
		$daemon = $this->get_coins_daemon($name);
		$transactions = $daemon->listtransactions($account);
		$money = 0;
		foreach($transactions as $transaction) {
			if($transaction['category']=="receive") {
				if($confirmations<=$transaction['confirmations']) {
					$amount = abs($transaction['amount']);
					$money+=$amount;
				} else {
					return false;
				}
			} else if ($transaction['category']=="send") {
				if($confirmations<=$transaction['confirmations']) {
					$amount = abs($transaction['amount']);
					$money -= $amount;
				} else {
					return false;
				}
			}
         }
		 return $money;
	}
	
	public function set_coins_balance($name, $account, $account2, $amount)
	{
		$balance=$this->get_coins_balance($name, $account, 6);
		$daemon = $this->get_coins_daemon($name);
		if($balance==false)
		{
			return false;
		}
		if($balance<0)
		{
			return false;
		}
		if($amount<0)
		{
			return false;		
		}
		$diff=$amount-$balance;
		if($diff==0)
		{
			return $balance;
		}
		if($diff<0)
		{
			$daemon->sendtoaddress($daemon->getaccountaddress($account2), abs($diff));
			
			$balance = $this->get_coins_balance($name, $account, 6);
			while($balance == false)
			{
				$balance = $this->get_coins_balance($name, $account, 6);
			}
			return $balance;
		}
		else
		{
			$daemon->sendtoaddress($daemon->getaccountaddress($account), $diff);
			$balance = $this->get_coins_balance($name, $account, 6);
			while($balance == false)
			{
				$balance = $this->get_coins_balance($name, $account, 6);
			}
			return $balance;
		}
		return $balance;
	}

	public function get_coins_balance_from_prefix($prefix, $user_session)
	{
		if($this->coins[$this->get_coins_name_of_prefix($prefix)]["enabled"]==false)
			return "";
		return userbalance($user_session,$prefix);
	}

	public function get_coins_address($name, $wallet_id)
	{
		if($this->coins[$name]["enabled"]==false)
			return "";
		$daemon = $this->get_coins_daemon($name);
		if($daemon==false)
		{
			return "";
		}
		return $daemon->getaccountaddress($wallet_id);
	}

	public function get_coins_address_from_prefix($prefix, $wallet_id)
	{
		$name=$this->get_coins_name_of_prefix($prefix);
		if($this->coins[$name]["enabled"]==false)
			return "";
		$daemon = $this->get_coins_daemon($name);
		if($daemon==false)
		{
				return "";
		}
		return $daemon->getaccountaddress($wallet_id);
	}


	private function enable_coins()
	{
		if($this->is_enabled_coins==true)
			return;

		for($i=0; $i < $this->enabled_coins_count; $i++)
		{
			for($j = 0; $j < $this->coins_count; $j++)
			{
				if($this->coins_names[$j]==$this->enabled_coins[$i])
				{
					$name = $this->coins_names[$j];
					$rpc_user = $this->coins[$name]["rpcsettings"]["user"];
					$rpc_pass = $this->coins[$name]["rpcsettings"]["pass"];
					$rpc_host = $this->coins[$name]["rpcsettings"]["host"];
					$rpc_port = $this->coins[$name]["rpcsettings"]["port"];
					$this->coins[$name]["enabled"]=true;
					$this->set_coins_daemon($name, $rpc_user, $rpc_pass, $rpc_host, $rpc_port);
					$j = $this->coins_count;
				}
			}
		}
		$this->is_enabled_coins=true;
	}

	private function enable_default_coins()
	{
		if($this->is_enabled_default_coins==true)
			return;

		for($i = 0; $i < $this->coins_count; $i++)
		{
			$name = $this->coins_names[$i];
			$rpc_user = $this->coins[$name]["rpcsettings"]["user"];
			$rpc_pass = $this->coins[$name]["rpcsettings"]["pass"];
			$rpc_host = $this->coins[$name]["rpcsettings"]["host"];
			$rpc_port = $this->coins[$name]["rpcsettings"]["port"];
			$this->coins[$name]["enabled"]=true;
			$this->set_coins_daemon($name, $rpc_user, $rpc_pass, $rpc_host, $rpc_port);
		}
		$this->is_enabled_default_coins=true;
	}
	
	public function outputCoinButtonLinks($website)
	{
		for($i=0;$i < $this->coins_count; $i+=3)
		{
			if($i/3>0)
					$iid = '&iid=' . $i/3;
				else
					$iid = "";
			echo "<td><div class=\"coin-button\"><a href=\"".$website.".php?c=".$this->coins_names_prefix[0+$i].$iid."\" class=\"coin-link\">".$this->coins_names_prefix[1+$i]."/".$this->coins_names_prefix[0+$i]."</a></div></td>";
			echo "<td style=\"padding-left: 20px;\"><div class=\"coin-button\"><a href=\"home.php?c=".$this->coins_names_prefix[2+$i].$iid."\" class=\"coin-link\">".$this->coins_names_prefix[1+$i]."/".$this->coins_names_prefix[2+$i]."</a></div></td>";
		}
	}
	
	public function coinSelecter($coin_selecter)
	{
		if(!$coin_selecter)
			return false;
		$trade_coin = false;
		for($i=0;$i < $this->coins_count; $i+=3)
		{
			if($coin_selecter==$this->coins_names_prefix[0+$i]) 
			{ 
				$trade_coin = $this->coins_names_prefix[0+$i];
				$this->instance_id=$i/3;
				return $trade_coin;
			}
			if($coin_selecter==$this->coins_names_prefix[2+$i]) 
			{ 
				$trade_coin = $this->coins_names_prefix[2+$i];
				$this->instance_id=$i/3;
				return $trade_coin;
			}
				
		}
		return $trade_coin;
	}
	
	public function coinSelecterSelect($coin_selecter)
	{
		$trade_coin = $this->coinSelecter($coin_selecter);
		if(!$trade_coin)
			return false;
		for($i=0;$i < $this->coins_count; $i+=3)
		{
			if($trade_coin == $this->coins_names_prefix[0+$i])
			{
				$this->set_current_from_trade_coin_prefix_and_name($this->coins_names_prefix[0+$i], $this->coins_names[0+$i]);
				$this->set_current_to_trade_coin_prefix_and_name($this->coins_names_prefix[1+$i], $this->coins_names[1+$i]);
			}
			if($trade_coin == $this->coins_names_prefix[2+$i])
			{
				$this->set_current_from_trade_coin_prefix_and_name($this->coins_names_prefix[2+$i], $this->coins_names[2+$i]);
				$this->set_current_to_trade_coin_prefix_and_name($this->coins_names_prefix[1+$i], $this->coins_names[1+$i]);
			}	
		}
		return $trade_coin;
	}
	
	public function outputBalances($user_session)
	{
		if(!$user_session) {
			echo '<b>Finances:</b><p></p>
			<table style="width: 100%;">
			<tr>';
			for($i=0;$i < $this->coins_count; $i+=3)
			{
				if($i/3>0)
					$iid = '?iid=' . $i/3;
				else
					$iid = "";
				echo'
					<td align="right" style="padding-left: 5px;" nowrap><a href="fundsbtc.php'.$iid.'">'.$this->coins_names_prefix[0+$i].'</a></td>
					<td align="right" style="padding-left: 5px;" nowrap><span id="balance-btc">?</span></td>
					<td align="right" style="padding-left: 5px;" nowrap><a href="fundsbtcry.php'.$iid.'">'.$this->coins_names_prefix[1+$i].'</a></td>
					</tr><tr>
						<td align="right" style="padding-left: 5px;" nowrap><span id="balance-btcry">?</span></td>
					</tr><tr>
						<td align="right" style="padding-left: 5px;" nowrap><a href="fundsbtcryx.php'.$iid.'">'.$this->coins_names_prefix[2+$i].'</a></td>
						<td align="right" style="padding-left: 5px;" nowrap><span id="balance-btcryx">?</span></td>
					';
			}
			echo'</tr>
				</table>';
		} else {
			echo '<b>Finances:</b><p></p>
				<table style="width: 100%;">
				<tr>';
			for($i=0;$i < $this->coins_count; $i+=3)
			{
				echo'
					<td align="right" style="padding-left: 5px;" nowrap><a href="fundsbtc.php'.$iid.'">'.$this->coins_names_prefix[0+$i].'</a></td>
					<td align="right" style="padding-left: 5px;" nowrap><span id="balance-btc">'.userbalance($user_session,$this->coins_names_prefix[0+$i]).'</span></td>
					</tr><tr>
						<td align="right" style="padding-left: 5px;" nowrap><a href="fundsbtcry.php'.$iid.'">'.$this->coins_names_prefix[1+$i].'</a></td>
						<td align="right" style="padding-left: 5px;" nowrap><span id="balance-btcry">'.userbalance($user_session,$this->coins_names_prefix[1+$i]).'</span></td>
					</tr><tr>
						<td align="right" style="padding-left: 5px;" nowrap><a href="fundsbtcryx.php'.$iid.'">'.$this->coins_names_prefix[2+$i].'</a></td>
						<td align="right" style="padding-left: 5px;" nowrap><span id="balance-btcryx">'.userbalance($user_session,$this->coins_names_prefix[2+$i]).'</span></td>
					';
			}
				echo'
				</tr>
				</table>
				';
		}
	}
	
	public function outputFooter($website)
	{
		echo'<b>Trade Sections:</b> ';
		$link = "";
		for($i=0;$i < $this->coins_count; $i+=3)
		{
			if($i!=0)
				echo', ';
			if($i/3>0)
					$iid = '&iid=' . $i/3;
				else
					$iid = "";
			echo'<a href="'.$website.'.php?c='.$this->coins_names_prefix[2+$i].$iid.'">'.$this->coins_names_prefix[1+$i].'/'.$this->coins_names_prefix[2+$i].'</a>, <a href="home.php?c='.$this->coins_names_prefix[0+$i].$iid.'">'.$this->coins_names_prefix[1+$i].'/'.$this->coins_names_prefix[0+$i].'</a>';
		}
	}
	
	public function getInstanceId()
	{
		return $this->instance_id;
	}
	
	public function getCoinsInstanceId()
	{
		return $this->instance_id*3;
	}
	
	public function getSelectInstanceId()
	{
		return $this->select_instance_id/3;
	}
	
	public function setSelectInstanceId($id)
	{
		$this->select_instance_id=$id/3;
	}
	
	public function getCoinsSelectInstanceId()
	{
		return $this->select_instance_id*3;
	}
	
	public function getBitcoindDaemons()
	{
		$array = array();
		$count = 0;
		for($i=0,$j=0;$i < $this->coins_count; $i++)
		{
			$r = $i%3;
			if($r==0||$r==3)
			{
				$array[$j] = array();
				$array[$j]["name"] = $this->coins_names[$i];
				$array[$j]["prefix"] = $this->coins_names_prefix[$i];
				$array[$j]["daemon"] = $this->coins[$this->coins_names[$i]]["daemon"];
				$j++;
			}
		}
		$count = count($array);
		return array($array, $count);
	}
	
	public function getBitcrystaldDaemons()
	{
		$array = array();
		$count = 0;
		for($i=0,$j=0;$i < $this->coins_count; $i++)
		{
			$r = $i%3;
			if($r==1)
			{
				$array[$j] = array();
				$array[$j]["name"] = $this->coins_names[$i];
				$array[$j]["prefix"] = $this->coins_names_prefix[$i];
				$array[$j]["daemon"] = $this->coins[$this->coins_names[$i]]["daemon"];
				$j++;
			}
		}
		$count = count($array);
		return array($array, $count);
	}
	
	public function getBitcrystalxdDaemons()
	{
		$array = array();
		$count = 0;
		for($i=0,$j=0;$i < $this->coins_count; $i++)
		{
			$r = $i%3;
			if($r==2)
			{
				$array[$j] = array();
				$array[$j]["name"] = $this->coins_names[$i];
				$array[$j]["prefix"] = $this->coins_names_prefix[$i];
				$array[$j]["daemon"] = $this->coins[$this->coins_names[$i]]["daemon"];
				$j++;
			}
		}
		$count = count($array);
		return array($array, $count);
	}
	
	public function update($user_session, $wallet_id)
	{
		if(!$user_session)
			return false;
		$rv=$this->getBitcoindDaemons();
		$Bitcoind = $rv[0];
		$rv=$this->getBitcrystaldDaemons();
		$Bitcrystald = $rv[0];
		$rv=$this->getBitcrystalxdDaemons();
		$Bitcrystalxd = $rv[0];
		$count_daemons = $rv[1];
		$Bitcoind_Account_Address=array();
		$Bitcrystald_Account_Address=array();
		$Bitcrystalxd_Account_Address=array();
		$string="";
		$i=0;
		for(;$i<$count_daemons;$i++)
		{
			$Bitcoind_Account_Address[$i] = $Bitcoind[$i]["daemon"]->getaccountaddress($wallet_id);
			$Bitcrystald_Account_Address[$i] = $Bitcrystald[$i]["daemon"]->getaccountaddress($wallet_id);
			$Bitcrystalxd_Account_Address[$i] = $Bitcrystalxd[$i]["daemon"]->getaccountaddress($wallet_id);
			if($i!=0&&$i+1<$count_daemons)
				$string = $string .",";
			$string=$string . "'".$Bitcoind_Account_Address[$i]."','".$Bitcrystald_Account_Address[$i]."','".$Bitcrystalxd_Account_Address[$i]."'";
		}
	    if($i<10)
			$string = $string . ",";
		for(;$i<10;$i++)
		{
			$string = $string . "'0'";
			if($i+1<10)
				$string = $string .",";
		}
		$SQL = "SELECT * FROM balances WHERE username='$user_session' and trade_id = '".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."'";
		$result = mysql_query($SQL);
		$num_rows = mysql_num_rows($result);
		if($num_rows!=1) {
			if(!mysql_query("INSERT INTO balances (id,username,coin1,coin2,coin3,coin4,coin5,coin6,coin7,coin8,coin9,coin10,trade_id) VALUES ('','$user_session','0','0','0','0','0','0','0','0','0','0','".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."')")) {
				die("Server error");
			} else {
				$r_system_action = "success";
			}
		}
		$SQL = "SELECT * FROM addresses WHERE username='$user_session' and trade_id = '".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."'";
		$result = mysql_query($SQL);
		$num_rows = mysql_num_rows($result);
		if($num_rows!=1) {
			if(!mysql_query("INSERT INTO addresses (id,username,coin1,coin2,coin3,coin4,coin5,coin6,coin7,coin8,coin9,coin10,trade_id) VALUES ('','$user_session',".$string.",'".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."')")) {
				die("Server error");
			} else {
				$r_system_action = "success";
			}
		}

		for($i=0;$i<$count_daemons;$i++)
		{
			$Bitcoind_List_Transactions = $Bitcoind[$i]["daemon"]->listtransactions($wallet_id,50);
   
			foreach($Bitcoind_List_Transactions as $Bitcoind_List_Transaction) {
				if($Bitcoind_List_Transaction['category']=="receive") {
					if(6<=$Bitcoind_List_Transaction['confirmations']) {
						$DEPOSIT_tx_type = 'deposit';
						$DEPOSIT_coin_type = $Bitcoind[$i]["prefix"];
						$DEPOSIT_date = date('n/j/y h:i a',$Bitcoind_List_Transaction['time']);
						$DEPOSIT_address = $Bitcoind_List_Transaction['address'];
						$DEPOSIT_amount = abs($Bitcoind_List_Transaction['amount']);
						$DEPOSIT_txid = $Bitcoind_List_Transaction['txid'];
						$SQL = "SELECT * FROM transactions WHERE coin='$DEPOSIT_coin_type' and txid='$DEPOSIT_txid' and trade_id = '".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."'";
						$result = mysql_query($SQL);
						$num_rows = mysql_num_rows($result);
						if($num_rows!=1) {
							if(!mysql_query("INSERT INTO transactions (id,date,username,action,coin,address,txid,amount,trade_id) VALUES ('','$DEPOSIT_date','$user_session','$DEPOSIT_tx_type','$DEPOSIT_coin_type','$DEPOSIT_address','$DEPOSIT_txid','$DEPOSIT_amount','".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."')")) {
								die("Server error");
							} else {
								$result = plusfunds($user_session,$Bitcoind[$i]["prefix"],$DEPOSIT_amount);
								if($result) {
									$r_system_action = "success";
								} else {
									die("Server error");
								}
							}
						}
					}
				}
			}
			
			$Bitcrystald_List_Transactions = $Bitcrystald[$i]["daemon"]->listtransactions($wallet_id,50);
   
			foreach($Bitcrystald_List_Transactions as $Bitcrystald_List_Transaction) {
				if($Bitcrystald_List_Transaction['category']=="receive") {
					if(6<=$Bitcrystald_List_Transaction['confirmations']) {
						$DEPOSIT_tx_type = 'deposit';
						$DEPOSIT_coin_type = $Bitcrystald[$i]["prefix"];
						$DEPOSIT_date = date('n/j/y h:i a',$Bitcrystald_List_Transaction['time']);
						$DEPOSIT_address = $Bitcrystald_List_Transaction['address'];
						$DEPOSIT_amount = abs($Bitcrystald_List_Transaction['amount']);
						$DEPOSIT_txid = $Bitcrystald_List_Transaction['txid'];
						$SQL = "SELECT * FROM transactions WHERE coin='$DEPOSIT_coin_type' and txid='$DEPOSIT_txid' and trade_id = '".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."'";
						$result = mysql_query($SQL);
						$num_rows = mysql_num_rows($result);
						if($num_rows!=1) {
							if(!mysql_query("INSERT INTO transactions (id,date,username,action,coin,address,txid,amount,trade_id) VALUES ('','$DEPOSIT_date','$user_session','$DEPOSIT_tx_type','$DEPOSIT_coin_type','$DEPOSIT_address','$DEPOSIT_txid','$DEPOSIT_amount','".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."')")) {
								die("Server error");
							} else {
								$result = plusfunds($user_session,$Bitcrystald[$i]["prefix"],$DEPOSIT_amount);
								if($result) {
									$r_system_action = "success";
								} else {
									die("Server error");
								}
							}
						}
					}
				}
			}
			
			$Bitcrystalxd_List_Transactions = $Bitcrystald[$i]["daemon"]->listtransactions($wallet_id,50);
   
			foreach($Bitcrystalxd_List_Transactions as $Bitcrystalxd_List_Transaction) {
				if($Bitcrystalxd_List_Transaction['category']=="receive") {
					if(6<=$Bitcrystalxd_List_Transaction['confirmations']) {
						$DEPOSIT_tx_type = 'deposit';
						$DEPOSIT_coin_type = $Bitcrystalxd[$i]["prefix"];
						$DEPOSIT_date = date('n/j/y h:i a',$Bitcrystalxd_List_Transaction['time']);
						$DEPOSIT_address = $Bitcrystalxd_List_Transaction['address'];
						$DEPOSIT_amount = abs($Bitcrystalxd_List_Transaction['amount']);
						$DEPOSIT_txid = $Bitcrystalxd_List_Transaction['txid'];
						$SQL = "SELECT * FROM transactions WHERE coin='$DEPOSIT_coin_type' and txid='$DEPOSIT_txid' and trade_id = '".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."'";
						$result = mysql_query($SQL);
						$num_rows = mysql_num_rows($result);
						if($num_rows!=1) {
							if(!mysql_query("INSERT INTO transactions (id,date,username,action,coin,address,txid,amount,trade_id) VALUES ('','$DEPOSIT_date','$user_session','$DEPOSIT_tx_type','$DEPOSIT_coin_type','$DEPOSIT_address','$DEPOSIT_txid','$DEPOSIT_amount','".$my_coins->coins_names_prefix[0]."_".$my_coins->coins_names_prefix[1]."_".$my_coins->coins_names_prefix[2]."')")) {
								die("Server error");
							} else {
								$result = plusfunds($user_session,$Bitcrystalxd[$i]["prefix"],$DEPOSIT_amount);
								if($result) {
									$r_system_action = "success";
								} else {
									die("Server error");
								}
							}
						}
					}
				}
			}
			
			$Bitcoind_Balance[$i] = userbalance($user_session,$Bitcoind[$i]["prefix"]);      // Simple function to call the users balance
			$Bitcrystald_Balance[$i] = userbalance($user_session,$Bitcrystald[$i]["prefix"]);
			$Bitcrystalxd_Balance[$i] = userbalance($user_session,$Bitcrystalx[$i]["prefix"]);
		}
	
	}
	
	public static function get()
	{
		if(self::$SINGLETON == NULL)
			self::$SINGLETON=new w_coins();
		return self::$SINGLETON;
	}
}
?>