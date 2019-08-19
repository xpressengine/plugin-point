@section('page_title')
    <h2>{{xe_trans('point::pointEarnUseLog')}}</h2>
@stop

<div class="row">
    <div class="col-sm-12">
        <div class="panel-group">
            <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">
                            </h3>
                        </div>
                        <div class="pull-right">
                            <div class="input-group search-group">
                                <form>
                                    <div class="search-btn-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="xi-calendar-check"></i></button>
                                        </div>
                                        <div class="search-input-group">
                                            <input type="text" name="start_date" class="form-control" placeholder="{{xe_trans('xe::enterStartDate')}}" value="{{ Request::get('start_date') }}" >
                                            <input type="text" name="end_date" class="form-control" placeholder="{{xe_trans('xe::enterEndDate')}}" value="{{ Request::get('end_date') }}" >
                                        </div>
                                    </div>

                                    <div>
                                        <div class="search-input-group">
                                            <input type="text" name="user_email" class="form-control" aria-label="Text input with dropdown button" placeholder="{{xe_trans('xe::enterEmail')}}" value="{{Request::get('user_email')}}">
                                            <button class="btn-link">
                                                <i class="xi-search"></i><span class="sr-only">{{xe_trans('xe::search')}}</span>
                                            </button>
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
                                <th scope="col">{{xe_trans('xe::type')}}</th>
                                <th scope="col">{{xe_trans('xe::date')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <a href="{{route('point::setting.show', ['id' => $log->user_id])}}">{{ $log->user->getDisplayName() }}</a>
                                    </td>
                                    <td>{{ $log->point }}</td>
                                    <td>{{ xe_trans($log->title) }}</td>
                                    <td>{{ $log->created_at->format('Y.m.d H:i:s') }}</td>
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


