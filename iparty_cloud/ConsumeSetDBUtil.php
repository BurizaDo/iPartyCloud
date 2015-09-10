<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ConsumeSetDBUtil{
    public static function addSet($clubId, $title, $content, $price, $validate_time_start, $validate_time_end){
        var_dump('hahah');
        $connection = DBUtil::connectDB();
        var_dump('hahah2');
        $result = mysql_query("insert into consume_set (title, content, club_id, price, valid_time_start, valid_time_end) "
                . "values ('{$title}', '{$content}', '{$clubId}', '{$price}', '{$validate_time_start}', '{$validate_time_end}')", $connection);
        var_dump(mysql_error());
        $id = mysql_insert_id(); 
        mysql_close();
        return $result != FALSE ? $id : FALSE;
        
    }
    
    public static function getAllSetsByClubId($clubId){
        $con = DBUtil::connectDB();
        $result = mysql_query("SELECT * FROM consume_set where club_id={$clubId}", $con);
        $sets = [];
       
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['id'] = $row['id'];
            $r['title'] = $row['title'];
            $r['content'] = $row['content'];
            $r['clubId'] = $row['club_id'];
            $r['price'] = $row['price'];
            $r['valid_time_start'] = $row['valid_time_start'];
            $r['valid_time_end'] = $row['valid_time_end'];
            $sets[] = $r;
        }
        mysql_close();        
        return $sets;
    }
    
    public static function getSetById($setId){
        $conn = DBUtil::connectDB();
        $result = mysql_query("SELECT * from consume_set where id={$setId}", $conn);
        $sets = [];
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['id'] = $row['id'];
            $r['title'] = $row['title'];
            $r['content'] = $row['content'];
            $r['clubId'] = $row['club_id'];
            $r['price'] = $row['price'];
            $r['valid_time_start'] = $row['valid_time_start'];
            $r['valid_time_end'] = $row['valid_time_end'];
            $sets[] = $r;
        }
        mysql_close();        
        return $sets;        
    }
    
    public static function deleteSet($setId){
        $con = DBUtil::connectDB();
        mysql_query("DELETE FROM consume_set where id={$setId}", $con);
        mysql_close();
        return mysql_errno() == 0;        
    }
}