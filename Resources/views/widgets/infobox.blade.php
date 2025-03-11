<div class="card border-blue-bottom">
  <div class="content">
    <div class="row">
      <div class="col-xs-5">
        <div class="icon-big icon-info text-center">
          <i class="pe-7s-back"></i>
        </div>
      </div>
      <div class="col-xs-7">
        <div class="numbers">
          <p>Transfers</p>
          <a href="{{ route('admin.sptransfer.index') }}">{{ $pending }} pending</a>
        </div>
      </div>
    </div>
    <div class="footer">
      <hr>
    </div>
  </div>
</div>