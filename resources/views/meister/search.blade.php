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

        {!! Form::label('content', 'メッセージ:') !!}
        {!! Form::text('content') !!}

        {!! Form::submit('投稿') !!}

    {!! Form::close() !!}
    
    
    @if(empty($search))
    から
    {{$search}}
    @else
    {{$search}}
    検索結果を表示
    @endif

    @foreach ($meisterArray as $key1 => $round)
        {{--アコーディオン部分 --}}
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" href="#collapse{{ $maxRound - $key1 }}">{{ $maxRound - $key1 }}回戦<small>　　(タップで開閉)</small></a>
                    </h4>
                </div>
                @if($maxRound == ($maxRound- $key1) )
                    <div id="collapse{{ $maxRound - $key1 }}" class="panel-collapse collapse in">
                @else
                    <div id="collapse{{ $maxRound - $key1 }}" class="panel-collapse collapse">
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
    

    元URL{{ $meisterUrl }}
@endsection



