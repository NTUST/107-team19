@extends('layouts.app_b')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">編輯活動</div>

                    <div class="card-body">
                        <form method="POST" id='f' action="{{ route('manager_update')}}">
                            @csrf
                            <input name="id" type="hidden" value="{{ $event->id or old('id') }}">
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">活動名稱</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ $event->name or old('name')}}" required autofocus>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="content" class="col-md-4 col-form-label text-md-right">活動說明</label>

                                <div class="col-md-6">
                                    <textarea id="content"  class="form-control{{ $errors->has('content') ? ' is-invalid' : '' }}" name="content" >{{ $event->content or old('content')}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="type" class="col-md-4 col-form-label text-md-right">活動報名</label>

                                <div class="col-md-6">
                                    <select class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}" id="type" name="type">
                                        <option value="0">不須事先報名</option>
                                        <option value="1" selected>須事先報名</option>
                                        {{--<option value="0">須事先報名，但可現場報名</option>--}}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="namelist" @if(!(old('type') or $event->type)) style="display:none" @endif>
                                <label for="namelist_id" class="col-md-4 col-form-label text-md-right">使用名冊</label>

                                <div class="col-md-6">
                                    <select class="form-control{{ $errors->has('namelist_id') ? ' is-invalid' : '' }}" id="namelist_id" name="namelist_id">
                                        <option value="0">請選擇一個名冊</option>
                                        @foreach($namelists as $namelist)
                                            <option value="{{ $namelist->id }}">{{ $namelist->name }}</option>
                                        @endforeach
                                        {{--<option value="0">須事先報名，但可現場報名</option>--}}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="member_quantity" class="col-md-4 col-form-label text-md-right">人數</label>

                                <div class="col-md-6">
                                    <input id="member_quantity" type="text" class="form-control{{ $errors->has('member_quantity') ? ' is-invalid' : '' }}" name="member_quantity" value="{{ $event->member_quantity or old('member_quantity')}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="hold_time" class="col-md-4 col-form-label text-md-right">活動舉辦時間</label>

                                <div class="col-md-6">
                                    <input id="hold_time" class="form-control" type="datetime-local" name="hold_time" value="{{ $event->custom_time or old('hold_time')}}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button  class="btn btn-primary" id="submit" >送出</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('document').ready(function() {
            $('#type').val({{ $event->type or old('type')}});
            $('#namelist_id').val({{ $event->namelist_id or old('namelist_id')}});
            $('#type').change( function(){
               if($(this).val() === '0') {
                   $('#namelist_id').val('0');
                   $('div#namelist').hide();
               } else if($(this).val() === '1') {
                   $('#namelist_id').val('1');
                   $('div#namelist').show();
               } else {
                    var i=1;
               }
            });
        });
    </script>
@endsection