@extends('style')
@section('content')
    {!! Rapyd::head() !!}
    <style>
        #fg_tags label{
            margin-left: 30px;
            width: 120px;
        }
        input[type=checkbox] {
            margin-right: 5px;
        }
    </style>
    <div style="padding:2%">
        <div class="rpd-edit">
            {!! $form !!}
        </div>
    </div>
@endsection