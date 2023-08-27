//header open close
function openNav() {
    document.getElementById("mySidebar").style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
    document.getElementById("mySidebar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
}
//header open close




// data Table
    new DataTable('#myTable');
// data Table







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