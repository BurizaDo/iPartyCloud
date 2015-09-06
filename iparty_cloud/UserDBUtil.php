<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UserDBUtil{
    public static function getUserByUserName($userName){
        $conn = DBUtil::connectDB();
        $result = mysql_query("select * from user where username='{$username}'", $conn);
        $user = [];
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['id'] = $row['id'];
            $r['nick'] = $row['nick'];
            $r['gender'] = $row['gender'];
            $r['avatar'] = $row['avatar'];
            $r['username'] = $row['username'];
            $user[] = $r;
        }        
        mysql_close($conn);
        return $user;
    }
    
    public static function addUser($username, $password){
        $con = DBUtil::connectDB();
        $pwd = md5($password);
        $ret = mysql_query("insert into user (username,password) VALUES('{$username}', '{$pwd}')", $con);
        mysql_close($con);
        return $ret ? mysql_insert_id() : 0;
    }
    
    public static function updateUser($username, $nick, $avatar, $gender){
        $sentence = '';
        if($nick != NULL){
            $sentence = "nick='{$nick}'";
        }
        if($avatar != NULL){
            if(!empty($sentence)){
                $sentence += ',';
            }
            $sentence += "avatar='{$avatar}'";
        }
        if($gender != NULL){
            if(!empty($sentence)){
                $sentence += ',';
            }
            $sentence += "gender='{$gender}'";
        }
        if(empty($sentence)){
            return TRUE;
        }
        $conn = DBUtil::connectDB();
        $result = mysql_query("update user set {$sentence} where username='$username'", $conn);
        mysql_close();
        return $result;
    }
}