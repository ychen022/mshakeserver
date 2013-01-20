<?php
$action = $_POST['action'];
if ($action=='login'){echo "login_success";}
else if($action=='init'){
    $result = array();
    $result['address'] = "18 Catania";
    $result['city'] = "Newport Beach";
    $result['state'] = "CA";
    $result['zipcode'] = "92657";
    $result['distance'] = "8";
    $result['gender'] = "male";
    $result['groupsize'] = 2;
    $result['pricemax'] = 18;
    $result['pricemin'] = 60;
    $type = array();
    $type['cuisine_1'] = 0;
    $type['cuisine_2'] = 0;
    $type['cuisine_3'] = 0;
    $type['cuisine_4'] = 1;
    $type['cuisine_5'] = 0;
    $type['cuisine_6'] = 0;
    $type['cuisine_7'] = 0;
    $type['cuisine_8'] = 1;
    $type['cuisine_9'] = 0;
    $type['cuisine_10'] = 0;
    $type['cuisine_11'] = 0;
    $type['cuisine_12'] = 0;
    $result['type'] = $type;
    echo json_encode($result);
} else if($action=="logout"){
    echo "logout_success";
} else if($action=="startmatch"){
    $result = array();
    $match = array();
    $group1 = array();
    $group1info = array();
    $group1info['nop'] = 2;
    $type = array();
    $type[] = 'cuisine_1';
    $type[] = 'cuisine_5';
    $type[] = 'cuisine_12';
    $group1info['foodtype'] = $type;
    $group1info['pricemin'] = 20;
    $group1info['pricemax'] = 60;
    $group1info['avgdist'] = 8.5175;
    $group1info['capacity'] = 5;
    $group1['group'] = $group1info;
    $member1 = array();
    $member1['firstname'] = "John";
    $member1['lastname'] = "Bowler";
    $member1['gender'] = "male";
    $member1['foodtype'] = $type;
    $member1['photolink'] = "John";
    $member2 = array();
    $member2['firstname'] = "Min";
    $member2['lastname'] = "Zhang";
    $member2['gender'] = "female";
    $member2['foodtype'] = $type;
    $member2['photolink'] = "John";
    $member = array();
    $member[] = $member1;
    $member[] = $member2;
    $group1['member'] = $member;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $match[] = $group1;
    $result['match'] = $match;
    echo json_encode($result);
}