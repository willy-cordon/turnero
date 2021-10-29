@extends('layouts.admin')
@section('styles')
    <title>{{ config('app.name') }} | {{ trans('scheduler.suppliers.title_singular') }}</title>
@endsection
@section('content')
<a class="back-link" href="{{route('scheduler.suppliers.index')}}"><i class="fas fa-long-arrow-alt-left"></i> {{trans('scheduler.suppliers.title')}}</a>

<div class="card">

    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('scheduler.suppliers.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)
            <div class="form-row">

                <div class="col-md-2 form-group {{ $errors->has('wms_id') ? 'has-error' : '' }}">
                    <label for="wms_id">{{ trans('scheduler.suppliers.fields.wms_id') }}*</label>
                    <input type="text" id="wms_id" name="wms_id" class="form-control" value="{{ old('wms_id', isset($supplier) ? $supplier->wms_id : '') }}">
                    @if($errors->has('wms_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('wms_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.wms_id_helper') }}
                    </p>
                </div>

                @if(config('app.single_name'))
                <div class="col-md-6 form-group {{ $errors->has('wms_name') ? 'has-error' : '' }}">
                    <label for="wms_name">{{ trans('scheduler.suppliers.fields.wms_name') }}*</label>
                    <input type="text" id="wms_name" name="wms_name" class="form-control" value="{{ old('wms_name', isset($supplier) ? $supplier->wms_name : '') }}">
                    @if($errors->has('wms_name'))
                        <em class="invalid-feedback">
                            {{ $errors->first('wms_name') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.wms_name_helper') }}
                    </p>
                </div>
                @elseif(!config('app.single_name'))
                    <div class="col-md-3 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">{{ trans('scheduler.suppliers.fields.name') }}*</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($supplier) ? $supplier->name : '') }}">
                        @if($errors->has('name'))
                            <em class="invalid-feedback">
                                {{ $errors->first('name') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.suppliers.fields.name_helper') }}
                        </p>
                    </div>
                    <div class="col-md-3 form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
                        <label for="lastname">{{ trans('scheduler.suppliers.fields.lastname') }}*</label>
                        <input type="text" id="lastname" name="lastname" class="form-control" value="{{ old('lastname', isset($supplier) ? $supplier->lastname : '') }}">
                        @if($errors->has('lastname'))
                            <em class="invalid-feedback">
                                {{ $errors->first('lastname') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('scheduler.suppliers.fields.lastname_helper') }}
                        </p>
                    </div>
                @endif


                <div class="col-md-4 show-group">
                    <span>{{ trans('scheduler.clients.title_singular') }}</span>
                    <span style="height: 44px; line-height: 43px;">{{isset($client) ? $client->name: ''}}</span>
                    <input type="hidden" id="client_id" name="client_id" value="{{isset($client) ? $client->id: ''}}">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 form-group {{ $errors->has('wms_date') ? 'has-error' : '' }}">
                    <label for="wms_date">{{ trans('scheduler.suppliers.fields.wms_date') }}*</label>
                    <div class='input-group wmsdate'>
                        <input style="background: #FFF;" type='text' id="wms_date" name="wms_date" class="form-control"  value="{{ old('wms_date', isset($supplier) ? $supplier->wms_date : '') }}" readonly/>
                        <span class="input-group-addon date-picker-button">
                            <span class="glyphicon glyphicon-calendar">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                        </span>
                    </div>
                    @if($errors->has('wms_date'))
                        <em class="invalid-feedback">
                            {{ $errors->first('wms_date') }}
                        </em>
                    @endif

                </div>

                <div class="form-group col-4 {{ $errors->has('wms_gender') ? 'has-error' : '' }}">
                    <label for="wms_gender">{{ trans('scheduler.suppliers.fields.wms_gender') }}*

                    </label>
                    <select name="wms_gender" id="wms_gender" class="form-control"  >
                        <option></option>
                        @foreach($genders as $gender)
                            <option value="{{ $gender }}" {{ (collect(old('wms_gender', isset($supplier) ? $supplier->wms_gender : ''))->contains($gender)) ? 'selected':'' }}>{{ $gender }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('wms_gender'))
                        <em class="invalid-feedback">
                            {{ $errors->first('wms_gender') }}
                        </em>
                    @endif

                </div>

                <div class="form-group col-4 {{ $errors->has('status') ? 'has-error' : '' }}">
                    <label for="status">{{ trans('scheduler.suppliers.fields.status_supplier') }}*

                    </label>
                    <select name="status" id="status" class="form-control"  {{$disabled}} {{$disabledCreate}}>
                        <option value="{{ isset($statusCreate) ? $statusCreate : ''}}"  >{{ isset($statusCreate) ? $statusCreate : ''}}</option>
                        @foreach($status as $state)
                            <option value="{{ $state }}" {{ (collect(old('status', isset($supplier) ? $supplier->status : ''))->contains($state)) ? 'selected':'' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <em class="invalid-feedback">
                            {{ $errors->first('status') }}
                        </em>
                    @endif

                </div>

            </div>

            <div class="form-row">
                <div class="col-md-3 form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                    <label for="address">{{ trans('scheduler.suppliers.fields.address') }}*</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address', isset($supplier) ? $supplier->address : '') }}">
                    @if($errors->has('address'))
                        <em class="invalid-feedback">
                            {{ $errors->first('address') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.address_helper') }}
                    </p>
                </div>
                <div class="col-md-1 form-group {{ $errors->has('aux5') ? 'has-error' : '' }}">
                    <label for="aux5">{{ trans('scheduler.suppliers.fields.aux5') }}</label>
                    <input type="text" id="aux5" name="aux5" class="form-control" value="{{ old('aux5', isset($supplier) ? $supplier->aux5 : '') }}">
                    @if($errors->has('aux5'))
                        <em class="invalid-feedback">
                            {{ $errors->first('aux5') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.aux5_helper') }}
                    </p>
                </div>
                <div class="col-md-4 form-group {{ $errors->has('aux4') ? 'has-error' : '' }}">
                    <label for="aux4">{{ trans('scheduler.suppliers.fields.aux4') }}*</label>
                    <select name="aux4" id="aux4" class="form-control to-select2" data-placeholder="{{trans('scheduler.suppliers.fields.aux4_placeholder')}}">
                        @if(count($cps)>1)
                            <option></option>
                            @foreach($cps as $cp)
                                <option value="{{ $cp }}" {{ (collect(old('aux4', isset($supplier) ? $supplier->aux4 : '' ))->contains($cp)) ? 'selected':'' }}>{{$cp }}</option>
                            @endforeach

                        @endif
                    </select>
                    @if($errors->has('aux4'))
                        <em class="invalid-feedback">
                            {{ $errors->first('aux4') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.aux4_helper') }}
                    </p>
                </div>
                <div class="col-md-2 form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                    <label for="phone">{{ trans('scheduler.suppliers.fields.phone') }}*</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', isset($supplier) ? $supplier->phone : '') }}" placeholder="{{trans('scheduler.suppliers.fields.phone_placeholder')}}">
                    @if($errors->has('phone'))
                        <em class="invalid-feedback">
                            {{ $errors->first('phone') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.phone_helper') }}
                    </p>
                </div>
                <div class="col-md-2 form-group {{ $errors->has('contact') ? 'has-error' : '' }}">
                    <label for="contact">{{ trans('scheduler.suppliers.fields.contact') }}</label>
                    <input type="text" id="contact" name="contact" class="form-control" value="{{ old('contact', isset($supplier) ? $supplier->contact : '') }}" placeholder="{{trans('scheduler.suppliers.fields.contact_placeholder')}}">
                    @if($errors->has('contact'))
                        <em class="invalid-feedback">
                            {{ $errors->first('contact') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.contact_helper') }}
                    </p>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">{{ trans('scheduler.suppliers.fields.email') }}*</label>
                    <input type="text" id="email" name="email" class="form-control" value="{{ old('email', isset($supplier) ? $supplier->email : '') }}">
                    @if($errors->has('email'))
                        <em class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.email_helper') }}
                    </p>
                </div>
                <div class="col-md-4 form-group {{ $errors->has('aux1') ? 'has-error' : '' }}">
                    <label for="aux1">{{ trans('scheduler.suppliers.fields.aux1') }}*</label>
                    <input type="text" id="aux1" name="aux1" class="form-control" value="{{ old('aux1', isset($supplier) ? $supplier->aux1 : '') }}">
                    @if($errors->has('aux1'))
                        <em class="invalid-feedback">
                            {{ $errors->first('aux1') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.aux1_helper') }}
                    </p>
                </div>
                <div class="col-md-4 form-group {{ $errors->has('aux2') ? 'has-error' : '' }}">
                    <label for="aux2">{{ trans('scheduler.suppliers.fields.aux2') }}*</label>
                    <input type="text" id="aux2" name="aux2" class="form-control" value="{{ old('aux2', isset($supplier) ? $supplier->aux2 : '') }}" placeholder="{{trans('scheduler.suppliers.fields.contact_placeholder')}}">
                    @if($errors->has('aux2'))
                        <em class="invalid-feedback">
                            {{ $errors->first('aux2') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.aux2_helper') }}
                    </p>
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-4 {{ $errors->has('scheme_id') ? 'has-error' : '' }}">
                    <label for="scheme_id">{{ trans('scheduler.suppliers.fields.scheme') }}</label>
                    <select name="scheme_id" id="scheme_id" class="form-control to-select2" data-minimum-results-for-search="-1" >
                        <option></option>
                        @foreach($schemes as $scheme)
                            <option value="{{ $scheme->id }}" {{ (collect(old('action', isset($supplier) ? $supplier->scheme_id : ''))->contains($scheme->id)) ? 'selected':'' }}>{{ $scheme->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('scheme_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('scheme_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.scheme_helper') }}
                    </p>
                </div>

                <div class="form-group col-md-4 {{ $errors->has('supplier_group_id') ? 'has-error' : '' }}">
                    <label for="supplier_group_id">{{ trans('scheduler.suppliers.fields.supplier_group') }}</label>
                    <select name="supplier_group_id" id="supplier_group_id" class="form-control to-select2" data-minimum-results-for-search="-1" {{ $disabledSupplierGroup  }} >
                        <option></option>
                        @foreach($supplierGroups as $supplierGroup)
                            <option value="{{ $supplierGroup->id }}" {{ (collect(old('action', isset($supplier) ? $supplier->supplier_group_id : ''))->contains($supplierGroup->id)) ? 'selected':'' }}>{{ $supplierGroup->name }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('supplier_group_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('supplier_group_id') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.supplier_group_helper') }}
                    </p>
                </div>

                <div class="form-group col-sm-2 text-center justify-content-center">
                    <label for="comorbidity">{{ trans('scheduler.suppliers.fields.comorbidity') }}</label>
                    <div style="clear: both"></div>
                    <input name="comorbidity" type="hidden" value="0"/>
                    <input id="comorbidity"
                           name="comorbidity"
                           value="1"
                           type="checkbox"
                           data-toggle="toggle"
                           data-on="{{ trans('global.yes') }}"
                           data-off="{{ trans('global.no') }}"
                           data-onstyle="success" data-offstyle="primary"
                            {{ ( old('comorbidity',  isset($supplier) ? $supplier->comorbidity : '' ) == 1 ? 'checked':'') }}>
                </div>

            </div>
                <div class=" form-group {{ $errors->has('aux3') ? 'has-error' : '' }}">
                    <label for="aux3">{{ trans('scheduler.suppliers.fields.aux3') }}</label>
                    <input type="text" id="aux3" name="aux3" class="form-control" value="{{ old('aux3', isset($supplier) ? $supplier->aux3 : '') }}">
                    @if($errors->has('aux3'))
                        <em class="invalid-feedback">
                            {{ $errors->first('aux3') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.aux3_helper') }}
                    </p>
                </div>

            <div class="form-row mb-4">
                <div class="form-group col-12 {{ $errors->has('validate_address') ? 'has-error' : '' }}">
                    <label for="address_address">{{ trans('scheduler.suppliers.fields.validate_address') }}</label>*
                    <input type="text" id="address-input" name="validate_address" class="form-control map-input" value="{{ old('validate_address', isset($supplier) ? $supplier->validate_address : '') }}">
                    @if($errors->has('validate_address'))
                        <em class="invalid-feedback">
                            {{ $errors->first('validate_address') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('scheduler.suppliers.fields.validate_address_helper') }}
                    </p>


                    <input type="hidden" name="validate_latitude" id="address-latitude" value="{{ old('validate_latitude', isset($supplier) ? $supplier->validate_latitude : '') }}" />
                    <input type="hidden" name="validate_longitude" id="address-longitude" value="{{ old('validate_longitude', isset($supplier) ? $supplier->validate_longitude : '') }}" />
                    <input type="hidden" name="validate_json" id="validation_json" value="{{ old('validation_json', isset($supplier) ? $supplier->validate_json : '') }}" />
                </div>
                <div id="address-map-container">
                    <div id="address-map"></div>
                </div>
            </div>

            <div>
                <input class="btn btn-success" type="submit" value="{{ trans('global.save') }}">
                <a href="{{ route('scheduler.suppliers.index')}}" class="btn btn-danger">{{ trans('global.cancel') }}</a>
            </div>
        </form>


    </div>
</div>
@endsection

@section('scripts')
    @parent
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_api_key') }}&libraries=places&callback=initialize" async defer></script>
    <script src=" {{ asset('js/mapInput.js') }}"></script>
    <script>
        $(function () {
            $('.wmsdate').datetimepicker({
                format: 'DD/MM/YYYY',
                locale: 'es',
                ignoreReadonly: true,
                useCurrent: false
            });

        })

    </script>
@endsection