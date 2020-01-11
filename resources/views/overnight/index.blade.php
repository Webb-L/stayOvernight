@extends('default')
@section('head')
@endsection
@section('main')
    <div class="container mt-5">
        <h1 class="mb-5">填写信息</h1>
        <form class="mt-5" action="{{route('overnight.store')}}" method="post">
            <div class="form-group">
                @if($errors->any()>=0)
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>错误</strong> {{$error}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endforeach
                @endif

                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>成功</strong> {{Session::get('success')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(Session::has('danger'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>错误</strong> {{Session::get('danger')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">名字<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="username" value="{{old('username')}}">
                <small id="emailHelp" class="form-text text-muted">输入您的名字，如：张三。</small>
            </div>
            <div class="form-group">
                <label for="exampleInputHostel1">宿舍号<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="exampleInputHostel1" name="hostel" value="{{old('hostel')}}">
                <small id="emailHostel" class="form-text text-muted">输入您的宿舍号，如：101。</small>
            </div>
            <div class="form-group">
                <label for="exampleInputTime1">留宿时间<span class="text-danger">*</span></label>
                <div class="row ml-5">
                   <div class="col-3 ml-5">
                       <div class="form-check">
                           <input class="form-check-input" name="time[]" type="checkbox" value="周六" id="defaultCheck1">
                           <label class="form-check-label" for="defaultCheck1">
                               周六
                           </label>
                       </div>
                   </div>
                    <div class="col-3">
                       <div class="form-check">
                           <input class="form-check-input" name="time[]" type="checkbox" value="周日" id="defaultCheck2">
                           <label class="form-check-label" for="defaultCheck2">
                               周日
                           </label>
                       </div>
                   </div>
                </div>
            </div>
            <div class="form-group">
                <label for="exampleInputRemarks1">备注：</label>
                <textarea name="remarks" class="form-control" id="exampleInputRemarks1" cols="15" rows="5"></textarea>
            </div>
            @csrf
            <button type="submit" class="btn btn-primary">提交</button>
        </form>
    </div>
@endsection
