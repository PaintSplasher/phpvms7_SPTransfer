@extends('sptransfer::layouts.admin')
@section('title', 'HUB Transfer')
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card border-blue-bottom">
        <div class="content">
          <div class="row">
            <form action="{{ route('admin.sptransfer.storeSettings') }}" method="POST" >
              @csrf
              <div class="col-lg-12">
                <input type="hidden" name="id" value="{{ $settings->id }}">
                <span style="float:right"><button type="submit" class="btn btn-success">Save</button></span>
                <h5>Hub Transfer Settings</h5>
                <div class="content table-responsive table-full-width">
                  <div class="row">
                    <table class="table table-hover table-responsive" id="spsettings">
                      <tbody>
                        <tr>
                          <td>
                            <p>Price per request</p>
                            <p style="float:left; margin-right: 10px; margin-left: 2px;"><i class="fas fa-info-circle text-primary"></i> The amount the pilot gets charged for every request (0 = disabled)</p>
                          </td>
                          <td>
                            <input type="number" class="form-control" name="sp_price" step="1" min="0" max="100000" placeholder="0" value="{{ $settings->price }}">
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <p>Type of charge</p>
                            <p style="float:left; margin-right: 10px; margin-left: 2px;"><i class="fas fa-info-circle text-primary"></i> This option does not have any impact if your price per request is disabled</p>
                          </td>
                          <td align="center">
                            <select class="select2" name="sp_charge" style="width: 100%;">
                              <option value="0" {{ $settings->charge_type == 0 ? 'selected' : '' }}>Charge for every request</option>
                              <option value="1" {{ $settings->charge_type == 1 ? 'selected' : '' }}>Charge only on approval</option>
                          </select>                          
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <p>Limit per request</p>
                            <p style="float:left; margin-right: 10px; margin-left: 2px;"><i class="fas fa-info-circle text-primary"></i> The limit in days the pilot has to wait until he can make another request (0 = disabled)</p>
                          </td>
                          <td>
                            <input type="number" class="form-control" name="sp_days" step="1" min="0" max="1000" placeholder="0" value="{{ $settings->limit }}">
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <p>Discord Private Webhook URL</p>
                            <p style="float:left; margin-right: 10px; margin-left: 2px;"><i class="fas fa-info-circle text-primary"></i> The Discord Webhook URL for private notifications</p>
                          </td>
                          <td>
                            <input type="text" class="form-control" name="sp_discordurl" value="{{ $settings->discord_url }}">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="card border-blue-bottom">
        <div class="content">
          <div class="row">
            <div class="col-lg-12">
              <h5>Hub Transfer Requests</h5>
              <div class="row">
                <table class="table table-hover table-responsive" id="sptransfer">
                  <thead>
                    <tr>
                      <th>@sortablelink('id', 'ID')</th>
                      <th>@sortablelink('user.name', 'Name')</th>
                      <th class="text-center">Current</th>
                      <th class="text-center">Request</th>
                      <th>Transfer Reason</th>
                      <th class="text-right">@sortablelink('created_at', 'Date')</th>
                      <th class="text-right">@sortablelink('state', 'Status')</th>
                      <th class="text-right">Reject Reason</th>
                      <th class="text-right">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($requests as $request)
                    <tr>
                      <td>{{ $request->id }}</td>
                      <td><a href="{{ route('admin.users.edit', [$request->user_id]) }}">{{ $request->user->name }}</a></td>
                      <td class="text-center">{{ $request->hub_initial_id }}</td>
                      <td class="text-center">{{ $request->hub_request_id }}</td>
                      <td style="word-break: break-word">{{ $request->reason }}</td>
                      <td class="text-right">{{ $request->created_at->format('d. F Y - H:i') }} UTC</td>
                      <td class="text-right">{{ Modules\SPTransfer\Models\Enums\Status::label($request->state) }}</td>
                      <td class="text-right">{{ $request->reject_reason ?? '-' }}</td>
                      <td class="text-right">
                        <form method="POST" action="{{ route('admin.sptransfer.update') }}" style="display:inline;" class="decision-form form-inline" id="decision-form">
                          @csrf
                          <span id="init-buttons">
                            <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                            <input type="hidden" name="id" value="{{ $request->id }}">
                            @if($request->state == 0)
                              <button type="submit" name="decision" value="ack" class="btn btn-success">Approve</button>
                              <button type="button" class="btn btn-warning" id="reject-button">Reject</button>
                            @endif
                            <button type="submit" name="decision" value="del" class="btn btn-danger">Delete</button>
                          </span>
                          <div id="reason-input" style="display:none;">
                            <input type="text" name="reason" class="form-control" placeholder="Reason for this rejection" maxlength="50">
                            <button type="submit" name="decision" value="rej" id="submit-reason" class="btn btn-warning" style="margin-top:0px;">Reject</button>
                          </div>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 text-center">
            {{ $requests->withQueryString()->links('admin.pagination.default') }}
          </div>
        </div>
      </div>
      <p class="text-center">Crafted with <i class="fas fa-heart text-danger"></i> by <a href="https://github.com/PaintSplasher/phpvms7_sptransfer" target="_blank">Sass-Projects</p>
    </div>
  </div>
</div>
@endsection
@section('scripts')
  @parent
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          var rejectButton = document.getElementById('reject-button');
          var reasonInputDiv = document.getElementById('reason-input');
          var submitReasonButton = document.getElementById('submit-reason');
          var decisionForm = document.getElementById('decision-form');

          if (rejectButton) {
              rejectButton.addEventListener('click', function(event) {
                  event.preventDefault();
                  if (reasonInputDiv) {
                      reasonInputDiv.style.display = 'block';
                  }
                  var initButtonsDiv = document.getElementById('init-buttons');
                  if (initButtonsDiv) {
                      initButtonsDiv.style.display = 'none';
                  }
              });
          }
          
          if (submitReasonButton) {
              submitReasonButton.addEventListener('click', function() {
                  var reasonInput = document.getElementById('reason');
                  if (reasonInput && reasonInput.value.trim() !== '') {
                      decisionForm.submit();
                  }
              });
          }
      });
  </script>
@endsection
