@extends('layouts.admin.app')

@section('title', $store->name . "'s " . translate('messages.settings'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('public/assets/admin/css/croppie.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        @include('admin-views.vendor.view.partials._header', ['store' => $store])
        <!-- Page Heading -->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="vendor">
                        <form action="{{ route('admin.store.update-meta-data', [$store['id']]) }}" method="post"
                            enctype="multipart/form-data" class="col-12">
                            @csrf
                            @include('admin-views.business-settings.landing-page-settings.partial._meta_data', ['submit' => true])
                        </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function readURL(input, viewer) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + viewer).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this, 'viewer');
        });

        $("#coverImageUpload").change(function() {
            readURL(this, 'coverImageViewer');
        });
    </script>
@endpush
