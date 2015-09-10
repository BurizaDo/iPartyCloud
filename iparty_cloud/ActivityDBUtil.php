<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ActivityDBUtil{
    public static function addActivity($clubId, $title, $images, $content, $validTimeStart = 0, $validTimeEnd = 0){
        $connection = DBUtil::connectDB();
        $sql = "insert into activity (club_id, title, images, content, valid_time_start, valid_time_end) "
                . "values ('{$clubId}', '{$title}', '{$images}', '{$content}', '{$validTimeStart}', '{$validTimeEnd}')";
        $result = mysql_query($sql, $connection);
        $id = mysql_insert_id();
        mysql_close();
        return $result != FALSE ? $id : FALSE;
    }
    
    public static function getActivities($start, $size = 30){
        $con = DBUtil::connectDB();
        $result = mysql_query("SELECT * FROM activity order by publish_time desc limit {$start}, {$size}", $con);
        $activities = [];
       
        while($row = mysql_fetch_array($result)){
            $r = [];
            $r['id'] = $row['id'];
            $r['title'] = $row['title'];
            $r['images'] = json_decode($row['images']);
            $r['content'] = $row['content'];
            $r['valid_time_start'] = $row['valid_time_start'];
            $r['valid_time_end'] = $row['valid_time_end'];
            $r['publish_time'] = $row['publish_time'];
            $r['user_ids'] = json_decode($row['user_ids']);
            $r['club'] = ClubDBUtil::getClubById($row['club_id']);
            $activities[] = $r;
        }
        mysql_close();        
        return $activities;
    }
    
    public static function getActivityById($activityId){
        $con = DBUtil::connectDB();
        $result = mysql_query("SELECT * FROM activity where id={$activityId}", $con);
        $activities = [];
        while($row = mysql_fetch_array($result)){
            $r['title'] = $row['title'];
            $r['images'] = json_decode($row['images']);
            $r['content'] = $row['content'];
            $r['valid_time_start'] = $row['valid_time_start'];
            $r['valid_time_end'] = $row['valid_time_end'];
            $r['publish_time'] = $row['publish_time'];
            $r['user_ids'] = json_decode($row['user_ids']);
            $r['club'] = ClubDBUtil::getClubById($row['club_id']);
            $activities[] = $r;
        }
        mysql_close();        
        return $activities;        
    }
    
    public static function joinActivity($userId, $activityId){
        $activity = self::getActivityById($activityId);
        if(empty($activity)) return FALSE;
        $currentIds = $activity[0]['user_ids'];
        if($currentIds != null){
            $newIds = $currentIds;
        }else{
            $newIds = [];
        }
        $newIds[] = $userId;
        $jsonIds = json_encode($newIds);
        $conn = DBUtil::connectDB();
        $result = mysql_query("update activity set user_ids='{$jsonIds}' where id={$activityId}", $conn);
        mysql_close();
        return $result != FALSE ? TRUE : FALSE;
    }
    
    public static function cancelJoinActivity($userId, $activityId){
        $activity = self::getActivityById($activityId);
        $userIds = $activity[0]['user_ids'];
        foreach($userIds as $key => $id){
            if($id == $userId){
                unset($userIds[$key]);    
                break;
            }
        }
        $newIds = json_encode($userIds);
        $conn = DBUtil::connectDB();
        $result = mysql_query("update activity set user_ids='{$newIds}' where id={$activityId}", $conn);
        mysql_close();
        return $result != FALSE ? TRUE : FALSE;
    }
}