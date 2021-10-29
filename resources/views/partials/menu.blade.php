<div class="sidebar">
    <nav class="sidebar-nav">

        <ul class="nav">
            <li class="nav-item nav-dropdown">
                <a class="nav-link  nav-dropdown-toggle" href="#">
                    <i class="nav-icon fas fa-fw fa-tachometer-alt">

                    </i>
                    {{ trans('global.dashboard') }}
                </a>
                <ul class="nav-dropdown-items">
                    @foreach($all_locations as $location)
                    <li class="nav-item">
                        <a href="{{ route("scheduler.appointments-panel.index", $location->id) }}" class="nav-link {{ request()->is('scheduler/appointments-panel/'.$location->id) ? 'active' : '' }}">
                            <i class="fas fa-calendar-plus nav-icon">

                            </i>
                            {{  $location->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </li>

            @can('scheduler_user')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-calendar-alt nav-icon">

                        </i>
                        {{ trans('scheduler.global.user_title') }}
                    </a>
                    <ul class="nav-dropdown-items">

                        <li class="nav-item">
                            <a href="{{ route("scheduler.appointments.index") }}" class="nav-link {{ request()->is('scheduler/appointments') || request()->is('scheduler/appointments/*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-plus nav-icon">

                                </i>
                                {{ trans('scheduler.appointments.title') }}
                            </a>
                        </li>


                        @can('users_manage')
                        <li class="nav-item">
                            <a href="{{ route("scheduler.appointment-change-logs.index") }}" class="nav-link {{ request()->is('appointment-change-logs') || request()->is('appointment-change-logs') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list nav-icon"></i>
                                {{ trans('scheduler.appointment_change_log.title_menu') }}
                            </a>
                        </li>
                        @endcan

                    </ul>
                </li>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas  fa-users nav-icon">

                        </i>
                        {{ trans('scheduler.suppliers.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="{{ route("scheduler.suppliers.index") }}" class="nav-link {{ (request()->is('scheduler/suppliers') || request()->is('scheduler/suppliers/*')) && (!request()->is('scheduler/suppliers/workflow')) ? 'active' : '' }}">
                                <i class="fa-fw fas fa-users nav-icon">

                                </i>
                                {{ trans('scheduler.suppliers.list') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.supplier.workflow") }}" class="nav-link {{ request()->is('scheduler/suppliers/workflow')? 'active' : '' }}">
                                <i class="fa-fw fas fa-user-cog nav-icon">

                                </i>
                                {{ trans('scheduler.suppliers.workflow') }}
                            </a>
                        </li>
                    </ul>
                </li>

                {{--/////////Vigilance////////--}}
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-tasks nav-icon activities-icon">

                        </i>
                        {{ trans('scheduler.global.activities_user_title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.allVigilance") }}" class="nav-link {{ request()->is('scheduler/activity-instances/allVigilance')  ? 'active' : '' }}">
                                <i class="fas fa-check-double nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.All') }}
                                <span id="vi-all" class="menu-counter mc-all" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.inProgressVigilance") }}" class="nav-link {{ request()->is('scheduler/activity-instances/inProgressVigilance') ? 'active' : '' }}">
                                <i class="fas fa-spinner nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.InProgress') }}
                                <span id="vi-in-progress" class="menu-counter mc-in-progress" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            {{--    ediary        --}}
            @cannot('scheduler_doctor')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-tasks nav-icon activities-icon">

                        </i>
                        {{ trans('scheduler.global.activities_title_eDiary') }}
                    </a>
                    <ul class="nav-dropdown-items">

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.allEDiary") }}" class="nav-link {{ request()->is('activity-groups') || request()->is('activity-groups') ? 'active' : '' }}">
                                <i class="fas fa-check-double nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.All') }}
                                <span id="ed-all" class="menu-counter mc-all" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>

                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.pendingEDiary") }}" class="nav-link {{ request()->is('activity-actions') || request()->is('activity-actions') ? 'active' : '' }}">
                                <i class="fas fa-exclamation nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.Pending') }}

                                <span id="ed-pending" class="menu-counter mc-pending" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>

                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.expiredEDiary") }}" class="nav-link {{ request()->is('activities') || request()->is('activities') ? 'active' : '' }}">
                                <i class="fas fa-hourglass-end nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.Expired') }}

                                <span id="ed-expired" class="menu-counter mc-expired" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.inProgressEDiary") }}" class="nav-link {{ request()->is('activities') || request()->is('activities') ? 'active' : '' }}">
                                <i class="fas fa-spinner nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.InProgress') }}

                                <span id="ed-in-progress" class="menu-counter mc-in-progress" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>

                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instance-change-date.index") }}" class="nav-link {{ request()->is('scheduler/activity-instance-change-date')  ? 'active' : '' }}">
                                <i class="fas fa-calendar nav-icon"></i>
                                {{ trans('scheduler.activity_instance_change_date.title') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcannot
            {{--    END ediary        --}}
            {{--    Activity appointment        --}}
            @can('scheduler_user')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-tasks nav-icon activities-icon">

                        </i>
                        {{ trans('scheduler.global.activities_title_appointment') }}
                    </a>
                    <ul class="nav-dropdown-items">

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.allAppointment") }}" class="nav-link {{ request()->is('activity-groups') || request()->is('activity-groups') ? 'active' : '' }}">
                                <i class="fas fa-check-double nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.All') }}
                                <span id="tu-all" class="menu-counter mc-all" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.pendingAppointment") }}" class="nav-link {{ request()->is('activity-actions') || request()->is('activity-actions') ? 'active' : '' }}">
                                <i class="fas fa-exclamation nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.Pending') }}
                                <span id="tu-pending" class="menu-counter mc-pending" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.expiredAppointment") }}" class="nav-link {{ request()->is('activities') || request()->is('activities') ? 'active' : '' }}">
                                <i class="fas fa-hourglass-end nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.Expired') }}
                                <span id="tu-expired" class="menu-counter mc-expired" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-instances.inProgressAppointment") }}" class="nav-link {{ request()->is('activities') || request()->is('activities') ? 'active' : '' }}">
                                <i class="fas fa-spinner nav-icon"></i>
                                {{ trans('scheduler.activity_instances_filter_global.InProgress') }}
                                <span id="tu-in-progress" class="menu-counter mc-in-progress" >
                                    <i class="fas fa-spinner fa-spin fa-fw"></i>
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            {{--    End Activity appointment        --}}

            @can('scheduler_admin')
                <li class="nav-item">
                    <a href="{{ route("scheduler.transportation-vouchers.index") }}" class="nav-link {{ request()->is('transportation-vouchers') || request()->is('transportation-vouchers') ? 'active' : '' }}">
                        <i class="fas fa-car nav-icon"></i>
                        {{ trans('scheduler.transportation_vouchers.title') }}
                    </a>
                </li>
            @endcan
            @can('scheduler_user')
                <li class="nav-item">
                    <a href="{{ route("scheduler.notifications-email.index") }}" class="nav-link {{ request()->is('notifications') || request()->is('notifications') ? 'active' : '' }}">
                        <i class="far fa-envelope nav-icon"></i>
                        {{ trans('scheduler.notifications.title') }}
                        @if($unread_notifications != -1)
                            <span style="background: red; color: #FFF; width: 25px; height: 25px; border-radius: 50%; display: inline-block; text-align: center; font-size: 10px; line-height: 25px; font-weight: bold;">
                                {{$unread_notifications}}
                            </span>
                        @endif
                    </a>
                </li>
            @endcan

            @canany(['scheduler_doctor', 'scheduler_admin'])
                <li class="nav-item">
                    <a href="{{ route("scheduler.supplier-intervention-logs.index") }}" class="nav-link {{ request()->is('transportation-vouchers') || request()->is('transportation-vouchers') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list nav-icon"></i>
                        {{ trans('scheduler.supplier_intervention_log.title_singular') }}
                    </a>
                </li>
            @endcanany

            @can('scheduler_admin')
                <li class="nav-item">
                    <a href="{{ route("scheduler.entities-export.index") }}" class="nav-link {{ request()->is('entities-export') || request()->is('entities-export') ? 'active' : '' }}">
                        <i class="fas fa-file-download nav-icon"></i>
                        {{ trans('scheduler.entities_export.title') }}
                    </a>
                </li>
            @endcan
            @canany(['scheduler_doctor', 'scheduler_admin'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fas fa-exchange-alt nav-icon"></i>

                        {{ trans('scheduler.migrations_menu.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('scheduler_admin')
                        <li class="nav-item">
                            <a href="{{ route("scheduler.appointment-admin-tools.migrations") }}" class="nav-link {{ request()->is('scheduler/docks') || request()->is('scheduler/docks/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-user nav-icon"></i>
                                {{ trans('scheduler.supplier_migrations.title') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activities-admin-tools.migrations") }}" class="nav-link {{ request()->is('scheduler/activity_migrations') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-tasks nav-icon"></i>
                                {{ trans('scheduler.activity_migrations.menu_migration') }}
                            </a>
                        </li>
                        @endcan
                        @canany(['scheduler_doctor', 'scheduler_admin'])
                        <li class="nav-item">
                            <a href="{{ route("scheduler.intervention-migrate.migrations") }}" class="nav-link {{ request()->is('scheduler/activity_migrations') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                {{ trans('scheduler.intervention_migration.title_menu') }}
                            </a>
                        </li>
                        @endcanany


                    </ul>
                </li>
            @endcan



            @can('scheduler_admin')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-cogs nav-icon">

                        </i>
                        {{ trans('scheduler.global.admin_title_min') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        <!--
                        <li class="nav-item">
                            <a href="{{ route("scheduler.appointment-types.index") }}" class="nav-link {{ request()->is('scheduler/appointment-types') || request()->is('scheduler/appointment-types/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-cog nav-icon">

                                </i>
                                {{ trans('scheduler.types.title_simple') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.appointment-origins.index") }}" class="nav-link {{ request()->is('scheduler/appointment-origins') || request()->is('scheduler/appointment-origins/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-cog nav-icon">

                                </i>
                                {{ trans('scheduler.origins.title_simple') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.appointment-unload-types.index") }}" class="nav-link {{ request()->is('scheduler/appointment-unload-types') || request()->is('scheduler/appointment-unload-types/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-cog nav-icon">

                                </i>
                                {{ trans('scheduler.unload_types.title_simple') }}
                            </a>
                        </li>
                        -->
                        <li class="nav-item">
                            <a href="{{ route("scheduler.appointment-actions.index") }}" class="nav-link {{ request()->is('scheduler/appointment-actions') || request()->is('scheduler/appointment-actions/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-cog nav-icon">

                                </i>
                                {{ trans('scheduler.actions.title_simple') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.settings.create") }}" class="nav-link {{ request()->is('scheduler/settings') || request()->is('scheduler/settings/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-sliders-h nav-icon">

                                </i>
                                {{ trans('scheduler.settings.title_singular') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.clients.index") }}" class="nav-link {{ request()->is('scheduler/clients') || request()->is('scheduler/clients/*') ? 'active' : '' }}">
                                 <i class="fa-fw fas fa-vial nav-icon"></i>

                                {{ trans('scheduler.clients.title') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("scheduler.locks.index") }}" class="nav-link {{ request()->is('scheduler/locks') || request()->is('scheduler/locks/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-ban nav-icon">

                                </i>
                                {{ trans('scheduler.locks.title') }}
                            </a>
                        </li>
                            <li class="nav-item">
                            <a href="{{ route("scheduler.cell-locks.index") }}" class="nav-link {{ request()->is('scheduler/cell-locks') || request()->is('scheduler/cell-locks/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-ban nav-icon">

                                </i>
                                {{ trans('scheduler.cell_locks.title_menu') }}
                            </a>
                        </li>
                            <li class="nav-item">
                                <a href="{{ route("scheduler.supplier-groups.index") }}" class="nav-link {{ request()->is('scheduler/supplier-groups/index')? 'active' : '' }}">
                                    <i class="fa-fw fas fa-user-cog nav-icon">

                                    </i>
                                    {{ trans('scheduler.supplier_groups.title') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("scheduler.docks.index") }}" class="nav-link {{ request()->is('scheduler/docks') || request()->is('scheduler/docks/*') ? 'active' : '' }}">
                                    <i class="fa-fw far fa-hospital nav-icon">

                                    </i>
                                    {{ trans('scheduler.docks.title') }}
                                </a>
                            </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.locations.index") }}" class="nav-link {{ request()->is('scheduler/locations') || request()->is('scheduler/locations/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-cog nav-icon">

                                </i>
                                {{ trans('scheduler.locations.title') }}
                            </a>
                        </li>
                        <li class="nav-item">
                                <a href="{{ route("scheduler.schemes.index") }}" class="nav-link {{ request()->is('scheduler/schemes') || request()->is('scheduler/schemes/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-cog nav-icon">

                                    </i>
                                    {{ trans('scheduler.schemes.title') }}
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{ route("scheduler.sequence.index") }}" class="nav-link {{ request()->is('scheduler/sequence/index')? 'active' : '' }}">
                                    <i class="fa-fw fas fa-cog nav-icon">

                                    </i>
                                    {{ trans('scheduler.sequence.title_singular') }}
                                </a>
                            </li>

                    </ul>
                </li>
            @endcan


            @can('users_manage')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-cogs nav-icon">

                        </i>
                        {{ trans('scheduler.global.activities_title_min') }}
                    </a>
                    <ul class="nav-dropdown-items">

                        {{-- Activity Groups --}}
                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-groups.index") }}" class="nav-link {{ request()->is('activity-groups') || request()->is('activity-groups') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-cog nav-icon">

                                </i>
                                {{ trans('scheduler.activity_groups.title') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activity-actions.index") }}" class="nav-link {{ request()->is('activity-actions') || request()->is('activity-actions') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-cog nav-icon">

                                </i>
                                {{ trans('scheduler.activity_actions.title') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route("scheduler.activities.index") }}" class="nav-link {{ request()->is('activities') || request()->is('activities') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-sliders-h nav-icon">

                                </i>
                                {{ trans('scheduler.activities.title') }}
                            </a>
                        </li>



                    </ul>
                </li>
            @endcan

            @can('users_manage')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        {{ trans('cruds.userManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                     <!--   <li class="nav-item">
                            <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-unlock-alt nav-icon">

                                </i>
                                {{ trans('cruds.permission.title') }}
                            </a>
                        </li>-->
                        <li class="nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-briefcase nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-user nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan






        </ul>

    </nav>
    <span style="padding: 5px 15px; border-top: 1px solid #545454;">{{trans('global.version')}} {{config('app.version')}}</span>
    <button class="sidebar-minimizer brand-minimizer" type="button">

    </button>
</div>
