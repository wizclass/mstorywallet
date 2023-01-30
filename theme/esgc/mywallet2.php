<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
// include_once(G5_LIB_PATH . '/blocksdk.lib.php');
// include_once(G5_LIB_PATH.'/crypto.lib.php');

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


//지갑 생성
/* $callback = G5_URL . "/plugin/blocksdk/point-callback.php";
      $blocksdk_conf = Crypto::GetConfig();

      if(empty($member['mb_9'])==true && $blocksdk_conf['de_eth_use'] == 1){
        $address = Crypto::GetClient("eth")->createAddress([
          "name" => "member_no_".$member['mb_no']
        ]);
        
        Crypto::CreateWebHook($callback,"eth",$address['address']);
        
        // $update_sql .= empty($update_sql) ? "" : ","; 
        $update_sql = "mb_9='{$address['address']}'";
        $member['mb_9'] = $address['address'];
        
        $sql = "
        insert into 
        blocksdk_member_eth_addresses (id, address, private_key) 
        values ('{$address['id']}', '{$address['address']}','{$address['private_key']}')
        ";
        sql_fetch($sql);
      }

      if(empty($update_sql) == false){
        $sql = "UPDATE {$g5['member_table']} SET {$update_sql} WHERE mb_no={$member['mb_no']}";
        sql_query($sql);
      } */

// $wallet_sql = "SELECT private_key FROM blocksdk_member_eth_addresses WHERE address = '{$member['mb_9']}'";
// $wallet_row = sql_fetch($wallet_sql);
// $private_key = $wallet_row['private_key'];
// $mb_id = $member['mb_id'];


// if($member['eth_download'] == "0"){      
//     include_once(G5_LIB_PATH."/download_key/set_private_key.php"); 
// }

// if($member['eth_download'] == "1"){
//   include_once(G5_LIB_PATH."/download_key/get_private_key.php");

// }




/*날짜계산*/
$qstr = "stx=" . $stx . "&fr_date=" . $fr_date . "&amp;to_date=" . $to_date;
$query_string = $qstr ? '?' . $qstr : '';

$fr_date = date("Y-m-d", strtotime(date("Y-m-d") . "-1 day"));
$to_date = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day"));

$sql_search_deposit = " WHERE mb_id = '{$member['mb_id']}' ";
$sql_search_deposit .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

$rows = 15; //한페이지 목록수


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

//  출금 승인 내역 
$amt_auth_log = sql_query("SELECT * from {$g5['withdrawal']} WHERE mb_id = '{$member['mb_id']}'  AND status = 1 ");
$auth_cnt = sql_num_rows($amt_auth_log);

?>


<!-- <link rel="stylesheet" href="<?= G5_THEME_CSS_URL ?>/withdrawal.css"> -->
<!-- <script type="text/javascript" src="./js/qrcode.js"></script> -->

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>

<script src="<?=G5_THEME_URL?>/_common/js/qrcode.js"></script>

<style>
  input[type='text'].modal_input{background: #ededed;
    margin-top: 10px;
    box-shadow: inset 1px 1px 1px rgb(0 0 0 / 50%);
    border: 0;
    text-align: center;
    width: 50%;}
  .time_remained{display:block;text-align:center}
  .processcode{color:red;display:block;text-align:center;font-size:13px;}
</style>

<main>
  <div class='container mywallet'>
    <?php 
        include_once(G5_THEME_PATH.'/status_card.php');
    ?>
    
    <!-- 입금 -->
    <section id='deposit' class='loadable'>
      <div class="content_box3">
        <!-- <h3 class="wallet_title" >입금지갑주소</h3> -->
        <!-- <div class="row ">
          <div class='col-12 text-center bank_info'>
            <?= $bank_name ?> : <input type="text" id="bank_account" class="bank_account" value="<?= $bank_account ?>" title='bank_account' disabled />(<?= $account_name ?>)
            <?if ($sel_price) { ?>
              <div class='sel_price'>입금액 : <span class='price'><?= Number_format($sel_price) ?><?= ASSETS_CURENCY ?></span></div>
            <?}?>
          </div>

        </div>
        <div class='col-12'>
          <button class="btn wd line_btn " style="background: #f5f5f5;" id="accountCopy" onclick="copyURL('#bank_account')">
            <span > 계좌복사 </span>
          </button>
        </div> -->
            <div class="content_wrap" id="eth">
                <div class="wallet qrBox">
                    <div class="eth_qr_img qr_img" id="my_eth_qr"></div>
                </div>
                <div class="qrBox_right">
                    <input type="text" id="my_eth_wallet" class="wallet_addr" value="" title='my address' disabled/>
                    <p class="explain_text">최소 입금 수량은 0.321 ESGC 입니다.<br>0.321 ESGC 미만 입금 시 잔고 반영이 불가합니다.<br>입금 후 하단의 입금신청을 눌러주세요.</p>
                    <button class="btn wd line_btn wd" id="accountCopy" onclick="copyURL('#my_eth_wallet')">
                            <span >주소복사</span>
                    </button>
                </div>
                <div>
                    <p class="title">TXID 주소 입력</p>
                    <input type="text" id="deposit_name" class='p15' placeholder="">
                </div>
                <div>
                    <p class="title">입금금액</p>
                    <input type="text" id="deposit_value" class='p15' placeholder="" inputmode="numeric">
                    <label class='currency-right'><?= ASSETS_CURENCY ?></label>
                    <p class="price">70.650.340 <span class="won">KRW</span></p>
                </div>
                <div>
                    <p class="guide"><a data-toggle="collapse" href="#guide" role="button" aria-expanded="false" aria-controls="guide">안내사항<img src="<?=G5_THEME_URL?>/img/guide_more.png" alt=""></a></p>
                    <div class="guide_content collapse" id="guide">
                        <p class="title"><img src="<?=G5_THEME_URL?>/img/caution.png" alt="">입금 전 꼭! 알아두세요.</p><br><br>
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
                    <button class="btn btn_wd font_white deposit_request" data-currency="<?= ASSETS_CURENCY ?>">
                        <span >입금 신청</span>
                    </button>
                </div>
                
            </div>
          <!-- 이더전용입금 -->
            
            <!-- <div class="wallet qrBox">
                <div class="eth_qr_img qr_img" id="my_eth_qr"></div>
            </div> 
            <div class='qrBox_right'>
                <input type="text" id="my_eth_wallet" class="wallet_addr" value="" title='my address' disabled/>
                <button class="btn wd line_btn wd" id="accountCopy" onclick="copyURL('#my_eth_wallet')">
                        <span >주소복사</span>
                </button>
                <input type="text" id="deposit_name" class='b_ghostwhite p15' placeholder="TXID 주소 입력">
            </div> -->
      </div>


      <!-- <div class="col-sm-12 col-12 content-box round mt20" id="eth"> -->
        <!-- <h3 class="wallet_title" >입금확인요청 </h3> <span class='desc'> - 지갑입금후 1회만 요청해주세요</span> -->
        <!-- <div style="clear:both"></div> -->
        <!-- <div class="row">
          <div class="btn_ly qrBox_right "></div>
          <div class="col-sm-12 col-12 withdraw mt20"> -->
            <!-- <input type="text" id="deposit_name" class='b_ghostwhite p15' placeholder="TXID 주소 입력"> -->

            <!-- <input type="text" id="deposit_value" class='b_ghostwhite p15' placeholder="입금액을 입력해주세요" inputmode="numeric">
            <label class='currency-right'><?= ASSETS_CURENCY ?></label>
          </div> -->

          <!-- <div class='col-sm-12 col-12 '>
            <button class="btn btn_wd font_white deposit_request" data-currency="<?= ASSETS_CURENCY ?>">
              <span >입금확인요청</span>
            </button>
          </div> -->
        <!-- </div>
      </div> -->

    </section>

  


  <!-- 출금 -->
  <section id='withdraw' class='loadable'>
    <div class="content_box3">
      <!-- <h3 class="wallet_title">출금</h3> -->
      <!-- <div class="coin_select_wrap">
          <select class="form-control" name="" id="select_coin" onchange="change_coin();">
            <option value="<?= ASSETS_CURENCY ?>" selected><?= ASSETS_CURENCY ?></option>
            <option value="<?= WITHDRAW_CURENCY ?>"><?= WITHDRAW_CURENCY ?></option>
          </select>
      </div>  -->
        
        <div class="content_wrap">
            <div>
                <p class="title">총 출금 가능 코인</p>
                <div class="b_ghostwhite" id="available_withdraw"><?= $total_token_balance ?> <?= ASSETS_CURENCY ?></div>
            </div>
            <div>
                <p class="title">TXID 주소 입력</p>
                <input type="text" id="withdraw_wallet_address" class=" " placeholder="" value="<?= $member['mb_wallet'] ?>">
            </div>
            <div>
                <p id="withdraw_fee" class="title fee">출금금액 (수수료:<?= $withdrwal_fee ?>%)</p>
                <div class="withdraw_fee_wrap">
                    <input type="text" id="sendValue" class="send_coin " placeholder="" inputmode="numeric">
                    <button type='button' id='max_value' class='btn inline' value=''>Max</button>
                    <label class='currency-right' id="withdraw_currency"><?= ASSETS_CURENCY ?></label>
                </div>
                <div class="real_withdraw">
                    <span>실 출금 금액(수수료 제외) : </span><span id='fee_val'></span>
                </div>
            </div>
            <div>
                <p class="title">핀 번호</p>
                <input type="password" id="pin_auth_with" class="" name="pin_auth_code"  maxlength="6" placeholder="">
            </div>
            <div>
                <p class="guide"><a data-toggle="collapse" href="#guide" role="button" aria-expanded="false" aria-controls="guide">안내사항<img src="<?=G5_THEME_URL?>/img/guide_more.png" alt=""></a></p>
                <div class="guide_content collapse" id="guide">
                    <p class="title"><img src="<?=G5_THEME_URL?>/img/caution.png" alt="">출금 전 꼭! 알아두세요.</p><br><br>
                    <p>1. 디지털 자산의 특성상 출금 신청이 완료되면 취소 할 수 없습니다. 보내기전 주소와 수량을 꼭 확인 해주세요.<br><br>
                       2. 이더리움은 이더리움 지갑으로만 송금이 가능합니다. 오입금에 주의하시기 바랍니다.<br><br>
                       3. 출금이 이루어지는 주소는 타지갑의 입금 주소와 동일하지 않습니다.<br><br> 
                       4. 출금 신청 완료 이후의 송금 과정은 블록체인 네트워크에서 처리됩니다. 이 과정에서 송금 지연 등 의 문제가
                       발생 할 수 있습니다.<br><br>
                       5. 부정 거래가 의심되는 경우 출금이 제한 될 수 있습니다.<br><br>
                       6. 타인의 지시나 요청 등으로 본인 명의 ESG Chain Wallet 계정을 타인에게 대여 시 법적 처벌대상이 될 수 있습니다.<br><br>
                       7. 실명 인증된 계정을 타인에게 대여하는 경우 개인 정보 노출 위험에 처할 수 있습니다.<br><br>
                       8. 출금은 익일 순차적으로 처리됩니다.
                    </p>
                </div>
                <div class="send-button-container row">
                    <div class="col-5">
                        <button id="pin_open" class="btn wd yellow form-send-button" >인증</button>
                    </div>
                    <div class="col-7">
                        <button type="button" class="btn wd btn_wd form-send-button" id="Withdrawal_btn" data-toggle="modal" data-target="" disabled>출금 신청</button>
                    </div>
                </div>
            </div>
        </div>

      <!-- <div class="row">
        <div class='col-12'><label class="sub_title">- 출금지갑정보 (최초 1회입력))</label></div>
        <div class='col-12'>
          <input type="text" id="withdraw_wallet_address" class="b_ghostwhite " placeholder="출금지갑주소" value="<?= $member['mb_wallet'] ?>">
        </div> -->
        <!-- <div class='col-6'>
          <input type="text" id="withdrawal_account_name" class="b_ghostwhite " placeholder="예금주" value="<?= $member['account_name'] ?>">
        </div>
        <div class='col-12'>
          <input type="text" id="withdrawal_bank_account" class="b_ghostwhite " placeholder="출금계좌" value="<?= $member['bank_account'] ?>">
        </div> -->
      <!-- </div> -->

      <!-- <div class="input_shift_value">
        <label class="sub_title" id="withdraw_fee">- 출금금액 (수수료:<?= $withdrwal_fee ?>%)</label>
        <span style='display:inline-block;float:right;'><button type='button' id='max_value' class='btn inline' value=''>max</button></span>

        <input type="text" id="sendValue" class="send_coin b_ghostwhite " placeholder="출금 금액을 입력해주세요">
        <label class='currency-right' id="withdraw_currency"><?= ASSETS_CURENCY ?></label> 
        
          <div class='fee' style='color:black;padding-right:3px;letter-spacing:-0.5px'>
            <span>실 출금 금액(수수료 제외) : </span><span id='fee_val' style='color:red;margin-right:10px;font-size:14px;font-weight:bold'></span>
          </div>
      </div>

      <div class="b_line5"></div>
      <div class="otp-auth-code-container mt20">
        <div class="verifyContainerOTP">
          <label class="sub_title" >- 출금 비밀번호</label>
          <input type="password" id="pin_auth_with" class="b_ghostwhite" name="pin_auth_code"  maxlength="6" placeholder="6 자리 핀코드를 입력해주세요">
        </div>
      </div>  -->

      <!-- <div class="send-button-container row">
        <div class="col-5">
          <button id="pin_open" class="btn wd yellow form-send-button" >인증</button>
        </div>
        <div class="col-7">
          <button type="button" class="btn wd btn_wd form-send-button" id="Withdrawal_btn" data-toggle="modal" data-target="" disabled>출금 신청</button>
        </div>
      </div> -->
    
    <!-- 출금내역 -->
    <!-- <div class="history_box content-box mt40">
      <h3 class="hist_tit" >출금 내역</h3>
      <div class="b_line2"></div>

      <? if (sql_num_rows($result_withdraw) == 0) { ?>
        <div class="no_data">출금내역이 존재하지 않습니다</div>
      <? } ?>

      <? while ($row = sql_fetch_array($result_withdraw)) { ?>
        <div class='hist_con'>
            <div class="hist_con_row1">
                <div class="row">
                  <span class="hist_date"><?= $row['create_dt'] ?></span>
                  <span class="hist_value "> <?=shift_auto($row['amt_total'],"coin") ?><?= $row['coin'] ?></span>
                </div>

                <div class="row">
                  <span class="hist_withval"> <?= shift_auto($row['amt'],"coin") ?> <?= $row['coin'] ?> / <label>Fee : </label> <?= shift_auto($row['fee'],"coin") ?><?= $row['coin'] ?></span>
                  <span class="hist_value status"><?=shift_auto($row['out_amt'],"coin")?> <?= $row['coin'] ?></span>
                </div> -->

                <!-- <div class="row">
                  <span class='hist_bank'><label>Address : </label><?=$row['addr']?></span>
                </div> -->
                
                <!-- <div class="row">
                  <span class="hist_withval f_small"><label>Result :</label> </span>
                  <span class="hist_value status"><? string_shift_code($row['status']) ?></span>
                </div>
            </div>
        </div>
      <? } ?>
      </div>
    </div> -->
        
  </section>
  </div>


  <div class="container history_wrap mt30">
      <div class="history_nav_wrap">
          <a class="active" href="">전체</a>
          <a href="">입금</a>
          <a href="">출금</a>
      </div>
      <div class="history_box">
        
      <? if (sql_num_rows($result_deposit) == 0) { ?>
        <div class="no_data"> 입금내역이 존재하지 않습니다.</div>
      <? } ?>

      <div class='hist_con'>
      <? while ($row = sql_fetch_array($result_deposit)) { ?>
      
          <div class="hist_con_row1">
              <div class="row">
                  <span class="hist_date"><?= $row['create_dt'] ?></span>
                  <span class="hist_value"><?= Number_format($row['in_amt']) ?> <?= $row['coin'] ?></span>
              </div>

              <div class="row">
                  <span class='hist_name'><?=retrun_tx_func($row['txhash'],"eth")?></span>
                  <span class="hist_value status"><? string_shift_code($row['status']) ?></span>
              </div>
          </div>
      
      <? } ?>
      </div>


      <? if (sql_num_rows($result_withdraw) == 0) { ?>
        <div class="no_data">출금내역이 존재하지 않습니다</div>
      <? } ?>

      <div class='hist_con'>
      <? while ($row = sql_fetch_array($result_withdraw)) { ?>
            <div class="hist_con_row1">
                <div class="row">
                  <span class="hist_date"><?= $row['create_dt'] ?></span>
                  <span class="hist_value "> <?=shift_auto($row['amt_total'],"coin") ?><?= $row['coin'] ?></span>
                </div>

                <div class="row">
                  <span class="hist_withval"> <?= shift_auto($row['amt'],"coin") ?> <?= $row['coin'] ?> / <label>Fee : </label> <?= shift_auto($row['fee'],"coin") ?><?= $row['coin'] ?></span>
                  <span class="hist_value status"><?=shift_auto($row['out_amt'],"coin")?> <?= $row['coin'] ?></span>
                </div>

                <!-- <div class="row">
                  <span class='hist_bank'><label>Address : </label><?=$row['addr']?></span>
                </div> -->
                
                <div class="row">
                  <span class="hist_withval f_small"><label>Result :</label> </span>
                  <span class="hist_value status"><? string_shift_code($row['status']) ?></span>
                </div>
            </div>
      <? } ?>
      </div>




        <!-- <div class='hist_con'>
            <div class="hist_con_row1 deposit">
                <div class="hist_left">
                    <img src="<?=G5_THEME_URL?>/img/deposit.svg" alt="">
                </div>
                <div class="hist_mid">
                    <p class="tx_id">Oxa32b...3d8z99e</p>      
                    <p class="hist_date">2022-12-12</p>
                </div>
                <div class="hist_right">
                    <p class='hist_value'>+ 5,000,000 <span class="currency">ESGC</span></p>
                    <p class="hist_won">70,650,340 KRW</p>    
                </div>
            </div>
            <div class="hist_con_row1 deposit">
                <div class="hist_left">
                    <img src="<?=G5_THEME_URL?>/img/withdraw.svg" alt="">
                </div>
                <div class="hist_mid">
                    <p class="tx_id">Oxa32b...38z99e</p>      
                    <p class="hist_date">2022-12-12</p>
                </div>
                <div class="hist_right">
                    <p class='hist_value'>+ 5,000,000 <span class="currency">ESGC</span></p>
                    <p class="hist_won">70,650,340 KRW</p>    
                </div>
            </div>
            <div class="hist_con_row1 withdraw">
                <div class="hist_left">
                    <img src="<?=G5_THEME_URL?>/img/withdraw.svg" alt="">
                </div>
                <div class="hist_mid">
                    <p class="tx_id">Oxa32b...38z99e</p>      
                    <p class="hist_date">2022-12-12</p>
                </div>
                <div class="hist_right">
                    <p class='hist_value'>+ 5,000,000 <span class="currency">ESGC</span></p>
                    <p class="hist_won">70,650,340 KRW</p>    
                </div>
            </div>
        </div> -->
        <div class="more"><a href="">+ 더보기</a></div>


        <?php
        $pagelist = get_paging($config['cf_write_pages'], $page, $total_page_deposit, "{$_SERVER['SCRIPT_NAME']}?id=mywallet&$qstr&view=deposit");
        echo $pagelist;
        ?>


        </div>
    </div>
</main>

<?php include_once(G5_THEME_PATH . '/_include/tail.php'); ?>
<div class="gnb_dim"></div>
</section>


<!-- <script src="<?= G5_THEME_URL ?>/_common/js/timer.js"></script> -->

<script>
  window.onload = function() {
    switch_func("<?= $view ?>");
    // move(<?= $bonus_per ?>); 
    // getTime("<?= $next_rate_time ?>");
  }
  
  // $(function() {
    $(".top_title h3").html("<span >입출금</span>");

    var debug = "<?= $is_debug ?>";

    /* if(debug){
      console.log('[ Mode : debug ]');
      $('#Withdrawal_btn').attr('disabled',false);
    } */

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
    var mb_block = Number("<?= $member['mb_block'] ?>"); // 차단

    var mb_id = '<?= $member['mb_id'] ?>';
    var nw_with = '<?= $nw_with ?>'; // 출금서비스 가능여부
    var personal_with = '<?=$member['mb_leave_date']?>'; // 별도구분회원 여부

    // 출금설정
    var out_fee = (<?= $withdrwal_fee ?> * 0.01);
    var out_min_limit = '<?= $withdrwal_min_limit ?>';
    var out_max_limit = '<?= $withdrwal_max_limit ?>';
    var out_day_limit = '<?= $withdrwal_day_limit ?>';

    // 최대출금가능금액
    var out_mb_max_limit = "<?= $total_token_balance ?>";

    
    onlyNumber('pin_auth_with');

    
    // 출금금액 변경 
    let prev = "";
    function input_change() {
      let input = document.getElementById('sendValue');
      let regexp = /^\d+(\.\d{0,8})?$/;
     
      if(input.value.search(regexp)==-1) {
        input.value = prev;
      }else{
        prev = input.value;
      }

      let fee_calc = calculate_math(Number(input.value) - Number(input.value * out_fee),8);

      $('.fee').css('display', 'block');
      $('#fee_val').text(`${fee_calc} ${ASSETS_CURENCY}`);
    }

    $('#sendValue').change(input_change);


    // 출금가능 맥스
    $('#max_value').on('click', function() {
      $("#sendValue").val(out_mb_max_limit.replace(/,/g,''));
      input_change();
    });


    /*핀 입력*/
    $('#pin_open').on('click', function(e) {

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
    $('#Withdrawal_btn').on('click', function() {

      var inputVal = $('#sendValue').val().replace(/,/g, '');
      // console.log(` out_min_limit : ${out_min_limit}\n out_max_limit:${out_max_limit}\n out_day_limit:${out_day_limit}\n out_fee: ${out_fee}`);
      

      // 출금계좌정보확인
      // var withdrawal_bank_name = $('#withdrawal_bank_name').val();
      // var withdrawal_account_name = $('#withdrawal_account_name').val();
      // var withdrawal_bank_account = $('#withdrawal_bank_account').val();
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

      // if(out_count < 1 && kyc_cert != 1){
      //   dialogModal('KYC 인증 미등록/미승인 ', "<strong> KYC인증이 미등록 또는 미승인 상태입니다.<br>안전한 출금을 위해 최초 1회 KYC 인증을 진행해주세요<br><a href='/page.php?id=profile' class='btn btn-primary'>KYC인증</a></strong>", 'warning');
      //   return false;
      // }

      // // 계좌정보 입력 확인
      // // if (withdrawal_bank_name == '' || withdrawal_bank_account == '' || withdrawal_account_name == '') {
      // //   dialogModal('출금계좌확인', '<strong> 출금 계좌정보를 입력해주세요.</strong>', 'warning');
      // //   return false;
      // // }

      // if (withdraw_wallet_address == '') {
      //   dialogModal('출금 지갑주소 확인', '<strong> 출금 지갑주소를 입력해주세요.</strong>', 'warning');
      //   return false;
      // }

      // // 출금서비스 이용가능 여부 확인
      // if (nw_with == 'N') {
      //   dialogModal('서비스이용제한', '<strong>현재 출금가능한 시간이 아닙니다.</strong>', 'warning');
      //   return false;
      // }

      // if(personal_with != ''){
      //   dialogModal('서비스이용제한', '<strong>관리자에게 연락주세요</strong>', 'warning');
      //   return false;
      // }
      

      // // 금액 입력 없거나 출금가능액 이상일때  
      // if (inputVal == '' || inputVal > out_mb_max_limit) {
      //   console.log(`input : ${inputVal} \n max : ${out_mb_max_limit}`);
      //   dialogModal('금액 입력 확인', '<strong>출금 금액을 확인해주세요.</strong>', 'warning');
      //   return false;
      // }

      // // 최소 금액 확인
      // if (out_min_limit != 0 && inputVal < Number(out_min_limit)) {
      //   dialogModal('금액 입력 확인', '<strong> 최소가능금액은 ' + Price(out_min_limit) + ' ' + ASSETS_CURENCY + '입니다.</strong>', 'warning');
      //   return false;
      // }

      // //최대 금액 확인
      // if (out_max_limit != 0 && inputVal > Number(out_max_limit)) {
      //   dialogModal('금액 입력 확인', '<strong> 1회 출금 가능금액은 ' + Price(out_max_limit) + ' ' + ASSETS_CURENCY + '입니다.</strong>', 'warning');
      //   return false;
      // }
      
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
            select_coin : ASSETS_CURENCY,
            wallet_address : withdraw_wallet_address
            // bank_name: withdrawal_bank_name,
            // bank_account: withdrawal_bank_account,
            // account_name: withdrawal_account_name
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

      /* if (!mb_block) {
      } else {
        dialogModal('Withdraw Failed', "<p>Not available right now</p>", 'failed');
      } */

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

    

    
    /* 입금 */




    /*입금 확인 요청 - coin */
    /* $('.deposit_request.eth').on('click', function (e) {
      var d_price = $('#deposit_value').val();

      if($('.d_price').text() != ""){
          d_price = $('.d_price').text();
      }
      
      var coin = $(this).data('currency');
      var hash_target = $(this).parent().parent().find('.confirm_hash');
      
      if(hash_target.val()==""){
          dialogModal('Deposit Confirmation Request','<p>Transaction Hash is empty!</p>','warning');
          return;
      }

      if(debug) console.log('입금 : '+ coin +' || tx :' + hash_target.val());

      $.ajax({
        url: '/util/request_deposit.php',
        type: 'POST',
        cache: false,
        async: false,
        data: {
          "mb_id" : mb_id,
          "coin" : coin,
          "hash": hash_target.val(),
          "d_price" : d_price
        },
        dataType: 'json',
        success: function(result) {
          if(result.response == "OK"){
            dialogModal('Deposit Request', 'Deposit Request success', 'success');
            $('.closed').click(function(){
              location.reload();
            });
          }else{
            if(debug) dialogModal('Deposit Request',result.data,'failed'); 
            else dialogModal('Deposit Request','<p>ERROR<br>Please try later</p>','failed');
          }
        },
        error: function(e){
          if(debug) dialogModal('ajax ERROR','IO ERROR','failed'); 
        }
        
      });
    });  */


    // 입금확인요청 
    $('.deposit_request').on('click', function(e) {
      var d_name = $('#deposit_name').val(); // 입금자
      var d_price = $('#deposit_value').val().replace(/,/g, ""); // 입금액
      var coin = $(this).data('currency');

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
            dialogModal('', '<p class="modal_title">입금 신청 완료</p><p class="modal_sub_text">입금 신청이 완료되었습니다.<br> 완료 되기까지 시간이 소요될 수 있습니다.</p>', 'warning');
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
    let selected = document.getElementById('select_coin');
    let available_withdraw = document.getElementById('available_withdraw');
    let withdraw_wallet_address = document.getElementById('withdraw_wallet_address');
    let withdraw_currency = document.getElementById('withdraw_currency');
    let withdraw_value = document.getElementById('sendValue');
    let cal_fee = document.getElementsByClassName('fee');
    let withdraw_fee = document.getElementById('withdraw_fee');
    // let selected_val = selected.options[selected.selectedIndex].value;
    
    let get_changing_info = () => {
      let bucks, coin, address, fee, min, max;

      if(target == "ETH"){
        bucks = "<?=$total_eth_balance?>";
        coin = "<?=WITHDRAW_CURENCY?>";
        address = "<?=$member['eth_my_wallet'] ? $member['eth_my_wallet'] : ""?>";
        fee = "<?=$withdrwal_eth_fee?>";
        min = "<?=$withdrawal_eth_min?>";
        max = "<?=$withdrawal_eth_min?>";
      }else{
        bucks = "<?=$total_token_balance?>";
        coin = "<?=ASSETS_CURENCY?>";
        address = "<?=$member['mb_wallet'] ? $member['mb_wallet'] : ""?>";
        fee = "<?=$withdrwal_fee?>";
        min = "<?=$withdrawal_min?>";
        max = "<?=$withdrawal_min?>";
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
    withdraw_fee.innerText = `출금금액 (수수료:${fee}%)`;
    out_fee = fee * 0.01; 
    withdraw_value.value = "";
    cal_fee[0].style.display = "none";

    // console.log(available_withdraw.innerText, `지갑주소 : ${address}`, `코인 : ${coin}`, `수량 : ${bucks}`, `최소리밋 : ${min}`, `최대리밋 : ${max}`, `수수료 : ${fee}`);
  }

  

  function switch_func(n) {
    $('.loadable').removeClass('active');
    $('#' + n).toggleClass('active');
  }

  function switch_func_paging(n) {
    $('.loadable').removeClass('active');
    $('#' + n).toggleClass('active');
    window.location.href = window.location.pathname + "?id=mywallet&'<?= $qstr ?>'&page=1&view=" + n;
  }

  function copyURL(addr) {
    alert("지갑주소가 복사 되었습니다");
    var temp = $("<input>");
    $("body").append(temp);
    temp.val($(addr).val()).select();
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

  
</script>