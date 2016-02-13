<?php

// Database Class
// Description: A class, which you can use when you work with different databases.
// It makes easier your work, because any time you use the same class methods and parameters.
// In addition it helps very quickly change database, which you use in your project because you
// change only one parameter and your project will work with new database.
// Inputs:None
// Returns:None

//Methods list:
// - Open: open server connection
// - Close: close server connection
// - SelectDB: select database
// - Query: send query and return result identifier
// - DataSeek: move row pointer to specified row number (starting at 0)
// - FieldName: get the name of a field
// - FieldType: get the type of the field
// - FieldLenght: get the length of the field
// - FetchRow: get row as enumerated array
// - FetchArray: fetch row as array
// - FetchObject: fetch row as object
// - Result: get result data
// - FreeResult: free result memory
// - RowsNumber: get number of rows in result
// - FieldsNumber: get number of fields in result
// - CurRecNumber: get current row number (starting at 0)
// - RecordNumber: get current row number (starting at 1)
// - MoveFirstRec: move to first record and get it as enumerated array
// - MoveLastRec: move to last record and get it as enumerated array
// - MovePreviousRec: move to previous record and get it as enumerated array
// - MoveNextRec: move to next record and get it as enumerated array
// - MoveToRec: move to specified record and get it as enumerated array (starting at 1)
//
//Inputs:
// - dbType: databases type: mssql, mysql, pg
// - connectType: connection type: c - common connection, p - open persistent connection
// - connect: for MS SQL Server - server name, for MySQL - hostname [:port] [:/path/to/socket], for PostgreSQL - host, port, tty, options, dbname (without username and password)
// - username
// - password
// - dbName: database name
// - query: SQL query
// - result: result set identifier
// - RowNumber:
// - offset: field identifier
// - ResultType: a constant and can take the following values: PGSQL_ASSOC, PGSQL_NUM, and PGSQL_BOTH
// - FieldName
//
//Returns:
// - result: result set identifier
// - connect link identifier
// - record number (starting at 0: CurrRecNumber or starting at 1: RecordNumber)
// - number of fields in the specified result set
// - number of rows in the specified result set

Class clsDatabase
{
    var $dbType;						// databases type: mssql, mysql, pg, [access]
    var $connectType;					// connection type: c - common connection, p - open persistent connection
    var $idCon; 					    	// connection index
    var $curRow;					    	// current row number of data from the result associated with the specified result identifier array
    var $seek;						// current row number of data from DataSeek function array
	var $LogFile;					// Naveed: Error Log File
	
    // *************************
    // ** CONSTRUCTOR METHODS **
    // *************************
	function clsDatabase($varType, $varConnectType = "c", $varHost, $varUser, $varPassword, $varDB, $varLogFile = '')
	{
		$this->Open($varType, $varConnectType, $varHost, $varUser, $varPassword);
		$this->SelectDb($varDB);
		$this->LogFile = $varLogFile;
	}

    // **********************
    // ** STANDARD METHODS **
    // **********************

    ////////////////////////////
    // Open Server Connection //
    ////////////////////////////
    Function Open($dbType, $connectType = "c", $connect, $username = "", $password = "")
    {
      $this->dbType = $dbType;

      Switch ($dbType)
      {
        Case "mssql":
          If ($connectType == "c")
            $idCon = mssql_connect($connect, $username, $password);
          Else
            $idCon = mssql_pconnect($connect, $username, $password);
          Break;

        Case "mysql":
          If ($connectType == "c")
	         $idCon = mysql_connect($connect, $username, $password) or print('Unable to Connect to the Main Database...');
          Else
            $idCon = mysql_pconnect($connect, $username, $password) or print('Please Wait...<script language=JavaScript>this.location.reload();</script>');
          Break;

        Case "pg":
          If ($connectType == "c")
            $idCon = pg_connect($connect . " user=" . $username . " password=" . $password);
          Else
            $idCon = pg_pconnect($connect . " user=" . $username . " password=" . $password);
          Break;

        // [NEW] Microsoft Access Database
        Case "access":
          $idCon = new COM('ADODB.Connection') or die("Cannot Start ADO!");
          
          if (!file_exists("../$connect"))
            die("Sorry, database file does not exist!<br>$connect ");
          
          $idCon->Open("DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$connect;pwd=$password");
          Break;
        Default:
          $idCon = 0;
          die("Database open unsuccessful!\n");
          Break;
      }

      $this->idCon = $idCon;
      Return $idCon;
    }
    
    /////////////////////////////
    // Close Server Connection //
    /////////////////////////////
    Function Close()
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_close($this->idCon);
          Break;
        Case "mysql":
          $r = mysql_close($this->idCon) or die("Cannot Close MySQL Database");
          Break;
        Case "pg":
          $r = pg_close($this->idCon);
          Break;

        // [NEW] Microsoft Access
        Case "access":
          $r = $this->idCon->Close();
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    ////////////////////////////////////////////////////////////////
    // Set Current Active Database with Specified Link Identifier //
    ////////////////////////////////////////////////////////////////
    Function SelectDb($dbName)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_select_db($dbName);
          Break;
        Case "mysql":
          $r = mysql_select_db($dbName) or die("Cannot Select the specified database... or database does not exist");
          Break;
        Case "pg":
          $r = False;
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    /////////////////////////////////////////////
    // Send Query to Currently Active Database //
    /////////////////////////////////////////////
    Function Query($query)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_query($query, $this->idCon);
          Break;
        Case "mysql":
          $r = mysql_query($query, $this->idCon) or $this->LogError(mysql_error($this->idCon));
          Break;
        Case "pg":
          $r = pg_exec($this->idCon, $query);
          Break;

        Default:
          $r = False;
          Break;
      }

      Return $r;
    }
	
	function LogError($sError)
	{
		//print('<!-- QF#00034134: ' . mysql_error($this->idCon) . '-->');
		if (strpos('Lost connection to MySQL', $sError) > 0)
		{
			print('<script language=javascript>window.location=\'' . $_SERVER['SERVER_NAME'] . '&' . $_SERVER['QUERY_STRING'] . '\';</script>');
			exit();
		}
		
		$handle = fopen($this->LogFile, "a+");
		$sErrorMsg =  "Error Generated at " . date("F j, Y, g:i a  :  ") . "\r\n";
		$sErrorMsg .= "Error Message  : ". $sError . "\r\n";
		$sErrorMsg .= "Script Name    : " . $_SERVER["SCRIPT_NAME"] . "\r\n";
		$sErrorMsg .= "************************************************************\r\n";
		fwrite($handle, $sErrorMsg);
		fclose($handle);
	}


    /////////////////////////////////////////////////////
    // Move Internal Row Pointer of the Result         //
    // associated with the specified result identifier //
    /////////////////////////////////////////////////////
    Function DataSeek($result, $RowNumber)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_data_seek($result, $RowNumber);
          Break;
        Case "mysql":
          $r = mysql_data_seek($result, $RowNumber);
          Break;
        Case "pg":
          $r = False;
          Break;
        Default:
          $r = False;
          Break;
      }
      $this->seek[$result] = (int) $RowNumber;
      Return $r;
    }

    /////////////////////////////
    // Get the Name of a Field //
    /////////////////////////////
    Function FieldName($result, $offset)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_field_name($result, $offset);
          Break;
        Case "mysql":
          $r = mysql_field_name($result, $offset);
          Break;
        Case "pg":
          $r = pg_fieldname($result, $offset);
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    /////////////////////////////
    // Get the Type of a Field //
    /////////////////////////////
    Function FieldType($result, $offset)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_field_type($result, $offset);
          Break;
        Case "mysql":
          $r = mysql_field_type($result, $offset);
          Break;
        Case "pg":
          $r = pg_fieldtype($result, $offset);
          Break;
        Default:
          $r = False;
        Break;
      }
      Return $r;
    }

    ///////////////////////////////
    // Get the Length of a Field //
    ///////////////////////////////
    Function FieldLength($result, $offset)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_field_length($result, $offset);
          Break;
        Case "mysql":
          $r = mysql_field_len($result, $offset);
          Break;
        Case "pg":
          $r = pg_fieldsize($result, $offset);
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    ////////////////////////////////////////////////////////////////////////
    // Fetches one row of data from the result                            //
    // associated with the specified result identifier                    //
    // (result column is stored in an array offset, starting at offset 0) //
    ////////////////////////////////////////////////////////////////////////
    Function FetchRow($result, $RowNumber = 0)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_fetch_row($result);
          Break;
        Case "mysql":
          $r = mysql_fetch_row($result);
          Break;
        Case "pg":
          $r = pg_fetch_row($result, $RowNumber);
          If ($r)
          {
            $this->curRow[$result] = $RowNumber;
            $this->seek[$result] = $RowNumber;
          }
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    //////////////////////////////////////////////////////////////////////////////
    // fetch row as array (you can using the field names as keys - msssq, mysql //
    // only for pg you can use RowNumber and ResultType)                        //
    //////////////////////////////////////////////////////////////////////////////
    Function FetchArray($result, $RowNumber = 0, $ResultType = 2)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_fetch_array($result);
          Break;
        Case "mysql":
          $r = mysql_fetch_array($result);
          Break;
        Case "pg":
          $r = pg_fetch_array($result, $RowNumber, $ResultType);
          If ($r)
          {
            $this->curRow[$result] = $RowNumber;
            $this->seek[$result] = $RowNumber;
          }
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    ///////////////////////////////////////////////////////////////////////////
    // fetch row as object (you can only access the data by the field names) //
    // only for pg you can use RowNumber and ResultType)                     //
    ///////////////////////////////////////////////////////////////////////////
    Function FetchObject($result, $RowNumber = 0, $ResultType = 2)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_fetch_object($result);
          Break;
        Case "mysql":
          $r = mysql_fetch_object($result);
          Break;
        Case "pg":
          $r = pg_fetch_object($result, $RowNumber, $ResultType);
          If ($r)
          {
            $this->curRow[$result] = $RowNumber;
            $this->seek[$result] = $RowNumber;
          }
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    ////////////////////////////////////////////////////////
    // returns the contents of one cell from a result set //
    ////////////////////////////////////////////////////////
    Function Result($result, $RowNumber, $FieldName)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_result($result, $RowNumber, $FieldName);
          Break;
        Case "mysql":
          $r = mysql_result($result, $RowNumber, $FieldName);
          Break;
        Case "pg":
          $r = pg_result($result, $RowNumber, $FieldName);
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    ////////////////////////
    // Free Result Memory //
    ////////////////////////
    Function FreeResult($result)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_free_result($result);
          Break;
        Case "mysql":
          $r = mysql_free_result($result);
          Break;
        Case "pg":
          $r = pg_freeresult($result);
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    //////////////////////////////////
    // Get Number of Rows in Result //
    //////////////////////////////////
    Function RowsNumber($result)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_num_rows($result);
          Break;
        Case "mysql":
          $r = mysql_num_rows($result);
          Break;
        Case "pg":
          $r = pg_numrows($result);
          Break;
        Case "access":
          $r = $result->RowCount();
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    ////////////////////////////////////
    // Get Number of Fields in Result //
    ////////////////////////////////////
    Function FieldsNumber($result)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
          $r = mssql_num_fields($result);
          Break;
        Case "mysql":
          $r = mysql_num_fields($result);
          Break;
        Case "pg":
          $r = pg_numfields($result);
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }

    // ************************
    // ** ADDITIONAL METHODS **
    // ************************

    ////////////////////////////////////////////
    // Get Current Row Number (Starting at 0) //
    ////////////////////////////////////////////
    Function CurRecNumber($result)
    {
      $r = $this->curRow[$result];
      Return $r;
    }

    ////////////////////////////////////////////
    // Get Current Row Number (Starting at 1) //
    ////////////////////////////////////////////
    Function RecordNumber($result)
    {
      $cr = $this->CurRecNumber($result) + 1;
      Return $cr;
    }


    /////////////////////////////////////////////////////////
    // Move to first record and get it as enumerated array //
    /////////////////////////////////////////////////////////
    Function MoveFirstRec($result)
    {
      Switch ($this->dbType)
      {
        Case "pg":
          $r = $this->FetchRow($result, 0);
          Break;
        Default:
          $rn = $this->DataSeek($result, 0);
          If ($rn)
          {
            $r = $this->FetchRow($result);
            If ($r) $this->curRow[$result] = $this->seek[$result];
          }
          Else
            $r = False;
          Break;
      }
      Return $r;
    }

    ////////////////////////////////////////////////////////
    // Move to last record and get it as enumerated array //
    ////////////////////////////////////////////////////////
    Function MoveLastRec($result)
    {
      $rs = $this->RowsNumber($result);
      If ($rs)
      {
        $rs--;
        Switch ($this->dbType)
        {
          Case "pg":
            $r = $this->FetchRow($result, $rs);
            Break;
          Default:
            $rn = $this->DataSeek($result, $rs);
            If ($rn)
            {
              $r = $this->FetchRow($result);
              If ($r) $this->curRow[$result] = $this->seek[$result];
            }
            Else
              $r = False;
            Break;
        }
      }
      Return $r;
    }

    ////////////////////////////////////////////////////////////
    // Move to previous record and get it as enumerated array //
    ////////////////////////////////////////////////////////////
    Function MovePreviousRec($result)
    {
      $rs = $this->CurRecNumber($result);
      If ($rs)
      {
        $rs--;
        Switch ($this->dbType)
        {
          Case "pg":
            $r = $this->FetchRow($result, $rs);
            Break;
          Default:
            $rn = $this->DataSeek($result, $rs);
            If ($rn)
            {
              $r = $this->FetchRow($result);
              If ($r) $this->curRow[$result] = $this->seek[$result];
            }
            Else
              $r = False;
            Break;
        }
      }
      Return $r;
    }

    ////////////////////////////////////////////////////////
    // Move to next record and get it as enumerated array //
    ////////////////////////////////////////////////////////
    Function MoveNextRec($result)
    {
      $rs = $this->CurRecNumber($result);
      $rn = $this->RowsNumber($result);
      $rs++;
      If ($rs != $rn)
      {
        Switch ($this->dbType)
        {
          Case "pg":
            $r = $this->FetchRow($result, $rs);
            Break;
          Default:
            $re = $this->FetchRow($result);
            If ($re)
            {
              $r = $re;
              $this->curRow[$result]++;
              $this->seek[$result] = $this->curRow[$result];
            }
            Else
              $r = False;
            Break;
        }
      }
      Return $r;
    }

    /////////////////////////////////////////////////////////////////////////////
    // Move to specified record and get it as enumerated array (starting at 1) //
    /////////////////////////////////////////////////////////////////////////////
    Function MoveToRec($result, $RowNumber)
    {
      $rn = $this->RowsNumber($result);
      If ($RowNumber > 0 And $RowNumber < $rn)
      {
        $RowNumber--;
        Switch ($this->dbType)
        {
          Case "pg":
            $r = $this->FetchRow($result, $RowNumber);
            Break;
          Default:
            $rn = $this->DataSeek($result, $RowNumber);
            If ($rn)
            {
              $r = $this->FetchRow($result);
              If ($r) $this->curRow[$result] = $this->seek[$result];
            }
            Else
              $r = False;
            Break;
        }
      }
      Return $r;
    }
    
    ///////////////////////////
    // NEW METHODS BY NAVEED //
    ///////////////////////////
	function AffectedRows($result)
    {
      Switch ($this->dbType)
      {
        Case "mssql":
		  // Don't Know
          Break;
        Case "mysql":
          $r = mysql_affected_rows();
          Break;
        Case "pg":
          Break;
        Default:
          $r = False;
          Break;
      }
      Return $r;
    }
}
?>