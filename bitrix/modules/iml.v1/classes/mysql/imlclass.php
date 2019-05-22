<?
class sqlimldriver{
	public function imlLog($wat,$sign){imlHelper::imlLog($wat,$sign);}
	
	protected static $tableName = 'ipol_iml';
	
	public static function Add($Data){
        // = $Data = format:
		// PARAMS - ALL INFO
		// ORDER_ID - corresponding order
		// STATUS - response from iml
		// MESSAGE - info from server
		// BARCODE && ENCBARCODE - recieved from logistics
		// OK - 0 / 1 - was confirmed
		// UPTIME - время добавления
		// ATTEMPT - номер попытки отправки
		
		global $DB;
        
		if(!$Data['STATUS'])
			$Data['STATUS']='NEW';
		if($Data['STATUS']=='NEW')
			$Data['MESSAGE']='';
		if(!$Data['ATTEMPT'])
			$Data['ATTEMPT']=1;
		if(is_array($Data['PARAMS'])) {
			$Data['PARAMS'] = serialize($Data['PARAMS']);
		}
		if(!$Data['BARCODE'])
			$Data['BARCODE']='';
		if(!$Data['ENCBARCODE'])
			$Data['ENCBARCODE']='';
		
		$Data['UPTIME']=mktime();
			
		$rec = self::CheckRecord($Data['ORDER_ID']);
		if($rec){
			if($rec['STATUS']!='SENDED'){
				$strUpdate = $DB->PrepareUpdate(self::$tableName, $Data);
				$strSql = "UPDATE ".self::$tableName." SET ".$strUpdate." WHERE ID=".$rec['ID'];
				$DB->Query($strSql, false, $err_mess.__LINE__);
			}else
				imlHelper::errorLog(GetMessage('IPOLIML_ERRLOG_NOUPDSND').$Data['ORDER_ID']);
		}else{
			$arInsert = $DB->PrepareInsert(self::$tableName, $Data);
			$strSql =
				"INSERT INTO ".self::$tableName."(".$arInsert[0].") ".
				"VALUES(".$arInsert[1].")";
			$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		}
		return self::CheckRecord($Data['ORDER_ID']); 
    }
	
	public static function select($arOrder=array("ID","DESC"),$arFilter=array(),$arNavStartParams=array()){
		global $DB;
		
		$strSql='';
		
		$where='';
		if(strpos($arFilter['>=UPTIME'],".")!==false)
			$arFilter['>=UPTIME']=strtotime($arFilter['>=UPTIME']);
		if(strpos($arFilter['<=UPTIME'],".")!==false)
			$arFilter['<=UPTIME']=strtotime($arFilter['<=UPTIME']);

	 	if(count($arFilter)>0)
			foreach($arFilter as $field => $value){
				if(strpos($field,'!')!==false)
					$where.=' and '.substr($field,1).' != "'.$value.'"';
				elseif(strpos($field,'<=')!==false)
					$where.=' and '.substr($field,2).' <= "'.$value.'"';				
				elseif(strpos($field,'>=')!==false)
					$where.=' and '.substr($field,2).' >= "'.$value.'"';
				elseif(strpos($field,'>')!==false)
					$where.=' and '.substr($field,1).' > "'.$value.'"';				
				elseif(strpos($field,'<')!==false)
					$where.=' and '.substr($field,1).' < "'.$value.'"';
				else{
					if(is_array($value)){
						$where.=' and (';
						foreach($value as $val)
							$where.=$field.' = "'.$val.'" or ';
						$where=substr($where,0,strlen($where)-4).")";
					}else
						$where.=' and '.$field.' = "'.$value.'"';
				}
			}
		if($where) 
			$strSql.="
			WHERE ".substr($where,4);
			
		if(in_array($arOrder[0],array('ID','ORDER_ID','STATUS','BARCODE','UPTIME'))&&($arOrder[1]=='ASC'||$arOrder[1]=='DESC'))
			$strSql.="
			ORDER BY ".$arOrder[0]." ".$arOrder[1];
		
		$cnt=$DB->Query("SELECT COUNT(*) as C FROM ".self::$tableName.$strSql, false, $err_mess.__LINE__)->Fetch();
		
		if($arNavStartParams['nPageSize']==0)
			$arNavStartParams['nPageSize']=$cnt['C'];
		
		$strSql="SELECT * FROM ".self::$tableName.$strSql;

		$res = new CDBResult();
		$res->NavQuery($strSql,$cnt['C'],$arNavStartParams);

		return $res;
	}
		
	public static function Delete($orderId){
		global $DB;
		$orderId = $DB->ForSql($orderId);
		$strSql =
            "DELETE FROM ".self::$tableName."
            WHERE ORDER_ID='".$orderId."'";
		$DB->Query($strSql, true);
        
        return true; 
    }

	public static function GetByOI($orderId){
		global $DB;
		$orderId=$DB->ForSql($orderId);
		$strSql =
            "SELECT PARAMS, STATUS, MESSAGE, OK, ATTEMPT ".
            "FROM ".self::$tableName." ".
			"WHERE ORDER_ID = '".$orderId."'";
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		$arReturn=array();
		if($arr = $res->Fetch())
			return $arr;
		else return false;
	}

	public static function CheckRecord($orderId){
		global $DB;
		
		$orderId = $DB->ForSql($orderId);
        $strSql =
            "SELECT ID, STATUS ".
            "FROM ".self::$tableName." ".
			"WHERE ORDER_ID = '".$orderId."'";
	
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if($res && $arr = $res->Fetch())
			return $arr;
		return false;
	}
	
	public static function updateStatus($oId,$status,$message='',$barcode='',$encBarcode=''){
		global $DB;
		$oId = $DB->ForSql($oId);
		$okStat='';
		$status = $DB->ForSql($status);
		if($status=='OK')
			$okStat=" OK='1',";
		if($status=='DELETE')
			$okStat=" OK='',";
		$message = $DB->ForSql($message);
		$barcode = $DB->ForSql($barcode);
		$encBarcode = $DB->ForSql($encBarcode);
		
		$setStr = "STATUS ='".$status."', MESSAGE = '".$message."',";
		if($barcode)
			$setStr.="BARCODE = '".$barcode."', ENCBARCODE = '".$encBarcode."',";
		$setStr.=$okStat." UPTIME= '".mktime()."'";
		
		$strSql =
            "UPDATE ".self::$tableName." 
			SET ".$setStr."
			WHERE ORDER_ID = '".$oId."'";
		if($DB->Query($strSql, true))
			return true;
		else 
			return false;
	}
}
?>