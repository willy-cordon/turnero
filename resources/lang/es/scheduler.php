<?php

return [
    'global'           => [
        'admin_title'       => 'Configuración',
        'user_title'         => 'Turnero',
        'activities_user_title' => 'Act. Vigilancia',
        'admin_title_min'       => 'Conf. Turnos',
        'activities_title_min'       => 'Conf. Actividades',
        'activities_title_eDiary'       => 'Act. E-Diary',
        'activities_title_appointment'       => 'Act. Turno',

    ],
    'appointments'           => [
        'title'                 => 'Turnos',
        'title_singular'        => 'Turno',
        'reservation'           => 'Reserva',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'nro'                 => 'Nro',
            'pallets_qty'        => 'Pallets Cant.',
            'pallets_qty_helper' => '',
            'pallets_qty_min'    => 'Pallets',
            'trucks_qty'         => 'Camiones Cant.',
            'trucks_qty_helper'  => '',
            'trucks_qty_min'     => 'Camiones',
            'packages_qty'       => 'Bultos Cant.',
            'packages_qty_helper'=> '',
            'packages_qty_min'   => 'Bultos',
            'sku_qty'            => 'SKU Cant.',
            'sku_qty_helper'     => '',
            'sku_qty_min'        => 'SKUs',
            'comments'           => 'Comentarios',
            'comments_helper'    => '',
            'type'               => 'Tipo de visita',
            'type_helper'        => '',
            'type_placeholder'   => 'Seleccione un tipo',
            'transportation'     => 'Transporte',
            'transportation_helper'        => '',
            'transportation_placeholder'   => 'Seleccione un método de transporte',
            'next_step'     => 'Próxima Acción',
            'next_step_helper'        => '',
            'next_step_placeholder'   => 'Seleccione la próxima acción',
            'action'             => 'Estado',
            'action_helper'      => '',
            'action_placeholder' => 'Seleccione un estado',
            'unload_type'        => 'Tipo de descarga',
            'unload_type_helper' => '',
            'unload_type_placeholder' => 'Seleccione un tipo de descarga',
            'unload_type_min'    => 'Descarga',
            'origin'             => 'Procedencia',
            'origin_helper'      => '',
            'origin_placeholder' => 'Seleccione una procedencia',
            'client'             => 'Estudio',
            'client_placeholder' => 'Seleccione un estudio',
            'client_helper'      => '',
            'supplier'           => 'Voluntario',
            'supplier_placeholder'=> 'Seleccione el voluntario',
            'supplier_helper'    => '',
            'supplier_name'      => 'Nombre del Voluntario',
            'supplier_dni'       => 'DNI del Voluntario',
            'supplier_date'       => 'Fecha de nacimiento',
            'supplier_age'       => 'Edad',
            'supplier_gender'    => 'Género',
            'supplier_phone'     => 'Telefono - celular',
            'supplier_aux2'      => 'Telefono (contacto emergencia)',
            'supplier_email'     => 'EMAIL del Voluntario',
            'supplier_address'   => 'Dirección del Voluntario',
            'supplier_validate_address' => 'Dirección validada',
            'purchase_order'     => 'Orden de compra',
            'purchase_order_placeholder'  => 'Seleccione las ordenes de compra',
            'purchase_order_min' => 'OC',
            'purchase_order_helper'=> '',
            'need_assistance'    => 'Asistencia en sitio',
            'need_assistance_min'    => 'Asistencia',
            'date'               => 'Fecha',
            'hour'               => 'Hora',
            'start'              => 'Hora Inicio',
            'end'                => 'Hora Fin',
            'dock'               => 'Circuito',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
            'date_hour'          => 'Fecha y hora',
            'required_date'      => 'Fecha Próxima Visita',
            'required_date_helper'=> '',
            'pick_date'          => 'Agendar',
            'synchronized_at'    => 'Sincronizado',
            'created_by'         => 'Responsable',
            'original_created_by'         => 'Creado por',
            'updated_by'         => 'Actualizado por',
            'search'             => 'Buscar por',
            'scheme'             => 'Esquema',
            'supplier_comorbidity' => 'Comorbilidades'

        ],
    ],
    'locks'           => [
        'title'                 => 'Bloqueo de turnos',
        'title_singular'        => 'Bloqueo',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'lock_date'          => 'Fecha',
            'lock_helper'        => '',
            'available_appointments'        => 'Turnos disponibles',
            'available_appointments_helper' => '',
            'location'        => 'Tipo de turno',
            'location_helper' => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
        ],
    ],
    'types'           => [
        'title'                 => 'Tipos de visitas',
        'title_singular'        => 'Tipo de visita',
        'title_simple'          => 'Tipos',
        'title_simple_singular' => 'Tipo',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Nombre',
            'name_helper'        => '',
            'description'        => 'Descripción',
            'description_helper' => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
        ],
    ],
    'origins'           => [
        'title'                 => 'Orígenes de turno',
        'title_singular'        => 'Orígen de turno',
        'title_simple'          => 'Orígenes',
        'title_simple_singular' => 'Orígen',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Nombre',
            'name_helper'        => '',
            'description'        => 'Descripción',
            'description_helper' => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
        ],
    ],
    'unload_types'           => [
        'title'                 => 'Tipos de descarga de turno',
        'title_singular'        => 'Tipo de descarga de turno',
        'title_simple'          => 'Tipos de descarga',
        'title_simple_singular' => 'Tipo de descarga',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Nombre',
            'name_helper'        => '',
            'description'        => 'Descripción',
            'description_helper' => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
        ],
    ],
    'actions'           => [
        'title'                 => 'Estados',
        'title_singular'        => 'Estado',
        'title_simple'          => 'Estados',
        'title_simple_singular' => 'Estado',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Nombre',
            'name_helper'        => '',
            'description'        => 'Descripción',
            'description_helper' => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
        ],
    ],
    'locations'           => [
        'title'                 => 'Tipos de turnos',
        'title_singular'        => 'Tipo de turno',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Nombre',
            'name_helper'        => '',
            'description'        => 'Descripción',
            'description_helper' => '',
            'prev_action'        => 'Estado Anterior',
            'prev_action_placeholder' => 'Seleccione el estado anterior',
            'prev_action_helper' => '',
            'prev_location'      => 'Turno Anterior - Rango',
            'prev_location_placeholder' => 'Seleccione el tipo de turno',
            'prev_location_helper' => '',
            'prev_days_from'       => 'Dias - Desde',
            'prev_days_from_helper' => '',
            'prev_days_to'        => 'Dias - Hasta',
            'prev_days_to_helper' => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
            'unique_appointment' => 'Turno único',
            'scheme'            => 'Esquema',
            'scheme_helper'     => '',
            'scheme_place_holder' => 'Seleccione esquema',
            'bcc_emails_create' => 'Mails para notificar en la creación',
            'bcc_emails_create_helper' => 'Ingrese los emails separados por una coma',
            'bcc_emails_canceled' => 'Mails para notificar en la cancelación',
            'bcc_emails_canceled_helper' => 'Ingrese los emails separados por una coma',
            'enable_past_days' => 'Turno hacia atras',
            'prev_location_workflow' => 'Turno Anterior - Seguimiento',
            'prev_location_workflow_placeholder' => 'Seleccione el tipo de turno',
            'prev_location_workflow_helper' => '',
            'sequence' => 'Secuencia',
            'sequence_helper' => '',




        ],
    ],
    'docks'           => [
        'title'                 => 'Circuitos',
        'title_singular'        => 'Circuito',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Nombre',
            'name_helper'        => '',
            'location'           => 'Sucursal',
            'location_helper'    => '',
            'location_placeholder' => 'Seleccione una sucursal',
            'description'        => 'Descripción',
            'description_helper' => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
        ],
    ],
    'clients'           => [
        'title'                 => 'Estudios',
        'title_singular'        => 'Estudio',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Nombre',
            'name_helper'        => '',
            'token'              => 'Token',
            'token_helper'       => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
        ],
    ],
    'suppliers'           => [
        'title'                 => 'Voluntarios',
        'title_singular'        => 'Voluntario',
        'list'              => 'Listado',
        'workflow'          => 'Seguimiento',
        'title-workflow'                 => 'Seguimiento de voluntarios',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'wms_name'           => 'Apellido y Nombres',
            'wms_name_helper'    => '',
            'wms_name_min'       => 'Nombre',
            'wms_id'             => 'DNI',
            'status_intervened'  => 'Estado de intervención',
            'status_supplier'    => 'Estado voluntario',
            'wms_id_helper'      => 'Solo Números',
            'address'            => 'Dirección (lugar de retiro)',
            'address_helper'     => 'Calle Número',
            'phone'              => 'Teléfono - Celular',
            'phone_min'          => 'Celular',
            'phone_helper'       => 'Cod. Area + Nro',
            'phone_placeholder'  => 'Ej. 1199999999',
            'email'              => 'Email',
            'email_helper'       => '',
            'contact'            => 'Teléfono - Fijo',
            'contact_min'        => 'Tel. Fijo',
            'contact_helper'     => 'Cod. Area + Nro',
            'contact_placeholder'=> 'Ej. 02099999999',
            'aux1'               => 'Apellido y Nombres (Contacto de emergencia)',
            'aux1_min'           => 'Nombre Contacto',
            'aux1_helper'        => '',
            'aux2'               => 'Teléfono (Contacto de emergencia)',
            'aux2_min'           => 'Tel. Contacto',
            'aux2_helper'     => 'Cod. Area + Nro',
            'aux2_placeholder'=> 'Ej. 02099999999',
            'aux3'               => 'Comentarios',
            'aux3_helper'        => '',
            'aux4'               => 'Localidad',
            'aux4_helper'        => '',
            'aux4_placeholder'   => 'Seleccione a localidad',
            'aux5'               => 'Piso/Depto.',
            'aux5_helper'        => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
            'created_by'         => 'Creado por',
            'wms_date'           => 'Fecha de nacimiento',
            'wms_gender'         => 'Género',
            'wms_age'            => 'Edad',
            'validate_address'  => 'Validar dirección',
            'validate_address_helper'  => 'Ingrese una dirección y use el autocompletado',
            'address_validate'  => 'Dirección validada',
            'responsible'       => 'Responsable',
            'recruiter'         => 'Reclutador',
            'owner'             => 'Dueño del voluntario',
            'current_visit'     => 'Visita Actual',
            'current_visit_type'=> 'Tipo',
            'current_status'     => 'Estado',
            'current_date'       => 'Fecha',
            'next_visit_type'    => 'Próxima acción',
            'next_visit_range'   => 'Rango',
            'tracing'            => 'Seguimiento',
            'scheme'             => 'Esquema',
            'scheme_helper'      => '',
            'supplier_group'             => 'Grupo de voluntario',
            'supplier_group_helper'      => '',
            'name' => 'Nombre',
            'name_helper' => '',
            'lastname' => 'Apellido',
            'lastname_helper' => '',
            'comorbidity' => 'Comorbilidades'

        ],
    ],
    'purchase_orders'           => [
        'title'                 => 'Ordenes',
        'title_singular'        => 'Orden',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'number'             => 'Número',
            'number_helper'      => '',
            'due_date'           => 'Vencimiento',
            'due_data_helper'    => '',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
            'line_number'        => 'Número de línea',
            'line_number_helper' => '',
            'part_code'          => 'Parte Código',
            'part_code_helper'   => '',
            'part_description'   => 'Parte Descripción',
            'part_description_helper'=> '',
            'quantity'           => 'Cantidad',
            'quantity_helper'    => ''

        ],
    ],
    'settings'           => [
        'title'                 => 'Configuraciones generales',
        'title_singular'        => 'Generales',
        'fields'         => [
            'init_hour'        => 'Hora de Inicio',
            'init_hour_helper' => '',
            'end_hour'          => 'Hora de Fin',
            'end_hour_helper'   => '',
            'appointment_init_minutes_size'   => 'Tiempo por defecto del turno',
            'appointment_init_minutes_size_helper'=> '',

        ],
    ],
    'activity_groups'    =>[
        'title'            =>'Grupos',
        'title_singular'   =>'Grupo de actividad',
        'droptitle'        =>'Actividades',
        'fields'         =>[
            'name'         => 'Nombre',
            'description'  => 'Descripción',
            'location'     => 'Tipo de turno',
            'type'          => 'Generación de actividades del grupo',
            'type_helper'   => '',
            'type_min'          => 'Tipo',
            'group_type'      => 'Tipo de grupo'
        ]
    ],
    'activity_actions' => [
        'title'   =>'Acciones',
        'title_singular' => 'Acción',
        'fields'  =>[
            'name'    => 'Nombre',
            'description' => 'Descripción',
            'status'       => 'Estado al que pasa la actividad',
            'activity_status_triggered_helper' => '',
            'activity'     => 'Actividad automática generada',
            'activity_fired_helper' => ''

        ]
    ],
    'activities' =>[
        'title'       => 'Actividades',
        'title_singular' => 'Actividad',
        'fields'  =>[
            'name'         => 'Nombre',
            'description'  => 'Descripción',
            'question_name' => 'Nombre pregunta',
            'days_from_appointment' => 'Dias desp. del turno',
            'actions'       => 'Acciones',
            'group'         => 'Grupo',
            'group_type'         => 'Tipo de Grupo',
            'created_activity' => 'Cuando se crea la actividad',
        ]
    ],
    'activity_instances'           => [
        'title'                 => 'Actividades',
        'title_singular'        => 'Actividad',
        'title_all_min'         =>  'Todas',
        'title_all'         =>  'Todas las actividades',
        'title_pending_min'         =>  'Pendientes',
        'title_pending'         =>  'Actividades pendientes con fecha menor o igual a hoy',
        'required'              => 'Complete una opción',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'supplier_name'      => 'Voluntario',
            'supplier_dni'       => 'DNI',
            'supplier_email'     => 'E-Mail',
            'supplier_phone'     => 'Teléfono',
            'activity_group_name'=> 'Grupo de actividades',
            'activity_name'      => 'Actividad',
            'activity_question'  => 'Pregunta',
            'activity_answer'    => 'Respuesta',
            'activity_action'    => 'Acción',
            'status'             => 'Estado',
            'status_placeholder' => 'Seleccione un estado',
            'action'             => 'Acción',
            'action_placeholder' => 'Seleccione una acción',
            'answer_placeholder' => 'Seleccione una opción',
            'date'               => 'Fecha',
            'date_helper'        => '',
            'appointment_nro'    => 'Turno',
            'appointment'    => 'Turno',
            'appointment_helper'    => '',
            'appointment_placeholder'    => 'Seleccione el turno',
            'supplier'    => 'Voluntario',
            'supplier_helper'    => '',
            'supplier_placeholder'    => 'Seleccione el voluntario',
            'activity'    => 'Actividad',
            'activity_helper'    => '',
            'activity_placeholder'    => 'Seleccione la actividad',
            'created_at'         => 'Creado',
            'created_at_helper'  => '',
            'updated_at'         => 'Modificado',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Borrado',
            'deleted_at_helper'  => '',
            'created_by'         => 'Responsable',
            'updated_by'         => 'Actualizado por',
            'search'             => 'Buscar por',
            'appointment_status' => 'Estado del turno',
            'intervened'         => 'Intervenido',
            'supplier_group'     => 'Grupo de voluntario'
        ],
    ],

    'activity_migrations' => [
        'menu_migration' => 'Actividades',
        'title_migration_appointment' => 'Migración de actividades por turno',
        'title_migration_user'  => 'Migracion de actividades por Usuario',
        'buttonMigration'     => 'Migrar',
        'fields' => [
            'appointment_id' => 'Numero de turno',
            'users_migration' => 'Asignar al usuario',
            'userFrom'        => 'Migrar de:',
            'userTo'          =>'Migrar a:'

        ]

    ],

    'transportation_vouchers' =>[
        'title' => 'Vouchers',
        'title_singular' => 'Voucher',
        'title_print_original' => 'Original - Voucher Remis ',
        'title_print_duplicate' => 'Duplicado - Voucher Remis ',
        'fields' => [
            'address' => 'Dirección',
            'id'      => 'ID',
            'date'    => 'Fecha',
            'dateTime'    => 'Fecha y hora',
            'supplier' => 'Voluntario',
            'dni'      => 'DNI',
            'license_plate' => 'Patente'


        ]
    ],

    'cell_locks' => [
        'title_menu' => 'Bloqueo individual',
        'title' => 'Bloqueo de turnos individuales',
        'title_singular' => 'Bloqueo individual',
        'fields' => [
            'lock_type' => ' Tipo de bloqueo',
            'lock_date' => ' Fecha',
            'hour'      => ' Hora',
            'dock_name' => ' Circuito',
            'location'  => 'Tipo de visita'
        ]
    ],



    'supplier_migrations' => [
        'title' => 'Voluntarios',
        'supplier_migration'=> 'Migración de Voluntarios',
        'fields' => [
            'supplier_id' => 'Voluntario',
            'user_id'    => 'Turnista',
            'supplier_helper' => '',
            'user_helper' => ''
        ]
    ],

    'supplier_intervention_log' => [
        'title' => 'Registro intervención de voluntario',
        'title_or' => 'Registro intervención de voluntarios',
        'title_singular' => 'Registro intervención',
        'fields' => [
            'reasons_placeHolder' => 'Seleccione una razón',
            'reasons_intervention' => 'Razón de intervención',
            'description' => 'Descripción',
            'description_helper' => '',
            'supplierInterventionId' => 'Id',
            'supplier_name' => 'Voluntario',
            'supplier_dni'  => 'Dni',
            'created_by'    => 'Creado por',
            'updated_by'    => 'Actualizado por',
            'date_created'  => 'Fecha creación',
            'date_updated'  => 'Fecha actualización'


        ]


    ],

    'notifications' => [
        'title' => 'Notificaciones',
        'title_singular' => 'Notificacion',
        'fields' => [
            'status' => 'Estado',
            'date'   => 'Fecha',
            'email_subject' => 'Asunto Email',
            'supplier_name' => 'Voluntario',
            'supplier_dni'  => 'Dni',
            'type'          => 'Tipo',
            'notified_to'   => 'Usuario Notificado'
            ]
        ],

    'migrations_menu' => [
        'title' => 'Migraciones',
    ],
    'intervention_migration' => [
        'title'          => 'Migración de voluntarios intervenidos',
        'title_menu' => 'Intervenciones',
        'buttonMigration' => 'Migrar',
        'fields' => [
            'supplier_intervention' => 'Voluntario intervenido',
            'supplier_intervention_helper' => '',
            'doctor'                => 'Médico',
            'doctor_helper'         => ''

        ]


    ],
    'appointment_change_log' => [
        'title' => 'Registro de turnos',
        'title_menu' => 'Registro turnos',
        'fields' => [
            'appointment_id'        => 'ID Turno',
            'appointment_date'      => 'Fecha y hora',
            'appointment_status'    => 'Estado',
            'change_log_type'       => 'Dato Actualizado',
            'supplier_name'         => 'Voluntario',
            'supplier_dni'          => 'Dni',
            'supplier_phone'        => 'Teléfono',
            'supplier_address'      => 'Dirección',
            'supplier_validate_address'      => 'Dirección Validada',
            'user_update'           => 'Actualizado por',
            'update_date'           => 'Fécha de modificación'

        ]
    ],
    'activity_instance_change_date' => [
        'title' => 'Cambio de día',
        'send'=> 'Enviar',
        'fields' => [
            'supplier'      =>'Voluntario',
            'activity_type' => 'Tipo de actividad',
            'day'           =>'Día',
            'day_helper'    => '',
            'activity_type_helper' => '',
            'supplier_helper' => ''
        ]
    ],

    'activity_instances_filter_global' => [
        'All' => 'Todas',
        'Pending' => 'Pendientes',
        'Expired' => 'Expiradas',
        'InProgress' => 'En gestión',
        'areYouSure' => 'Está seguro de realizar la confirmación?',
        'masive'     => 'Confirmación masiva'
    ],

    'entities_export' => [
        'title' => 'Exportaciones',

    ],
    'schemes' => [
        'title' => 'Esquemas',
        'title_singular' => 'Esquema',
        'fields'=> [
            'name' => 'Nombre',
            'name_helper' => '',
            'description' => 'Descripción',
            'description_helper'=> '',
        ]
    ],
    'supplier_groups' => [
        'title' => 'Grupo de voluntarios',
        'title_singular' => 'Grupo de voluntario',
        'fields' => [
            'name' => 'Nombre',
            'name_helper' => '',
            'description' => 'Descripción',
            'description_helper' => '',

        ]

    ],
    'sequence' => [
        'title' => 'Secuencias',
        'title_singular' => 'Secuencia',
        'fields'=> [
            'name' => 'Nombre',
            'name_helper' => '',
            'description' => 'Descripción',
            'description_helper' => '',
            'show_in_workflow' => 'Mostrar en Seguimiento'

        ]
    ],

    'home' => [
        'title' => 'Home',
        'fields' => [
            'no_sequence' => 'Sin secuencia'
        ]
    ]
];
