@extends('default')
@section('head')
    <style>
        body,
        .container {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
        }

        .text-white {
            text-shadow: 0 0 3px #000;
        }
    </style>
@endsection
@section('main')
    <div class="container d-flex justify-content-center align-content-center">
        <div class="row d-flex align-content-center">
            <div class="col-12 text-center">
                <h1 class="text-white">我要留宿</h1>
            </div>
            <div class="col-12 text-center">
                <p class="text-white text-center" id="introduce"></p>
            </div>
            <div class="col-12 text-center">
                <div class="btn-group">
                    <a class="btn btn-primary" href="{{route('overnight.index')}}">开始留宿</a>
                    <a class="btn btn-danger" href="{{route('overnight.show','')}}">取消留宿</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body')
    <script>
        $(() => {
            $.ajax({
                url: 'image',
                type: 'get',
                dataType: 'json',
            })
                .then(res => {
                    $('#introduce').text(res['images'][0]['copyright']);
                    $('body').css({
                        'background': '#000 url(' + "https://www.bing.com" + res['images'][0]['url'] + ') no-repeat center center',
                        'background-size': 'cover',
                        'backdrop-filter': 'blur(5px)'
                    })
                });
        });
    </script>
@endsection
