@extends('layouts.app_b')

@section('content')
    <div class="container">
        @if(count($events))
        @foreach($events as $key =>$event)
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-light text-dark">
                    <div class="card-header">
                        <a class="card-title h5" href="{{ route('manager_info', ['id' => $event->id]) }}">{{ $event->name }}</a>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $event->content }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('manager_check',['id'=>$event->id]) }}" class="btn btn-primary">簽到</a>
                        {{--<button type="button" class="btn btn-primary" href="{{ route('manager_check',['id'=>$event->id]) }}">簽到</button>--}}
                        <a href="{{ route('manager_edit', ['id'=>$event->id]) }}" class="btn btn-primary">編輯活動</a>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal{{ $key }}">刪除活動</button>
                    </div>
                </div>
                <br>
            </div>
            <div class="modal fade" id="deleteModal{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalCenterTitle{{ $key }}">確定要刪除 {{ $event->name }} 嗎?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            點擊確定來刪除活動
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger delete-event" data-dismiss="modal" value="{{ $event->id }}">確定</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-primary text-center">目前沒有活動，快去新增一個吧</div>
                </div>
            </div>
        @endif
    </div>
    <script>
//        $('button.delete').click(function() {
//            $(this).parents('div.row').remove();
//        })
        $('document').ready(function() {
            $('button.delete-event').click(function() {
                var t = $(this);
               $.get('/manager/delete/'+$(this).val(), function(data, status) {
                   if(status === 'success' && data.msg) {
                       t.parents('div.row').remove();
                       $('body').attr('class', '').attr('style', '');
                   }
               });
            });
        });

    </script>
@endsection