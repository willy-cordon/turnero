<form id="delete-form-{{$model->id}}" action="{{ route($destroy_method, $model->id) }}" method="POST"  style="display: inline-block;">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <button type="button"  class="btn btn-xs btn-danger" data-toggle="modal" data-target="#confirm-submit-{{$model->id}}" title="{{ $destroy_label ?? trans('global.delete')}}"><i class='fas fa-trash'></i> {{ $destroy_label ?? ""}}</button>


    <div class="modal fade" id="confirm-submit-{{$model->id}}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ trans('global.delete') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ trans('global.areYouSure') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('global.no') }}</button>
                    <button type="button" class="btn btn-primary" onclick="$(this).closest('form').submit();">{{ trans('global.yes') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>