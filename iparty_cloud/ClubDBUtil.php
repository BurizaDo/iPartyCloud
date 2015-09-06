<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ClubDBUtil{
    public static function addClub($name, $longitude, $latitude, $images, $address, $meta = NULL){
        $connection = DBUtil::connectDB();
        $result = mysql_query("insert into club (name, longitude, latitude, images, address, meta) values ('{$name}', '{$longitude}', '{$latitude}', '{$images}', '{$address}', '{$meta}')", $connection);
        mysql_close();
        return $result != FALSE ? TRUE : FALSE;
    }
    
    public static function getClubs($userLongitude, $userLatitude, $range = 10){
        $searchRange = 180 / pi() * ($range / 6372.797);
        $lngR = $searchRange / cos($userLatitude * pi() / 180.0); 
        $maxLat = $userLatitude + $searchRange; 
        $minLat = $userLatitude - $searchRange;
        $maxLng = $userLongitude + $lngR; 
        $minLng = $userLongitude - $lngR;
        $con = DBUtil::connectDB();
        $result = mysql_query("SELECT * FROM club where ((latitude BETWEEN {$minLat} AND {$maxLat}) AND (longitude BETWEEN {$minLng} AND {$maxLng})", $con);
        $clubs = [];
       
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['name'] = $row['name'];
            $r['longitude'] = $row['longitude'];
            $r['latitude'] = $row['latitude'];
            $r['images'] = json_decode($row['images']);
            $r['address'] = $row['address'];
            $r['meta'] = json_decode($row['meta']);
            $clubs[] = $r;
        }
        mysql_close();        
        return $clubs;
    }
    
    public static function getClubById($clubId){
        $con = DBUtil::connectDB();
        $result = mysql_query("SELECT * FROM club where id={$clubId}", $con);
        $msgs = [];
        while($row = mysql_fetch_array($result)){
            $r['name'] = $row['name'];
            $r['longitude'] = $row['longitude'];
            $r['latitude'] = $row['latitude'];
            $r['images'] = json_decode($row['images']);
            $r['address'] = $row['address'];
            $r['meta'] = json_decode($row['meta']);
            $msgs[] = $r;
        }
        mysql_close();        
        return $msgs;        
    }
    
    public static function deleteMessageById($messageId){
        $con = DBUtil::connectDB();
        mysql_query("DELETE FROM message where id={$messageId}", $con);
        mysql_close();
        return mysql_errno() == 0;
    }
    
    public static function getUserMessage($userId, $start = 0, $size = 30){
        $con = DBUtil::connectDB();
        $result = mysql_query("SELECT * FROM message where userId = '{$userId}' order by id desc limit {$start}, {$size}", $con);
        $msgs = [];
       
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['id'] = $row['id'];
            $r['userId'] = $row['userId'];
            $r['destination'] = $row['destination'];
            $r['start_time'] = $row['start_time'];
            $r['end_time'] = $row['end_time'];
            $r['message'] = $row['message'];
            $msgs[] = $r;
        }
        mysql_close();        
        return $msgs;        
    }
    
    public static function updateUserProfile($userId, $name, $age, $gender, $signature, $avatar, $images){
        $con = DBUtil::connectDB();
        $result = mysql_query("select * from user where userId='{$userId}'");
        $exist = mysql_num_rows($result) > 0;
        $ret = TRUE;
        if($exist){
            $ret = mysql_query("update user set name='{$name}',signature='{$signature}',avatar='{$avatar}',images='{$images}', age='{$age}', gender='{$gender}' where userId='{$userId}'");
        }else{
            $ret = mysql_query("insert into user (userId,name,age,gender,signature,avatar,images) VALUES('{$userId}','{$name}','{$age}','{$gender}', '{$signature}','{$avatar}','{$images}')");
        }
        mysql_close($con);
        return $ret;
    }
    
    public static function getUser($userId){
        $con = DBUtil::connectDB();
        $ids = explode(',', $userId);
        $query = '';
        foreach($ids as $id){
           $query = $query . "userId='{$id}' or ";
        }
        $query = trim($query, ' or ');
        $result = mysql_query("SELECT * FROM user where " . $query, $con);
        $users = [];
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['userId'] = $row['userId'];
            $r['name'] = $row['name'];
            $r['age'] = $row['age'];
            $r['signature'] = $row['signature'];
            $r['avatar'] = $row['avatar'];
            $r['images'] = $row['images'];
            $r['gender'] = $row['gender'];
            $r['password'] = $row['password'];
            $users[$row['userId']]= $r;
        }
        mysql_close();
        return $users;
    }
    
    public static function getMarkUserCount($messageId){
        $con = DBUtil::connectDB();
        $result = mysql_query("select count(*) from beento where messageId='{$messageId}'", $con);
        $row = mysql_fetch_array($result)[0];
        return $row;
    }
    
    public static function getMarkRecords($messageId, $userId){
        $con = DBUtil::connectDB();
        $result = mysql_query("select * from beento where messageId='{$messageId}' AND userId='{$userId}'", $con);        
        $records = [];
        
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['destination'] = $row['destination'];
            $r['id'] = $row['id'];
            $records[] = $r;
        }        
        mysql_close();
        return $records;
    }
    
    public static function markAsBeenTo($userId, $message, $hasBeenTo = TRUE){
        $records = DBUtil::getMarkRecords($message['id'], $userId);
        $recordId = NULL;
        foreach($records as $r){
            if($r['destination'] == $message['destination']){
                $recordId = $r['id'];
                break;
            }
        }
       
        if($hasBeenTo){
            if(!$recordId){
                $query = "INSERT INTO beento(messageId, userId, destination) VALUES ('{$message['id']}', '{$userId}', '{$message['destination']}')";
            }
        }else{
            if($recordId){
                $query = "DELETE FROM beento WHERE id={$recordId}";
            }
        }
        if($query){
            $con = self::connectDB();
            mysql_query($query, $con);
            mysql_close($con);
        }
        return TRUE;
    }
    
    public static function addUser($name, $pwd){
        $con = self::connectDB();
        $password = md5($pwd);
        $userId = md5($name);
        $ret = mysql_query("insert into user (userId,name,password) VALUES('{$userId}','{$name}', '{$password}')", $con);
        mysql_close($con);
        return $ret ? $userId : 0;
    }
    
    public static function commitComment($messageId, $userId, $replyId, $message, $timestamp){
        $con = self::connectDB();        
        $ret = mysql_query("insert into comments (messageId,userId,replyId,message,timestamp) VALUES('{$messageId}','{$userId}','{$replyId}', '{$message}', '{$timestamp}')", $con);
        mysql_close($con);        
        return $ret;
    }
    
    public static function getComments($messageId, $from, $size){
        $con = self::connectDB();
        $result = mysql_query("SELECT * FROM comments where messageId={$messageId} order by timestamp desc limit {$from}, {$size}", $con);
        $comments = [];
       
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['id'] = $row['id'];
            $r['messageId'] = $row['messageId'];
            $r['userId'] = $row['userId'];
            $r['replyId'] = $row['replyId'];
            $r['message'] = $row['message'];
            $r['timestamp'] = $row['timestamp'];
            $comments[] = $r;
        }
        mysql_close();        
        return $comments;
       
    }
}