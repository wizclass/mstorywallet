<?
	$imsrc = $_GET['img'];
?>

<!DOCTYPE html>
<html lang="ko-KR">
<head>
	<meta charset="utf-8" />
	<title>ESG WALLET DESIGN</title>

		<link rel="styleSheet" href="./sian.css">
	<script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	

	
</head>

<body>
<div id="info_index">
<div class="closed"><a href="#" ><i class="material-icons md-36 md-light">close</i></a></div>

<?include_once('./lnb.php');?>

<span class="info_title"><a href="./images.php?img=<?=$imsrc?>" target="_blank" class="ori">MOBILE 주요화면</a></span>
</div>
</div>



<div class="top mobile" style="text-align:center;">
<!--
	<div class="top_back mobile" style="background:url('./img/<?=$imsrc?>.jpg') no-repeat 0 0; min-width:360px;width:100%;max-width:720px; border:1px solid #aaa; border-radius:10px;">
	</div>
-->
	
	<div class="mobile_real" style="display:inline-block;"><img src="./img/<?=$imsrc?>.jpg"><span class='title'>[반응형/확대]</span></div>

	<div class="mobile_mokup" style="display:inline-block"><img src="./img/<?=$imsrc?>.jpg" style="width:360px;"><span class='title'>[모바일화면사이즈]</span></div>
	
</div>

<script type="text/javascript" src="./quick.js"></script>
</body>
</html>
