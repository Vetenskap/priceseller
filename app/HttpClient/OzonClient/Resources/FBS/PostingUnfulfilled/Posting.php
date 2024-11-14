<?php

namespace App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled;

use App\Models\OzonMarket;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Posting
{
    const STATUSES = [
        ["value" => "acceptance_in_progress", "label" => "идёт приёмка"],
        ["value" => "awaiting_approve", "label" => "ожидает подтверждения"],
        ["value" => "awaiting_packaging", "label" => "ожидает упаковки"],
        ["value" => "awaiting_registration", "label" => "ожидает регистрации"],
        ["value" => "awaiting_deliver", "label" => "ожидает отгрузки"],
        ["value" => "arbitration", "label" => "арбитраж"],
        ["value" => "client_arbitration", "label" => "клиентский арбитраж доставки"],
        ["value" => "delivering", "label" => "доставляется"],
        ["value" => "driver_pickup", "label" => "у водителя"],
        ["value" => "not_accepted", "label" => "не принят на сортировочном центре"],
    ];

    const ATTRIBUTES = [
        'Основная информация' => [
            ['name' => 'available_actions', 'label' => 'Доступные действия и информация об отправлении'],
            ['name' => 'delivering_date', 'label' => 'Дата передачи отправления в доставку'],
            ['name' => 'in_process_at', 'label' => 'Дата и время начала обработки отправления'],
            ['name' => 'is_express', 'label' => 'Использовалась ли быстрая доставка Ozon Express'],
            ['name' => 'is_multibox', 'label' => 'Признак, что в отправлении есть многокоробочный товар и нужно передать количество коробок для него'],
            ['name' => 'multi_box_qty', 'label' => 'Количество коробок, в которые упакован товар'],
            ['name' => 'order_id', 'label' => 'Идентификатор заказа, к которому относится отправление'],
            ['name' => 'order_number', 'label' => 'Номер заказа, к которому относится отправление'],
            ['name' => 'parent_posting_number', 'label' => 'Номер родительского отправления, в результате разделения которого появилось текущее'],
            ['name' => 'posting_number', 'label' => 'Номер отправления'],
            ['name' => 'prr_option', 'label' => 'Код услуги погрузочно-разгрузочных работ'],
            ['name' => 'shipment_date', 'label' => 'Дата и время, до которой необходимо собрать отправление'],
            ['name' => 'status', 'label' => 'Статус отправления'],
            ['name' => 'substatus', 'label' => 'Подстатус отправления'],
            ['name' => 'tpl_integration_type', 'label' => 'Тип интеграции со службой доставки'],
            ['name' => 'tracking_number', 'label' => 'Трек-номер отправления'],
        ],
        'Контактные данные получателя' => [
            ['name' => 'addressee_name', 'label' => 'Имя покупателя'],
            ['name' => 'addressee_phone', 'label' => 'Контактный телефон'],
        ],
        'Данные аналитики' => [
            ['name' => 'analytics_data_city', 'label' => 'Город доставки'],
            ['name' => 'analytics_data_delivery_date_begin', 'label' => 'Дата и время начала доставки'],
            ['name' => 'analytics_data_delivery_date_end', 'label' => 'Дата и время конца доставки'],
            ['name' => 'analytics_data_delivery_type', 'label' => 'Способ доставки'],
            ['name' => 'analytics_data_is_legal', 'label' => 'Признак, что получатель юридическое лицо'],
            ['name' => 'analytics_data_is_premium', 'label' => 'Наличие подписки Premium'],
            ['name' => 'analytics_data_payment_type_group_name', 'label' => 'Способ оплаты'],
            ['name' => 'analytics_data_region', 'label' => 'Регион доставки'],
            ['name' => 'analytics_data_tpl_provider', 'label' => 'Служба доставки'],
            ['name' => 'analytics_data_tpl_provider_id', 'label' => 'Идентификатор службы доставки'],
            ['name' => 'analytics_data_warehouse', 'label' => 'Название склада отправки заказа'],
            ['name' => 'analytics_data_warehouse_id', 'label' => 'Идентификатор склада'],
        ],
        'Штрихкоды отправления' => [
            ['name' => 'barcodes_lower_barcode', 'label' => 'Нижний штрихкод на маркировке отправления'],
            ['name' => 'barcodes_upper_barcode', 'label' => 'Верхний штрихкод на маркировке отправления'],
        ],
        'Информация об отмене' => [
            ['name' => 'cancellation_affect_cancellation_rating', 'label' => 'Влияет ли отмена на рейтинг продавца'],
            ['name' => 'cancellation_cancel_reason', 'label' => 'Причина отмены'],
            ['name' => 'cancellation_cancel_reason_id', 'label' => 'Идентификатор причины отмены отправления'],
            ['name' => 'cancellation_cancellation_initiator', 'label' => 'Инициатор отмены'],
            ['name' => 'cancellation_cancellation_type', 'label' => 'Тип отмены отправления'],
            ['name' => 'cancellation_cancelled_after_ship', 'label' => 'Произошла ли отмена после сборки отправления'],
        ],
        'Данные о покупателе' => [
            'Информация об адресе доставки' => [
                ['name' => 'customer_address_address_tail', 'label' => 'Адрес в текстовом формате'],
                ['name' => 'customer_address_city', 'label' => 'Город доставки'],
                ['name' => 'customer_address_comment', 'label' => 'Комментарий к заказу'],
                ['name' => 'customer_address_country', 'label' => 'Страна доставки'],
                ['name' => 'customer_address_district', 'label' => 'Район доставки'],
                ['name' => 'customer_address_latitude', 'label' => 'Широта'],
                ['name' => 'customer_address_longitude', 'label' => 'Долгота'],
                ['name' => 'customer_address_provider_pvz_code', 'label' => 'Код пункта выдачи заказов 3PL провайдера'],
                ['name' => 'customer_address_pvz_code', 'label' => 'Код пункта выдачи заказов'],
                ['name' => 'customer_address_region', 'label' => 'Регион доставки'],
                ['name' => 'customer_address_zip_code', 'label' => 'Почтовый индекс получателя'],
            ],
            ['name' => 'customer_customer_id', 'label' => 'Идентификатор покупателя'],
            ['name' => 'customer_name', 'label' => 'Имя покупателя'],
            ['name' => 'customer_phone', 'label' => 'Контактный телефон'],
        ],
        'Метод доставки' => [
            ['name' => 'delivery_method_id', 'label' => 'Идентификатор способа доставки'],
            ['name' => 'delivery_method_name', 'label' => 'Название способа доставки'],
            ['name' => 'delivery_method_tpl_provider', 'label' => 'Служба доставки'],
            ['name' => 'delivery_method_tpl_provider_id', 'label' => 'Идентификатор службы доставки'],
            ['name' => 'delivery_method_warehouse', 'label' => 'Название склада'],
            ['name' => 'delivery_method_warehouse_id', 'label' => 'Идентификатор склада'],
        ],
        'Данные о стоимости товара, размере скидки, выплате и комиссии' => [
            ['name' => 'financial_data_cluster_from', 'label' => 'Код региона, откуда отправляется заказ'],
            ['name' => 'financial_data_cluster_to', 'label' => 'Код региона, куда доставляется заказ'],
            'Услуги' => [
                ['name' => 'financial_data_posting_services_marketplace_service_item_deliv_to_customer', 'label' => 'Последняя миля'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_direct_flow_trans', 'label' => 'Магистраль'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_dropoff_ff', 'label' => 'Обработка отправления на фулфилмент складе (ФФ)'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_dropoff_pvz', 'label' => 'Обработка отправления в ПВЗ'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_dropoff_sc', 'label' => 'Обработка отправления в сортировочном центре.'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_fulfillment', 'label' => 'Сборка заказа'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_pickup', 'label' => 'Выезд транспортного средства по адресу продавца для забора отправлений (Pick-up)'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_return_after_deliv_to_customer', 'label' => 'Обработка возврата'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_return_flow_trans', 'label' => 'Обратная магистраль'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_return_not_deliv_to_customer', 'label' => 'Обработка отмен'],
                ['name' => 'financial_data_posting_services_marketplace_service_item_return_part_goods_customer', 'label' => 'Обработка невыкупа'],
            ]
        ],
        'Cписок продуктов, для которых нужно передать страну-изготовителя' => [
            ['name' => 'requirements_products_requiring_gtd', 'label' => 'Список идентификаторов товаров (SKU), для которых нужно передать номера таможенной декларации (ГТД)'],
            ['name' => 'requirements_products_requiring_country', 'label' => 'Список идентификаторов товаров (SKU), для которых нужно передать информацию о стране-изготовителе'],
            ['name' => 'requirements_products_requiring_mandatory_mark', 'label' => 'Список идентификаторов товаров (SKU), для которых нужно передать маркировку «Честный ЗНАК»'],
            ['name' => 'requirements_products_requiring_jw_uin', 'label' => 'Список товаров, для которых нужно передать уникальный идентификационный номер (УИН) ювелирного изделия'],
            ['name' => 'requirements_products_requiring_rnpt', 'label' => 'Список идентификаторов товаров (SKU), для которых нужно передать регистрационный номер партии товара (РНПТ)'],
        ]
    ];

    // Основная информация
    /* Доступные действия и информация об отправлении */
    protected $available_actions;

    /* Дата передачи отправления в доставку */
    protected $delivering_date;

    /* Дата и время начала обработки отправления */
    protected $in_process_at;

    /* Использовалась ли быстрая доставка Ozon Express */
    protected $is_express;

    /* Признак, что в отправлении есть многокоробочный товар и нужно передать количество коробок для него */
    protected $is_multibox;

    /* Количество коробок, в которые упакован товар */
    protected $multi_box_qty;

    /* Идентификатор заказа, к которому относится отправление */
    protected $order_id;

    /* Номер заказа, к которому относится отправление */
    protected $order_number;

    /* Номер родительского отправления, в результате разделения которого появилось текущее */
    protected $parent_posting_number;

    /* Номер отправления */
    protected $posting_number;

    /* Код услуги погрузочно-разгрузочных работ */
    protected $prr_option;

    /* Дата и время, до которой необходимо собрать отправление */
    protected $shipment_date;

    /* Статус отправления */
    protected $status;

    /* Подстатус отправления */
    protected $substatus;

    /* Тип интеграции со службой доставки */
    protected $tpl_integration_type;

    /* Трек-номер отправления */
    protected $tracking_number;

    // Контактные данные получателя
    /* Имя покупателя */
    protected $addressee_name;

    /* Контактный телефон */
    protected $addressee_phone;

    // Данные аналитики
    /* Город доставки */
    protected $analytics_data_city;

    /* Дата и время начала доставки */
    protected $analytics_data_delivery_date_begin;

    /* Дата и время конца доставки */
    protected $analytics_data_delivery_date_end;

    /* Способ доставки */
    protected $analytics_data_delivery_type;

    /* Признак, что получатель юридическое лицо */
    protected $analytics_data_is_legal;

    /* Наличие подписки Premium */
    protected $analytics_data_is_premium;

    /* Способ оплаты */
    protected $analytics_data_payment_type_group_name;

    /* Регион доставки */
    protected $analytics_data_region;

    /* Служба доставки */
    protected $analytics_data_tpl_provider;

    /* Идентификатор службы доставки */
    protected $analytics_data_tpl_provider_id;

    /* Название склада отправки заказа */
    protected $analytics_data_warehouse;

    /* Идентификатор склада */
    protected $analytics_data_warehouse_id;

    // Штрихкоды отправления
    /* Нижний штрихкод на маркировке отправления */
    protected $barcodes_lower_barcode;

    /* Верхний штрихкод на маркировке отправления */
    protected $barcodes_upper_barcode;

    // Информация об отмене
    /* Влияет ли отмена на рейтинг продавца */
    protected $cancellation_affect_cancellation_rating;

    /* Причина отмены */
    protected $cancellation_cancel_reason;

    /* Идентификатор причины отмены отправления */
    protected $cancellation_cancel_reason_id;

    /* Инициатор отмены */
    protected $cancellation_cancellation_initiator;

    /* Тип отмены отправления */
    protected $cancellation_cancellation_type;

    /* Произошла ли отмена после сборки отправления */
    protected $cancellation_cancelled_after_ship;

    // Данные о покупателе" => "Информация об адресе доставки
    /* Адрес в текстовом формате */
    protected $customer_address_address_tail;

    /* Город доставки */
    protected $customer_address_city;

    /* Комментарий к заказу */
    protected $customer_address_comment;

    /* Страна доставки */
    protected $customer_address_country;

    /* Район доставки */
    protected $customer_address_district;

    /* Широта */
    protected $customer_address_latitude;

    /* Долгота */
    protected $customer_address_longitude;

    /* Код пункта выдачи заказов 3PL провайдера */
    protected $customer_address_provider_pvz_code;
    /* Код пункта выдачи заказов */
    protected $customer_address_pvz_code;

    /* Регион доставки */
    protected $customer_address_region;

    /* Почтовый индекс получателя */
    protected $customer_address_zip_code;

    /* Идентификатор покупателя */
    protected $customer_customer_id;

    /* Имя покупателя */
    protected $customer_name;

    /* Контактный телефон */
    protected $customer_phone;

    // Метод доставки
    /* Идентификатор способа доставки */
    protected $delivery_method_id;

    /* Название способа доставки */
    protected $delivery_method_name;

    /* Служба доставки */
    protected $delivery_method_tpl_provider;

    /* Идентификатор службы доставки */
    protected $delivery_method_tpl_provider_id;

    /* Название склада */
    protected $delivery_method_warehouse;

    /* Идентификатор склада */
    protected $delivery_method_warehouse_id;

    // Данные о стоимости товара, размере скидки, выплате и комиссии" => "Услуги
    /* Код региона, откуда отправляется заказ */
    protected $financial_data_cluster_from;

    /* Код региона, куда доставляется заказ */
    protected $financial_data_cluster_to;

    /* Последняя миля */
    protected $financial_data_posting_services_marketplace_service_item_deliv_to_customer;

    /* Магистраль */
    protected $financial_data_posting_services_marketplace_service_item_direct_flow_trans;

    /* Обработка отправления на фулфилмент складе (ФФ) */
    protected $financial_data_posting_services_marketplace_service_item_dropoff_ff;

    /* Обработка отправления в ПВЗ */
    protected $financial_data_posting_services_marketplace_service_item_dropoff_pvz;

    /* Обработка отправления в сортировочном центре */
    protected $financial_data_posting_services_marketplace_service_item_dropoff_sc;

    /* Сборка заказа */
    protected $financial_data_posting_services_marketplace_service_item_fulfillment;

    /* Выезд транспортного средства по адресу продавца для забора отправлений (Pick-up) */
    protected $financial_data_posting_services_marketplace_service_item_pickup;

    /* Обработка возврата */
    protected $financial_data_posting_services_marketplace_service_item_return_after_deliv_to_customer;

    /* Обратная магистраль */
    protected $financial_data_posting_services_marketplace_service_item_return_flow_trans;

    /* Обработка отмен */
    protected $financial_data_posting_services_marketplace_service_item_return_not_deliv_to_customer;

    /* Обработка невыкупа */
    protected $financial_data_posting_services_marketplace_service_item_return_part_goods_customer;

    // Cписок продуктов, для которых нужно передать страну-изготовителя, номер грузовой таможенной декларации (ГТД), РНПТ или маркировку «Честный ЗНАК»
    /* Список идентификаторов товаров (SKU), для которых нужно передать номера таможенной декларации (ГТД) */
    protected $requirements_products_requiring_gtd;

    /* Список идентификаторов товаров (SKU), для которых нужно передать информацию о стране-изготовителе */
    protected $requirements_products_requiring_country;

    /* Список идентификаторов товаров (SKU), для которых нужно передать маркировку «Честный ЗНАК» */
    protected $requirements_products_requiring_mandatory_mark;

    /* Список товаров, для которых нужно передать уникальный идентификационный номер (УИН) ювелирного изделия */
    protected $requirements_products_requiring_jw_uin;

    /* Список идентификаторов товаров (SKU), для которых нужно передать регистрационный номер партии товара (РНПТ) */
    protected $requirements_products_requiring_rnpt;

    protected $products;


    public function __construct(Collection $posting)
    {
        // Основная информация
        $this->available_actions = $posting->get('available_actions');
        $this->delivering_date = $posting->get('delivering_date');
        $this->in_process_at = $posting->get('in_process_at');
        $this->is_express = $posting->get('is_express');
        $this->is_multibox = $posting->get('is_multibox');
        $this->multi_box_qty = $posting->get('multi_box_qty');
        $this->order_id = $posting->get('order_id');
        $this->order_number = $posting->get('order_number');
        $this->parent_posting_number = $posting->get('parent_posting_number');
        $this->posting_number = $posting->get('posting_number');
        $this->prr_option = $posting->get('prr_option');
        $this->shipment_date = $posting->get('shipment_date');
        $this->status = $posting->get('status');
        $this->substatus = $posting->get('substatus');
        $this->tpl_integration_type = $posting->get('tpl_integration_type');
        $this->tracking_number = $posting->get('tracking_number');

        // Контактные данные получателя
        $this->addressee_name = $posting->get('addressee')?->get('name');
        $this->addressee_phone = $posting->get('addressee')?->get('phone');

        // Данные аналитики
        $this->analytics_data_city = $posting->get('analytics_data')?->get('city');
        $this->analytics_data_delivery_date_begin = $posting->get('analytics_data')?->get('delivery_date_begin');
        $this->analytics_data_delivery_date_end = $posting->get('analytics_data')?->get('delivery_date_end');
        $this->analytics_data_delivery_type = $posting->get('analytics_data')?->get('delivery_type');
        $this->analytics_data_is_legal = $posting->get('analytics_data')?->get('is_legal');
        $this->analytics_data_is_premium = $posting->get('analytics_data')?->get('is_premium');
        $this->analytics_data_payment_type_group_name = $posting->get('analytics_data')?->get('payment_type_group_name');
        $this->analytics_data_region = $posting->get('analytics_data')?->get('region');
        $this->analytics_data_tpl_provider = $posting->get('analytics_data')?->get('tpl_provider');
        $this->analytics_data_tpl_provider_id = $posting->get('analytics_data')?->get('tpl_provider_id');
        $this->analytics_data_warehouse = $posting->get('analytics_data')?->get('warehouse');
        $this->analytics_data_warehouse_id = $posting->get('analytics_data')?->get('warehouse_id');

        // Штрихкоды отправления
        $this->barcodes_lower_barcode = $posting->get('barcodes')?->get('lower_barcode');
        $this->barcodes_upper_barcode = $posting->get('barcodes')?->get('upper_barcode');

        // Информация об отмене
        $this->cancellation_affect_cancellation_rating = $posting->get('cancellation')?->get('affect_cancellation_rating');
        $this->cancellation_cancel_reason = $posting->get('cancellation')?->get('cancel_reason');
        $this->cancellation_cancel_reason_id = $posting->get('cancellation')?->get('cancel_reason_id');
        $this->cancellation_cancellation_initiator = $posting->get('cancellation')?->get('cancellation_initiator');
        $this->cancellation_cancellation_type = $posting->get('cancellation')?->get('cancellation_type');
        $this->cancellation_cancelled_after_ship = $posting->get('cancellation')?->get('cancelled_after_ship');
        // Данные о покупателе
        // Информация об адресе доставки
        $this->customer_address_address_tail = $posting->get('customer')?->get('address')?->get('address_tail');
        $this->customer_address_city = $posting->get('customer')?->get('address')?->get('city');
        $this->customer_address_comment = $posting->get('customer')?->get('address')?->get('comment');
        $this->customer_address_country = $posting->get('customer')?->get('address')?->get('country');
        $this->customer_address_district = $posting->get('customer')?->get('address')->get('district');
        $this->customer_address_latitude = $posting->get('customer')?->get('address')->get('latitude');
        $this->customer_address_longitude = $posting->get('customer')?->get('address')->get('longitude');
        $this->customer_address_provider_pvz_code = $posting->get('customer')?->get('address')->get('provider_pvz_code');
        $this->customer_address_pvz_code = $posting->get('customer')?->get('address')->get('pvz_code');
        $this->customer_address_region = $posting->get('customer')?->get('address')->get('region');
        $this->customer_address_zip_code = $posting->get('customer')?->get('address')->get('zip_code');

        $this->customer_customer_id = $posting->get('customer')?->get('customer_id');
        $this->customer_name = $posting->get('customer')?->get('name');
        $this->customer_phone = $posting->get('customer')?->get('phone');

        // Метод доставки
        $this->delivery_method_id = $posting->get('delivery_method')?->get('id');
        $this->delivery_method_name = $posting->get('delivery_method')?->get('name');
        $this->delivery_method_tpl_provider = $posting->get('delivery_method')?->get('tpl_provider');
        $this->delivery_method_tpl_provider_id = $posting->get('delivery_method')?->get('tpl_provider_id');
        $this->delivery_method_warehouse = $posting->get('delivery_method')?->get('warehouse');
        $this->delivery_method_warehouse_id = $posting->get('delivery_method')?->get('warehouse_id');

        // Данные о стоимости товара, размере скидки, выплате и комиссии
        $this->financial_data_cluster_from = $posting->get('financial_data')?->get('cluster_from');
        $this->financial_data_cluster_to = $posting->get('financial_data')?->get('cluster_to');
        // Услуги
        $this->financial_data_posting_services_marketplace_service_item_deliv_to_customer = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_deliv_to_customer');
        $this->financial_data_posting_services_marketplace_service_item_direct_flow_trans = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_direct_flow_trans');
        $this->financial_data_posting_services_marketplace_service_item_dropoff_ff = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_dropoff_ff');
        $this->financial_data_posting_services_marketplace_service_item_dropoff_pvz = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_dropoff_pvz');
        $this->financial_data_posting_services_marketplace_service_item_dropoff_sc = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_dropoff_sc');
        $this->financial_data_posting_services_marketplace_service_item_fulfillment = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_fulfillment');
        $this->financial_data_posting_services_marketplace_service_item_pickup = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_pickup');
        $this->financial_data_posting_services_marketplace_service_item_return_after_deliv_to_customer = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_return_after_deliv_to_customer');
        $this->financial_data_posting_services_marketplace_service_item_return_flow_trans = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_return_flow_trans');
        $this->financial_data_posting_services_marketplace_service_item_return_not_deliv_to_customer = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_return_not_deliv_to_customer');
        $this->financial_data_posting_services_marketplace_service_item_return_part_goods_customer = $posting->get('financial_data')?->get('posting_services')->get('marketplace_service_item_return_part_goods_customer');

        // Список продуктов, для которых нужно передать страну-изготовителя, номер грузовой таможенной декларации (ГТД), регистрационный номер партии товара (РНПТ) или маркировку «Честный ЗНАК»
        $this->requirements_products_requiring_gtd = $posting->get('requirements')?->get('products_requiring_gtd');
        $this->requirements_products_requiring_country = $posting->get('requirements')?->get('products_requiring_country');
        $this->requirements_products_requiring_mandatory_mark = $posting->get('requirements')?->get('products_requiring_mandatory_mark');
        $this->requirements_products_requiring_jw_uin = $posting->get('requirements')?->get('products_requiring_jw_uin');
        $this->requirements_products_requiring_rnpt = $posting->get('requirements')?->get('products_requiring_rnpt');

        $this->products = $posting->get('products')->map(function (Collection $product) {
            return new Product($product);
        });
    }

    /**
     * @return mixed
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getStatus(): string
    {
        return match ($this->status) {
            "acceptance_in_progress" => "идёт приёмка",
            "awaiting_approve" => "ожидает подтверждения",
            "awaiting_packaging" => "ожидает упаковки",
            "awaiting_registration" => "ожидает регистрации",
            "awaiting_deliver" => "ожидает отгрузки",
            "arbitration" => "арбитраж",
            "client_arbitration" => "клиентский арбитраж доставки",
            "delivering" => "доставляется",
            "driver_pickup" => "у водителя",
            "not_accepted" => "не принят на сортировочном центре",
            default => "",
        };
    }

    public function getAvailableActions(): Collection
    {
        return $this->available_actions->map(function ($available_action) {
            return match ($available_action) {
                'arbitration' => 'открыть спор',
                'awaiting_delivery' => 'перевести в статус «Ожидает отгрузки»',
                'can_create_chat' => 'начать чат с покупателем',
                'cancel' => 'отменить отправление',
                'click_track_number' => 'просмотреть по трек-номеру историю изменения статусов в личном кабинете',
                'customer_phone_available' => 'телефон покупателя',
                'has_weight_products' => 'весовые товары в отправлении',
                'hide_region_and_city' => 'скрыть регион и город покупателя в отчёте',
                'invoice_get' => 'получить информацию из счёта-фактуры',
                'invoice_send' => 'создать счёт-фактуру',
                'invoice_update' => 'отредактировать счёт-фактуру',
                'label_download_big' => 'скачать большую этикетку',
                'label_download_small' => 'скачать маленькую этикетку',
                'label_download' => 'скачать этикетку',
                'non_int_delivered' => 'перевести в статус «Условно доставлен»',
                'non_int_delivering' => 'перевести в статус «Доставляется»',
                'non_int_last_mile' => 'перевести в статус «Курьер в пути»',
                'product_cancel' => 'отменить часть товаров в отправлении',
                'set_cutoff' => 'необходимо указать дату отгрузки, воспользуйтесь методом /v1/posting/cutoff/set',
                'set_timeslot' => 'изменить время доставки покупателю',
                'set_track_number' => 'указать или изменить трек-номер',
                'ship_async_in_process' => 'отправление собирается',
                'ship_async_retry' => 'собрать отправление повторно после ошибки сборки',
                'ship_async', 'ship' => 'собрать отправление',
                'ship_with_additional_info' => 'необходимо заполнить дополнительную информацию',
                'update_cis' => 'изменить дополнительную информацию',
                default => "",
            };
        });
    }

    public function getCancellationCancellationType(): string
    {
        return match ($this->cancellation_cancellation_type) {
            'seller' => 'отменено продавцом',
            'client', 'customer' => 'отменено покупателем',
            'ozon' => 'отменено Ozon',
            'system' => 'отменено системой',
            'delivery' => 'отменено службой доставки',
            default => "",
        };
    }

    public function getPrrOption(): string
    {
        return match ($this->prr_option) {
            'lift' => 'подъём на лифте',
            'stairs' => 'подъём по лестнице',
            'none' => 'покупатель отказался от услуги, поднимать товары не нужно',
            'delivery_default' => 'доставка включена в стоимость, по условиям оферты нужно доставить товар на этаж',
            default => "",
        };
    }

    public function getSubstatus(): string
    {
        return match ($this->substatus) {
            'posting_acceptance_in_progress' => 'идёт приёмка',
            'posting_in_arbitration' => 'арбитраж',
            'posting_created', 'posting_split_pending' => 'создано',
            'posting_in_carriage' => 'в перевозке',
            'posting_not_in_carriage' => 'не добавлено в перевозку',
            'posting_registered' => 'зарегистрировано',
            'posting_transferring_to_delivery' => 'передаётся в доставку',
            'posting_awaiting_passport_data' => 'ожидает паспортных данных',
            'posting_awaiting_registration' => 'ожидает регистрации',
            'posting_registration_error' => 'ошибка регистрации',
            'posting_canceled' => 'отменено',
            'posting_in_client_arbitration' => 'клиентский арбитраж доставки',
            'posting_delivered' => 'доставлено',
            'posting_received' => 'получено',
            'posting_conditionally_delivered' => 'условно доставлено',
            'posting_in_courier_service' => 'курьер в пути',
            'posting_in_pickup_point' => 'в пункте выдачи',
            'posting_on_way_to_city' => 'в пути в ваш город',
            'posting_on_way_to_pickup_point' => 'в пути в пункт выдачи',
            'posting_returned_to_warehouse' => 'возвращено на склад',
            'posting_transferred_to_courier_service' => 'передаётся в службу доставки',
            'posting_driver_pick_up' => 'у водителя',
            'posting_not_in_sort_center' => 'не принято на сортировочном центре',
            'sent_by_seller' => 'отправлено продавцом',
            default => "",
        };
    }

    public function getTplIntegrationType(): string
    {
        return match ($this->tpl_integration_type) {
            'ozon' => 'доставка службой Ozon',
            '3pl_tracking' => 'доставка интегрированной службой',
            'non_integrated' => 'доставка сторонней службой',
            'aggregator' => 'доставка через партнёрскую доставку Ozon',
            'hybryd' => 'доставка Почты России',
            default => "",
        };
    }



    public function toCollection(OzonMarket $market): Collection
    {
        return collect([
            'available_actions' => $this->getAvailableActions(),
            'delivering_date' => $this->delivering_date,
            'in_process_at' => $this->in_process_at,
            'is_express' => $this->is_express,
            'is_multibox' => $this->is_multibox,
            'multi_box_qty' => $this->multi_box_qty,
            'order_id' => $this->order_id,
            'order_number' => $this->order_number,
            'parent_posting_number' => $this->parent_posting_number,
            'posting_number' => $this->posting_number,
            'prr_option' => $this->getPrrOption(),
            'shipment_date' => $this->shipment_date,
            'status' => $this->getStatus(),
            'substatus' => $this->getSubstatus(),
            'tpl_integration_type' => $this->getTplIntegrationType(),
            'tracking_number' => $this->tracking_number,
            'addressee_name' => $this->addressee_name,
            'addressee_phone' => $this->addressee_phone,
            'analytics_data_city' => $this->analytics_data_city,
            'analytics_data_delivery_date_begin' => $this->analytics_data_delivery_date_begin,
            'analytics_data_delivery_date_end' => $this->analytics_data_delivery_date_end,
            'analytics_data_delivery_type' => $this->analytics_data_delivery_type,
            'analytics_data_is_legal' => $this->analytics_data_is_legal,
            'analytics_data_is_premium' => $this->analytics_data_is_premium,
            'analytics_data_payment_type_group_name' => $this->analytics_data_payment_type_group_name,
            'analytics_data_region' => $this->analytics_data_region,
            'analytics_data_tpl_provider' => $this->analytics_data_tpl_provider,
            'analytics_data_tpl_provider_id' => $this->analytics_data_tpl_provider_id,
            'analytics_data_warehouse' => $this->analytics_data_warehouse,
            'analytics_data_warehouse_id' => $this->analytics_data_warehouse_id,
            'barcodes_lower_barcode' => $this->barcodes_lower_barcode,
            'barcodes_upper_barcode' => $this->barcodes_upper_barcode,
            'cancellation_affect_cancellation_rating' => $this->cancellation_affect_cancellation_rating,
            'cancellation_cancel_reason' => $this->cancellation_cancel_reason,
            'cancellation_cancel_reason_id' => $this->cancellation_cancel_reason_id,
            'cancellation_cancellation_initiator' => $this->cancellation_cancellation_initiator,
            'cancellation_cancellation_type' => $this->getCancellationCancellationType(),
            'cancellation_cancelled_after_ship' => $this->cancellation_cancelled_after_ship,
            'customer_address_address_tail' => $this->customer_address_address_tail,
            'customer_address_city' => $this->customer_address_city,
            'customer_address_comment' => $this->customer_address_comment,
            'customer_address_country' => $this->customer_address_country,
            'customer_address_district' => $this->customer_address_district,
            'customer_address_latitude' => $this->customer_address_latitude,
            'customer_address_longitude' => $this->customer_address_longitude,
            'customer_address_provider_pvz_code' => $this->customer_address_provider_pvz_code,
            'customer_address_pvz_code' => $this->customer_address_pvz_code,
            'customer_address_region' => $this->customer_address_region,
            'customer_address_zip_code' => $this->customer_address_zip_code,
            'customer_customer_id' => $this->customer_customer_id,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'delivery_method_id' => $this->delivery_method_id,
            'delivery_method_name' => $this->delivery_method_name,
            'delivery_method_tpl_provider' => $this->delivery_method_tpl_provider,
            'delivery_method_tpl_provider_id' => $this->delivery_method_tpl_provider_id,
            'delivery_method_warehouse' => $this->delivery_method_warehouse,
            'delivery_method_warehouse_id' => $this->delivery_method_warehouse_id,
            'financial_data_cluster_from' => $this->financial_data_cluster_from,
            'financial_data_cluster_to' => $this->financial_data_cluster_to,
            'financial_data_posting_services_marketplace_service_item_deliv_to_customer' => $this->financial_data_posting_services_marketplace_service_item_deliv_to_customer,
            'financial_data_posting_services_marketplace_service_item_direct_flow_trans' => $this->financial_data_posting_services_marketplace_service_item_direct_flow_trans,
            'financial_data_posting_services_marketplace_service_item_dropoff_ff' => $this->financial_data_posting_services_marketplace_service_item_dropoff_ff,
            'financial_data_posting_services_marketplace_service_item_dropoff_pvz' => $this->financial_data_posting_services_marketplace_service_item_dropoff_pvz,
            'financial_data_posting_services_marketplace_service_item_dropoff_sc' => $this->financial_data_posting_services_marketplace_service_item_dropoff_sc,
            'financial_data_posting_services_marketplace_service_item_fulfillment' => $this->financial_data_posting_services_marketplace_service_item_fulfillment,
            'financial_data_posting_services_marketplace_service_item_pickup' => $this->financial_data_posting_services_marketplace_service_item_pickup,
            'financial_data_posting_services_marketplace_service_item_return_after_deliv_to_customer' => $this->financial_data_posting_services_marketplace_service_item_return_after_deliv_to_customer,
            'financial_data_posting_services_marketplace_service_item_return_flow_trans' => $this->financial_data_posting_services_marketplace_service_item_return_flow_trans,
            'financial_data_posting_services_marketplace_service_item_return_not_deliv_to_customer' => $this->financial_data_posting_services_marketplace_service_item_return_not_deliv_to_customer,
            'financial_data_posting_services_marketplace_service_item_return_part_goods_customer' => $this->financial_data_posting_services_marketplace_service_item_return_part_goods_customer,
            'requirements_products_requiring_gtd' => $this->requirements_products_requiring_gtd,
            'requirements_products_requiring_country' => $this->requirements_products_requiring_country,
            'requirements_products_requiring_mandatory_mark' => $this->requirements_products_requiring_mandatory_mark,
            'requirements_products_requiring_jw_uin' => $this->requirements_products_requiring_jw_uin,
            'requirements_products_requiring_rnpt' => $this->requirements_products_requiring_rnpt,
            'products' => $this->products->map(function (Product $product) use ($market) {
                $product->loadLink($market);
                $product->fetchAttribute($market);
                return $product->toCollection();
            })
        ]);
    }


}
