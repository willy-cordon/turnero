<?php


Route::redirect('/', 'admin/home');

Auth::routes(['register' => false]);

// Change Password Routes...
Route::get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
Route::patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

Route::group(['middleware' => ['auth', 'password.refresh'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('permissions', 'Admin\PermissionsController');
    Route::delete('permissions_mass_destroy', 'Admin\PermissionsController@massDestroy')->name('permissions.mass_destroy');
    Route::resource('roles', 'Admin\RolesController');
    Route::delete('roles_mass_destroy', 'Admin\RolesController@massDestroy')->name('roles.mass_destroy');
    Route::resource('users', 'Admin\UsersController');
    Route::delete('users_mass_destroy', 'Admin\UsersController@massDestroy')->name('users.mass_destroy');
});


Route::group(['middleware' => ['scheduler.admin.permission', 'auth', 'password.refresh'], 'prefix' => 'scheduler', 'as' => 'scheduler.'], function () {
    Route::resource('appointment-types', 'Scheduler\AppointmentTypeController')->except(['show']);
    Route::patch('appointment-types/{appointment_type_id}/restore','Scheduler\AppointmentTypeController@restore')->name('appointment-types.restore');

    Route::resource('appointment-origins', 'Scheduler\AppointmentOriginController')->except(['show']);
    Route::patch('appointment-origins/{appointment_origin_id}/restore','Scheduler\AppointmentOriginController@restore')->name('appointment-origins.restore');

    Route::resource('appointment-unload-types', 'Scheduler\AppointmentUnloadTypeController')->except(['show']);
    Route::patch('appointment-unload-types/{appointment_unload_type_id}/restore','Scheduler\AppointmentUnloadTypeController@restore')->name('appointment-unload-types.restore');

    Route::resource('appointment-actions', 'Scheduler\AppointmentActionController')->except(['show']);
    Route::patch('appointment-actions/{appointment_action_id}/restore','Scheduler\AppointmentActionController@restore')->name('appointment-actions.restore');

    Route::resource('locations', 'Scheduler\LocationController')->except(['show']);
    Route::patch('locations/{location_id}/restore','Scheduler\LocationController@restore')->name('locations.restore');

    Route::resource('docks', 'Scheduler\DockController')->except(['show']);
    Route::patch('docks/{dock_id}/restore','Scheduler\DockController@restore')->name('docks.restore');

    Route::resource('clients', 'Scheduler\ClientController')->except(['show']);

    Route::get('/settings', 'Scheduler\SettingsController@create')->name('settings.create');
    Route::put('/settings', 'Scheduler\SettingsController@update')->name('settings.update');

    Route::get('locks/create/{location}', 'Scheduler\SchedulerLockController@create')->name('locks.create');
    Route::resource('locks', 'Scheduler\SchedulerLockController')->except(['create', 'show']);

    Route::get('cell-locks/get-cell-locks', 'Scheduler\SchedulerCellLockController@getCellLocks')->name('cell-locks.get-cell-locks');
    Route::get('cell-locks/create/{location}', 'Scheduler\SchedulerCellLockController@create')->name('cell-locks.create');
    Route::resource('cell-locks', 'Scheduler\SchedulerCellLockController')->except(['create', 'show']);

    Route::get('activity-groups/create/{location}', 'Scheduler\ActivityGroupController@create')->name('activity-groups.create');
    Route::resource('activity-groups', 'Scheduler\ActivityGroupController')->except(['create', 'show']);
    Route::patch('activity-groups/{activity_group_id}/restore','Scheduler\ActivityGroupController@restore')->name('activity-groups.restore');


    Route::get('activities-admin-tools','Scheduler\ActivityAdminTools@index')->name('activities-admin-tools.migrations');
    Route::put('activities-admin-tools/migration-appointment', 'Scheduler\ActivityAdminTools@updateMigrationAppointment')->name('activities-admin-tools.migration-appointment');
    Route::put('activities-admin-tools/migration-user', 'Scheduler\ActivityAdminTools@updateMigrationUser')->name('activities-admin-tools.migration-user');

    Route::get('appointment-admin-tools', 'Scheduler\AppointmentAdminToolsController@index')->name('appointment-admin-tools.migrations');
    Route::put('appointment-admin-tools/migration-supplier', 'Scheduler\AppointmentAdminToolsController@updateMigrationSupplier')->name('appointment-admin-tools.migration-supplier');

    Route::resource('schemes', 'Scheduler\SchemeController')->except(['show']);
    Route::patch('schemes/{scheme_id}/restore','Scheduler\SchemeController@restore')->name('schemes.restore');

    Route::resource('supplier-groups','Scheduler\SupplierGroupController')->except(['show']);
    Route::patch('supplier-groups/{supplierGroups_id}/restore','Scheduler\SupplierGroupController@restore')->name('supplier-groups.restore');

    Route::resource('sequence', 'Scheduler\SequenceController')->except(['show']);
    Route::patch('sequence/{sequence_id}/restore','Scheduler\SequenceController@restore')->name('sequence.restore');





});

Route::group(['middleware' => ['scheduler.coordinator.permission', 'auth','password.refresh'], 'prefix' => 'scheduler', 'as' => 'scheduler.'], function () {

    Route::get('transportation-vouchers/get-vouchers', 'Scheduler\TransportationVoucherController@getVouchers')->name('transportation-vouchers.get-vouchers');
    Route::get('transportation-vouchers/create/{appointment}', 'Scheduler\TransportationVoucherController@create')->name('transportation-vouchers.create');
    Route::resource('transportation-vouchers', 'Scheduler\TransportationVoucherController')->except(['create', 'destroy','update']);
});

Route::group(['middleware' => ['scheduler.user.permission', 'auth', 'password.refresh'], 'prefix' => 'scheduler', 'as' => 'scheduler.'], function () {
    Route::get('suppliers/search', 'Scheduler\SupplierController@search')->name('suppliers.search');
    Route::get('suppliers/get-suppliers', 'Scheduler\SupplierController@getSuppliers')->name('suppliers.get-suppliers');
    Route::get('suppliers/get-workflow-suppliers', 'Scheduler\SupplierController@getWorkflowSuppliers')->name('suppliers.get-workflow-suppliers');
    Route::get('suppliers/get-by-client', 'Scheduler\SupplierController@getByClient')->name('supplier.get-by-client');
    Route::put('suppliers/toggle-intervention', 'Scheduler\SupplierController@toggleIntervention')->name('supplier.toggle-intervention');
    Route::get('suppliers/workflow', 'Scheduler\SupplierController@workflow')->name('supplier.workflow');
    Route::get('suppliers/timeline/{supplier}', 'Scheduler\SupplierController@timeLine')->name('supplier.timeline');

    Route::resource('suppliers', 'Scheduler\SupplierController');

    Route::get('appointments/complete-range', 'Scheduler\AppointmentController@addIsInRange')->name('activity-instances.complete-range');
    Route::get('appointments/check-appointment', 'Scheduler\AppointmentController@checkAppointment')->name('appointments.check-appointment');
    Route::get('appointments/create/{location}', 'Scheduler\AppointmentController@create')->name('appointments.create');
    Route::get('appointments/get-appointments', 'Scheduler\AppointmentController@getAppointments')->name('appointments.get-appointments');
    Route::get('appointments/get-by-supplier', 'Scheduler\AppointmentController@getBySupplier')->name('appointments.get-by-supplier');
    Route::resource('appointments', 'Scheduler\AppointmentController')->except('create');

    Route::get('purchase-orders/search', 'Scheduler\PurchaseOrderController@search')->name('purchase-orders.search');
    Route::get('purchase-orders/get-by-id', 'Scheduler\PurchaseOrderController@getById')->name('purchase-orders.get-by-id');
    Route::resource('purchase-orders', 'Scheduler\PurchaseOrderController')->only(['show', 'index', 'search']);
    Route::get('appointments-panel/{location}', 'Scheduler\AppointmentPanelController@index')->name('appointments-panel.index');

    //Activities e-diary
    Route::get('activity-instances/allEDiary','Scheduler\ActivityInstanceController@indexAllEDiary')->name('activity-instances.allEDiary');
    Route::get('activity-instances/pendingEDiary','Scheduler\ActivityInstanceController@indexPendingEDiary')->name('activity-instances.pendingEDiary');
    Route::get('activity-instances/expiredEDiary','Scheduler\ActivityInstanceController@indexExpiredEDiary')->name('activity-instances.expiredEDiary');
    Route::get('activity-instances/inProgressEDiary','Scheduler\ActivityInstanceController@indexInProgressEDiary')->name('activity-instances.inProgressEDiary');
    //Activities Vigilance
    Route::put('activity-instances/updateCheckbox','Scheduler\ActivityInstanceController@updateCheckbox')->name('activity-instances.updateCheckbox');

    Route::get('activity-instances/allVigilance','Scheduler\ActivityInstanceController@indexAllVigilance')->name('activity-instances.allVigilance');
    Route::get('activity-instances/pendingVigilance','Scheduler\ActivityInstanceController@indexPendingVigilance')->name('activity-instances.pendingVigilance');
    Route::get('activity-instances/expiredVigilance','Scheduler\ActivityInstanceController@indexExpiredVigilance')->name('activity-instances.expiredVigilance');
    Route::get('activity-instances/inProgressVigilance','Scheduler\ActivityInstanceController@indexInProgressVigilance')->name('activity-instances.inProgressVigilance');
    //Activities Appointment
    Route::get('activity-instances/allAppointment','Scheduler\ActivityInstanceController@indexAllAppointment')->name('activity-instances.allAppointment');
    Route::get('activity-instances/pendingAppointment','Scheduler\ActivityInstanceController@indexPendingAppointment')->name('activity-instances.pendingAppointment');
    Route::get('activity-instances/expiredAppointment','Scheduler\ActivityInstanceController@indexExpiredAppointment')->name('activity-instances.expiredAppointment');
    Route::get('activity-instances/inProgressAppointment','Scheduler\ActivityInstanceController@indexInProgressAppointment')->name('activity-instances.inProgressAppointment');

    Route::get('activity-instances/export','Scheduler\ActivityInstanceController@export')->name('activity-instance.export');
    Route::get('activity-instances/get-activity-instances', 'Scheduler\ActivityInstanceController@getActivityInstances')->name('activity-instances.get-activity-instances');
    Route::get('activity-instances/create-initial', 'Scheduler\ActivityInstanceController@createInitial')->name('activity-instances.create-initial');
    Route::get('activity-instances/create-finish', 'Scheduler\ActivityInstanceController@createFinish')->name('activity-instances.create-finish');
    Route::get('activity-instances/get-counters', 'Scheduler\ActivityInstanceController@getCounters')->name('activity-instances.get-counters');
    Route::get('activity-instances/update-activities-eDiary', 'Scheduler\ActivityInstanceController@updateActivityEdiary')->name('activity-instances.update-activities-eDiary');

    Route::resource('activity-instances', 'Scheduler\ActivityInstanceController')->except(['destroy','show']);

    Route::get('activity-actions/create','Scheduler\ActivityActionController@create')->name('activity-actions.create');
    Route::resource('activity-actions','Scheduler\ActivityActionController')->except(['create','show']);
    Route::patch('activity-actions/{activity_action_id}/restore','Scheduler\ActivityActionController@restore')->name('activity-actions.restore');

    Route::get('activities/create','Scheduler\ActivityController@create')->name('activities.create');
    Route::resource('activities','Scheduler\ActivityController')->except(['create','show']);
    Route::patch('activities/{activity_id}/restore','Scheduler\ActivityController@restore')->name('activity.restore');

    Route::post('notifications-email/edit-status','Scheduler\NotificationController@setStatus')->name('notifications-email.edit-status');
    Route::get('notifications-email/get-notifications','Scheduler\NotificationController@getNotifications')->name('notifications-email.get-notifications');
    Route::resource('notifications-email', 'Scheduler\NotificationController')->except(['show','create','destroy']);

    Route::get('supplier-intervention-logs/get-interventions', 'Scheduler\SupplierInterventionLogController@getSupplierInterventionLog')->name('supplier-intervention-logs.save.get-interventions');
    Route::post('supplier-intervention-logs/save', 'Scheduler\SupplierInterventionLogController@save')->name('supplier-intervention-logs.save');
    Route::resource('supplier-intervention-logs', 'Scheduler\SupplierInterventionLogController')->except(['show','create']);

    Route::get('intervention-migrate', 'Scheduler\InterventionMigrateController@index')->name('intervention-migrate.migrations');
    Route::put('intervention-migrate/migration-intervention', 'Scheduler\InterventionMigrateController@migrationIntervention')->name('intervention-migrate.migration-intervention');

    Route::get('appointment-change-logs', 'Scheduler\AppointmentChangeLogController@index')->name('appointment-change-logs.index');
    Route::get('appointment-change-logs/get-change-logs', 'Scheduler\AppointmentChangeLogController@getAppointmentChangeLog')->name('appointment-change-logs.getAppointmentChangeLog');

    Route::get('activity-instance-change-date', 'Scheduler\ActivityInstanceChangeDateController@index')->name('activity-instance-change-date.index');
    Route::put('activity-instance-change-date/activity-instance-change-day', 'Scheduler\ActivityInstanceChangeDateController@activityInstanceChangeDay')->name('activity-instance-change-date.activityInstanceChangeDay');


    Route::get('entities-export/activities','Scheduler\EntitiesExportController@activitiesExport')->name('entities-export.activitiesExport');
    Route::get('entities-export/suppliers','Scheduler\EntitiesExportController@suppliersExport')->name('entities-export.suppliersExport');
    Route::get('entities-export/appointments','Scheduler\EntitiesExportController@appointmentsExport')->name('entities.export-appointmentsExport');
    Route::get('entities-export/intervened','Scheduler\EntitiesExportController@intervenedExport')->name('entities-export.intervenedExport');
    Route::resource('entities-export','Scheduler\EntitiesExportController')->except(['show','create','destroy']);

});





