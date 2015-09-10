<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ReservationDBUtil{
    public static function addReservation($customerId, $clubId, $setId, $status) {
        $connection = DBUtil::connectDB();
        $result = mysql_query("insert into reservation (customer_id,  consume_set_id, club_id, status) "
                . "values ('{$customerId}', '{$setId}', '{$clubId}', '{$status}')", $connection);
        $id = mysql_insert_id();
        mysql_close();
        return $result != FALSE ? $id : 0;
        
    }
    
    public static function getReservationByUserId($userId){
        $connection = DBUtil::connectDB();
        $result = mysql_query("select * from reservation where customer_id={$userId}", $connection);
        $reservations = [];
        while($row = mysql_fetch_array($result)){
            $r['customerId'] = $row['customer_id'];
            $r['consumeSet'] = ConsumeSetDBUtil::getSetById($row['consume_set_id']);
            $r['club'] = ClubDBUtil::getClubById($row['club_id']);
            $r['status'] = $row['status'];
            $reservations[] = $r;
        }
        mysql_close();        
        return $reservations;
    }
    
    public static function getReservationById($id){
        $connection = DBUtil::connectDB();
        $result = mysql_query("select * from reservation where id={$id}", $connection);
        $reservations = [];
        while($row = mysql_fetch_array($result)){
            $r['customerId'] = $row['customer_id'];
            $r['consumeSet'] = ConsumeSetDBUtil::getSetById($row['consume_set_id']);
            $r['club'] = ClubDBUtil::getClubById($row['club_id']);
            $r['status'] = $row['status'];
            $reservations[] = $r;
        }
        mysql_close();        
        return $reservations;        
    }
    
    public static function updateStatusAndPayChannel($status, $channel = NULL) {
        $connection = DBUtil::connectDB();
        $ret = mysql_query("update reservation set status='{$status}',pay_channel='{$channel}'", $connection);
        mysql_close();
        return $ret;
    }
    
    public static function cancelReservation($reservationId){
        $connection = DBUtil::connectDB();
        mysql_query("delete from reservation where id={$reservationId}", $connection);
        mysql_close();
        return mysql_errno() == 0;
    }
}    