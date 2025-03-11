@extends('sptransfer::layouts.frontend')
@section('title', 'HUB Transfer')
@section('content')
<div class="row">
  <div class="col-sm-8">
    <div class="card mb-2">
      <div class="card-header p-1">
        <h5 class="m-1">
          @lang('SPTransfer::common.title-a') <i class="fas fa-exchange-alt float-end" aria-hidden="true"></i>
        </h5>
      </div>
      <div class="card-body">
        @include('flash::message')
        @if($state === 0)
          <h6 class="m-2">@lang('SPTransfer::common.reqform')</h6>
          <div class="alert alert-warning" role="alert">@lang('SPTransfer::common.reqis') {{ $status }}.</div>
        @else
          @if($limit)
            <div class="alert alert-info" role="alert">@lang('SPTransfer::common.limited-a') {{ $daysLimit }} @lang('SPTransfer::common.days').</div>
          @else
            <form method="POST" action="{{ route('sptransfer.airline.store') }}">
              @csrf
              <h6>@lang('SPTransfer::common.based-a') {{ $current_airline_name }} ({{ strtoupper($current_airline) }})</h6>
              <div class="mt-3">
                <label>@lang('SPTransfer::common.desired-a')</label>
                <select name="airline_request_id" id="airline_request_id" class="form-control select2" required>
                  @foreach($airlines as $airline_request)
                    <option value="{{ $airline_request->id }}">{{ $airline_request->icao }} - {{ $airline_request->name }}</option>
                  @endforeach                  
                </select>       
              </div>
              <div class="mt-3">
                <label>@lang('SPTransfer::common.reason-a')</label>
                <textarea name="reason" id="reason" class="form-control" maxlength="100" placeholder="@lang('SPTransfer::common.chars')" required></textarea>
              </div>
              <button type="submit" class="btn btn-sm bg-info float-end mt-2 p-0 px-1">@lang('SPTransfer::common.request')</button>
            </form>
          @endif
        @endif
      </div>
      <div class="card-footer p-0 px-1 small fw-bold text-end">
        @if($spfinance)
          @if($charge_type === 0)
            <i class="fas fa-money-bill-wave text-danger float-start m-1" aria-hidden="true"></i> @lang('SPTransfer::common.charged') {{ $spvalue }} @lang('SPTransfer::common.onrequest').
          @else
            <i class="fas fa-money-bill-wave text-danger float-start m-1" aria-hidden="true"></i> @lang('SPTransfer::common.charged') {{ $spvalue }} @lang('SPTransfer::common.ontransfer').
          @endif                
        @endif
      </div>
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
                <th class="text-nowrap">@lang('SPTransfer::common.reqhub-a')</th>
                <td class="text-end">{{ $request_airline->icao }} - {{ $request_airline->name }}</td>
              </tr>
              <tr>
                <th class="text-nowrap">@lang('SPTransfer::common.reqdate')</th>
                <td class="text-end">{{ $lasttransfer->created_at->format('d. F Y - H:i') }} UTC</td>
              </tr>
              <tr>
                <th class="text-nowrap">@lang('SPTransfer::common.reqstatus')</th>
                <td class="text-end">{{ $status }}@if($status == 'Rejected'): {{ $lasttransfer->reject_reason ?? '-' }} @endif </td>
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
  @parent
  @include('scripts.airport_search')
@endsection
