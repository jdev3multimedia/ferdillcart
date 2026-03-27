@extends('layouts.admin.app')

@section('title','')


@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 0;  /* this affects the margin in the printer settings */
        }

    </style>
@endpush


@section('content')

@include('admin-views.order.partials._invoice')

@endsection

@push('script')
    <script>
        function printDiv(divName) {
            window.open('{{route("admin.order.print-invoice",["id" => $order->id])}}', '_blank');
        }

    </script>
@endpush
