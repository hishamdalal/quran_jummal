<?php
/******************************************************
 * @Project:  Arabic Jummal
 * @Version: 1.0.1
 * @Author: hishamdalal@gmail.com
 * @license:	GPL Version 3
 *****************************************************/
 
Class Jummal
{
	private $signs = ['﴿', '(', '﷽', "\n", "\r", "\t"];
	#private $alphabet = ['ا', 'ب', 'ت', 'ث', 'ج', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ك', 'ل', 'م', 'ن', 'ه', 'و', 'ي'];
	private $unicode = [
			"~[\x{0600}-\x{061F}]~u",	
			"~[\x{063B}-\x{063F}]~u",	
			"~[\x{064B}-\x{065E}]~u",	
			"~[\x{066A}-\x{06FF}]~u",	
		];
		
	private $jummal_table = [
		'ا'=>1,  'ب'=>2, 'ج'=>3, 'د'=>4, 'ه'=>5, 'و'=>6, 'ز'=>7, 'ح'=>8, 'ط'=>9,
		'ي'=>10, 'ك'=>20, 'ل'=>30, 'م'=>40, 'ن'=>50, 'س'=>60, 'ع'=>70, 'ف'=>80, 'ص'=>90,
		'ق'=>100, 'ر'=>200, 'ش'=>300, 'ت'=>400, 'ث'=>500, 'خ'=>600, 'ذ'=>700, 'ض'=>800, 'ظ'=>900,
		'غ'=>1000,
		//' '=>0
	];
	private $counter = 0;
	private $normalize = [];
	private $str = "";
	private $verses_array = [];
	private $info = [];
	private $sum = [];
	
	//------------------------------------------------------------------------//
	function __construct(){
		$this->normalize[]['اي'] = "اء";
		$this->normalize[]['يا'] = "يئ";
		$this->normalize[]['ا'] = "ءا";
		$this->normalize[]['ي'] = "ي|ى|ئ";
		$this->normalize[]['و'] = "ؤ";
		$this->normalize[]['ا'] = "ءا|ا|أ|إ|آ";
		$this->normalize[]['ه'] = "ه|ة";
		//$this->normalize['_'] = "\d+";
		
		$this->counter = 0;
		$this->info = [];
		$this->sum = [];
		$this->sum['words'] = 0;
		$this->sum['chars'] = 0;
		$this->sum['verse_words'] = 0;
		$this->sum['verse_chars'] = 0;
		$this->sum['total_jummal'] = 0;
		$this->sum['verse_jummal'] = 0;
		$this->sum['words_jummal'] = 0;
		
	}
	
	//------------------------------------------------------------------------//
	function set_verses($verses){
		$str = trim($verses) ? trim($verses) : "";
		if($str){
			$str = preg_replace($this->unicode, "", $str); // Remove tashkeel signs

			$str = str_replace($this->signs, '', $str); // Remove tashkeel signs

			$str = preg_replace("/(\﴾|\))/", '', $str);  // Remove (, )
			$str = preg_replace("/\s\s/", " ", $str); // Remove double spaces
			#$str = preg_replace("/(\d+)/", '', $str);  //Remove digits
			
			// Replace 'hamza, taa marbota, alef maksora'
			foreach($this->normalize as $k=>$ary){
				foreach($ary as $letter=>$letters){
					$str = preg_replace("/(".$ary[$letter].")/", $letter, $str);
				}
			}
			
			// Conver verses to array
			$this->verses_array = $this->split_verses($str);
		}
		$this->str = $str;
	}

	//------------------------------------------------------------------------//
	function split_verses($str){
		$pattern = "/\s?+(\d+)\s?+/";
		return preg_split($pattern, $str, -1, PREG_SPLIT_NO_EMPTY);
	}
	
	//------------------------------------------------------------------------//
	function run(){
		if(!$this->verses_array){return;}
		
		foreach($this->verses_array as $v_id=>$verse){
			$v_id++;
			
			$this->info['verses'][$v_id]['verse'] = $verse;
			$this->sum['verse_jummal'] = 0;
			$this->sum['verse_words'] = 0;
			$this->sum['verse_chars'] = 0;
			
			// Split verse to words as array
			$words_array = preg_split('/\s+/', $verse, null, PREG_SPLIT_NO_EMPTY);
			
			foreach($words_array as $w_id=>$word){
				// Split word to chars as array
				$chars_array = preg_split('//u', $word, null, PREG_SPLIT_NO_EMPTY);
				#words_count = mb_strlen($word);
				
				// Counting...
				$w_id++;
				$this->sum['words']++;
				$this->sum['verse_words']++;
				$this->info['verses'][$v_id]['words'][$w_id]['word'] = $word;
				$this->info['verses'][$v_id]['words'][$w_id]['count'] = count($chars_array);
				$this->sum['words_jummal'] = 0;
				
				
				foreach($chars_array as $ch_id=>$char){
					
					// Get jummal value for current char
					$jummal_value = @$this->jummal_table[$char];
					
					#$this->info[$v_id]['words'][$w_id]['jummal_letters'][$ch_id] = [$char=>$jummal_value];
					
					$this->counter += $jummal_value;
					
					$this->sum['chars']++;
					$this->sum['verse_chars']++;
					$this->sum['total_jummal'] += $jummal_value;
					$this->sum['verse_jummal'] += $jummal_value;
					$this->sum['words_jummal'] += $jummal_value;
				}
				
				$this->info['verses'][$v_id]['words'][$w_id]['jummal'] = $this->sum['words_jummal'];
			}
			$this->info['verses'][$v_id]['count']['words'] = $this->sum['verse_words'];
			$this->info['verses'][$v_id]['count']['chars'] = $this->sum['verse_chars'];
			$this->info['verses'][$v_id]['jummal'] = $this->sum['verse_jummal'];
			
		}

		$this->info['count']['verse'] = $v_id;
		$this->info['count']['words'] = $this->sum['words'];
		$this->info['count']['chars'] = $this->sum['chars'];
		$this->info['jummal'] = $this->sum['total_jummal'];
		
	}
	
	//------------------------------------------------------------------------//
	function get_verses_str(){
		return $this->str;
	}
	
	//------------------------------------------------------------------------//
	function get_info(){
		return $this->info;
	}
	
	//------------------------------------------------------------------------//
	function render(){
		if(!$this->info){return;}
		?>
		<a class="btn center" href="#bottom"><?=__('Bottom')?></a><br />
		<table border="1" class="center">
			<tr><th><?=__('ID')?></th><th class="mid_width"><?=__('Verse')?></th><th><?=__('Data')?></th></tr>
		<?php
		foreach($this->info as $key=>$data){
			if($key=='verses'){foreach($data as $v_id=>$verses){
			?>
			<tr>
				<th><?=$v_id?></th>
				<td class="mid_width">
					<?=$verses['verse'];?>
				</td>
				<td>
					<table border="1">
					<tr><th><?=__('ID')?></th><th><?=__('Word')?></th><th><?=__('Count')?></th><th><?=__('Jummal')?></th></tr>
					<?php
						foreach($verses['words'] as $w_id=>$word_data){
							$word = $word_data['word'];
							$count = $word_data['count'];
							$jummal = $word_data['jummal'];
							?>
							<tr><td><?=$w_id?></td><td><?=$word?></td><td><?=$count?></td><td><?=$jummal?></td></tr>
							<?php 
						}
						?>
					<tr><th></th><th><?=$verses['count']['words']?></th><th><?=$verses['count']['chars']?></th><th><?=$verses['jummal']?></th></tr>
					
					</table>
				</td>
			</tr>
			<?php
			}}
		}
			?>
			
			<tr>
				<th></th>
				<th>
					<table border="1">
						<tr>
							<th><?=__('Verses')?></th>
						</tr>
						<tr>
							<th><?=$this->info['count']['verse']?></th>
						</tr>
					</table>
				</th>
				<th>
					<table border="1">
						<tr>
							<th><?=__('Words')?></th>
							<th><?=__('Chars')?></th>
							<th><?=__('Jummal')?></th>
						</tr>
						<tr>
							<th><?=$this->info['count']['words']?></th>
							<th><?=$this->info['count']['chars']?></th>
							<th><?=$this->info['jummal']?></th>
						</tr>
					</table>
				</th>
			</tr>			
			
		</table>
		<br />
		<a class="btn center" name="bottom" href="#top"><?=__('Top')?></a>
		<?php
	}
	
	//------------------------------------------------------------------------//
	
}
