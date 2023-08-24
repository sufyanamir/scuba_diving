<style>
  .modal-header,
  .modal-body,
  .modal-footer {
    border: none;
  }
</style>
<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header">
        <h5 class="modal-title" id="">Add Company</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M13 1L1 13M1 1L13 13" stroke="#667085" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
          <div class="row">
            <div class="col-8">
              <div class="row">
                <div class="col-6 mb-3">
                  <x-input :name="'company_name'" :value="''" :label="'Name'"></x-input>
                </div>
                <div class="col-6 mb-3">
                  <x-input :name="'company_email'" :value="''" :label="'Email'"></x-input>
                </div>
                <div class="col-12 mb-3">
                  <x-input :name="'company_phone'" :value="''" :label="'Phone Number'"></x-input>
                </div>
                <div class="col-12 mb-3">
                  <x-text-area :name="'company_address'" :value="''" :label="'Address'"></x-text-area>
                </div>
              </div>
            </div>
            <div class="col-4">

            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              <h5 class="text-left" id="">Add Admin Details</h5>
            </div>
            <div class="col-6 mb-3">
              <x-input :name="'admin_name'" :value="''" :label="'Admin Name'"></x-input>
            </div>
            <div class="col-6 mb-3">
              <x-input :name="'admin_email'" :value="''" :label="'Email'"></x-input>
            </div>
            <div class="col-6 mb-3">
              <x-input :name="'admin_phone'" :value="''" :label="'Phone Number'"></x-input>
            </div>
            <div class="col-6 mb-3">
              <x-input :name="'admin_userName'" :value="''" :label="'User Name'"></x-input>
            </div>
            <div class="col-8 mb-3">
              <x-text-area :name="'admin_address'" :value="''" :label="'Address'"></x-text-area>
            </div>
            <div class="col-4 my-auto">
              <x-submit-button :name="'submit'" :value="'Save'"></x-submit-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>