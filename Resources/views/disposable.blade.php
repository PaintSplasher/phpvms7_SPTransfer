@extends('sptransfer::layouts.frontend')
@section('title', 'HUB Transfer')
@section('content')
<div class="row">
  <div class="col-sm-8">
    <div class="card mb-2">
      <div class="card-header p-1">
        <h5 class="m-1">
          @lang('SPTransfer::common.title') <i class="fas fa-exchange-alt float-end" aria-hidden="true"></i>
        </h5>
      </div>
      <div class="card-body">
        @include('flash::message')
        @if($state === 0)
        <h6 class="m-2">@lang('SPTransfer::common.reqform')</h6>
        <div class="alert alert-warning" role="alert">@lang('SPTransfer::common.reqis') {{ $status }}.</div>
        @else
        @if($limit)
        <div class="alert alert-info" role="alert">@lang('SPTransfer::common.limited') {{ $daysLimit }} @lang('SPTransfer::common.days').</div>
        @else
        <form method="POST" action="{{ route('sptransfer.store') }}">
          @csrf
          <h6>@lang('SPTransfer::common.based') {{ $current_hub_name }} ({{ strtoupper($current_hub) }})</h6>
          <div class="mt-3">
            <label>@lang('SPTransfer::common.desired')</label>
            <select name="hub_request" id="hub_request" class="form-control airport_search hubs_only" required></select>       
          </div>
          <div class="mt-3">
            <label>@lang('SPTransfer::common.reason')</label>
            <textarea name="reason" id="reason" class="form-control" maxlength="100" placeholder="@lang('SPTransfer::common.chars')" required></textarea>
          </div>
          <button type="submit" class="btn btn-sm bg-info float-end mt-2 p-0 px-1">@lang('SPTransfer::common.request')</button>
        </form>
        @endif
        @endif
      </div>
      @if($spfinance)
      <div class="card-footer p-0 px-1 small fw-bold text-end">
        <i class="fas fa-money-bill-wave text-danger float-start m-1" aria-hidden="true"></i> @lang('SPTransfer::common.charged') {{ $spvalue }} @lang('SPTransfer::common.transfer').        
      </div>
      @endif 
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card mb-2">
      <div class="card-header p-1">
        <h5 class="m-1">
          @lang('SPTransfer::common.lasttitle') <i class="fas fa-info-circle float-end" aria-hidden="true"></i>
        </h5>
      </div>
      <div class="card-body p-0 table-responsive">
        @if(empty($lasttransfer))
        <div class="alert alert-info m-3" role="alert">@lang('SPTransfer::common.didnot')</div>
        @else
        <table class="table table-sm table-borderless table-striped text-start mb-0">
          <tbody>
            <tr>
              <th class="text-nowrap">@lang('SPTransfer::common.transferid')</th>
              <td class="text-end">{{ $lasttransfer->id }}</td>
            </tr>
            <tr>
              <th class="text-nowrap">@lang('SPTransfer::common.reqhub')</th>
              <td class="text-end">{{ $lasttransfer->hub_request }}</td>
            </tr>
            <tr>
              <th class="text-nowrap">@lang('SPTransfer::common.reqdate')</th>
              <td class="text-end">{{ $lasttransfer->created_at->format('d. F Y - H:i') }} UTC</td>
            </tr>
            <tr>
              <th class="text-nowrap">@lang('SPTransfer::common.reqstatus')</th>
              <td class="text-end">
                @if($status === 'Pending') <span class="badge bg-secondary text-black">{{ $status }}</span> @endif
                @if($status === 'Rejected') <span class="badge bg-warning text-black">{{ $status }}</span> @endif
                @if($status === 'Accepted') <span class="badge bg-success text-black">{{ $status }}</span> @endif
              </td>
            </tr>
            <tr>
              <th>@lang('SPTransfer::common.reqreason')</th>
              <td class="text-end" style="word-break: break-word">{{ $lasttransfer->reason }}</td>
            </tr>
          </tbody>
        </table>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
@include('scripts.airport_search')
@endsection