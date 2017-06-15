<div class="__xe_point_section_{{ $seq }}">
    <form action="{{ route('point::section.update') }}" data-submit="xe-ajax" method="POST" data-callback="showMessage">
        {{ method_field('put') }}
        <ul class="list-unstyled">
            @foreach($actions as $action)
                <li>
                    <label>
                        {{ $action['title'] }}
                        {{ uio('formText', ['name'=> $action['name'], 'value'=>$action['point'], 'type'=>'number']) }}
                    </label>
                </li>
            @endforeach
        </ul>
        <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
    </form>
</div>
