@section('page_title')
    <h2>{{xe_trans('point::logForUser', ['user_name' => $user->getDisplayName()])}}</h2>
@stop

<div class="container-fluid container-fluid--part site-manager">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-group">
                <div class="panel">
                        <div class="panel-heading">
                            <div class="pull-left">
                                <h3 class="panel-title">
                                    {{xe_trans('point::pointEarnUseLog')}}
                                </h3>
                            </div>
                            <div class="pull-right">
                                <h3 class="panel-title">
                                    {{ number_format($record->point) }}점
                                </h3>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">{{xe_trans('xe::date')}}</th>
                                    <th scope="col">{{xe_trans('xe::type')}}</th>
                                    <th scope="col">{{xe_trans('point::point')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y.m.d H:i:s') }}</td>
                                        <td>{{ xe_trans($log->title) }}</td>
                                        <td>{{ $log->point }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($pagination = $logs->render())
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
