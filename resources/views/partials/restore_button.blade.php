<form id="delete-form-{{$model->id}}" action="{{ route($restore_method, $model->id) }}" method="POST"  style="display: inline-block;">
    <input type="hidden" name="_method" value="PATCH">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="submit" class="btn btn-xs btn-success" value="{{ $restore_label }}">
</form>