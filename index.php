<?php
define('LANG', 'ar');
if(file_exists('lang/'.LANG.'.php')){
	include_once 'lang/'.LANG.'.php';
}
include_once 'helpers.php';
include_once 'jummal.class.php';

?>
<!DOCTYPE html> 
<html lang="en"> 
<head> 
	<meta charset="utf-8" /> 
	<title><?=__('Advansed Jummal Calculator')?></title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

	<div class="container">
		<?php
		
		$jummal = new Jummal();
		if( isset($_POST['verses']) ){
			$jummal->set_verses(@$_POST['verses']);
			$jummal->run();
		}
		?>
		
		<form method="post" class="center">
			<div><textarea name="verses" cols="65" rows="10" 
					placeholder="قُلْ أَعُوذُ بِرَبِّ النَّاسِ ﴿1﴾ مَلِكِ النَّاسِ ﴿2﴾ إِلَٰهِ النَّاسِ ﴿3﴾ مِنْ شَرِّ الْوَسْوَاسِ الْخَنَّاسِ ﴿4﴾ الَّذِي يُوَسْوِسُ فِي صُدُورِ النَّاسِ ﴿5﴾ مِنَ الْجِنَّةِ وَالنَّاسِ ﴿6﴾"
				><?=$jummal->get_verses_str()?></textarea>
			</div>
			<div>
				<input value="<?=__('Convert')?>" type="submit" />
				<a target="_blank" href="https://equran.me">موقع القرآن</a>
			</div>
		</form>
		
		<br />

		<?php $jummal->render(); ?>
		<?php #pre($jummal->get_info() ); ?>
		
	</div>
	
</body>
</html>
