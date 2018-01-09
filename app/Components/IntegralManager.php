<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/9
 * Time: 11:05
 */

namespace App\Components;


use App\Models\IntegralGoods;
use App\Models\IntegralHistory;
use App\Models\IntegralRecord;

class IntegralManager
{
    const SIGN_INTEGRAL = 10;    //签到所获得的积分
    const INVITATION_INTEGRAL = 20;    //友情好友所获得的积分
    const COMMENT_INTEGRAL = 50;    //发表评论审核通过后所获得的积分

    /*
     * 获取积分商城的可兑换产品
     *
     * by zm
     *
     * 2018-01-09
     *
     */
    public static function IntegralGoodsLists(){
        $where=array(
            'status'=>1,
            'delete'=>0
        );
        $integral_goods=IntegralGoods::where($where)->get();
        return $integral_goods;
    }

    /*
     * 获取用户积分明细列表
     *
     * by zm
     *
     * 2018-01-09
     *
     */
    public static function getIntegralDetaileListsByUser($user_id){
        $integral_details=IntegralRecord::where('user_id',$user_id)->orderBy('id','desc')->get();
        return $integral_details;
    }

    /*
     * 游客端——获取积分兑换历史
     *
     * by zm
     *
     * 2018-01-09
     *
     */
    public static function getIntegralHistoryForUser($user_id){
        $integral_histories=IntegralHistory::where('user_id',$user_id)->orderBy('id','desc')->get();
        foreach ($integral_histories as $integral_history){
            $integral_history['goods_id']=self::getIntegralGoodsById($integral_history['goods_id']);
        }
        return $integral_histories;
    }

    /*
     * 旅行社端——获取积分兑换历史
     *
     * by zm
     *
     * 2018-01-09
     *
     */
    public static function getIntegralHistoryForOrganization($organization_id){
        $integral_histories=IntegralHistory::where('organization_id',$organization_id)->orderBy('id','desc')->get();
        foreach ($integral_histories as $integral_history){
            $integral_history['user_id']=UserManager::getUserInfoById($integral_history['user_id']);
            $integral_history['goods_id']=self::getIntegralGoodsById($integral_history['goods_id']);
        }
        return $integral_histories;
    }

    /*
     * 根据Id获取积分商城产品信息
     *
     * by zm
     *
     * 2018-01-09
     *
     */
    public static function getIntegralGoodsById($id){
        //基本信息
        $integral_goods=IntegralGoods::where('id',$id)->first();
        return $integral_goods;
    }

    /*
     * 旅行社端——添加积分兑换历史/修改兑换状态
     *
     * by zm
     *
     * 2018-01-09
     *
     */
    public static function setIntegralStatusById($data){
        //基本信息
        if(array_key_exists('id',$data)){
            $integral=self::getIntegralHistoryById($data['id']);
            $data['status']=1;
        }
        else{
            $integral = new IntegralHistory();
            $user=UserManager::getUserInfoById($data['user_id']);
            $data['organization_id']=$user['organization_id'];
        }
        $integral = self::setIntegralHistoryStatus($integral, $data);
        $integral->save();
        $integral = self::getIntegralHistoryById($integral->id);
        return $integral;
    }

    /*
     * 根据Id获取积分兑换历史详情
     *
     * by zm
     *
     * 2018-01-09
     *
     */
    public static function getIntegralHistoryById($id){
        //基本信息
        $integral_history=IntegralHistory::where('id',$id)->first();
        return $integral_history;
    }
    
    /*
     * 配置添加/修改兑换积分商品历史的状态的参数
     *
     * By zm
     *
     * 2018-01-09
     *
     */
    public static function setIntegralHistoryStatus($integral_goods,$data){
        if (array_key_exists('goods_id', $data)) {
            $integral_goods->goods_id = array_get($data, 'goods_id');
        }
        if (array_key_exists('user_id', $data)) {
            $integral_goods->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('status', $data)) {
            $integral_goods->status = array_get($data, 'status');
        }
        if (array_key_exists('organization_id', $data)) {
            $integral_goods->organization_id = array_get($data, 'organization_id');
        }
        return $integral_goods;
    }

    /*
     * 按用户编号添加积分记录
     */
    public static function addIntegralRecord($data){
        if($data['type']==1){
            $content="签到+".SIGN_INTEGRAL;
        }
        else if($data['type']==2){
            $content="邀请好友成功+".INVITATION_INTEGRAL;
        }
        else if($data['type']==2){
            $content="发表评论并审核通过+".COMMENT_INTEGRAL;
        }
        else{
            $content=$data['content'];
        }
    }
}