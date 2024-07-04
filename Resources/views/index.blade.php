@extends('sptransfer::layouts.frontend')
@section('title', 'HUB Transfer')
@section('content')
<div class="row">
  <div class="col-md-12">
    <h2>@lang('SPTransfer::common.title')</h2>
    @include('flash::message')
    <div class="row">
      <div class="col-sm-8">
        @if($state === 0)
          <h4 class="description mt-0">@lang('SPTransfer::common.reqform')</h4>
          <div class="alert alert-warning" role="alert">@lang('SPTransfer::common.reqis') {{ $status }}.</div>
        @else
          @if($limit)
            <div class="alert alert-info" role="alert">@lang('SPTransfer::common.limited') {{ $daysLimit }} @lang('SPTransfer::common.days').</div>
          @else
            <form method="POST" action="{{ route('sptransfer.store') }}">
              @csrf
              <h4 class="description mt-0">@lang('SPTransfer::common.based') {{ $current_hub_name }} ({{ strtoupper($current_hub) }})</h4>
              <div class="mt-3">
                <div>@lang('SPTransfer::common.desired')</div>
                <select name="hub_request_id" id="hub_request_id" class="form-control airport_search hubs_only" required></select>       
              </div>
              <div class="mt-3">
                <div>@lang('SPTransfer::common.reason')</div>
                <textarea name="reason" id="reason" class="form-control" maxlength="100" placeholder="@lang('SPTransfer::common.chars')" required></textarea>
              </div>
              @if($spfinance) <p class="float-left text-muted mt-3">@lang('SPTransfer::common.charged') {{ $spvalue }} @lang('SPTransfer::common.transfer').</p> @endif
              <button type="submit" class="btn btn-outline-info pull-right btn-lg">@lang('SPTransfer::common.request')</button>
            </form>
          @endif
        @endif
      </div>
      <div class="col-sm-4">
        <h4 class="description mt-0">@lang('SPTransfer::common.lasttitle')</h4>
        @if(empty($lasttransfer))
          <div class="alert alert-info" role="alert">@lang('SPTransfer::common.didnot')</div>
        @else
          <div class="table-responsive">
            <table class="table table-striped">
              <tbody>
                <tr>
                  <td>@lang('SPTransfer::common.transferid')</td>
                  <td>{{ $lasttransfer->id }}</td>
                </tr>
                <tr>
                  <td>@lang('SPTransfer::common.reqhub')</td>
                  <td>{{ $lasttransfer->hub_request }}</td>
                </tr>
                <tr>
                  <td>@lang('SPTransfer::common.reqdate')</td>
                  <td>{{ $lasttransfer->created_at->format('d. F Y - H:i') }} UTC</td>
                </tr>
                <tr>
                  <td>@lang('SPTransfer::common.reqstatus')</td>
                  <td>{{ $status }}</td>
                </tr>
                <tr>
                  <td>@lang('SPTransfer::common.reqreason')</td>
                  <td>{{ $lasttransfer->reason }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
  @parent
  @include('scripts.airport_search')
@endsection
