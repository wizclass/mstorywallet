<?php
$sub_menu = "600100";
include_once('./_common.php');
$g5['title'] = '수당 설정/관리';

include_once('../admin.head.php');
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');
$token = get_token();
?>

<style>
    table {width:100%;}
    table tbody td, table tfoot td{border:0;}
    table tbody td { height:40px;border-bottom:1px solid #f5f5f5} 
    /* table tbody tr:first-child td{background:#efefef;padding-bottom:20px;border-bottom:2px solid #333;}   */
    /* table tbody tr:nth-child(2) td{padding-top:30px;}  */
    tfoot td{background:white}
    .bonus_input{box-shadow:none;text-shadow:none;padding:10px;border:0;background: whitesmoke;}
	.btn_ly{text-align:center;}
    .btn_confirm.btn_submit:hover{background:black !important;}
    .bonus_source{height:36px;}
    hr{height:1px;float:left;width:60%;display:block;background:#333;margin:20px 0;}
</style>

<div class="local_desc01 local_desc">
    <p>
        - 마케팅수당설정<br>
        - 마이닝 : 1MH/S 당 지급량 (고정값)(<?=strtoupper($minings[$now_mining_coin])?>)<br>
        - 마이닝매칭 : 마이닝지급량의 % 입력
	</p>
</div>

<form name="allowance" id="allowance" method="post" action="./bonus_config_update.php" onsubmit="return frmconfig_check(this);" >
<div class="tbl_head02 tbl_wrap">
    <table >
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
	<p> 수당 설정 </p>
    <tr>
        <th scope="col" width="40px">No</th>
        <!-- <th scope="col" width="40px">사용설정</th> -->
        <th scope="col" width="80px">수당명</th>	
        <th scope="col" width="50px">수당코드</th>
        <th scope="col" width="50px">수당지급수단</th>
        <th scope="col" width="50px">수당한계 (%)</th>
		<th scope="col" width="150px">마이닝 지급량(coin)</th>
		<th scope="col" width="15px">수당지급제한(대수)</th>
        <th scope="col" width="80px">수당지급방식</th>
        <th scope="col" width="250px">수당설명</th>
    </tr>
    </thead>

    <tbody>
    <?for($i=0; $row=sql_fetch_array($list); $i++){
        if($row['used'] == 2){
        ?>
        <td style="text-align:center"><input type="hidden" name="idx[]" value="<?=$row['idx']?>"><?=$i?></td>
        <!-- <td style="text-align:center"><input type='checkbox' class='checkbox' name='check' <?php echo $row['used'] > 1?'checked':''; ?>>
            <input type="hidden" name="used[]" class='used' value="<?=$row['used']?>">
        </td> -->
        <input type="hidden" name="used[]" class='used' value="<?=$row['used']?>">
        <td style="text-align:center"><input class='bonus_input' name="name[]"  value="<?=$row['name']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="code[]"  value="<?=$row['code']?>"></input></td>
        
        <td style="text-align:center"><input class='bonus_input' name="kind[]"  value="<?=$row['kind']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="limited[]"  value="<?=$row['limited']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="rate[]"  value="<?=$row['rate']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="layer[]"  value="<?=$row['layer']?>"></input></td>
        <td style="text-align:center">
            <select id="bonus_source" class='bonus_source' name="source[]">
                <?php echo option_selected(0, $row['source'], "ALL"); ?>
                <?php echo option_selected(1, $row['source'], "추천인[tree]"); ?>
                <?php echo option_selected(2, $row['source'], "바이너리[binary]"); ?>
            </select>
        </td>
        <td style="text-align:center"><input class='bonus_input' name="memo[]"  value="<?=$row['memo']?>" style='width:90%;'></input></td>
        </tr>
        <?}}?>
    </tbody>

    
    
    <tfoot>
        <td colspan=9 height="100px" style="padding:10px 0" class="btn_ly">
            <input  style="align:center;padding:10px 30px;background:cornflowerblue;" type="submit" class="btn btn_confirm btn_submit" value="저장하기" id="com_send"></input>
        </td>
    </tfoot>
</table>
</div>
</form>

<style>
    #mining_log{width:400px;margin: 20px;}
    #mining_log .head{border:1px solid #eee;background:orange;display: flex;width:inherit}
    #mining_log .body{border:1px solid #eee;display: flex;width:inherit}
    #mining_log dt,#mining_log dd{display:block;padding:5px 10px;text-align: center;width:inherit;}
    #mining_log dd{border-left:1px solid #eee}
</style>

<div id='mining_log'>
    마이닝 지급량 기록 (최근 7일)
    <div class='head'>
        <dt>지급일</dt>
        <dd>마이닝지급량</dd>
    </div>

    <?
        $mining_rate_result = sql_query("SELECT day,rate from soodang_mining group by day order by day desc limit 0,7");
        while($row = sql_fetch_array($mining_rate_result)){
    ?>
    <div class='body'>
        <dt><?=$row['day']?></dt>
        <dd><?=$row['rate']?></dd>
    </div>
    <?}?>
</div>

<script>

    function frmconfig_check(f){
        
    }

    $(document).ready(function(){

        $(".checkbox" ).on( "click",function(){
            if($("input:checkbox[name='check']").is(":checked") == true){
                console.log( $(this).next().val() );
                $(this).next().val(1);
            }else{
                $(this).next().val(0);
            }
        });
        
    });

</script>
</div>

<?php
include_once ('../admin.tail.php');
?>