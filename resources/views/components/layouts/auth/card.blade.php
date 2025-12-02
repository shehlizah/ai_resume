<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner">
      <!-- Login -->
      <div class="card px-sm-6 px-0">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center">
            <a href="index.html" class="app-brand-link gap-2"><x-app-logo /></a>
          </div>
          <!-- /Logo -->

          <!-- Content -->
          {{ $slot }}
          <!-- /Content -->
        </div>
      </div>
    </div>
  </div>
</div>