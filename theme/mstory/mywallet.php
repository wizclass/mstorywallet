<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
include_once(G5_PLUGIN_PATH.'/Encrypt/rule.php');

header("Content-Type:text/html;charset=utf-8");


if($nw['nw_with'] == 'N'){
  alert("현재 서비스를 이용할수없습니다.");
}

login_check($member['mb_id']);
$title = 'Mywallet';


// 입금설정
$deposit_setting = wallet_config('deposit');
$deposit_fee = $deposit_setting['fee'];
$deposit_min_limit = $deposit_setting['amt_minimum'];
$deposit_max_limit = $deposit_setting['amt_maximum'];
$deposit_day_limit = $deposit_setting['day_limit'];

// 출금설정(esgc)
$withdrwal_setting = wallet_config('withdrawal');
$withdrwal_fee = $withdrwal_setting['fee'];
$withdrwal_min_limit = $withdrwal_setting['amt_minimum'];
$withdrwal_max_limit = $withdrwal_setting['amt_maximum'];
$withdrwal_day_limit = $withdrwal_setting['day_limit'];

// 출금설정(eth)
$withdrwal_eth_setting = wallet_config('withdrawal_eth');
$withdrwal_eth_fee = $withdrwal_eth_setting['fee'];
$withdrwal_eth_min_limit = $withdrwal_eth_setting['amt_minimum'];
$withdrwal_eth_max_limit = $withdrwal_eth_setting['amt_maximum'];
$withdrwal_eth_day_limit = $withdrwal_eth_setting['day_limit'];


  // 수수료제외 실제 출금가능금액
  if ($withdrwal_max_limit != 0 && ($total_token_balance * $withdrwal_max_limit * 0.01) < $total_token_balance) {
    $total_token_balance = $total_token_balance * ($withdrwal_max_limit * 0.01);
  }

  if ($withdrwal_eth_max_limit != 0 && ($total_eth_balance * $withdrwal_eth_max_limit * 0.01) < $total_eth_balance) {
    $total_eth_balance = $total_eth_balance * ($withdrwal_eth_max_limit * 0.01);
  }

  $total_token_balance = shift_coin($total_token_balance); // esgc
  $total_eth_balance = shift_coin($total_eth_balance); // eth


//계좌정보
$wallet_setting = wallet_config('wallet_addr');
$wallet_address = $wallet_setting['wallet_addr'];
// $bank_name = $wallet_setting['bank_name'];
// $bank_account = $wallet_setting['bank_account'];
// $account_name = $wallet_setting['account_name'];

//시세 업데이트 시간
// $next_rate_time = next_exchange_rate_time();

//보너스/예치금 퍼센트
// $bonus_per = bonus_state($member['mb_id']);

$deposit_withdraw_sql = "(select txhash as credit, coin,amt,create_dt,'deposit' as states, status from {$g5['deposit']} where mb_id = '{$member['mb_id']}') 
    union all 
    (select addr as credit, coin, amt_total AS amt ,create_dt, 'withdraw' as states, status from {$g5['withdrawal']} where mb_id = '{$member['mb_id']}') 
    order by create_dt desc limit 0,3";
    // echo $deposit_withdraw_sql ;
$deposit_withdraw_result = sql_query($deposit_withdraw_sql);
$deposit_withdraw_cnt = sql_num_rows($deposit_withdraw_result);

// 패키지 선택하고 들어왔으면 입금할 가격표시
if ($_GET['sel_price']) {
  $sel_price = $_GET['sel_price'];
}

// print_r($coin);
// 입금 OR 출금
if ($_GET['view'] == 'withdraw') {

  $view = 'withdraw';
  $history_target = $g5['withdrawal'];
} else {
  $view = 'deposit';
  $history_target = $g5['deposit'];
}

//kyc인증
$kyc_cert = $member['kyc_cert'];

/*날짜계산*/
$qstr = "stx=" . $stx . "&fr_date=" . $fr_date . "&amp;to_date=" . $to_date;
$query_string = $qstr ? '?' . $qstr : '';

$fr_date = date("Y-m-d", strtotime(date("Y-m-d") . "-1 day"));
$to_date = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day"));

$sql_search_deposit = " WHERE mb_id = '{$member['mb_id']}' ";
// $sql_search_deposit .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

$rows = 3; //한페이지 목록수

//입금내역
$sql_common_deposit = "FROM {$g5['deposit']}";

$sql_deposit = " select count(*) as cnt {$sql_common_deposit} {$sql_search_deposit} ";
$row_deposit = sql_fetch($sql_deposit);

$total_count_deposit = $row_deposit['cnt'];
$total_page_deposit  = ceil($total_count_deposit / $rows);

if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record_deposit = ($page - 1) * $rows; // 시작 열

$sql_deposit = " select * {$sql_common_deposit} {$sql_search_deposit} order by create_dt desc limit {$from_record_deposit}, {$rows} ";
$result_deposit = sql_query($sql_deposit);
// echo $sql_deposit;

//출금내역
$sql_common = "FROM {$g5['withdrawal']}";
// $sql_common ="FROM wallet_withdrawal_request";

$sql_search = " WHERE mb_id = '{$member['mb_id']}' ";
// $sql_search .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
if ($debug) echo "<code>" . $sql . "</code>";

$row = sql_fetch($sql);
$total_count = $row['cnt'];
$withdrawal_count = $row['cnt'];

$total_page  = ceil($total_count / $rows);
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record = ($page - 1) * $rows; // 시작 열

$sql = " select * {$sql_common} {$sql_search} order by create_dt desc limit {$from_record}, {$rows} ";
$result_withdraw = sql_query($sql);

// 출금 승인 내역 
$amt_auth_log = sql_query("SELECT * from {$g5['withdrawal']} WHERE mb_id = '{$member['mb_id']}'  AND status = 1 ");
$auth_cnt = sql_num_rows($amt_auth_log);


//출금주소 
$with_wallet = Decrypt($member['mb_wallet'],$member['mb_id'],'x');
$with_eth_wallet = Decrypt($member['eth_my_wallet'],$member['mb_id'],'x')
?>

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>
<script src="<?=G5_THEME_URL?>/_common/js/qrcode.js"></script>
<style>
  input[type='text'].modal_input{
    background: #ededed;
    margin-top: 10px;
    box-shadow: inset 1px 1px 1px rgb(0 0 0 / 50%);
    border: 0;
    text-align: center;
    width: 50%;
  }
  .time_remained{
    display: block;
    text-align: center;
  }
  .processcode{
    color: red;
    display: block;
    text-align: center;
    font-size: 13px;
  }
</style>

<main style="margin-top: -20px; padding-top: 20px">
  <div class='container mywallet'>
    <div class="status_card_wrap esgc">
      <h5>출금가능수량</h5>
      <div class="quantity_wrap">
        <p class="esgc_quantity"><?=$shift_total_token_balance?> <span class="currency"><?=ASSETS_CURENCY?></span></p>
        <!-- <p class="price"><?=$total_token_balance_krw?> <?=BALANCE_CURENCY?></p> -->
      </div>
      <div class="link_btn_wrap">
        <a class="deposit" href="javascript:link('deposit','esgc');">입금</a>
        <a class="withdraw" href="javascript:link('withdraw','esgc');">출금</a>
      </div>
    </div>
    <!-- 입금 -->
    <section id='deposit' class='loadable <?=$_GET["view"] == "deposit" ? "active" : "" ?>'>
      <div class="box_ty01">
        <div class="content_wrap" id="eth">
          <div class="wallet qrBox">
            <div class="eth_qr_img qr_img" id="my_eth_qr"></div>
          </div>
          <div class="qrBox_right">
            <input type="text" id="my_eth_wallet" class="wallet_addr" value="" title='my address' readonly/>
            <p class="explain_text">최소 입금 수량은 <?=$deposit_min_limit?> <?=ASSETS_CURENCY?> 입니다.<br><?=$deposit_min_limit?> ESGC 미만 입금 시 잔고 반영이 불가합니다.<br>입금 후 하단의 입금신청을 눌러주세요.</p>
            <button class="btn wd line_btn wd" id="accountCopy" onclick="copyURL('#my_eth_wallet')">
              <span >주소복사</span>
            </button>
          </div>
          <div>
            <input type="text" id="deposit_name" class='p15' placeholder="TXID 입력">
          </div>
          <div class="deposit_value_wrap">
            <input type="text" inputmode=numeric id="deposit_value" class='p15' placeholder="입금수량">
            <label class='currency-right'><?= ASSETS_CURENCY ?></label>
          </div>
          <!-- 안내사항 -->
          <div>
            <p class="guide"><a data-toggle="collapse" href="#guide" role="button" aria-expanded="false" aria-controls="guide">안내사항<img src="<?=G5_THEME_URL?>/img/guide_more.png" alt=""></a></p>
            <div class="collapse" id="guide">
              <div class="guide_content">
                <p class="title"><img src="<?=G5_THEME_URL?>/img/caution.png" alt="">입금 전 꼭! 알아두세요.</p><br>
                <ul>
                  <li>
                    자금 세탁 행위 예방 및 전기 통신 금융 사기 피해 방지를 위해 계정당 첫 디지털 자산 입금 후 일정시간
                    동안 타지갑으로의 출금이 제한될 수 있습니다.<br><br>
                  </li>
                  <li>
                    위 주소로는 ESGC만 입금 가능합니다. 해당 주소로 다른 디지털 자산을 입금 시도 할 경우에 발생 할 수 있는
                    오류/손실은 복구 불가능합니다.<br><br>
                  </li>
                  <li>
                    입금 신청 이후, 입금 프로세스를 거쳐 잔고에 반영됩니다. 입금 프로세스는 네트워크 상황에 따라 소요 시간이
                    달라질 수 있습니다.<br><br>
                  </li>
                  <li>
                    위 주소는 입금 전용 주소입니다.<br><br>
                  </li>
                  <li>
                    입금은 익일 순차적으로 처리됩니다.<br><br>
                  </li>
                </ul>
              </div>
            </div>
            <button class="btn btn_wd font_white deposit_request" data-currency="<?= ASSETS_CURENCY ?>">
              <span >입금 신청</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- 출금 -->
    <section id='withdraw' class='loadable <?=$_GET["view"] == "withdraw" ? "active" : "" ?> esgc'>
      <div class="box_ty01">          
        <div class="content_wrap">
          <input id="available_withdraw" type="hidden" value="<?= $total_token_balance ?> <?= ASSETS_CURENCY ?>">
          <div>
         
            <input type="text" id="withdraw_wallet_address" class="p15" placeholder="지갑 주소 입력" value="<?=$with_wallet ?>">
          </div>
          <div>
            <div class="withdraw_fee_wrap">                   
              <input type="text" id="sendValue" inputmode=numeric class="send_coin p15" placeholder="출금수량 (수수료:<?= $withdrwal_fee ?>%)">
              <button type='button' id='max_value' class='btn inline' value=''>Max</button>
              <label class='currency-right' id="withdraw_currency"><?= ASSETS_CURENCY ?></label>
            </div>
            <div class="real_withdraw">
              <span id='fee_val'></span>
            </div>
          </div>
          <div>
            <input type="password" id="pin_auth_with" class="p15" name="pin_auth_code"  maxlength="6" placeholder="핀 번호">
          </div>
          <!-- 안내사항 -->
          <div>
            <p class="guide"><a data-toggle="collapse" href="#guide" role="button" aria-expanded="false" aria-controls="guide">안내사항<img src="<?=G5_THEME_URL?>/img/guide_more.png" alt=""></a></p>
            <div class="collapse" id="guide">
              <div class="guide_content">
                <p class="title"><img src="<?=G5_THEME_URL?>/img/caution.png" alt="">출금 전 꼭! 알아두세요.</p><br>
                <ul>
                  <li>
                    디지털 자산의 특성상 출금 신청이 완료되면 취소 할 수 없습니다. 보내기전 주소와 수량을 꼭 확인 해주세요.<br><br>
                  </li>
                  <li>
                    이더리움은 이더리움 지갑으로만 송금이 가능합니다. 오입금에 주의하시기 바랍니다.<br><br>
                  </li>
                  <li>
                    출금이 이루어지는 주소는 타지갑의 입금 주소와 동일하지 않습니다.<br><br>
                  </li>
                  <li>
                    출금 신청 완료 이후의 송금 과정은 블록체인 네트워크에서 처리됩니다. 이 과정에서 송금 지연 등의 문제가 발생 할 수 있습니다.<br><br>
                  </li>
                  <li>
                    부정 거래가 의심되는 경우 출금이 제한 될 수 있습니다.<br><br>
                  </li>
                  <li>
                    타인의 지시나 요청 등으로 본인 명의 MSTORY WALLET 계정을 타인에게 대여 시 법적 처벌대상이 될 수 있습니다.<br><br>
                  </li>
                  <li>
                    실명 인증된 계정을 타인에게 대여하는 경우 개인 정보 노출 위험에 처할 수 있습니다.<br><br>
                  </li>
                  <li>
                    출금은 익일 순차적으로 처리됩니다.<br><br>
                  </li>
                </ul>
              </div>
            </div>
            <div class="send-button-container row">
              <div class="col-5">
                <button id="pin_open" class="btn wd yellow form-send-button" >인증</button>
              </div>
              <div class="col-7">
                <button type="button" class="btn wd btn_wd form-send-button" id="Withdrawal_btn" data-toggle="modal" data-target="">출금 신청</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?if($memlev > 1){?>
    <div class="status_card_wrap eth">
      <h5>출금가능수량</h5>
      <div class="quantity_wrap">
        <p class="esgc_quantity"><?=$shift_total_eth_balance?> <span class="currency"><?=WITHDRAW_CURENCY?></span></p>
        <!-- <p class="price"><?=$total_eth_balance_krw?> <?=BALANCE_CURENCY?></p> -->
      </div>
      <div class="link_btn_wrap">
        <a href="javascript:link('withdraw','eth');">출금</a>
      </div>
    </div>
    <?}?>
  </div>
  <div>
    <div class="history_nav_wrap">
        <a class="active all_nav" href="">전체</a>
        <a class="deposit_nav" href="">입금</a>
        <a class="withdraw_nav" href="">출금</a>
    </div>
    <div class="history_box">
      <div class="all_history">
      <? if ($deposit_withdraw_cnt == 0) { ?>
        <div class="no_data">전체내역이 없습니다.</div>
      <? } else { ?>
        <div class='hist_con'>
        <? for ($i = 1; $i <= $row = sql_fetch_array($deposit_withdraw_result); $i++) { 
          $sign = $row['states'] == "deposit" ? ($row['amt'] >= 0 ? "+" : "") : "-";
          $receipt_coin = $row['coin'] == "ETH" ? clean_number_format(shift_coin($row['amt'])) : clean_number_format(shift_coin($row['amt'],BONUS_NUMBER_POINT),BONUS_NUMBER_POINT);
        ?>        
        <div class="hist_con_row1 <?=$row["states"] == "deposit" ? "deposit" : "withdraw"?>" data-offset="<?=$i?>">
          <div class="hist_left">
            <img src="<?=G5_THEME_URL?>/img/<?=$row["states"] == "deposit" ? "deposit" : "withdraw"?>.svg" alt="">
          </div>
          <div class="hist_mid">
            <? if ($row['states'] == "withdraw") { ?> 
              <p class="tx_id"><?=retrun_tx_func(Decrypt($row['credit'],$secret_key,$secret_iv),$row['coin'])?></p>     
            <? } else { ?>
              <p class="tx_id"><?=retrun_tx_func(Decrypt($row['credit'],$secret_key,$secret_iv),$row['coin'])?></p>  
            <? } ?>
              <p class="hist_date"><?= $row['create_dt'] ?></p>
              <p class="process_result">처리결과</p>
          </div>
          <div class="hist_right">
            <p class='hist_value'><?=$sign?><?= $receipt_coin ?> <span class="currency"><?= $row['coin'] ?></span></p>
            <!-- <p class="hist_won"><?=number_format(floor($coin['esgc_krw'] * $row['in_amt']))?> KRW</p>     -->
            <p class="hist_won"></p>    
            <span class="process_result"><? string_shift_code($row['status']) ?></span>    
          </div>
        </div>
        <? } ?>        
      </div>  
      <? } ?>
      <?php if($deposit_withdraw_cnt > 2) { ?>
        <div class="more"><a href="javascript:more_paging('hist_con','hist_con_row1',templete_all,'all','all_history',15)">+ 더보기</a></div>
      <? } ?>
      </div>
      <div class="deposit_history loadable">  
        <? if (sql_num_rows($result_deposit) == 0) { ?>
        <div class="no_data">입금내역이 없습니다.</div>
        <? } else { ?>
        <div class="hist_con">
          <? for ($i = 1; $i <= $row = sql_fetch_array($result_deposit); $i++) { ?>
          <div class="hist_con_row1 deposit" data-offset="<?=$i?>">
            <div class="hist_left">
              <img src="<?=G5_THEME_URL?>/img/deposit.svg" alt="">
            </div>
            <div class="hist_mid">
              <p class="tx_id"><?=retrun_tx_func(Decrypt($row['txhash'],$secret_key,$secret_iv),$row['coin'])?></p>
              <p class="hist_date"><?= $row['create_dt'] ?></p>
              <p class="process_result">처리결과</p>
            </div>
            <div class="hist_right">
              <p class='hist_value'><?=$row['in_amt'] >= 0 ? "+" : ""?><?= clean_number_format(shift_coin($row['in_amt'],BONUS_NUMBER_POINT),BONUS_NUMBER_POINT) ?> <span class="currency"><?= $row['coin'] ?></span></p>
              <!-- <p class="hist_won"><?=number_format(floor($coin['esgc_krw'] * $row['in_amt']))?> KRW</p>     -->
              <p class="hist_won"></p>    
              <span class="process_result"><? string_shift_code($row['status']) ?></span>    
            </div>
          </div>
          <? } ?>
        </div>
        <?php if(sql_num_rows($result_deposit) > 2) { ?>
        <div class="more"><a href="javascript:more_paging('hist_con','hist_con_row1',templete_deposit,'deposit','deposit_history',15)">+ 더보기</a></div>
        <? } ?>
        <? } ?>
      </div>
      <div class="withdraw_history loadable">
      <? if (sql_num_rows($result_withdraw) == 0) { ?>
        <div class="no_data">출금내역이 없습니다.</div>
      <? } else { ?>
        <div class='hist_con'>
          <? for ($i = 1; $i <= $row = sql_fetch_array($result_withdraw); $i++) { ?>
          <div class="hist_con_row1 withdraw" data-offset="<?=$i;?>">
            <div class="hist_left">
              <img src="<?=G5_THEME_URL?>/img/withdraw.svg" alt="">
            </div>
            <div class="hist_mid">
              <p class="tx_id"><?=retrun_tx_func(Decrypt($row['addr'],$secret_key,$secret_iv),$row['coin'])?></p>      
              <p class="hist_date"><?= $row['create_dt'] ?></p>
              <p class="process_result">처리결과</p>
            </div>
            <div class="hist_right">
              <p class='hist_value'>-<?=$row['coin'] == "ETH" ? clean_number_format(shift_coin($row['amt_total'])) : clean_number_format(shift_coin($row['amt_total'],BONUS_NUMBER_POINT),BONUS_NUMBER_POINT) ?> <span class="currency"><?= $row['coin'] ?></span></p>
              <!-- <p class="hist_won"><?=$row['coin'] == "ETH" ? number_format(floor($coin['eth_krw'] * $row['amt_total'])) : number_format(floor($coin['esgc_krw'] * $row['amt_total']))?> KRW</p>     -->
              <p class="hist_won"></p>    
              <span class="process_result"><? string_shift_code($row['status']) ?></span>    
            </div>
          </div>
          <? } ?>
        </div>
        <?php if(sql_num_rows($result_withdraw) > 2) { ?>
        <div class="more"><a href="javascript:more_paging('hist_con','hist_con_row1',templete_withdraw,'withdraw','withdraw_history',15)">+ 더보기</a></div>
        <? } ?>
      <? } ?>
      </div>
    </div>
  </div>
  <script>
    function more_paging(wrap,li,templete,type,parent,limit=15){

        let offset = $(`.${parent} .hist_con_row1:last-child`).data('offset');
        console.log(offset);
        let data = {
            limit:limit,
            offset:offset,
            type: type
        }

        ajax(
            `/util/mywallet_paging_proc.php`,
            "GET",
            data,
            (res)=>{    
                console.log(res)
                
                if(res.data.length < limit){
                    $(`.${type}_history .more`).attr('disabled', true).hide();
                }

                let html = "";
                res.data.forEach((res,i)=>{
                    offset += 1;
                    html += templete(res,offset);
                });

                $(`.${type}_history .${wrap}`).append(html);
            }
        )
    }

    function string_shift_code(val) {
      if(val == 0) {
          return "요청";
      } else if(val == 1) {
          return "승인";
      } else if(val == 2) {
          return "대기";
      } else if(val == 3) {
          return "불가";
      } else if(val == 4) {
          return "취소";
      } else {
          return "요청";
      }
    }

    function strpos(str,findStr) {
        return str.indexOf(findStr) != -1 ? "관리자 입금" : `<a href='https://etherscan.io/tx/${str}' target='_blank'>${short_code(str)}</a>`;
    }

    /* function short_code(string, char = 5) {
        if(string.length < 10) {
            return string;
        } else {
            return string.substr(0,5) + "....." + string.slice(-5);   
        }
    } */

    /* function retrun_tx_func(tx,coin){
      if(coin.toLowerCase() == 'eth' || ){
        return "<a href='https://etherscan.io/address/" + tx +"' target='_blank' style='text-decoration:underline'>" + short_code(tx) + "</a>";
      }else if(coin.toLowerCase() =='esgc'){
        return "<a href='https://etherscan.io/address/" + tx + "#tokentxns' target='_blank' style='text-decoration:underline'>" + short_code(tx) + "</a>";
      }else{
        return short_code(tx,10);
      }
    } */


    function shift_coin(val, num = <?=COIN_NUMBER_POINT?>) {

      let _num = Number("1".padEnd(num+1, '0'));
      return Math.floor(val * _num) / _num;
    }

    let BONUS_NUMBER_POINT = <?=BONUS_NUMBER_POINT?>;

    let templete_all = (data,index) => {

      if(data.states == "withdraw") {
        txId = `<p class="tx_id">${short_code(data.credit)}</p>`;
      } else txId = `<p class="tx_id">${retrun_tx_func(data.credit,'eth')}</p>`;

        return `<div class="hist_con_row1 <?=$row["states"] == "deposit" ? "deposit" : "withdraw"?>" data-offset="${index}">
                    <div class="hist_left">
                        <img src="<?=G5_THEME_URL?>/img/${data.states == "deposit" ? "deposit" : "withdraw"}.svg" alt="">
                    </div>
                    <div class="hist_mid">
                        ${txId}     
                        <p class="hist_date">${data.create_dt}</p>
                        <p class="process_result">처리결과</p>
                    </div>
                    <div class="hist_right">
                        
                        <p class='hist_value'>${data.states == "deposit" ? data.amt >= 0 ? "+" : "" : "-"}${data.coin == "ETH" ? Price(shift_coin(data.amt)) : Price(shift_coin(data.amt,BONUS_NUMBER_POINT))} <span class="currency">${data.coin}</span></p>
                        <p class="hist_won"></p>    
                        <span class="process_result">${string_shift_code(data.status)}</span>    
                    </div>
                </div>`;
    }

    let templete_withdraw = (data,index) => {
        return `<div class="hist_con_row1 withdraw" data-offset="${index}">
                    <div class="hist_left">
                        <img src="<?=G5_THEME_URL?>/img/withdraw.svg" alt="">
                    </div>
                    <div class="hist_mid">
                        <p class="tx_id">${short_code(data.addr)}</p>      
                        <p class="hist_date">${data.create_dt}</p>
                        <p class="process_result">처리결과</p>
                    </div>
                    <div class="hist_right">
                        <p class="hist_value">-${data.coin == "ETH" ? Price(shift_coin(data.amt_total)) : Price(shift_coin(data.amt_total,BONUS_NUMBER_POINT))} <span class="currency">${data.coin}</span></p>    
                        <span class="process_result">${string_shift_code(data.status)}</span>    
                      </div>
                </div>`;
                
    }

    let templete_deposit = (data,index) => {
        return `<div class="hist_con_row1 deposit" data-offset="${index}">
                    <div class="hist_left">
                        <img src="<?=G5_THEME_URL?>/img/deposit.svg" alt="">
                    </div>
                    <div class="hist_mid">
                        <p class="tx_id">${retrun_tx_func(data.txhash,'eth')}</p>     
                        <p class="hist_date">${data.create_dt}</p>
                        <p class="process_result">처리결과</p>
                    </div>
                    <div class="hist_right">
                        <p class="hist_value">${data.in_amt >= 0 ? "+" : ""}${Price(shift_coin(data.in_amt,BONUS_NUMBER_POINT))}<span class="currency"> ${data.coin}</span></p>    
                        <span class="process_result">${string_shift_code(data.status)}</span>    
                      </div>
                </div>`;
                
    }
  </script>
</main>

<?php include_once(G5_THEME_PATH . '/_include/tail.php'); ?>
<div class="gnb_dim"></div>
</section>

<script>
  window.onload = function() {
    var target = "<?=$_GET['target']?>";
    var view = "<?=$_GET['view']?>";

    if(view == "deposit" && target == "esgc") {
      link('deposit','esgc');
    } else if(view == "withdraw" && target == "esgc"){
      link('withdraw','esgc');
    } else if(view == "withdraw" && target == "eth") {
      link('withdraw','eth');
    }
    // switch_func("<?= $view ?>");
    // move(<?= $bonus_per ?>); 
    // getTime("<?= $next_rate_time ?>");
  }
  
  // $(function() {
    $(".top_title h3").html("<span >입출금</span>");

    var debug = "<?= $is_debug ?>";

    // 회사 지갑사용
    // var eth_wallet_addr = '<?= ETH_ADDRESS ?>';
    // if(eth_wallet_addr != ''){
    //     $('#eth_wallet_addr').val(eth_wallet_addr);
    //     generateQrCode("eth_qr_img",eth_wallet_addr, 80, 80);
    // }

    // 입금 전용 지갑사용
    var my_eth_wallet = "<?= $wallet_address ?>"
    if(my_eth_wallet != ''){
      $('#my_eth_wallet').val(my_eth_wallet);
        generateQrCode("my_eth_qr",my_eth_wallet, 80, 80);
    } 


    /* 출금*/
    var ASSETS_CURENCY = '<?= ASSETS_CURENCY ?>';
    var BALANCE_CURENCY = '<?=BALANCE_CURENCY?>';
    var mb_block = Number("<?= $member['mb_block'] ?>"); // 차단

    var mb_id = '<?= $member['mb_id'] ?>';
    var nw_with = '<?= $nw_with ?>'; // 출금서비스 가능여부
    var personal_with = '<?=$member['mb_leave_date']?>'; // 별도구분회원 여부

    // 출금설정
    var out_fee = <?= $withdrwal_fee ?>;
    var out_min_limit = '<?= $withdrwal_min_limit ?>';
    var out_max_limit = '<?= $withdrwal_max_limit ?>';
    var out_day_limit = '<?= $withdrwal_day_limit ?>';
    let pin_check =false;
    // 최대출금가능금액
    var out_mb_max_limit = "<?= $total_token_balance ?>";

    onlyNumber('pin_auth_with');
    
    // 출금금액 변경 
    let prev = "";
    function input_change() {
        let input = document.getElementById('sendValue');
        
        let shift_coin_value = ASSETS_CURENCY == "ETH" ?  <?=COIN_NUMBER_POINT?> : <?=BONUS_NUMBER_POINT?>;
        let fee_calc = input.value != "" ? "실 출금 금액(수수료 제외) : " + Price(Number(conv_number(input.value)) - Number(conv_number(out_fee)),shift_coin_value) + ` ${ASSETS_CURENCY}` : "";
        
        $('.fee').css('display', 'block');
        $('#fee_val').text(fee_calc);
    }

    $(document).on('change','.esgc #sendValue',function() {
      input_change();
    })

    $(document).on('keyup','.eth #sendValue',function(event){
        let input = document.getElementById('sendValue');
        

        input_change_eth(input)

        let fee_calc = input.value != "" ? "실 출금 금액(수수료 제외) : "+ Price(Number(conv_number(input.value)) - Number(conv_number(out_fee))) + ` ${ASSETS_CURENCY}` : "";

        $('.fee').css('display', 'block');
        $('#fee_val').text(fee_calc);
    })

    function input_change_eth(obj) {
        let pattern = /^\d+(\.)?(\d{0,8})?$/;
        let korean_check_pattern = /[가-힣ㄱ-ㅎㅏ-ㅣ\x20]/g; 
        let zero_check_pattern = /^(0)\d+/g;
        
        obj.value = obj.value.replace(zero_check_pattern,"");
        obj.value = obj.value.replace(korean_check_pattern,"");
        if(!pattern.test(obj.value)){
          obj.value = obj.value.slice(0, -1);
          return false;
        }
        

    };

    // 출금가능 맥스
    $(document).on('click',"#max_value", function() {
      $("#sendValue").val(out_mb_max_limit.replace(/,/g,''));
      input_change();
    });

    /*핀 입력*/
    $(document).on('click','#pin_open', function(e) {

      // 회원가입시 핀입력안한경우
      if ("<?= $member['reg_tr_password'] ?>" == "") {
        dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호(핀코드) 등록해주세요.</p>', 'warning');

        $('#modal_return_url').click(function() {
          location.href = "./page.php?id=profile"
        })
        return;
      }

      if ($('#pin_auth_with').val() == "") {
        dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호(핀코드) 입력해주세요.</p>', 'warning');
        return;
      }

      $.ajax({
        url: './util/pin_number_check_proc.php',
        type: 'POST',
        cache: false,
        async: false,
        data: {
          "mb_id": mb_id,
          "pin": $('#pin_auth_with').val()
        },
        dataType: 'json',
        success: function(result) {
          if (result.response == "OK") {
            dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호가 인증되었습니다.</p>', 'success');

            $('#Withdrawal_btn').attr('disabled', false);
            $('#pin_open').attr('disabled', true);
            $("#pin_auth_with").attr("readonly", true);
            pin_check = true;
          } else {
            dialogModal('출금 비밀번호(핀코드) 인증', '<p>출금 비밀번호가 일치 하지 않습니다.</p>', 'failed');
          }
        },
        error: function(e) {
          //console.log(e);
        }
      });
    });

    var time_reamin = false;
    var is_sms_submitted = false;
    var check_pin = false;
    var process_step = false;
    var mb_hp = '<?=$member['mb_hp']?>';

    function input_timer(time,where){
      var time = time;
      var min = '';
      var serc = '';

      var x = setInterval(function(){
        min = parseInt(time/50);
        sec = time%60;

        $(where).html(min + "분 " + sec + "초");
        time--;

        if(time < 0){
          clearInterval(x);
          $(where).html("시간초과");
          time_reamin = false;
        }
      },1000)
    }

    function check_auth_mobile(val){
      $.ajax({
          type: "POST",
          url: "./util/check_auth_sms.php",
          dataType: "json",
          cache: false,
          async: false,
          data: {
            pin: val,
          },
          success: function(res) {
            if (res.result == "success") {
              check_pin = true;
            } else {
              check_pin = false;
            }
          }
        });
    }
  
    // 출금요청
    $(document).on('click','#Withdrawal_btn', function() {

      var inputVal = Number($('#sendValue').val().replace(/,/g, ''));
      console.log(` out_min_limit : ${out_min_limit}\n out_max_limit:${out_max_limit}\n out_day_limit:${out_day_limit}\n out_fee: ${out_fee}`);
      
      let withdraw_wallet_address = document.getElementById('withdraw_wallet_address').value;

      // 모바일 등록 여부 확인
      if(mb_hp == '' || mb_hp.length < 10){
        dialogModal('정보수정', '<strong> 안전한 출금을 위해 인증가능한 모바일 번호를 등록해주세요.</strong>', 'warning');
        
        $('.closed').on('click',function(){
          location.href='/page.php?id=profile';
        })
        return false;
      }

      //KYC 인증
      var out_count = Number("<?=$auth_cnt ?>");
      var kyc_cert = Number("<?=$kyc_cert?>");

      if(out_count < 1 && kyc_cert != 1){
        dialogModal('KYC 인증 미등록/미승인 ', "KYC 인증 미등록/미승인<br>KYC인증이 미등록 또는 미승인 상태입니다.<br>안전한 출금을 위해 최초 1회 KYC 인증을<br>진행해주세요", 'kyc_warning');
        return false;
      }

      if(!pin_check){
        dialogModal('인증번호', '<strong>인증번호 확인을 위해 [인증]을 눌러주세요.</strong>', 'warning');
        return false;
      }

      // 계좌정보 입력 확인
      // if (withdrawal_bank_name == '' || withdrawal_bank_account == '' || withdrawal_account_name == '') {
      //   dialogModal('출금계좌확인', '<strong> 출금 계좌정보를 입력해주세요.</strong>', 'warning');
      //   return false;
      // }

      if (withdraw_wallet_address == '') {
        dialogModal('출금 지갑주소 확인', '<strong> 출금 지갑주소를 입력해주세요.</strong>', 'warning');
        return false;
      }

      // 출금서비스 이용가능 여부 확인
      if (nw_with == 'N') {
        dialogModal('서비스이용제한', '<strong>현재 출금가능한 시간이 아닙니다.</strong>', 'warning');
        return false;
      }

      if(personal_with != ''){
        dialogModal('서비스이용제한', '<strong>관리자에게 연락주세요</strong>', 'warning');
        return false;
      }
      

      // 금액 입력 없거나 출금가능액 이상일때  
      if (inputVal == '' || inputVal > Number(out_mb_max_limit)) {
        console.log(`input : ${inputVal} \n max : ${out_mb_max_limit}`);
        dialogModal('금액 입력 확인', '<strong>출금 수량을 확인해주세요.</strong>', 'warning');
        return false;
      }

      // 최소 금액 확인
      if (out_min_limit != 0 && inputVal < Number(out_min_limit)) {
        dialogModal('금액 입력 확인', '<strong> 최소 가능 수량은 ' + Price(out_min_limit) + ' ' + ASSETS_CURENCY + '입니다.</strong>', 'warning');
        return false;
      }

      //최대 금액 확인
      if (out_max_limit != 0 && inputVal > Number(out_max_limit)) {
        dialogModal('수량 입력 확인', '<strong> 1회 출금 가능 수량은 ' + Price(out_max_limit) + ' ' + ASSETS_CURENCY + '입니다.</strong>', 'warning');
        return false;
      }
      
      // process_pin_mobile().then(function (){

        $.ajax({
          type: "POST",
          url: "/util/withdrawal_proc.php",
          cache: false,
          async: false,
          dataType: "json",
          data: {
            mb_id: mb_id,
            func: 'withdraw',
            amt: inputVal,
            pin:$('#pin_auth_with').val(),
            select_coin : ASSETS_CURENCY,
            wallet_address : withdraw_wallet_address
          },
          success: function(res) {
            if (res.result == "success") {
              dialogModal('', '<p class="modal_title">출금 신청 완료</p><p class="modal_sub_text">출금 신청이 완료되었습니다.<br> 실제 출금까지 24시간 이상 소요될수있습니다.</p>', 'success');
              $('.closed').click(function() {
                location.href = '/page.php?id=mywallet&view=withdraw';
              });
            } else {
              dialogModal('Withdraw Failed', "<p>" + res.sql + "</p>", 'warning');
            }
          }
        });

      // });

    });

    function process_pin_mobile(){

      return new Promise(
        function(resolve,reject){
        dialogModal('본인인증', "<p>"+maskingFunc.phone(mb_hp)+"<br>모바일로 전송된 인증코드 6자리를 입력해주세요<br><input type='text' class='modal_input' id='auth_mobile_pin' name='auth_mobile_pin'></input><span class='time_remained'></span><span class='processcode'></span></p>", 'confirm');

        if( is_sms_submitted == false ){
          is_sms_submitted = true;

          $.ajax({
            type: "POST",
            url: "./util/send_auth_sms.php",
            cache: false,
            async: false,
            dataType: "json",
            data: {
              mb_id: mb_id,
            },
            success: function(res) {
              if (res.result == "success") {
                time_reamin = true;
                input_timer(res.time,'.time_remained');

                $('#modal_confirm').on('click',function(){
                  
                  if(!time_reamin){
                    is_sms_submitted = false;
                    alert("시간초과로 다시 시도해주세요");
                  }else{
                    var input_pin_val = $("#auth_mobile_pin").val();
                    check_auth_mobile(input_pin_val);

                    if(!check_pin){
                      $(".processcode").html("인증코드가 일치하지 않습니다.");
                      return false;
                    }else{
                      is_sms_submitted = false;
                      process_step = true;
                      resolve();
                    }
                    
                  }
                });

                $('#dialogModal .cancle').on('click',function(){
                  is_sms_submitted = false;
                  location.reload();
                });
                
              }
            }
          });

        }else{
          alert('잠시 후 다시 시도해주세요.');
        }
      });
    }
    
    // 입금확인요청 
    $(document).on('click','.deposit_request', function(e) {
      var d_name = $('#deposit_name').val(); // 입금자
      var d_price = $('#deposit_value').val().replace(/,/g, ""); // 입금액
      var coin = $(this).data('currency');
      console.log(d_price)
      // 입금설정
      var in_fee = (<?= $deposit_fee ?> * 0.01);
      var in_min_limit = '<?= $deposit_min_limit ?>';
      var in_max_limit = '<?= $deposit_max_limit ?>';
      var in_day_limit = '<?= $deposit_day_limit ?>';
      
      console.log(` in_min_limit : ${in_min_limit}\n in_max_limit:${in_max_limit}\n in_day_limit:${in_day_limit}\n in_fee: ${in_fee}`);
      console.log(' 입금자 : ' + d_name + ' || 입금액 :' + d_price);
      
      if (d_name == '' || d_price == '') {
        dialogModal('<p>입금 요청값 확인</p>', '<p class="modal_sub_text">항목을 입력해주시고 다시시도해주세요.</p>', 'warning');
        return false;
      }
      
      if(in_min_limit > 0 &&  Number(d_price) < Number(in_min_limit) ){
        dialogModal('<p>최소입금액 확인</p>', '<p>최소입금확인금액은 '+ Price(in_min_limit)+coin +' 입니다. </p>', 'warning');
        return false;
      }
      
      

      $.ajax({
        url: '/util/request_deposit.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {
          "mb_id": mb_id,
          "coin": coin,
          "hash": d_name,
          "d_price": d_price
        },
        success: function(result) {
          if (result.response == "OK") {
            dialogModal('', '<p class="modal_title">입금 신청 완료</p><p class="modal_sub_text">입금 신청이 완료되었습니다.<br> 완료 되기까지 시간이 소요될 수 있습니다.</p>', 'success');
            $('.closed').click(function() {
              location.reload();
            });
          } else {
            dialogModal('Deposit Request', result.data, 'failed');
          }
        },
        error: function(e) {
          if (debug) dialogModal('ajax ERROR', 'IO ERROR', 'failed');
        }

      });

    });
  // });


  function change_coin(target){
    let available_withdraw = document.getElementById('available_withdraw');
    let withdraw_wallet_address = document.getElementById('withdraw_wallet_address');
    let withdraw_currency = document.getElementById('withdraw_currency');
    let withdraw_value = document.getElementById('sendValue');
    // let cal_fee = document.getElementsByClassName('fee');
    // let withdraw_fee = document.getElementById('withdraw_fee');
    
    let get_changing_info = () => {
      let bucks, coin, address, fee, min, max;

      if(target == "eth" || target == "ETH"){
        bucks = "<?=$total_eth_balance?>";
        coin = "<?=WITHDRAW_CURENCY?>";
        address = "<?=$member['eth_my_wallet'] ? $with_eth_wallet  : ""?>";
        fee = "<?=$withdrwal_eth_fee?>";
        min = "<?=$withdrwal_eth_min_limit?>";
        max = "<?=$withdrwal_eth_max_limit?>";
        out_day_limit = "<?=$withdrwal_eth_day_limit?>";
      }else{
        bucks = "<?=$total_token_balance?>";
        coin = "<?=ASSETS_CURENCY?>";
        address = "<?=$member['mb_wallet'] ? $with_wallet  : ""?>";
        fee = "<?=$withdrwal_fee?>";
        min = "<?=$withdrwal_min_limit?>";
        max = "<?=$withdrwal_max_limit?>";
        out_day_limit = "<?=$withdrwal_day_limit?>";
      }

      return {
        bucks : bucks,
        coin : coin,
        address : address,
        fee : fee,
        min : min,
        max : max
      }
    }

    let {bucks, coin, address, fee, min, max} = get_changing_info();

    available_withdraw.innerText = `${bucks} ${coin}`;
    withdraw_wallet_address.value = address;
    withdraw_currency.innerText = coin;
    out_mb_max_limit = bucks;
    out_min_limit = min;
    out_max_limit = max;
    ASSETS_CURENCY = coin;
    // withdraw_fee.innerText = `출금수량 (수수료:${fee}%)`;
    out_fee = fee; 
    withdraw_value.value = "";
    withdraw_value.placeholder = `출금수량 (수수료 : ${fee} ${ASSETS_CURENCY})`;
    // cal_fee[0].style.display = "none";

    // console.log(available_withdraw.innerText, `지갑주소 : ${address}`, `코인 : ${coin}`, `수량 : ${bucks}`, `최소리밋 : ${min}`, `최대리밋 : ${max}`, `수수료 : ${fee}`);
  }
  
  function switch_func(n,target) {
    $('.mywallet .loadable').removeClass('active');
    $('#' + n).toggleClass('active');
    console.log(target)
    pin_check = false;
    let html = `<section id='withdraw' class='loadable dl active ${target}'>
      <div class="box_ty01">
        <div class="content_wrap">
          <div>
            <input id="available_withdraw" type="hidden" value="<?= $total_token_balance ?> <?= ASSETS_CURENCY ?>">
            <input type="text" id="withdraw_wallet_address" class="p15" placeholder="지갑주소 입력" value="<?= $with_wallet  ?>">
          </div>
          <div>
            <div class="withdraw_fee_wrap">
              <input type="text" ${(target == 'esgc') ? "inputmode=numeric": ""} id="sendValue" class="send_coin p15" placeholder="출금수량 (수수료:<?= $withdrwal_fee ?>%)">
              <button type='button' id='max_value' class='btn inline' value=''>Max</button>
              <label class='currency-right' id="withdraw_currency"><?= ASSETS_CURENCY ?></label>
            </div>
            <div class="real_withdraw">
              <span id='fee_val'></span>
            </div>
          </div>
          <div>
            <input type="password" id="pin_auth_with" class="p15" name="pin_auth_code"  maxlength="6" placeholder="핀 번호">
          </div>
          <div>
            <p class="guide"><a data-toggle="collapse" href="#guide" role="button" aria-expanded="false" aria-controls="guide">안내사항<img src="<?=G5_THEME_URL?>/img/guide_more.png" alt=""></a></p>
            <div class="collapse" id="guide">
              <div class="guide_content">
                <p class="title"><img src="<?=G5_THEME_URL?>/img/caution.png" alt="">출금 전 꼭! 알아두세요.</p><br>
                <ul>
                  <li>
                      디지털 자산의 특성상 출금 신청이 완료되면 취소 할 수 없습니다. 보내기전 주소와 수량을 꼭 확인 해주세요.<br><br>
                  </li>
                  <li>
                      이더리움은 이더리움 지갑으로만 송금이 가능합니다. 오입금에 주의하시기 바랍니다.<br><br>
                  </li>
                  <li>
                      출금이 이루어지는 주소는 타지갑의 입금 주소와 동일하지 않습니다.<br><br>
                  </li>
                  <li>
                      출금 신청 완료 이후의 송금 과정은 블록체인 네트워크에서 처리됩니다. 이 과정에서 송금 지연 등의 문제가 발생 할 수 있습니다.<br><br>
                  </li>
                  <li>
                      부정 거래가 의심되는 경우 출금이 제한 될 수 있습니다.<br><br>
                  </li>
                  <li>
                      타인의 지시나 요청 등으로 본인 명의 MSTORY WALLET 계정을 타인에게 대여 시 법적 처벌대상이 될 수 있습니다.<br><br>
                  </li>
                  <li>
                      실명 인증된 계정을 타인에게 대여하는 경우 개인 정보 노출 위험에 처할 수 있습니다.<br><br>
                  </li>
                  <li>
                      출금은 익일 순차적으로 처리됩니다.<br><br>
                  </li>
                </ul>
              </div>
            </div>
            <div class="send-button-container row">
              <div class="col-5">
                <button id="pin_open" class="btn wd yellow form-send-button" >인증</button>
              </div>
              <div class="col-7">
                <button type="button" class="btn wd btn_wd form-send-button" id="Withdrawal_btn" data-toggle="modal" data-target="">출금 신청</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>`;
      
    if(target == "eth") {
        $('#deposit').next('#withdraw').detach();
        if($('.mywallet section').length > 2) {
            return;
        } else {
          $('.status_card_wrap.eth').next('#withdraw').detach();  
          $('.status_card_wrap.eth').after(html);
        }
    } else if(target = "esgc") {
        if(!$('#deposit').next().hasClass('loadable')) {
          $('#deposit').after(html);
          
          if(n == "deposit") {
            $('.status_card_wrap.esgc').siblings('#withdraw').removeClass('active');
          } 
        }

        $('.status_card_wrap.eth').next().detach();
    }

    $('.guide_content').removeClass('show');
    $('.guide a img').css('transform','rotateX(0)');
  
    change_coin(target)
  }

  function switch_func_paging(n) {
    $('.loadable').removeClass('active');
    $('#' + n).toggleClass('active');
    window.location.href = window.location.pathname + "?id=mywallet&'<?= $qstr ?>'&page=1&view=" + n;
  }

  function copyURL(addr) {
    dialogModal('',`지갑주소가 복사 되었습니다.`,'success')
    var temp = $("<input>");
    $("body").append(temp);
    temp.val($(addr).val()).select();
    console.log($(addr).val())
    document.execCommand("copy");
    temp.remove();
  }

  //  QR코드
  function generateQrCode(qrImg, text, width, height){
      return new QRCode(document.getElementById(qrImg), {
          text: text,
          width: width,
          height: height,
          colorDark : "#000000",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });
  } 

  function link(type,target) {
      var url = location.href;
      if(url.indexOf("mywallet") != -1) {
          switch_func(type,target)
      } else {
          location.href = `page.php?id=mywallet&view=${type}&target=${target}`;
      }
  }

  $(".link_btn_wrap a").on('click',function(e) {
      $(this).addClass('active');
      $(this).siblings().removeClass('active');
  })

  $('.history_nav_wrap a').on('click',function(e) {
      e.preventDefault();
      let $target_nav = $(this).attr('class');
      
      if($target_nav == "deposit_nav") {
          $('.deposit_history').addClass('active').siblings().removeClass('active');
          $('.deposit_history.loadable').removeClass('active');
          $('.deposit_history').toggleClass('active');
          $('.all_history').addClass('loadable');
          $('')
      } else if($target_nav == "withdraw_nav") {
          $('.withdraw_history').addClass('active').siblings().removeClass('active');;
          $('.withdraw_history.loadable').removeClass('active');
          $('.withdraw_history').toggleClass('active');
          $('.all_history').addClass('loadable');
      } else if($target_nav == "all_nav") {
          $('.all_history').addClass('active').siblings().removeClass('active');;
          $('.all_history.loadable').removeClass('active');
          $('.all_history').toggleClass('active');
      }
      
      $('.' + $target_nav).addClass('active').siblings().removeClass('active');
  });

  if(performance.navigation.type != 1){
      if("<?=$_GET['target']?>" == "history_nav_wrap") {
        $('html, body').animate({
          scrollTop: 600
        }, 500);
    }
  }

  const url = location.href;
	if(url.indexOf('mywallet') != -1) {
		$("main").css({
      "padding-bottom": "0",
      "margin-bottom": "100px"
    });
	}

  $(document).on('click','.guide a', function() {
      collapse_image_rotate(this)
  });

  function collapse_image_rotate(target) {
    collapse_state = $(target).attr('aria-expanded');

    (collapse_state == 'true') ? deg = '180' : deg =  '360';

    $(target).find("img").css({
      "transform": `rotateX(${deg}deg)`,
      "transition": "all .2s"
    });
  }
  
</script>