<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Database Access Layer v1.2 r2 ==			║
║	Manages connection to a database using PDO.		║
║	Manages queries and results.				║
╚═══════════════════════════════════════════════════════════════╝
*/

class Database
{

private $Handler;

private $Statement;
private $Result;

public $Error;

	//--CONSTRUCTOR--
	public function __construct($inHost, $inDatabaseName, $inUser, $inPassword)
	{
		// Build DSN for MySQL and define options
		$lDSN = 'mysql:host='.$inHost.';dbname='.$inDatabaseName;
		$lConnection_options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8");

		// Try connecting
		try{
			$this->Handler = new PDO($lDSN, $inUser, $inPassword, $lConnection_options);
		   }
		catch(PDOException $e){ $this->Error = $e->getMessage(); die('Échec de connexion à la base de données!'); }
	}


	//--PREPARE SQL QUERY--
	public function SetQuery($inQuery)
	{
		// Prepare the query before binding and execution. Prevents injection.
		$this->Statement = $this->Handler->prepare($inQuery);
	}


	//--BIND VALUES TO AVOID INJECTION--
	public function Bind($inParam, $inValue, $inType = null)
	{
		// Determine the data type
		$lType = $inType;

		if (is_null($lType)) { 
			switch (true) {
				case is_int($inValue):
					$lType = PDO::PARAM_INT;
					break;
				case is_bool($inValue):
					$lType = PDO::PARAM_BOOL;
					break;
				case is_null($inValue):
					$lType = PDO::PARAM_NULL;
					break;
				default:
					$lType = PDO::PARAM_STR;
			}
		}

		// Bind data to the query's param
		$this->Statement->bindValue($inParam, $inValue, $lType);
	}


	//--EXECUTE PREPARED QUERY AND GET RESULT--
	public function FetchResult()
	{
		$r = $this->Statement->execute();
		if( $this->Statement->columnCount() ) {	$this->Result = $this->Statement->fetchAll(); }
		else {$this->Result = $r;}

		return $this->Result;
	}


	//--GET RESULT'S ROW COUNT--
	public function GetRowCount() { return $this->Statement->rowCount(); }

	//--GET RESULT'S COLUMN COUNT--
	public function GetColumnCount() { return $this->Statement->columnCount(); }


	//--START NEW TRANSACTION--
	public function StartTransaction() { return $this->Handler->beginTransaction(); }

	//--END SUCCESSFUL TRANSACTION--
	public function EndTransaction() { return $this->Handler->commit(); }

	//--CANCEL FAILED TRANSACTION--
	public function CancelTransaction() { return $this->Handler->rollBack(); }


	//--CLOSE THE CONNECTION--
	public function CloseConnection() { $this->Handler = null; }


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV id="debug">';
		echo '<b><u>Database</u></b><br />';
		echo 'Status: ' . $this->Handler->getAttribute(PDO::ATTR_CONNECTION_STATUS) . '<br />';
		echo '-------<br />';
		echo 'Last Statement: <br />' . $this->Statement->debugDumpParams() . '<br />';
		echo '-------<br />';
		echo 'Last Result: <br />';
		echo $this->GetRowCount() . ' row(s) were returned. <br />';
		echo $this->GetColumnCount() . ' column(s) were returned. <br />';
		echo '<pre>';
		print_r($this->Result);
		echo '</pre>';
		echo '-------<br />';
		echo 'Last error: ' . $this->Error . '<br />';
		echo '</DIV>';
	}

} // END of Database class

?>
