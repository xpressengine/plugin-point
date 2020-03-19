@section('page_title')
    <h2>{{xe_trans('point::levelSetup')}}</h2>
@stop

<ul class="nav nav-tabs">
    <li><a href="{{route('point::setting.index')}}">{{xe_trans('point::baseConfig')}}</a></li>
    <li class="active"><a href="{{route('point::setting.instance')}}">{{xe_trans('point::instanceConfig')}}</a></li>
    <li><a href="{{route('point::setting.user')}}">{{xe_trans('point::userPointList')}}</a></li>
</ul>

<div class="container-fluid container-fluid--part site-manager">
    @foreach ($sectionGroup as $key => $sections)
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-group">
                <strong>{{$key}}</strong>

                @foreach ($sections as $section)
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans($section['instanceName'])}}</h3>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <strong></strong>
                            {!! $section['section']->render() !!}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
