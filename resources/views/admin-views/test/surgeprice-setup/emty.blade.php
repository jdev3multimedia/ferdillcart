@extends('layouts.admin.app')

@section('title',translate('messages.new_page'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">

    <h3 class="mb-20">Surge Price</h3>

    <table id="#0" class="table m-0 table-borderless table-thead-bordered table-align-middle">
        <tbody id="table-div">
            <tr>
                <td colspan="">
                    <div class="bg-light rounded table-column p-5 text-center">
                        <div class="pt-5">
                            <img class="mb-20" src="{{asset('public/assets/admin/img/price-emty.png')}}" alt="status">
                            <h4 class="mb-3">Currently you don’t have any Surge Price</h4>
                            <p class="mb-20 fs-12 mx-auto max-w-400px">To enable surge pricing, you must create at least one Surge Price. In this page you see all the surge price you added.</p>
                            <a href="#0" class="btn btn--primary">
                                Create Surge Price
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

</div>


@endsection

@push('script_2')
@endpush