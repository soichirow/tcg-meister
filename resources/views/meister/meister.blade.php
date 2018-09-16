@extends('layouts.app')

@section('cover')
    <div class="cover">
        <div class="cover-inner">
            <div class="cover-contents">
                <h1>大会一覧</h1>
                
            </div>
        </div>
    </div>
@endsection


@section('content')
    {{--個別の大会名 --}}

    <h2 class=h2>{{ $title }}</h2>

    {!! Form::open(['method' => 'GET']) !!}
        <div class="col-xs-2">
            <div class="form-group">
                {!! Form::select('roundNum', $formSelectRoundNum,old('roundNum') ,['class' => 'form-control']) !!}
            </div>
        </div>
        
        <div class="col-xs-3">
            <div class="form-group">
                {!! Form::select('EntryNum', $formSelectEntryNum,old('EntryNum'),['class' => 'form-control']) !!}
            </div>
        </div>
        {!! Form::submit('検索', ['class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}
    
        {{--検索結果の表示 --}}
        @if(empty($searchResultRound))
            //何もしない
        @else
        
        <table class="table table-striped table-bordered">
            @foreach ($searchResult as $keyA => $row)
            <tr>
                @if($keyA == 0)
                <td>回戦数</td>
                @else 
                    <td>{{ $searchResultRound['roundNum'] }}回戦</td>
                @endif

                @foreach ($row as $keyB => $col)

                    <td>{{ $col }}</td>
                @endforeach
            </tr>
            @endforeach
        </table>
        @endif
        
        {{--検索結果の表示 --}}
<br>

    @foreach ($meisterArray as $key1 => $round)
        {{--アコーディオン部分 --}}
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" href="#collapse{{ $key1 }}">{{ $roundName[$key1] }}<small>　　(タップで開閉)</small></a>
                    </h4>
                </div>
                @if("round".$maxRound == ($key1) )
                    <div id="collapse{{ $key1 }}" class="panel-collapse collapse in">
                @else
                    <div id="collapse{{ $key1 }}" class="panel-collapse collapse">
                @endif

                    
        {{--アコーディオン部分 --}}
        <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <tbody>
            @foreach ($round as $key2 => $meister)
            
                <tr>
                    @foreach ($meister as $key3 => $row)
                    <td>{{ $row }}</td>

                    @endforeach
                </tr>
                
            @endforeach
            </tbody>
        </table>
        </div>
        <p>
        {{--アコーディオン部分の閉じる部分 --}}
                </div>
            </div>
        </div>
        {{--アコーディオン部分の閉じる部分 --}}
            
            
            
    @endforeach
    
元URL <a href="{{ $meisterUrl }}" target="_blank">{{ $meisterUrl }}</a>

@endsection



