<?php

set_time_limit(1000);

// PHP Simple HTML DOM Parser
require('simple_html_dom.php');
require('PHPExcel.php');

$models = array();

$headArray = [];
$headArray[] = 'name';
$headArray[] = 'description';

//$resultArray = getParsingResultNew('https://www.brandtpolaris.ru/technique/ranger/new/', '2021');

// rzr ------------------------------------------------------------------------------------------------------------------------------
$resultArray = getParsingResultNew('https://www.brandtpolaris.ru/technique/rzr/new/', '2021');

$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/rzr/2020/', '2020'));

// ranger 
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/ranger/new/', '2021'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/ranger/2020/', '2020'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/ranger/2019/', '2019'));

// general
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/general/new/', '2021'));

// квадроциклы
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/atv/new/', '2021'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/atv/2020/', '2020'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/atv/2013/', '2013'));

// ace
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/ace/new1/', '2021'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/ace/2017/', '2017'));

// снегоходы 
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/snowmobile/new/', '2022'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/snowmobile/2021/', '2021'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/snowmobile/2020/', '2020'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/snowmobile/2019/', '2019'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/snowmobile/2018/', '2018'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/snowmobile/2017/', '2017'));
$resultArray = array_merge($resultArray, getParsingResultNew('https://www.brandtpolaris.ru/technique/snowmobile/2015/', '2015'));

$sheet = array(
    $headArray
);

foreach ($resultArray as $row) {
	$rowArray = array();
	foreach($headArray as $specName) {
		$rowArray[] = $row[$specName];
	}
	$sheet[] = $rowArray;
}

$doc = new PHPExcel();
$doc->setActiveSheetIndex(0);
$doc->getActiveSheet()->fromArray($sheet, null, 'A1');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="your_name.xls"');
header('Cache-Control: max-age=0');
$writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');
$writer->save('descriptions_4.xls');

function getParsingResultNew($url, $year) {
	$result = file_get_contents($url);
	$html = str_get_html($result);

	$resultArray = [];
	foreach($html->find('ul.models li') as $element) {
		$name = '';
		$newArray = [];
		$a = $element->find('a.header_name_item')[0];
		$name = trim($year . " " . trim($a->title));
		$href = $a->href;
		$newArray['name'] = $name;
		$detailResult = file_get_contents('https://www.brandtpolaris.ru' . $href);
		$detailHtml = str_get_html($detailResult);
		$descriptionResult = "";
		$descriptionElement = $detailHtml->find('div.block_opisanie')[0];
		foreach ($descriptionElement->find('.soc_blocks') as $socBlock) {
			$socBlock->outertext = "";
		}
		foreach ($descriptionElement->find('img') as $img) {
			$img->width = 'auto';
			$img->height = 'auto';
			$img->src = "https://www.brandtpolaris.ru" . $img->src;
		}
		$descriptionResult = $descriptionElement->innertext;
		if ($descriptionResult) {
			$descriptionResult .= "<div style='clear: both;'> </div>";
		} 
		$newArray['description'] = $descriptionResult;
				
		$detailHtml->clear(); 
		unset($detailHtml);
			
		$resultArray[] = $newArray;
	}
	$html->clear(); 
	
	return $resultArray;
}


unset($html);
return;