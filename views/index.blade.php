@section('page_title')
    <h2><a href="{{ route('point::setting.index') }}"><i class="xi-arrow-left"></i>포인트 기본 설정</a></h2>
@stop

<div class="row">
    <div class="col-sm-12">
        <div class="panel-group">
            <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">기본 포인트 부여</h3>
                        </div>
                        <div class="pull-right">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="btn-link panel-toggle pull-right"><i class="xi-angle-down"></i><i class="xi-angle-up"></i><span class="sr-only">{{xe_trans('xe::fold')}}</span></a>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            {!! $section !!}
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

