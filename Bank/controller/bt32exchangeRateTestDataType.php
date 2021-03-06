<?php

require_once('bt32exchangeRateAPI_Fixer.php');
use ExchangeRate\API\Fixer as API_fixer;

function ScheduleTask($requestData){
	$isDebug = true;
	
	$baseCurrency = GetSystemBaseCurrency();
	$rateProvider = "";
	
	$responseArray = Core::CreateResponseArray();
	// select a exchange rate API
	$apiFixer = new API_fixer();
	$rateProvider = "FixerAPI";
	
	$apiFixer->Initialize();
	$apiFixer->SetBaseCurrency($baseCurrency);
	
    // store JSON data to file
    $sendAPIResult = $apiFixer->SendRateAPI();
	
    $apiResponseData = $apiFixer->GetData();
    $responseArray["data"] = $apiResponseData;
	
	$exchangeRateManager = new ExchangeRateManager();
	
	// if the system base currency not equal to the fixer base currency
	if($baseCurrency != $apiResponseData["JsonObj"]["base"]){
		$baseAmount = $apiResponseData["JsonObj"]["rates"][$baseCurrency];
    }

    //http://www.freeklijten.nl/2015/08/12/DateTime-createFromFormat-without-time
    $rateCollectDate = DateTime::createFromFormat('!Y-m-d', $apiResponseData["JsonObj"]["date"]);
    // print_r($rateCollectDate);
    
    $collectDateInStr = date('Y-m-d H:i:s', $apiResponseData["JsonObj"]["timestamp"]);
    
    // $collectDateInStr = date_format($rateCollectDate, 'Y-m-d H:i:s');
    
	if(!$isDebug)
	// insert Sell to bank section, sell base currency get foreign currency
	foreach ($apiResponseData["JsonObj"]["rates"] as $currency => $apiResponseRate){
		
		$rate = $apiResponseRate;
		if($baseCurrency != $apiResponseData["JsonObj"]["base"]){
			$rate = ($baseAmount / $apiResponseRate);
        }
        if($baseCurrency == $currency){
            continue;
        }
		
		$exchangeRateManager->Initialize();
		$exchangeRateManager->BuySellType = "Sell to Bank";
		$exchangeRateManager->Provider = $rateProvider;
		$exchangeRateManager->OutCurrencyID = $currency;
		$exchangeRateManager->OutAmount = 1;
		$exchangeRateManager->InCurrencyID = $baseCurrency;
		$exchangeRateManager->InAmount = $rate;
		$exchangeRateManager->Rate = $rate;
		$exchangeRateManager->EffectiveDate = $collectDateInStr;
		$exchangeRateManager->IsEffective = "Enabled";
		
		$exchangeRateManager->insert();
		// print_r($exchangeRateManager->_);
	}
	
    
    /*
	// insert Buy from bank section, buy foreign currency get base currency
	foreach ($apiResponseData["JsonObj"]["rates"] as $currency => $apiResponseRate){
		
		$rate = $apiResponseRate;
		if($baseCurrency != $apiResponseData["JsonObj"]["base"]){
			$rate = 1/($baseAmount / $apiResponseRate);
		}
        if($baseCurrency == $currency){
            continue;
        }
		
		$exchangeRateManager->Initialize();
		$exchangeRateManager->BuySellType = "Buy from Bank";
		$exchangeRateManager->Provider = $rateProvider;
		$exchangeRateManager->OutCurrencyID = $baseCurrency;
		$exchangeRateManager->OutAmount = 1;
		$exchangeRateManager->InCurrencyID = $currency;
		$exchangeRateManager->InAmount = $rate;
		$exchangeRateManager->Rate = $rate;
		$exchangeRateManager->EffectiveDate = $collectDateInStr;
		$exchangeRateManager->IsEffective = "Enabled";
		
		// $exchangeRateManager->insert();
		print_r($exchangeRateManager->_);
    }
    */
	
    $responseArray["test"] = GetAvailableParameterData();
	
    $responseArray["access_status"] = "OK";
	
	return $responseArray;
}

function GetSystemBaseCurrency(){
	return "HKD";
}
function GetDescription(){
    $desc = "Schedulable program, test program properties in all data type.";
    return $desc;
}

function GetAvailableParameterData(){
	$parameterList = new TaskParameterList();
	
	// api type [fixer | freeCurrencyConverter | openExchangeRate]
    /*
    $parameter = new TaskParameter();
	$parameter->Initialize("apiType", TaskParameter::DATATYPE_STRING, "Fixer");
	$parameter->validOptionList = array("Fixer", "Free Currency Converter", "Open Exchange Rate");
    */
    
    $parameter = new TaskParameter();
	$parameter->Initialize("testString", "test string", TaskParameter::DATATYPE_STRING);
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test string with length", "test string with length", TaskParameter::DATATYPE_STRING);
    $parameter->dataLength = 100;
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test text area", "test text area", TaskParameter::DATATYPE_TEXT_AREA);
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test text area with length", "test text area with length", TaskParameter::DATATYPE_TEXT_AREA);
    $parameter->dataLength = 900;
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test checkbox", "test checkbox", TaskParameter::DATATYPE_STRING);
	$parameter->SetCheckboxOptionList(array("Steam", "Origin", "Uplay"));
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test radio button", "test radio button", TaskParameter::DATATYPE_STRING);
	$parameter->SetRadioOptionList(array("VISA", "Master Card", "American Express", "Paypal"));
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test number", "test number", TaskParameter::DATATYPE_INTEGER, 0);
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test number with length", "test number with length", TaskParameter::DATATYPE_INTEGER, 0);
    $parameter->dataLength = 20;
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test double with length", "test double with length", TaskParameter::DATATYPE_DOUBLE, 0);
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test double", "test double", TaskParameter::DATATYPE_DOUBLE, 0);
    $parameter->dataLength = 20;
    $parameter->decimalPlaces = 4;
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test bool", "test bool", TaskParameter::DATATYPE_BOOLEAN, 0);
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test date", "test date", TaskParameter::DATATYPE_DATE);
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test datetime", "test datetime", TaskParameter::DATATYPE_DATETIME);
	$parameterList->AddParameter($parameter);
    
    $parameter = new TaskParameter();
	$parameter->Initialize("test time", "test time", TaskParameter::DATATYPE_TIME);
	$parameterList->AddParameter($parameter);
	
	/*
    $parameter = new TaskParameter();
	$parameter->Initialize("fixerAccountToken", TaskParameter::DATATYPE_STRING);
	$parameterList->AddParameter($parameter);
	*/
	
	return json_encode($parameterList->GetList(), JSON_PRETTY_PRINT);
}
function SetParameterData(){
}
function AddParameter($dataStructureList, $dataParameter){
    $newDataStructure = $dataStructureList;
    array_push($newDataStructure, $dataParameter);
    return $newDataStructure;
}


/*

function GetTableStructure(){
	$exchangeRateManager = new ExchangeRateManager();
    
    return $exchangeRateManager->selectPrimaryKeyList();
}

function CreateData($requestData){
	$responseArray = array();
	$updateArray = array();
	$exchangeRateManager = new ExchangeRateManager();
	$updateExchangeManager = new ExchangeRateManager();
    
	$createRows = new stdClass();
	$createRows = $requestData->Data->Header;
	foreach ($createRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$exchangeRateManager->$columnName = $value;
		}
		// update records for early effectiveDate with same currency as disabled
		$sql_str = "UPDATE `exchangerate` SET ";
		$sql_str .= " `IsEffective` = 'Disabled' ";
		$sql_str .= "WHERE `OutCurrencyID` = $exchangeRateManager->OutCurrencyID AND ";
		$sql_str .= "`InCurrencyID` = $exchangeRateManager->InCurrencyID AND ";
		$sql_str .= "`EffectiveDate` <= $exchangeRateManager->EffectiveDate";
		$updateArray = $updateExchangeManager->runSQL($sql_str);
		
		$responseArray = $exchangeRateManager->insert();
	}
	return $responseArray;
}

function FindData($requestData){
	$responseArray = array();
	$exchangeRateManager = new ExchangeRateManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
    
	foreach ($updateRows as $keyIndex => $rowItem) {
        foreach ($rowItem as $columnName => $value) {
            $exchangeRateManager->$columnName = $value;
        }
        $responseArray = $exchangeRateManager->select();
        break;
    }
    
	return $responseArray;
}

function GetData($requestData){
	$responseArray = array();
	$exchangeRateManager = new ExchangeRateManager();
    
	$offsetRecords = 0;
	$offsetRecords = $requestData->Offset;
	$pageNum = $requestData->PageNum;

	$responseArray = $exchangeRateManager->selectPage($offsetRecords);
    
	return $responseArray;

}

function UpdateData($requestData){
	$responseArray = array();
	$updateArray = array();
	$exchangeRateManager = new ExchangeRateManager();
	$updateExchangeManager = new ExchangeRateManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
	foreach ($updateRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$exchangeRateManager->$columnName = $value;
		}
		// update records for early effectiveDate with same currency as disabled
		$sql_str = "UPDATE `exchangerate` SET ";
		$sql_str .= " `IsEffective` = 'Disabled' ";
		$sql_str .= "WHERE `OutCurrencyID` = $exchangeRateManager->OutCurrencyID AND ";
		$sql_str .= "`InCurrencyID` = $exchangeRateManager->InCurrencyID AND ";
		$sql_str .= "`EffectiveDate` <= $exchangeRateManager->EffectiveDate AND ";
		$sql_str .= "`ExchangeRateID` <> $exchangeRateManager->ExchangeRateID AND ";
		$updateArray = $updateExchangeManager->runSQL($sql_str);
		
		$responseArray = $exchangeRateManager->update();

	}
	return $responseArray;
}

function DeleteData($requestData){
	$responseArray = array();
	$exchangeRateManager = new ExchangeRateManager();

	$deleteRows = new stdClass();
	$deleteRows = $requestData->Data->Header;
	foreach ($deleteRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$exchangeRateManager->$columnName = $value;
		}
		$responseArray = $exchangeRateManager->delete();

	}
	return $responseArray;
}

*/
?>