<?php
if (!defined('_GNUBOARD_')) exit;

function empty_mb_id($reg_mb_id)
{
    if (trim($reg_mb_id)=='')
        return "회원아이디를 입력해 주십시오.";
    else
        return "";
}

function valid_mb_id($reg_mb_id, $register_usepass = REGISTER_USEPASS)
{

    if($register_usepass == 0) {
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]*/", $reg_mb_id))
        return "회원아이디의 첫자는 영문자로 하고, 영문 또는 영문자와 숫자를 결합해 입력하세요.";
    else
        return "";
    } else {
        return valid_mb_email($reg_mb_id);
    }
    
    
}

function count_mb_id($reg_mb_id)
{
    if (strlen($reg_mb_id) < 4)
        return "회원아이디는 최소 4자 이상 입력하세요.";
    else
        return "";
}

function exist_mb_id($reg_mb_id)
{
    global $g5;

    $reg_mb_id = trim($reg_mb_id);
    if ($reg_mb_id == "") return "";

    $sql = " select count(*) as cnt from `{$g5['member_table']}` where mb_id = '$reg_mb_id' ";
    $row = sql_fetch($sql);
    if ($row['cnt'])
        return "이미 사용중인 회원아이디 입니다.";
    else
        return "";
}

function reserve_mb_id($reg_mb_id)
{
    global $config;
    if (preg_match("/[\,]?{$reg_mb_id}/i", $config['cf_prohibit_id']))
        return "이미 예약된 단어로 사용할 수 없는 회원아이디 입니다.";
    else
        return "";
}

function exist_mp_mb_id($reg_mb_id)
{
    global $g5;

    $reg_mb_id = trim($reg_mb_id);
    if ($reg_mb_id == "") return "";

    $sql = " select count(*) as cnt from `{$g5['member_table']}` where mb_id = '$reg_mb_id' and is_marketer = 'Y' ";
    $row = sql_fetch($sql);
    if ($row['cnt'])
        return "이미 사용중인 회원아이디 입니다.";
    else
        return "";
}

function empty_mb_nick($reg_mb_nick)
{
    if (!trim($reg_mb_nick))
        return "닉네임을 입력해 주십시오.";
    else
        return "";
}

function valid_mb_nick($reg_mb_nick)
{
    if (!check_string($reg_mb_nick, G5_HANGUL + G5_ALPHABETIC + G5_NUMERIC))
        return "닉네임은 공백없이 한글, 영문, 숫자만 입력 가능합니다.";
    else
        return "";
}

function count_mb_nick($reg_mb_nick)
{
    if (strlen($reg_mb_nick) < 4)
        return "닉네임은 한글 2글자, 영문 4글자 이상 입력 가능합니다.";
    else
        return "";
}

function exist_mb_nick($reg_mb_nick, $reg_mb_id)
{
    global $g5;
    $row = sql_fetch(" select count(*) as cnt from {$g5['member_table']} where mb_nick = '$reg_mb_nick' and mb_id <> '$reg_mb_id' ");
    if ($row['cnt'])
        return "이미 존재하는 닉네임입니다.";
    else
        return "";
}

function reserve_mb_nick($reg_mb_nick)
{
    global $config;
    if (preg_match("/[\,]?{$reg_mb_nick}/i", $config['cf_prohibit_id']))
        return "이미 예약된 단어로 사용할 수 없는 닉네임 입니다.";
    else
        return "";
}

function empty_mb_email($reg_mb_email)
{
    if (!trim($reg_mb_email))
        return "E-mail 주소를 입력해 주십시오.";
    else
        return "";
}

function valid_mb_email($reg_mb_email)
{
    if (!preg_match("/([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/", $reg_mb_email))
        return "E-mail 주소가 형식에 맞지 않습니다.";
    else
        return "";
}

// 금지 메일 도메인 검사
function prohibit_mb_email($reg_mb_email)
{
    global $config;

    list($id, $domain) = explode("@", $reg_mb_email);
    $email_domains = explode("\n", trim($config['cf_prohibit_email']));
    $email_domains = array_map('trim', $email_domains);
    $email_domains = array_map('strtolower', $email_domains);
    $email_domain = strtolower($domain);

    if (in_array($email_domain, $email_domains))
        return "$domain 메일은 사용할 수 없습니다.";

    return "";
}

function exist_mb_email($reg_mb_email, $reg_mb_id)
{
    global $g5;
    $row = sql_fetch(" select count(*) as cnt from `{$g5['member_table']}` where mb_email = '$reg_mb_email' and mb_id <> '$reg_mb_id' ");
    if ($row['cnt'])
        return "이미 사용중인 E-mail 주소입니다..";
    else
        return "";
}

function empty_mb_name($reg_mb_name)
{
    // if (!trim($reg_mb_name))
    //     return "이름을 입력해 주십시오.";
    // else
        return "";
}

function valid_mb_name($mb_name)
{
    if (!check_string($mb_name, G5_HANGUL))
        return "이름은 공백없이 한글만 입력 가능합니다.";
    else
        return "";
}

function valid_mb_hp($reg_mb_hp)
{
    $reg_mb_hp = preg_replace("/[^0-9]/", "", $reg_mb_hp);
    if(!$reg_mb_hp)
        return "휴대폰번호를 입력해 주십시오.";
    else {
        // if(preg_match("/^01[0-9]{8,9}$/", $reg_mb_hp))
        //     return "";
        // else
        //     return "휴대폰번호를 올바르게 입력해 주십시오.";
    }
}

function exist_mb_hp($reg_mb_hp, $reg_mb_id)
{
    // global $g5;

    // if (!trim($reg_mb_hp)) return "";

    // $reg_mb_hp = hyphen_hp_number($reg_mb_hp);

    // $sql = "select count(*) as cnt from {$g5['member_table']} where mb_hp = '$reg_mb_hp' and mb_id <> '$reg_mb_id' ";
    // $row = sql_fetch($sql);

    // if($row['cnt'])
    //     return " 이미 사용 중인 휴대폰번호입니다. ".$reg_mb_hp;
    // else
        return "";
}

function empty_mb_recommend($mb_recommend)
{
    if (!trim($mb_recommend))
        return "추천인을 입력해주세요.";
    else
        return "";
}

return;
?>
