@section('page_title')
    <h2><a href="{{ route('settings.user.index') }}"><i class="xi-arrow-left"></i></a>{{ $user->getDisplayName() }}님의 포인트 내역</h2>
@stop

<div class="row">
    <div class="col-sm-12">
        <div class="panel-group">
            <div class="panel">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title">
                                포인트 적립/사용 내역
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
                                <th scope="col">일시</th>
                                <th scope="col">구분</th>
                                <th scope="col">포인트</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y.m.d H:i:s') }}</td>
                                    <td>{{ $log->title }}</td>
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

