<?php
function pre($mixed, $title=''){
	echo '<pre>';
	echo $title ? "<h2>$title</h2>" : '';
	print_r($mixed);
	echo '</pre>';
}

function __($word){
	if(LANG!='en'){
		global $lng;
		return isset($lng[$word]) ? $lng[$word] : $word;
	}
	return $word;
}