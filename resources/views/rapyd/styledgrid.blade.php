@extends('style')
@section('content')
{!! Rapyd::head() !!}
<br>
<div style="padding:2%;background-color: #ffffff;margin: 2px 20px 0 20px;border-radius: 5px;">
    @if(!empty($title))
        <h4 class="pull-left" style="border-left: 3px solid #2cc6ba;padding-left: 7px">{!! $title !!}</h4>
    @else
        <h4 class="pull-left">列表</h4>
    @endif

    @if(isset($tips))
        <br>
        <br>
        <p style="color: #cecece;font-size: 12px">{!! $tips !!}</p>
    @endif
    <div class="pull-right" style="margin-bottom: 15px">
        {!! $filter or null !!}
    </div>
    <br>
    <br>
    <br>
    {!! $grid !!}

    <script>

    </script>
</div>
    @endsection