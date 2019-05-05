<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;
use App\Study;

class StoreController extends Controller
{
    public function addGoods(Request $request)
    {
        $openId = $request->input('openId');
        $goods = $request->input('goods');
        $coin = $request->input('coin');
        $file = $request->file('img');
        
        if($openId&&$goods&&$coin&&$file)
        {
            $newName = time().".".$file->extension();
            $file->move("image/goods",$newName);
            
            $newGoods = Store::create([
                'open_id'=>$openId,
                'goods'=>$goods,
                'coin'=>$coin,
                'img'=>"image/goods/".$newName
            ]);
            //如果创建新商品成功
            if($newGoods)
                return response()->json(['result' => 'success','msg' => ['goodsId' => $newGoods->id]]);
            else
                return response()->json(['result' => 'fail','msg' => 'addGoods error']);
        }else
            return response()->json(['result' => 'fail','msg' => 'lost param']);
    }

    public function updateGoods(Request $request)
    {
        $goods = $request->input('goods');
        $coin = $request->input('coin');
        $file = $request->file('img');
        $goodsId = $request->input('goodsId');
        if($goodsId){
            //寻找这个商品
            $Goods = Store::find($goodsId);
            if(!$Goods)
                return response()->json(['result' => 'fail','msg' => 'goodsId error']);
            //如果有coin/file/goods那么就更新相应的数据
            if($coin)
                $Goods->coin = $coin;
            if($file)
            {
                $newName = time().".".$file->extension();
                $file->move("image/goods",$newName);
                $Goods->img = "images/goods".$newName;
            }
            if($goods)
                $Goods->goods = $goods;
            //保存商品
            if($Goods->save())
                return response()->json(['result' => 'success','msg' => 'updateGoods success']);
            else
                return response()->json(['result' => 'success','msg' => 'updateGoods error']);
        }else
            return response()->json(['result' => 'fail','msg' => 'lost goodsId']);
    }

    public function deleteGoods(Request $request)
    {
        $goodsId = $request->input('goodsId');
        if($goodsId)
        {
            //寻找这个商品
            $Goods = Store::find($goodsId);
            if($Goods->delete())
                return response()->json(['result' => 'success','msg' => 'deleteGoods success']);
            else
                return response()->json(['result' => 'fail','msg' => 'deleteGoods error']);
        }
        else
            return response()->json(['result' => 'fail','msg' => 'lost goodsId']);
    }

    /**
     * 购买货物
      */
    public function buyGoods(Request $request)
    {
        $goodsId = $request->input('goodsId');
        $openId = $request->input('openId');
        if($goodsId&&$openId)
        {
            $goods = Store::find($goodsId);
            $userStudy = Study::find($openId);
            if($goods&&$userStudy){
                $coinCost = $goods->coin;   //这个商品的价格
                $myCoin = $userStudy->coin; //用户拥有的金币
                $restCoin = $myCoin-$coinCost;
                if($restCoin<0)
                    return response()->json(['result' => 'fail','msg' => 'you dont have enough coin']);
                else{
                    $userStudy->coin = $restCoin;
                    $userStudy->save();
                    return response()->json(['result' => 'success','msg' => ['restCoin'=>$restCoin]]);
                }
            }else
                return response()->json(['result' => 'fail','msg' => 'goods or userStudy not exist']);
        }
        else
            return response()->json(['result' => 'fail','msg' => 'lost param']);
    }

    //--------------------------------工具类---------------------------------------------
    /*
    *   获取商品
    */
    public static function getGoods($openId)
    {
        $Goods = Store::get()->where('open_id',$openId);
        $GoodsArr = array();
        $i=0;
        foreach($Goods as $goods)
        {
            $arr = array();
            $arr['goodsId'] = $goods->id;
            $arr['goodsImage'] = env('APP_URL').$goods->img;
            $arr['goodsName'] = $goods->goods;
            $arr['goodsCost'] = $goods->coin;
            $GoodsArr[$i] = $arr;
            $i++;
        }
        return $GoodsArr;
    }
}
