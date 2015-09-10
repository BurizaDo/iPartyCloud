<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class DBUtil{
    public static function connectDB(){
        $con = mysql_connect('localhost:8888', 'root', 'root');
        mysql_query("SET NAMES 'UTF8'", $con);          
        mysql_select_db('iParty', $con);
        return $con;
    }
}
