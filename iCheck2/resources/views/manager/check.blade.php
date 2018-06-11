@extends('layouts.app_b')

@section('content')
    <div class="container">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-light text-dark">
                    <div class="card-body">活動簽到</div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="@if( count($logs) > $event->member_quantity ) alert alert-danger @else  alert alert-secondary @endif" id="quantity-alert">
                    簽到人數 <a class="col" id="count-logs">{{ count($logs) }}</a>／<a class="col" id="m-quantity">{{ $event->member_quantity }}</a>
                </div>
            </div>
        </div>
        <br>
        <div class="row">

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">{{ $event->name }}</h2>
                    </div>
                    <div class="card-body">
                        {{--<ul class="nav nav-pills" role="tablist">--}}
                            {{--<li class="nav-item">--}}
                                {{--<a class="nav-link active" data-toggle="pill" href="#" id="card_mode">刷卡模式</a>--}}
                            {{--</li>--}}
                            {{--<li class="nav-item">--}}
                                {{--<a class="nav-link" data-toggle="pill" href="#" id="std_mode">學號模式</a>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                        <h3></h3>
                        <!-- Tab panes -->
                        {{--<div class="tab-content">--}}
                            <div id="card_id" class="container tab-pane active"><br>
                                <h4 id="form_title">請刷卡或輸入學號</h4>
                                <form class="form-inline"  id="id_form" >
                                    @csrf
                                    <input type="hidden" value="{{ $event->id }}" name="event_id">
                                    <label for="_id"></label>
                                    <input class="form-control col" id="_id" name="_id" autofocus>
                                </form>
                            </div>
                        {{--</div>--}}
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
                <br>
                <div class="alert" id="check-alert">
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-dark">
                    <div class="alert">
                        <h2 class="text-center">已簽到</h2>
                    </div>
                    <ul class="list-group" id="checkList">
                        @foreach( $logs as $log)
                            <li class="list-group-item">
                                <div class="col">
                                    <a class="col">{{$log->member_id}}</a>|<a class="col">{{$log->checkin_time}}</a>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                </div>
                {{--<input class="form-control" id="checked" type="text" placeholder="Search..">--}}

            </div>
        </div>
    </div>
    {{--<form method="post" action="{{ route('manager_check_API') }}">--}}
        {{--@csrf--}}
        {{--<input name="_id" value="B10430016">--}}
        {{--<input name="event_id" value="2">--}}
        {{--<button type="submit">1</button>--}}
    {{--</form>--}}
    <script>
        $(document).ready(function(){

            $("#card_mode").click(function(){
                $("#form_title").text("請刷卡");
            });
            $("#std_mode").click(function(){
                $("#form_title").text("請輸入學號");
            });
//            $("#checked").on("keyup", function() {
//                var value = $(this).val().toLowerCase();
//                $("#checkedList").filter(function() {
//                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
//                });
//            });
            $("#id_form").submit(function() {
                $.post("{{ route('manager_check_API') }}", $('#id_form').serialize(), function(data, status){
                    if( status === "success") {
                        $('#check-alert').empty();
                        if(data.msg === 1) {
                            if(data.in === 1) {
                                $('#check-alert').attr('class', 'alert alert-warning').append('重複簽到')
                            } else {
                                if(data.registration) {
                                    $('#check-alert').attr('class', 'alert alert-success').append('簽到成功');
                                } else if(data.registration === 0){
                                    $('#check-alert').attr('class', 'alert alert-danger').append('尚未報名');
                                } else {
                                    $('#check-alert').attr('class', 'alert alert-success').append('簽到成功，此活動不需報名');
                                }
                            }
                            $('#checkList').empty();
                            $.each(data.logs, function(k, v){
                                $('#checkList').append(
                                    $('<li>').attr('class', 'list-group-item').append(
                                        $('<div>').attr('class', 'col').append(
                                            $('<a>').attr('class','col').append(v.member_id),"|",
                                            $('<a>').attr('class','col').append(v.checkin_time)
                                        )));
                            });
                            $('#count-logs').text(data.logs.length);
                            if(data.logs.length > {{ $event->member_quantity }}) {
                                $('#quantity-alert').attr('class','alert alert danger');
                            }
                        } else {
                            $('#check-alert').attr('class', 'alert alert-danger').append('查無此人');
                        }
                        $('#_id').val("");
                    } else {
                        alert('請檢查網路連線');
                    }
                    $("#_id").focus();
                });
                return false;
            });
        });

    </script>
@endsection