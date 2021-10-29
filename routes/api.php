<?php

Route::group(['middleware' => ['scheduler.token.permission'], 'prefix' => '/v1', 'namespace'=>'Scheduler\api', 'as'=>'api.'], function () {
    Route::post('purchase-orders/{client_id}/{api_token}', 'GatewayController@storePurchaseOrders');
    Route::get('daily-appointments/{client_id}/{api_token}', 'GatewayController@getDailyAppointments');
    Route::patch('is-synchronized/{client_id}/{api_token}/appointment/{appointment_id}', 'GatewayController@changeAppointmentToSynchronized');
});
