<?php

return [
    'global'           => [
        'admin_title'       => 'Configuration',
        'user_title'         => 'Scheduler'

    ],
    'appointments'           => [
        'title'                 => 'Appointments',
        'title_singular'        => 'Appointment',
        'reservation'           => 'Reservation',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'nro'                 => 'Number',
            'pallets_qty'        => 'Pallets Qty',
            'pallets_qty_helper' => '',
            'pallets_qty_min'    => 'Pallets',
            'trucks_qty'         => 'Trucks Qty',
            'trucks_qty_helper'  => '',
            'trucks_qty_min'     => 'Trucks',
            'packages_qty'       => 'Package Qty',
            'packages_qty_helper'=> '',
            'packages'           => 'Packages',
            'sku_qty'            => 'SKU Qty',
            'sku_qty_helper'     => '',
            'sku_qty_min'        => 'SKUs',
            'comments'           => 'Comments',
            'comments_helper'    => '',
            'type'               => 'Type',
            'type_helper'        => '',
            'type_placeholder'   => 'Select a Type',
            'action'             => 'Action',
            'action_helper'      => '',
            'action_placeholder' => 'Select an Action',
            'unload_type'        => 'Unload Type',
            'unload_type_helper' => '',
            'unload_type_placeholder' => 'Select an Unload Type',
            'unload_type_min'    => 'Unload',
            'origin'             => 'Origin',
            'origin_helper'      => '',
            'origin_placeholder' => 'Select an Origin',
            'client'             => 'Client',
            'client_placeholder' => 'Select a client',
            'client_helper'      => '',
            'supplier'           => 'Supplier',
            'supplier_placeholder'=> 'Select a supplier',
            'supplier_helper'    => '',
            'purchase_order'     => 'Purchase Order',
            'purchase_order_placeholder'  => 'Select a purchase order',
            'purchase_order_min' => 'PO',
            'purchase_order_helper'=> '',
            'date'               => 'Date',
            'start'              => 'Start',
            'end'                => 'End',
            'dock'               => 'Dock',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
            'date_hour'          => 'Appointment data',
            'required_date'      => 'Required date',
            'required_date_helper'=> '',
            'pick_date'          => 'Pick a date',
            'synchronized_at'    => 'Synchronized'
        ],
    ],
    'types'           => [
        'title'                 => 'Appointments Types',
        'title_singular'        => 'Appointments Type',
        'title_simple'          => 'Types',
        'title_simple_singular' => 'Type',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Name',
            'name_helper'        => '',
            'description'        => 'Description',
            'description_helper' => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'origins'           => [
        'title'                 => 'Appointments Origins',
        'title_singular'        => 'Appointments Origin',
        'title_simple'          => 'Origins',
        'title_simple_singular' => 'Origin',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Name',
            'name_helper'        => '',
            'description'        => 'Description',
            'description_helper' => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'unload_types'           => [
        'title'                 => 'Appointments Unload Types',
        'title_singular'        => 'Appointments Unload Type',
        'title_simple'          => 'Unload Types',
        'title_simple_singular' => 'Unload Type',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Name',
            'name_helper'        => '',
            'description'        => 'Description',
            'description_helper' => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'actions'           => [
        'title'                 => 'Appointments Actions',
        'title_singular'        => 'Appointments Action',
        'title_simple'          => 'Unload Actions',
        'title_simple_singular' => 'Unload Action',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Name',
            'name_helper'        => '',
            'description'        => 'Description',
            'description_helper' => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'locations'           => [
        'title'                 => 'Locations',
        'title_singular'        => 'Location',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Name',
            'name_helper'        => '',
            'description'        => 'Description',
            'description_helper' => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'docks'           => [
        'title'                 => 'Docks',
        'title_singular'        => 'Dock',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Name',
            'name_helper'        => '',
            'location'           => 'Location',
            'location_helper'    => '',
            'location_placeholder' => 'Select a location',
            'description'        => 'Description',
            'description_helper' => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'clients'           => [
        'title'                 => 'Clients',
        'title_singular'        => 'Client',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'name'               => 'Name',
            'name_helper'        => '',
            'token'              => 'Token',
            'token_helper'       => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'suppliers'           => [
        'title'                 => 'Suppliers',
        'title_singular'        => 'Supplier',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'wms_name'           => 'WMS Name',
            'wms_name_helper'    => '',
            'wms_id'             => 'WMS Code',
            'wms_id_helper'      => '',
            'address'            => 'Address',
            'address_helper'     => '',
            'phone'              => 'Phone',
            'phone_helper'       => '',
            'email'              => 'Email',
            'email_helper'       => '',
            'contact'            => 'Contact',
            'contact_helper'     => '',
            'aux1'               => 'Aux 1',
            'aux1_helper'        => '',
            'aux2'               => 'Aux 2',
            'aux2_helper'        => '',
            'aux3'               => 'Aux 3',
            'aux3_helper'        => '',
            'aux4'               => 'Aux 4',
            'aux4_helper'        => '',
            'aux5'               => 'Aux 5',
            'aux5_helper'        => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
        ],
    ],
    'purchase_orders'           => [
        'title'                 => 'Purchase Orders',
        'title_singular'        => 'Purchase Order',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => '',
            'number'             => 'Number',
            'number_helper'      => '',
            'due_date'           => 'Due Date',
            'due_data_helper'    => '',
            'created_at'         => 'Created at',
            'created_at_helper'  => '',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => '',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => '',
            'line_number'        => 'Line Number',
            'line_number_helper' => '',
            'part_code'          => 'Part Code',
            'part_code_helper'   => '',
            'part_description'   => 'Part Description',
            'part_description_helper'=> '',
            'quantity'           => 'Quantity'

        ],
    ],
    'settings'           => [
        'title'                 => 'Settings',
        'title_singular'        => 'Setting',
        'fields'         => [
            'init_hour'        => 'Init Hour',
            'init_hour_helper' => '',
            'end_hour'          => 'End Hour',
            'end_hour_helper'   => '',
            'appointment_init_minutes_size'   => 'Default time spot',
            'appointment_init_minutes_size_helper'=> '',

        ],
    ]
];
