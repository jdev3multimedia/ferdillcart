@extends('layouts.admin.app')

@section('title', \App\Models\BusinessSetting::where(['key' => 'business_name'])->first()->value ?? translate('messages.dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="content container-fluid">
        <div class="page-header">
            <div class="d-flex flex-wrap gap-2 justify-content-between py-2">
                <div class="d-flex align-items-center flex-grow-1">
                    <img src="{{asset('/public/assets/admin/img/new-img/users.svg')}}" alt="img">
                    <div class="w-0 flex-grow pl-3">
                        <h1 class="page-header-title mb-1">{{translate('Dispatch Overview')}}</h1>
                        <p class="page-header-text text-dark m-0">
                            {{translate('Monitor your')}}
                            <span class="font-semibold">{{translate('Dispatch Management')}}</span>
                            {{translate('statistics by zone')}}
                        </p>
                    </div>
                </div>
                <div class="alert bg--10 font-bold fs-14" role="alert">
                    {{ translate('This_section_only_contains_Order_Data') }}
                </div>
            </div>
        </div>

        <div class="row g-1">
            <div class="col-lg-8">
                <div class="row gap__10 __customer-statistics-card-wrap-2">
                    <div class="col-sm-6">
                        <div class="__customer-statistics-card h-100">
                            <div class="title">
                                <img src="{{asset('public/assets/admin/img/new-img/deliveryman/active.svg')}}"
                                    alt="new-img">
                                <h4>{{$active_deliveryman}}</h4>
                            </div>
                            <h4 class="subtitle text-capitalize mt-2">{{translate('messages.active_delivery_man')}}</h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="__customer-statistics-card h-100 d-flex gap-3" style="--clr:#FF5A54">
                            <div>
                                <img width="48" height="48"
                                    src="{{asset('public/assets/admin/img/new-img/deliveryman/newly.svg')}}" alt="new-img">
                            </div>
                            <div class="d-flex justify-content-around gap-3 flex-grow-1">
                                <div>
                                    <h4 class="title">{{ $inactive_deliveryman }}</h4>
                                    <h4 class="subtitle text-capitalize">{{translate('messages.in_Active')}}</h4>
                                </div>
                                <div>
                                    <h4 class="title">{{ $suspend_deliveryman }}</h4>
                                    <h4 class="subtitle text-capitalize">{{ translate('suspended')}}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="__customer-statistics-card h-100">
                            <div class="title">
                                <img src="{{asset('public/assets/admin/img/new-img/deliveryman/active.svg')}}"
                                    alt="new-img">
                                <h4>{{ $unavailable_deliveryman }}</h4>
                            </div>
                            <h4 class="subtitle text-capitalize mt-2">{{ translate('Fully Booked Delivery Man')}}</h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="__customer-statistics-card h-100" style="--clr:#FF5A54">
                            <div class="title">
                                <img src="{{asset('public/assets/admin/img/new-img/deliveryman/in-active.svg')}}"
                                    alt="new-img">
                                <h4>{{$available_deliveryman}}</h4>
                            </div>
                            <h4 class="subtitle text-capitalize mt-2">{{translate('Available to assign more order')}}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="shadow--order-card">
                    <div class="row m-0">
                        <div class="col-12 p-0">
                            <a class="order--card h-100" href="#">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('public/assets/admin/img/dashboard/food/unassigned.svg')}}"
                                            alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('messages.unassigned_orders')}}</span>
                                    </h6>
                                    <span class="card-title text-00A3FF">
                                        {{$data['searching_for_dm']}}
                                    </span>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 p-0">
                            <a class="order--card h-100" href="#">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('public/assets/admin/img/dashboard/food/accepted.svg')}}"
                                            alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('Accepted by Delivery Man')}}</span>
                                    </h6>
                                    <span class="card-title text-success">
                                        {{$data['accepted_by_dm']}}
                                    </span>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 p-0">
                            <a class="order--card h-100" href="#">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('public/assets/admin/img/dashboard/food/out-for.svg')}}"
                                            alt="dashboard" class="oder--card-icon">
                                        <span>{{translate('Out for Delivery')}}</span>
                                    </h6>
                                    <span class="card-title text-success">
                                        {{$data['picked_up']}}
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="__map-wrapper-2 mt-3">
                    <div class="map-pop-deliveryman">
                        <form action="javascript:" id="search-form" class="map-pop-deliveryman-inner">
                            <label>{{ translate('Currently Active Delivery Men') }} </label>
                            <div class="position-relative mx-auto">
                                <i class="tio-search"></i>
                                <input type="text" name="search" class="form-control"
                                    placeholder="{{translate('Search Delivery Man ...')}}">
                            </div>
                            <a href="{{ route('admin.users.delivery-man.list') }}"
                                class="link font-semibold">{{ translate('View All Delivery Men') }}</a>
                        </form>
                    </div>
                    <div class="map-warper map-wrapper-2 rounded">
                        <div id="map-canvas" width="900px" class="rounded"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&callback=initialize&libraries=drawing,places,marker&v=3.61"></script>

<script>
    "use strict";
    let map; // Global declaration of the map
    let drawingManager;
    let lastpolygon = null;
    let polygons = [];
    let dmMarkers = [];

    function resetMap(controlDiv) {
        // Set CSS for the control border.
        const controlUI = document.createElement("div");
        controlUI.style.backgroundColor = "#fff";
        controlUI.style.border = "2px solid #fff";
        controlUI.style.borderRadius = "3px";
        controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
        controlUI.style.cursor = "pointer";
        controlUI.style.marginTop = "8px";
        controlUI.style.marginBottom = "22px";
        controlUI.style.textAlign = "center";
        controlUI.title = "Reset map";
        controlDiv.appendChild(controlUI);
        // Set CSS for the control interior.
        const controlText = document.createElement("div");
        controlText.style.color = "rgb(25,25,25)";
        controlText.style.fontFamily = "Roboto,Arial,sans-serif";
        controlText.style.fontSize = "10px";
        controlText.style.lineHeight = "16px";
        controlText.style.paddingLeft = "2px";
        controlText.style.paddingRight = "2px";
        controlText.innerHTML = "X";
        controlUI.appendChild(controlText);
        // Setup the click event listeners: simply set the map to Chicago.
        controlUI.addEventListener("click", () => {
            lastpolygon.setMap(null);
            $('#coordinates').val('');

        });
    }

    function initialize() {
        @php($default_location = \App\Models\BusinessSetting::where('key', 'default_location')->first())
        @php($default_location = $default_location->value ? json_decode($default_location->value, true) : 0)
        var myLatlng = {
            lat: {{ $default_location ? $default_location['lat'] : '23.757989' }},
            lng: {{ $default_location ? $default_location['lng'] : '90.360587' }}
            };
        var dmbounds = new google.maps.LatLngBounds(null);
        const mapId = "{{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}"

        var myOptions = {
            zoom: 13,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapId: mapId,
        }
        var deliveryMan = <?php echo json_encode($deliveryMen); ?>;
        map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

        var infowindow = new google.maps.InfoWindow();

        map.fitBounds(dmbounds);

        const { AdvancedMarkerElement } = google.maps.marker;
        deliveryMan.forEach(dm => {
            if (dm.lat) {
                const point = new google.maps.LatLng(dm.lat, dm.lng);
                dmbounds.extend(point);
                map.fitBounds(dmbounds);

                const activeIconContent = document.createElement("img");
                activeIconContent.src = "{{ asset('public/assets/admin/img/delivery_boy_active.png') }}";
                activeIconContent.alt = "Active DM";
                activeIconContent.style.width = '100%'; 
                activeIconContent.style.height = '100%'; 
                activeIconContent.style.borderRadius = '50%'; 

                const marker = new AdvancedMarkerElement({
                    position: point,
                    map: map,
                    title: dm.image,
                    content: activeIconContent, 
                });

                dmMarkers[dm.id] = marker;

                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.setContent(`
                <div style='float:left'>
                    <img style='max-height:40px;wide:auto;'  src='${dm.image_link}'>
                </div>
                <div style='float:right; padding: 10px;'>
                    <b>${dm.name}</b><br/>
                    ${dm.location}<br/>
                    Assigned Order: ${dm.assigned_order_count}
                </div>`);
                    infowindow.open(map, marker);
                });
            }
        });

    }

    $('#search-form').on('submit', function (e) {
        initialize();
        var deliveryMan = <?php echo json_encode($deliveryMen); ?>;
        var infowindow = new google.maps.InfoWindow();
        let formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: '{{ route('admin.users.delivery-man.active-search') }}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                let itemCount = 0;
                if (data.dm) {
                    deliveryMan.forEach(item => {

                        const isDMActive = data.dm.some(ddm => ddm.id === item.id);
                        if (isDMActive) {
                            itemCount++
                        }
                        const icon = isDMActive ?
                            "{{ asset('public/assets/admin/img/delivery_boy_active.png') }}" :
                            "{{ asset('public/assets/admin/img/delivery_boy_map_inactive.png') }}";

                        const newIconContent = document.createElement("img");
                        newIconContent.src = icon;
                        newIconContent.alt = isDMActive ? "Active" : "Inactive";
                        newIconContent.style.width = '100%';
                        newIconContent.style.height = '100%';
                        newIconContent.style.borderRadius = '50%';

                        const markerToUpdate = dmMarkers[item.id];
                        if (markerToUpdate) {
                            markerToUpdate.content = newIconContent;

                            if (isDMActive) {
                                itemCount++
                            }

                            map.panTo(markerToUpdate.position);
                            map.setZoom(20);
                            let dmViewContent = `
                <div style='float:left'>
                    <img style='max-height:40px;wide:auto;'  src='${item.image_link}'>
                </div>
                <div style='float:right; padding: 10px;'>
                    <b>${item.name}</b><br/>
                    ${item.location}<br/>
                    Assigned Order: ${item.assigned_order_count}
                </div>`

                            if (isDMActive && itemCount == 1) {
                                infowindow.setContent(dmViewContent);
                                infowindow.open(map, markerToUpdate);
                            } else {
                                google.maps.event.addListener(markerToUpdate, 'click', function () {
                                    infowindow.setContent(dmViewContent);
                                    infowindow.open(map, markerToUpdate);
                                });
                            }
                        }
                    });
                } else {
                    toastr.error('Delivery Man not found', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            },
        });
    });
</script>

@endpush