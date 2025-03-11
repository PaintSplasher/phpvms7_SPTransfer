@extends('sptransfer::layouts.frontend')
@section('title', 'Airline Transfer')
@section('content')
<div class="row">
  <div class="col-md-12">
    <h2>@lang('SPTransfer::common.title-a')</h2>
    @include('flash::message')
    <div class="row">
      <div class="col-sm-7">
        @if($state === 0)
          <h4 class="description mt-0">@lang('SPTransfer::common.reqform')</h4>
          <div class="alert alert-warning" role="alert">@lang('SPTransfer::common.reqis') {{ $status }}.</div>
        @else
          @if($limit)
            <div class="alert alert-info" role="alert">@lang('SPTransfer::common.limited-a') {{ $daysLimit }} @lang('SPTransfer::common.days').</div>
          @else
            <form method="POST" action="{{ route('sptransfer.airline.store') }}">
              @csrf
              <h4 class="description mt-0">@lang('SPTransfer::common.based-a') {{ $current_airline_name }} ({{ strtoupper($current_airline) }})</h4>
              <div class="mb-3">
                <div>@lang('SPTransfer::common.desired-a')</div>
                <select name="airline_request_id" id="airline_request_id" class="form-select" placeholder="@lang('SPTransfer::common.selectplace')" required>
                  @foreach($airlines as $airline_request)
                    <option value="{{ $airline_request->id }}">{{ $airline_request->icao }} - {{ $airline_request->name }}</option>
                  @endforeach                  
                </select> 
              </div>
              <div class="mb-3">
                <div>@lang('SPTransfer::common.reason-a')</div>
                <textarea name="reason" id="reason" class="form-control" maxlength="100" placeholder="@lang('SPTransfer::common.chars')" required></textarea>
              </div>
              @if($spfinance)
                @if($charge_type === 0)
                  <p class="float-left text-muted mt-3">@lang('SPTransfer::common.charged') {{ $spvalue }} @lang('SPTransfer::common.onrequest').</p>
                @else
                  <p class="float-left text-muted mt-3">@lang('SPTransfer::common.charged') {{ $spvalue }} @lang('SPTransfer::common.ontransfer').</p>
                @endif                
              @endif
              <button type="submit" class="btn btn-primary">@lang('SPTransfer::common.request')</button>
            </form>
          @endif
        @endif
      </div>
      <div class="col-sm-5">
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
                  <td>@lang('SPTransfer::common.reqhub-a')</td>
                  <td>{{ $request_airline->icao }} - {{ $request_airline->name }}</td>
                </tr>
                <tr>
                  <td>@lang('SPTransfer::common.reqdate')</td>
                  <td>{{ $lasttransfer->created_at->format('d. F Y - H:i') }} UTC</td>
                </tr>
                <tr>
                  <td>@lang('SPTransfer::common.reqstatus')</td>
                  <td>{{ $status }}@if($status == 'Rejected'): {{ $lasttransfer->reject_reason ?? '-' }} @endif </td>
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