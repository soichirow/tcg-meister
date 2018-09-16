<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;


class MeisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //最終的な表示用配列
        //$meisterArray[回線数][row][col]
        
        //スクレイピングしたデータを取り込む
        $url = "https://spreadsheets.google.com/feeds/list/1gKbM8tw4qfwr-Tc3RPwO1ExcXaC0oQ0zJtguNjva81w/od6/public/values?alt=json";
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json,true);

        //タイトルを取得
        $rownum = 0; $colnum = 13;
        $title = $arr[ "feed" ]["entry"][$rownum]["gsx"."$"."col".$colnum]["$"."t"];

        //URLを取得
        $rownum = 0; $colnum = 12;
        $meisterUrl = $arr[ "feed" ]["entry"][$rownum]["gsx"."$"."col".$colnum]["$"."t"];
        
        //最大回戦数を取得
        $rownum = 2; $colnum = 12;
        $maxRound = $arr[ "feed" ]["entry"][$rownum]["gsx"."$"."col".$colnum]["$"."t"];

        //回戦の名前を取得
        $rownum = 1; //$colnum = 12;
        $roundNameKeys = array_keys($arr[ "feed" ]["entry"][$rownum]);
        //$roundName  最終的にこの配列に格納する
        
        foreach($roundNameKeys as $key => $roundNameKey){
            if(strpos($roundNameKey,'gsx') !== false){
                $buf[$roundNameKey] = $roundNameKey;
            }
        }
        $cnt =0;
        foreach($buf as $key => $buf){
            $check = $arr[ "feed" ]["entry"][$rownum][$buf]["$"."t"];
            if( (empty($check)) or (strpos($check,'管理') !== false) or (strpos($check,'回戦数') !== false)){
            //if( (empty($check)) or (strpos($check,'管理') !== false) ){
            }else{
                $cnt++;
                $roundName['round'.$cnt] = ($arr[ "feed" ]["entry"][$rownum][$buf]["$"."t"]);
            }
        }



        //$meisterArrayという配列に入れて扱いやすくする
        $rownum = 4;
        $startNum = 3 ;                             //初期位置
        $laslNum = count($arr[ "feed" ]["entry"]);

        $round =1;
        
        //＝＝＝＝ここをふぉーいーちに変える
        for ($round = 1; $round <= $maxRound; $round++){
            for ($rownum = $startNum; $rownum < $laslNum; $rownum++){
                for ($colnum = 1; $colnum <= 6; $colnum++){
                    //横の行の１つ目がemptyかどうかしらべる。
                    $emptyRowCheck = $arr[ "feed" ]["entry"][$rownum]["gsx"."$"."col".$round."1"]["$"."t"];
                    //縦の列１つ目がemptyかどうかしらべる。（ブロックがない場合など）
                    $emptyColCheck = $arr[ "feed" ]["entry"][$startNum]["gsx"."$"."col".$round.$colnum]["$"."t"];
                    
                    
                    if(Empty($emptyColCheck) or empty($emptyRowCheck)){
                        break 1;
                    }else{
                        $meisterArray['round'.$round][$rownum-2][$colnum] = $arr[ "feed" ]["entry"][$rownum]["gsx"."$"."col".$round.$colnum]["$"."t"];
                    }
                    //$meisterArray["round".$round][$rownum][$colnum] = $arr[ "feed" ]["entry"][$rownum]["gsx"."$"."col".$round.$colnum]["$"."t"];
                }
            }
        }




        
        //表示用の配列を並び替える
        //①並べ替え用の配列を用意する。
        foreach ($meisterArray as $key => $value) {
          $sort[$key] = $key;
        }
        //並び替える
        array_multisort( $sort, SORT_DESC, SORT_REGULAR , $meisterArray);
        //＝＝＝＝＝$meisterArrayという配列に入れて扱いやすくする　ココまで＝＝＝＝＝


        //検索フォームに使う回戦数の配列
        for ($count = 1; $count <= $maxRound; $count++){
            $formSelectRoundNum[$count] = $count;
        }

        $formSelectRoundNum[100] = "回戦数";
        krsort($formSelectRoundNum);

        


        //検索フォームに使う受付番号の配列
        //$formSelectEntryNum = ;
        $searchWordCol = array_search("あなたのお名前",$meisterArray['round1'][1]);


        for ($count1 = 1; $count1 < $maxRound; $count1++){
            for ($count2 = 1; $count2 <= count($meisterArray['round'.$count1]); $count2++){
                $key = $meisterArray['round'.$count1][$count2][$searchWordCol];
                $formSelectEntryNum[$key] = $key;
            }
        }
        //あなたのお名前の部分の配列を削除
        unset($formSelectEntryNum["あなたのお名前"]);
        asort($formSelectEntryNum);
        array_unshift($formSelectEntryNum,"あなたのお名前");    //一番上に説明用にあなたのお名前を挿入
        
        
        
        
        
        //＝＝＝＝＝検索ボタンが押されたあとの処理＝＝＝＝＝
        if(!empty($request)) {
            $request->all();
            //フォームから送られた値を取得
            $searhPostKey1 = $request->only('roundNum');
            $searhPostKey2 = $request->only('EntryNum');
            

            //回戦数を絞り込み
            $chech = array_key_exists('round'.$searhPostKey1['roundNum'], $meisterArray);
            if($chech == true){
                
                $bufRound= $meisterArray['round'.$searhPostKey1['roundNum']];
            
                //名前を絞り込み
                $cnt = 0;
                foreach ($bufRound as $key => $value){
                    
                    if ($value === reset($bufRound)) {
                        $searchResult[0]=($value);
                    }
                    
                    if($searhPostKey2['EntryNum'] == $value[$searchWordCol]){
                        $cnt++;
                        $searchResult[$cnt] = $value;
                        // dump($searhPostKey1);
                        // dump($searhPostKey2);
                        // dump($searchResult);
                    }else{
                    
                    }
                    
                }
                unset($value);
                
            }else{
                $bufRound = 0;
                $searchResult =1;
            }
            
        }else{
            
        }
        //＝＝＝＝＝検索ボタンが押されたあとの処理＝＝＝＝＝
        
        
        
        
        // $players = $meisterArray[1];
        
        // $all_num =count($players);
        // $disp_limit = 25;
        // $page = 1;
        // $players = new LengthAwarePaginator($players , $all_num, $disp_limit, $page, array('path'=>'/meister'));
        // dump($players);
        // dd(222);
        

        
        return view('meister.meister',[
           'meisterArray' => $meisterArray,
           'title' => $title,
           'maxRound' => $maxRound,
           'meisterUrl' => $meisterUrl,
           'roundName' => $roundName,
           
           //検索フォーム
           'formSelectRoundNum' => $formSelectRoundNum,
           'formSelectEntryNum' => $formSelectEntryNum,
           
           //検索結果
            'searchResultRound' => $searhPostKey1,
            'searchResult' =>$searchResult,
            'cnt' => $cnt,
                    
           
        ]);
    }




























    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
