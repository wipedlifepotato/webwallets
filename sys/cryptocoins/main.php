<?php
require 'sys/vendor/autoload.php';
require_once('sys/sql.php');
require_once('sys/users.php');

use Denpa\Bitcoin\Client as BitcoinClient;
class CryptoCoin extends users{
        public function getUserPrefixForCoinsByName($n){
		$res = $this->sql->doSQL(sql::sqls['getUserPrefixForCoinByName'], $this->sql->escape_string($n));
		if($res == NULL) return ($this->sql->getLastSQLError() . "");
		return $prefix = mysqli_fetch_array($res)['user_prefix'];
		 		
	}	
        public function getCryptoCoins(){
		$res = $this->sql->doSQL(sql::sqls['getCryptoCoins']);
		if($res == NULL) die("sql err");
		return $res;
		 		
	}
        public function addCryptoCoin($name, $host, $port, $rpcuser, $rpcpassword){
		$host="localhost";//Tmp.. some vulb TODO: DELETE
		$res = $this->sql->doSQL(sql::sqls['addCryptoCoin'], 
			$this->sql->escape_string($name),$this->sql->escape_string($host),
			$this->sql->escape_string($port),$this->sql->escape_string($rpcuser),$this->sql->escape_string($rpcpassword) );
		if($res == NULL) return ($this->sql->getLastSQLError() . "");
		return True;
	}
	public function __construct(	$sql	){
			parent::__construct($sql);
			$this->coins =array();
			$coins = $this->getCryptoCoins();
			while($coin = mysqli_fetch_array($coins)){
					
				//foreach($coins as $coin){
				$tmp = sprintf("http://%s:%s@%s:%s/",$coin['rpcuser'],$coin['rpcpassword'],
					$coin['host'],$coin['port'] );
				$name=$coin['name'];
				$this->coins[$name] = new BitcoinClient($tmp);
			};
	}
//	"getWalletsOfUsername" => "SELECT wallets.*,cryptocoins.name FROM wallets INNER JOIN cryptocoins ON cryptocoins.id=wallets.cryptocoin_id WHERE wallets.owner_id='%s'",
	public function getWalletsOfUsername($username,$prefix=""){
		$user=$this->getUserByName($username);
		$res = $this->sql->doSQL(sql::sqls['getWalletsOfUsername'], $user['id']);
		if($res == NULL) die("sql err");
		$wallets=array();
		while($wallet = mysqli_fetch_array($res)){
			$wallets['name'] = $wallet;
		}

		return $wallet;
	}
	//public function __call($name, $arguments)
	//{echo "use the $...->coins[$name]";}

};
//$bitc = new BitcoinClient('http://gostcoinrpc:97WDPgQADfazR6pQRdMEjQeDeCSzTwVaMEZU1dGaTmLo@localhost:19376/');
//print("Balance now-> ".$bitc->getBalance()."\n");
//print("help now-> ".$bitc->help()."\n");

?>
