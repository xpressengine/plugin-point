@section('page_title')
    <h2>{{xe_trans('point::levelSetup')}}</h2>
@stop

<ul class="nav nav-tabs">
    <li class="active"><a href="{{route('point::setting.index')}}">{{xe_trans('point::baseConfig')}}</a></li>
    <li><a href="{{route('point::setting.instance')}}">{{xe_trans('point::instanceConfig')}}</a></li>
    <li><a href="{{route('point::setting.user')}}">{{xe_trans('point::userPointList')}}</a></li>
</ul>

<div class="container-fluid container-fluid--part site-manager">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-group">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans('point::baseConfig')}}</h3>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="__xe_point_section_default">
                                <form action="{{ route('point::config.update') }}" data-submit="xe-ajax" method="POST" data-callback="showMessage">
                                    {{ method_field('put') }}
                                    <ul class="list-unstyled">
                                        <li>
                                            <label>
                                                {{ xe_trans('point::pointFunctionOn') }}
                                            </label>
                                            {!! uio('formSelect', [
                                                'name'=> 'function_use',
                                                'value'=> $baseConfig['function_use'],
                                                'options' => [
                                                    ['text' => xe_trans('xe::use'), 'value' => 'use'],
                                                    ['text' => xe_trans('xe::disuse'), 'value' => 'disuse'],
                                                ],
                                                'description' => xe_trans('point::pointFunctionOnDescription')
                                            ]) !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('point::maxLevel') }}
                                            </label>
                                            {!! uio('formText', ['name'=> 'max_level', 'value'=> $baseConfig['max_level'], 'type'=>'number', 'description'=>xe_trans('point::maxLevelDescription')])  !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('point::pointName') }}
                                            </label>
                                            {!! uio('formText', ['name'=> 'point_name', 'value'=> $baseConfig['point_name'], 'type'=>'text', 'description'=>xe_trans('point::pointNameDescription')])  !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('point::levelIcon') }}
                                            </label>
                                            {!! uio('formSelect', [
                                                'name'=> 'level_icon',
                                                'value'=> $baseConfig['level_icon'],
                                                'options' => $levelIconOptions,
                                                'description' => xe_trans('point::levelIconDescription')
                                            ]) !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('point::disableDownload') }}
                                            </label>
                                            {!! uio('formSelect', [
                                                'name'=> 'disable_download',
                                                'value'=> $baseConfig['disable_download'],
                                                'options' => [
                                                    ['text' => xe_trans('xe::use'), 'value' => 'use'],
                                                    ['text' => xe_trans('xe::disuse'), 'value' => 'disuse'],
                                                ],
                                                'description' => xe_trans('point::disableDownloadDescription')
                                            ]) !!}
                                        </li>
                                        <li>
                                            <label>
                                                {{ xe_trans('point::disableReadBoard') }}
                                            </label>
                                            {!! uio('formSelect', [
                                                'name'=> 'disable_read_board',
                                                'value'=> $baseConfig['disable_read_board'],
                                                'options' => [
                                                    ['text' => xe_trans('xe::use'), 'value' => 'use'],
                                                    ['text' => xe_trans('xe::disuse'), 'value' => 'disuse'],
                                                ],
                                                'description' => xe_trans('point::disableReadBoardDescription')
                                            ]) !!}
                                        </li>
                                    </ul>
                                    <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans('xe::user')}}</h3>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                                {!! $section['user']->render() !!}
                        </div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans('board::board')}}</h3>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            {!! $section['board']->render() !!}
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans('point::groupInterlock')}}</h3>
                            <p class="help-block">{{xe_trans('point::groupInterlockDescription')}}</p>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="__xe_point_section_default">
                                <form action="{{ route('point::group.update') }}" data-submit="xe-ajax" method="POST" data-callback="showMessage">
                                    {{ method_field('put') }}
                                    <ul class="list-unstyled">
                                        @foreach ($groupList as $group)
                                            <li>
                                                <label>
                                                    {{ $group->name }}
                                                </label>
                                                @if ($defaultGroupId == $group->id)
                                                    {!! xe_trans('xe::defaultGroup') !!}
                                                @else
                                                    {!! uio('formText', ['name'=> $group->id, 'value'=> $groupConfig[$group->id], 'type'=>'number'])  !!}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                    <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">{{xe_trans('point::levelPoint')}}</h3>
                            <p class="help-block">{{xe_trans('point::levelPointDescription')}}</p>
                        </div>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="__xe_point_section_default">
                                <form action="{{ route('point::level_point.update') }}" data-submit="xe-ajax" method="POST" data-callback="showMessage">
                                    {{ method_field('put') }}
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">Level</th>
                                                <th scope="col">Level Icon</th>
                                                <th scope="col">Point</th>
                                                <th scope="col">User Group</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($levels as $level)
                                                    <tr>
                                                        <td>{{ $level }}</td>
                                                        <td><img src="{{$iconPath}}/{{$level}}.gif" alt="level {{$level}} icon"/></td>
                                                        <td>
                                                            <input type="number" name="level_{{$level}}" value="{{$levelPointConfig['level_' . $level]}}"/>
                                                            {{$baseConfig['point_name']}}
                                                        </td>
                                                        <td>
                                                            @if (isset($groupByLevel[$level]))
                                                                {{$groupByLevel[$level]}}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
