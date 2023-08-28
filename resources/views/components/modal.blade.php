<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border-radius: 15px;">
      <div class="modal-header">
        @if($modalId == 'add-staff')
        <h5 class="modal-title" id="">Add Staff</h5>
        @elseif($modalId == 'add-service')
        <h5 class="modal-title" id="">Add Service</h5>
        @elseif($modalId == 'add-customer')
        <h5 class="modal-title" id="">Add Customer</h5>
        @else
        <h5 class="modal-title" id="">Add Company</h5>
        @endif
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
                  <x-input :name="'name'" :value="''" :label="'Name'"></x-input>
                </div>
                <div class="col-6 mb-3">
                  @if($modalId == 'add-service')
                  <x-input :name="'subtitle'" :value="''" :label="'Subtitle'"></x-input>
                  @else
                  <x-input :name="'email'" :value="''" :label="'Email'"></x-input>
                  @endif
                </div>
                @if($modalId == 'add-staff')
                <div class="col-6 mb-3">
                  <x-input :name="'phone'" :value="''" :label="'Phone Number'"></x-input>
                </div>
                <div class="col-6 mb-3">
                  <select name="" id="" class="form-control" style="height: 51px; border-radius: 0.5rem;">
                    <option value="">Select</option>
                    <option value="">Manager</option>
                    <option value="">Worker</option>
                    <option value="">Trainer</option>
                  </select>
                </div>
                @elseif($modalId == 'add-service')
                <div class="col-12 mb-3">
                  <x-input :name="'charges'" :value="''" :label="'Charges'"></x-input>
                </div>
                @else
                <div class="col-12 mb-3">
                  <x-input :name="'phone'" :value="''" :label="'Phone Number'"></x-input>
                </div>
                @endif
                <div class="col-12 mb-3">
                  <x-text-area :name="'address'" :value="''" :label="'Address'"></x-text-area>
                </div>
              </div>
            </div>
            <div class="col-4">
              <x-drop-zone :name="'upload_image'"></x-drop-zone>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              @if($modalId == 'add-staff' || $modalId == 'add-customer')
              <h5 class="text-left" id="">Social Links</h5>
              @elseif($modalId == 'add-service')
              <h5 class="text-left" id="">Overheads & Costs</h5>
              @else
              <h5 class="text-left" id="">Add Admin Details</h5>
              @endif
            </div>
            @if($modalId == 'add-staff' || $modalId == 'add-customer')
            <div class="col-8 mb-3">
              <x-social-input :socialLogo="'assets/images/fb-logo.svg'" :name="'fb_acc'" :qrId="''" :qrName="''"></x-social-input>
              <x-social-input :socialLogo="'assets/images/ig-logo.svg'" :name="'ig_acc'" :qrId="''" :qrName="''"></x-social-input>
              <x-social-input :socialLogo="'assets/images/tt-logo.svg'" :name="'tt_acc'" :qrId="''" :qrName="''"></x-social-input>
            </div>
            @elseif($modalId == 'add-service')
            <div class="col-5 mb-3">
              <x-input :name="'cost_name'" :value="''" :label="'Cost Name'"></x-input>
            </div>
            <div class="col-5 mb-3">
              <x-input :name="'cost'" :value="''" :label="'Cost'"></x-input>
            </div>
            <div class="col-2 my-2">
              <x-plus-button :name="'add_row'" :label="'+'"></x-plus-button>
            </div>
            @else
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
            @endif
            <div class="{{ $modalId === 'add-service' ? 'col-12' : 'col-4' }} my-auto text-right">
              <x-submit-button :name="'submit'" :value="'Save'"></x-submit-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
   // Get references to the necessary elements
   const fileInput = document.getElementById('fileInput1');
  const profileImage = document.getElementById('profileImage');
  const form = document.getElementById('myForm');

  // Handle file input change
  fileInput.addEventListener('change', function(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    if (!$('.error-image').hasClass('d-none')) {
      $('.error-image').addClass('d-none');
    }

    // Check the file size and type of file
    if (file.type.startsWith('image/')) {
      if (file.size <= 1048576) {
        reader.readAsDataURL(file);

        reader.onload = function(e) {
          profileImage.src = e.target.result;
          form.action = "";
        };
      } else {
        $('.error-image').removeClass('d-none').text('The user pic should be less than or equal to 1024KB');
        console.log("Image size exceeds the limit of 1 MB.");
        fileInput.value = "";
      }
    } else {
      $('.error-image').removeClass('d-none').text('Please select an image file.');
      console.log("Please select an image file.");
      fileInput.value = "";
    }


  });
</script>