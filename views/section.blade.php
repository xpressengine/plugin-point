<div class="__xe_point_section_{{ $seq }}">
    <form action="{{ route('point::section.update') }}" data-submit="xe-ajax" method="POST" data-callback="showMessage">
        {{ method_field('put') }}
        <ul class="list-unstyled">
            @foreach($actions as $action)
                <li>
                    <label>
                        {{ xe_trans($action['title']) }}
                    </label>
                    {{ uio('formText', ['name'=> $action['name'], 'value'=>$action['point'], 'type'=>'number']) }}
                </li>
            @endforeach
        </ul>
        <button type="submit" class="btn btn-primary">{{xe_trans('xe::save')}}</button>
    </form>
</div>
