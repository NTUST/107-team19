@extends('layouts.app_b')

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <div class="card bg-light text-dark">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $event->name }}</h5>
                        <p class="card-text">{{ $event->content }}</p>
                        <div class="col-md-10">
                            <label for="member_quantity">人數</label>
                            <h3 id="member_quantity">{{ $event->member_quantity }}</h3>
                        </div>

                    </div>
                    <div class="card-footer">
                        <a href="{{ route('manager_check',['id'=>$event->id]) }}" class="btn btn-primary">簽到</a>
                        <a href="{{ route('manager_edit', ['id'=>$event->id]) }}" class="btn btn-primary">編輯活動</a>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">刪除活動</button>
                    </div>
                </div>
                <br>
            </div>
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalCenterTitle">確定要刪除 {{ $event->name }} 嗎?</h5>
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
                    }
                });
            });
        });

    </script>
@endsection