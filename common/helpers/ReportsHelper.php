<?php

namespace common\helpers;

use kartik\mpdf\Pdf;
use Yii;
use yii\db\ActiveRecord;

class ReportsHelper {
	
	/**
	 * Creates a PDF object based on a template and a map
	 * In views/templates/{your_template} should be index.php that will be the principal template file
	 * It supports HTML with keys to replace with value in the way {key_name}
	 * Also you can put an style.css file in your template folder if you are going to use styles for your template
	 * If you have a tag in your html with the class "repeat", the internal html of that tag will be repeated a maximum of
	 * n times per page, where n is the value of per-page attribute of that same tag. Also the tag should have an id
	 * and the map array needs to have the value of that id surrounded by brackets {id_value} as a key and the value should 
	 * be an array with the map for that repeat part, in the form as array. 
	 * Optionally you can set an attribute to the tag with the class "repeat" all-pages="1" and that allows to repeat the elements of
	 * the corresponding array in all pages
	 * '{id_value}' => [['{key1}' => 'value1', '{key2} => 'value2''],['{key1}' => 'value3', '{key2}' => 'value4']]
	 * The example will repeat the tag id="id_value" two times parsing {key1} and {key2} each time with the corresponding value
	 * 
	 * @param string $template the name of the folder in which will be the files for the generation of the pdf
	 * @param array $map An array in the form ['{key_to_find}' => value_to_replace] that will be parsed in the html
	 * @param string $destination The destination for the PDF ('I'=browser, 'D'=download, 'F'=file, 'S'=as string)
	 * @param string $filename The name of the pdf file in case of needed
	 * @param boolean $renderHeader If true it adds a common header to the pdf object
	 * @param boolean $renderFooter If true it adds a common footer to the pdf object 
	 * @return \kartik\mpdf\Pdf
	 */
	
	public static function getPdfObject($template='common', $map=[], $format = Pdf::FORMAT_LETTER, $orientation = Pdf::ORIENT_PORTRAIT, $destination='I', $filename=false, $renderHeader=true, $renderFooter = true){
			
			$header = $footer = '';
			if (!$filename)
				$filename = $template;
			
			$pageTemplate = Yii::$app->controller->renderPartial(sprintf('/templates/%s/index.php', $template));
			if ($renderHeader)
				$header = Yii::$app->controller->renderPartial('/templates/common/header.php');
		
			if ($renderFooter)
				$footer = Yii::$app->controller->renderPartial('/templates/common/footer.php');
			
			try {
				$headerCss = Yii::$app->controller->renderPartial('/templates/common/style.css');
			}
			catch (yii\base\ViewNotFoundException $e) {
				$headerCss = '';
			}
			try {
				$css = $headerCss.Yii::$app->controller->renderPartial(sprintf('/templates/%s/style.css', $template));
			}
			catch (yii\base\ViewNotFoundException $e){
				$css = $headerCss.'';
			}
				
			/*
			 * Do something with the iterative part here
			 */
			$result = self::processHtml($header.$pageTemplate.$footer, $map, 1);
		
			$pdf = new Pdf([
					'format' 	=> $format,
					'orientation' => $orientation,
					'content'   => $result,
					'cssInline' => $css,
		
			]);
			
			$pdf->destination = $destination;
			$pdf->filename = $filename.'.pdf';
			return $pdf;
	}
	
	/**
	 * Creates a map from an ActiveRecord Array changing the attribute format to the required by ReportsHelper {attribute_name} => attribute_value
	 * 
	 * @param array $modelsArray The ActiveRecord array to be used
	 * @param array $formats (optional) array that contains decimal numbers to tha attributes that should be formatted ['attribute_name' => decimal_digits]
	 * 
	 * @return array
	 */
	public static function fillMapFromModelsArray($modelsArray, $formats){
		$result = [];
		if ($modelsArray)
			foreach ($modelsArray as $record) {
				$result[] = self::fillMapFromStdClass($record, $formats);				
			}
		return $result;
	}
	
	/**
	 * Fills a map from the StdClass object using the format required by ReportsHelper {attribute_name} => attribute_value
	 * 
	 * @param \stdClass $object the \stdClass object to be used
	 * @param array $formats (optional) array that contains decimal numbers to tha attributes that should be formatted ['attribute_name' => decimal_digits]
	 * 
	 * @return array
	 */
	public static function fillMapFromStdClass($object, $formats=[]){
		$result = [];
		foreach ($object as $key => $value){
			if (isset($formats[$key])){
				if (is_array($formats[$key])){
					$decimals = $formats[$key][1];
					$result_value = call_user_func($formats[$key][0], $value);
				}
				elseif (is_integer($formats[$key])){
					$result_value = $value;
					$decimals = $formats[$key];
				}
				else {
					$decimals = 0;
					$result_value = call_user_func($formats[$key], $value);
				}
				$value = number_format($result_value, $decimals);
			}
			$result['{'.$key.'}'] = $value;
		}
		return $result;
	}
	
	public static function getFormattedDate($dateYmd=null, $format='dd-MMMM-yyyy', $locale='es_ES'){
		if ($dateYmd === null){
			$dateYmd = date('Y-m-d');		
		}
		$date = new \DateTime($dateYmd);
		$formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);
		$formatter->setPattern($format);
		
		return $formatter->format($date);
	}
	
	private function processHtml($pageTemplate, $map){
		setlocale(LC_TIME, "es_ES");
		$html = '';
		$page = 1;
		$pos=0;

		$unfinished = true;
		$pagebreak = '';
		$pageMap = [];
		do{
			
			$hfMap = ['{page}' => $page, '{current_date}' => self::getFormattedDate(date('Y-m-d'), 'MMMM dd \'de\' yyyy')];
			$pageMap[] = $map;
			$currentPageTemplate = self::processRepeat(true, $pageTemplate, $pageMap, $page, 1);
			//var_dump($currentPageTemplate);
			$pageMap[count($pageMap)-1] = ''; //Clean memory
			if ($currentPageTemplate == ''){
				//$currentPageTemplate = $pageTemplate;
				if (empty($html))
					$html = str_replace(array_keys($hfMap), self::array_string_values($hfMap),$pagebreak.$pageTemplate);;
				$unfinished = false;
			}			
			elseif ($currentPageTemplate == str_replace(array_keys($map), self::array_string_values($map), $pageTemplate)){
				$html .= str_replace(array_keys($hfMap), self::array_string_values($hfMap),$pagebreak.$currentPageTemplate);
				$unfinished = false;
			}
			else{
				$page++;
				$html .= str_replace(array_keys($hfMap), self::array_string_values($hfMap),$pagebreak.$currentPageTemplate);
				$pagebreak = '<pagebreak />';
				
			}
		}while($unfinished);
		
		
		$result = str_replace(array_keys($map), self::array_string_values($map), $html);
		
		//var_dump($result);
		
		return $result;
	}
	
	private function processRepeat($isRoot, $repeatTemplate, $map, $multiplier, $perPage=false, $allPages=false){	
		$result = '';
		$newRecords = false;
		if (!$perPage) $perPage = count($map);
		$continue =   ($allPages) ? 0 : 1;
		$template = $repeatTemplate;
		for ($i = ($multiplier - 1) * $perPage * $continue; $i < min(($multiplier*$perPage), count($map)); $i++){
			$noRepeats = true;
			$unfinished = false;
			$pos = 0;
			while ($tag = self::getTag(stripos($repeatTemplate, 'repeat', $pos), $repeatTemplate)){
				$noRepeats = false;
				$innerTemplate = $tag['inner_html'];
				$repeatMark = $tag['tag_text'].$tag['closing_tag_text'];
				$repeatsPerPage = $tag['per-page'];
				$repeatAllPages = $tag['all-pages'];
				
				$id = $tag['id'];
				$pos = $tag['final_pos'];
			
				$repeatMap = (isset($map[$i]['{'.$id.'}']))?$map[$i]['{'.$id.'}']:[];
					
				$repeatText = self::processRepeat(false, $innerTemplate, $repeatMap, $multiplier, $repeatsPerPage, $repeatAllPages);
				if ($repeatText != ''){
					$unfinished = true;
					if (!$repeatAllPages)
						$newRecords = true;
				}
				
				$template = str_replace($innerTemplate, $repeatText, $template);
				
			}
			if ($noRepeats || $unfinished)
				$result .= str_replace(array_keys($map[$i]), self::array_string_values($map[$i]), $template);
		}
		
		if ($isRoot && !$newRecords)
			$result = '';//str_replace(array_keys($map[$multiplier-1]), self::array_string_values($map[$multiplier-1]), $repeatTemplate);
		
		return $result;
		
	}

    /**
     * Returns an amount of money in words as a leyend formatted to by printed in an invoice
     *
     * @param float $amount Amount of money
     *
     * @return string The amount of money expressed in words as required by SAT
     */
    public static function moneyToWords($amount) {

        $nw = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $map = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',

        ];


        return str_replace(array_keys($map), array_values($map), $nw->format($amount));
    }
	
	private function array_string_values($array){
		$values = array_values($array);
		for ($i = 0 ; $i < count($values); $i++){
			if (is_array($values[$i]))
				$values[$i] = 'Error in template';
			
		}
		return $values;
	}
	
	private function getTag($position, $html){
		if ($position === false)
			return false;
		
		$iniPos = strrpos(substr($html, 0, $position), '<');
		$finPos = strpos($html, '>', $position);
		
		$tag = [];
		$tag['tag_text'] = substr($html, $iniPos, $finPos-$iniPos+1);
		
		$tagNameEndPos = self::firstStrPos($tag['tag_text'], [' ', '>'], 1);
		
		$tag['tag_name'] = substr($tag['tag_text'], 1, $tagNameEndPos-1);
		$tag['initial_pos'] = $iniPos;
		$tag['id'] = self::attributeValue($tag['tag_text'], 'id');
		$tag['classes'] = explode(' ', self::attributeValue($tag['tag_text'], 'class'));
		$tag['per-page'] = self::attributeValue($tag['tag_text'], 'per-page');
		$tag['all-pages'] = self::attributeValue($tag['tag_text'], 'all-pages');
		
		$finalTagPos = self::closingTagPos($html, $tag['tag_name'], $tag['initial_pos']+1);
		$finalTagFinPos = strpos($html, '>', $finalTagPos);
		
		$tag['final_pos'] = $finalTagFinPos;
		$tag['closing_tag_text'] = substr($html, $finalTagPos, $finalTagFinPos-$finalTagPos+1);
		$tag['inner_html'] = substr($html, $finPos+1, $finalTagPos - ($finPos+1));
		
		
		return $tag;
	}
	
	private function attributeValue($tagText, $attributeName){
		$keyPos = stripos($tagText, $attributeName);
		
		if ($keyPos){
			$valuePos = self::firstStrPos($tagText, ['"',"'"], $keyPos) + 1;
			
			/*
			$dqPos = strpos($tagText, '"', $keyPos);
			$sqPos = strpos($tagText, "'", $keyPos);
			if ($dqPos === false && $sqPos === false) $valuePos = false;
			if ($dqPos === false && $sqPos !== false) {$valuePos = $sqPos + 1; $qc = "'";}
			if ($dqPos !== false && $sqPos === false) {$valuePos = $dqPos + 1; $qc = '"';}
			if ($dqPos !== false && $sqPos !== false) {$valuePos = min($sqPos, $dqPos) + 1; $qc = $sqPos<$dqPos?"'":'"';}
			*/
			if ($valuePos !== false){
				$valueFinPos = strpos($tagText, $tagText[$valuePos-1], $valuePos);
				$value = substr($tagText, $valuePos, $valueFinPos - $valuePos);
				return $value;
			}
		}
		return false;
	}
	
	private function closingTagPos($html, $tagname, $pos){
		$count = 1;
		$next = $pos;
		
		do {
			$next += strlen($tagname);
			$next = stripos($html, $tagname, $next);
			if ($next === false)
				return false;
			if ($html[$next-1] == '/')
				$count--;
			elseif ($html[$next-1] == '<')
				$count++;		
		} while ($count > 0);
		
		return $next-2;
		
	}
	
	private function firstStrPos($string, $substrings, $start=0){
		$result = false;
		foreach ($substrings as $substring){
			$pos = stripos($string, $substring, $start);
			if ($result === false || ($pos !== false && $pos < $result)){
				$result = $pos;
			}	
		}
		return $result;
	}
	
}