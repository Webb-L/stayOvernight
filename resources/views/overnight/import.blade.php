@extends('default')
@section('head')
@endsection
@section('main')
    <div class="container mt-5">
        <h1 class="mb-5">验证信息</h1>
        <form class="mt-5" action="{{route('overnight.import')}}" method="post" enctype="multipart/form-data">
            <div class="form-group">
                @if($errors->any()>=0)
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>错误!</strong> {{$error}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endforeach
                @endif

                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>成功!</strong> {{Session::get('success')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(Session::has('danger'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>错误!</strong> {{Session::get('danger')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">密码：</label>
                <input type="password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="password" value="{{old('password')}}">
            </div>
            <div class="form-group">
                <label for="exampleFormControlFile1">上传：</label>
                <input type="file" class="form-control-file" id="exampleFormControlFile1" name="file" accept=".xls,.xlsx">
            </div>
            @csrf
            <button type="submit" class="btn btn-primary">提交</button>
        </form>
    </div>
@endsection
@section('body')
@endsection
