{{app('xe.frontend')->js('assets/vendor/bootstrap/js/bootstrap.min.js')->appendTo('head')->load()}}
{{app('xe.frontend')->js(
[
'assets/core/xe-ui-component/js/xe-page.js',
'assets/core/xe-ui-component/js/xe-form.js'
]
)->load()}}
{{app('xe.frontend')->html('point.section.form')->content("<script>
    window.showMessage = function(data) {
        window.XE.toast(data.type, data.message);
        if (data.type == 'success' && data.user_id != undefined && data.point != undefined) {
            $('.current_point-' + data.user_id).val(data.point);
        }
    }
</script>")->load()}}
@section('page_title')
    <h2>{{xe_trans('point::levelSetup')}}</h2>
@stop

<ul class="nav nav-tabs">
    <li><a href="{{route('point::setting.index')}}">{{xe_trans('point::baseConfig')}}</a></li>
    <li><a href="{{route('point::setting.instance')}}">{{xe_trans('point::instanceConfig')}}</a></li>
    <li class="active"><a href="{{route('point::setting.user')}}">{{xe_trans('point::userPointList')}}</a></li>
</ul>

<div class="container-fluid container-fluid--part site-manager">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-group">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">
                            </h3>
                            <p>
                                포인트를 증가시키려면 +를 감소시키려면 -를 숫자앞에 표기한 후 업데이트해 주세요. + 또는 - 표시가 없으면 입력한 값으로 설정됩니다.
                            </p>
                        </div>
                        <div class="pull-right">
                            <div class="input-group search-group">
                                <form>
                                    <div class="search-btn-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="xi-calendar-check"></i></button>
                                        </div>
                                        <div class="search-input-group">
                                            <input type="text" name="start_date" class="form-control" placeholder="{{xe_trans('xe::enterStartDate')}}" value="{{ $startDate }}" >
                                            <input type="text" name="end_date" class="form-control" placeholder="{{xe_trans('xe::enterEndDate')}}" value="{{ $endDate }}" >
                                        </div>
                                    </div>

                                    <div>
                                        <div>
                                            <select name="search_option" class="form-control">
                                                <option value="user_email" @if(Request::get('search_option')== 'user_email') selected="selected" @endif>{{xe_trans('xe::email')}}</option>
                                                <option value="user_displayName" @if(Request::get('search_option')== 'user_displayName') selected="selected" @endif>{{xe_trans('xe::displayName')}}</option>
                                            </select>
                                            <div class="search-input-group">
                                                <input type="text" name="search_val" class="form-control" aria-label="Text input with dropdown button" placeholder="{{ xe_trans('xe::enterKeyword') }}" value="{{Request::get('search_val')}}">
                                                <button class="btn-link">
                                                    <i class="xi-search"></i><span class="sr-only">{{xe_trans('xe::search')}}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">{{xe_trans('xe::id')}}</th>
                                <th scope="col">{{xe_trans('point::point')}}</th>
                                <th scope="col">{{xe_trans('point::level')}}</th>
                                <th scope="col">{{xe_trans('xe::date')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($userPoints as $point)
                                <tr>
                                    <td>
                                        <a href="{{route('point::setting.show', ['id' => $point->user_id])}}">{{ $point->user->getDisplayName() }}</a>
                                    </td>
                                    <td>
                                        <form action="{{ route('point::user_point.update') }}" data-submit="xe-ajax" method="POST" data-callback="showMessage">
                                            <input type="hidden" name="user_id" value="{{$point->user_id}}">
                                            <input type="text" class="current_point current_point-{{$point->user_id}}" value="{{ $point->point }}" readonly="readonly" disabled="disabled"> ->
                                            <input type="text" name="point" value="{{ $point->point }}">
                                            <button type="submit">업데이트</button>
                                        </form>
                                    </td>
                                    <td>{{ $point->level }}</td>
                                    <td>{{ $point->updated_at->format('Y.m.d H:i:s') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($pagination = $userPoints->render())
                        <div class="panel-footer">
                            <div class="pull-left">
                                <nav>
                                    {!! $pagination !!}
                                </nav>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
