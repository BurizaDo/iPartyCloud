<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'ClubDBUtil.php';
require_once 'ConsumeSetDBUtil.php';
require_once 'UserDBUtil.php';
require_once 'ActivityDBUtil.php';
require_once 'ReservationDBUtil.php';

class Api{
    public function addClub($name, $longitude, $latitude, $images, $address, $meta){
        return ClubDBUtil::addClub($name, $longitude, $latitude, $images, $address, $meta);
    }
    
    public function getClubs($longitude, $latitude, $range = 10){
        return ClubDBUtil::getClubs($longitude, $latitude, $range);
    }
    
    public function addSetOfClub($clubId, $title, $content, $price, $validate_time_start, $validate_time_end){
        return ConsumeSetDBUtil::addSet($clubId, $title, $content, $price, $validate_time_start, $validate_time_end);
    }
    
    public function getSetsOfClub($clubId){
        return ConsumeSetDBUtil::getAllSetsByClubId($clubId);
    }
    
    public function register($username, $password){
        $user = UserDBUtil::getUserByUserName($username);
        if($user && !empty($user)){
            throw new Exception('已注册', 1101);
        }
        return UserDBUtil::addUser($username, $password);
    }
    
    public function login($username, $password){
        $users = UserDBUtil::getUserByUserName($username);
        if(!$users || empty($users)){
            throw new Exception('用户不存在', 1102);
        }
        $user = array_values($users)[0];
        if($user['password'] != md5($password)){
            throw new Exception('密码错误', 1103);
        }
        unset($user['password']);
        return $user;
        
    }
    
    public function updateUserByUsername($username, $nick = NULL, $avatar = NULL, $gender = NULL){
        return UserDBUtil::updateUser($username, $nick, $avatar, $gender);
    }
    
    public function addActivity($clubId, $title, $images, $content, $validTimeStart, $validTimeEnd){
        return ActivityDBUtil::addActivity($clubId, $title, $images, $content, $validTimeStart, $validTimeEnd);
    }
    
    public function getActivities($start, $count = 30){
        return ActivityDBUtil::getActivities($start, $count);
    }
    
    public function joinActivity($username, $activityId){
        $userAry = self::checkUser($username);
        self::checkActivity($activityId);
        $user = array_values($userAry)[0];
        return ActivityDBUtil::joinActivity($user['id'], $activityId);
    }
    
    public function cancelJoinActivity($username, $activityId){
        $userAry = self::checkUser($username);
        self::checkActivity($activityId);
        $user = array_values($userAry)[0];
        return ActivityDBUtil::cancelJoinActivity($user['id'], $activityId);
    }
    
    public function addReservation($setId, $username){
        $set = self::checkSet($setId);
        $user = self::checkUser($username);
        
        $reservations = self::getReservationByUsername($username);
        foreach($reservations as $r){
            if($r['customerId'] == $user[0]['id']){
                throw new Exception('该用户已订此套餐', 1118);                
            }
        }
        return ReservationDBUtil::addReservation($user[0]['id'], $set[0]['clubId'], $set[0]['id'], 1);
    }
    
    //reservation status  
    //1:reserved not paid, not used 2:reserved and paid not used 3:reserved and paid and used 4:cancelled
    public function getReservationByUsername($username){
        $user = self::checkUser($username);
        return ReservationDBUtil::getReservationByUserId($user[0]['id']);
    }
    
    public function cancelReservation($reservationId, $username){
        $user = self::checkUser($username);
        $reservations = ReservationDBUtil::getReservationByUserId($user[0]['id']);
        foreach($reservations as $r){
            if($r['id'] == $reservationId){
                return ReservationDBUtil::updateStatusAndPayChannel($reservationId, 4);
            }
        }
        throw new Exception('用户与套餐不匹配', 1119);
    }
    
    public function updateReservationPayStatus($reservationId, $paid, $channel){
        self::checkReservation($reservationId);
        if($paid){
            return ReservationDBUtil::updateStatusAndPayChannel($reservationId, 2, $channel);
        }else{
            return ReservationDBUtil::updateStatusAndPayChannel($reservationId, 1);
        }
    }
    
    public function updateReservationUsed($reservationId){
        self::checkReservation($reservationId);
        return ReservationDBUtil::updateStatusAndPayChannel($reservationId, 3);
    }
    
    private function checkReservation($reservationId){
        $reservations = ReservationDBUtil::getReservationById($reservationId);
        if($reservations == null || empty($reservations)){
            throw new Exception('no such reservation', 1122);
        }
        return $reservations;
    }
    
    private function checkSet($setId){
        $setAry = ConsumeSetDBUtil::getSetById($setId);
        if($setAry == NULL || empty($setAry)){
            throw new Exception('套餐不存在', 1112);
        }
        return $setAry;
    }
    
    private function checkUser($username){
        $userAry = UserDBUtil::getUserByUserName($username);
        if($userAry == NULL || empty($userAry)){
            throw new Exception('用户不存在', 1110);
        }
        return $userAry;
    }
    
    private function checkActivity($activityId){
        $activity = ActivityDBUtil::getActivityById($activityId);
        if($activity == NULL || empty($activity)){
            throw new Exception('活动不存在', 1111);
        }
        return $activity;
    }    
}