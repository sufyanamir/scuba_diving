@include('layouts.header')

<div class="card px-0  box-shadow mt-4 ">
        <div class="row">
            <div class="col-8">
                <div class="mx-3 my-3">
                    <h4>Company List</h4>
                </div>
            </div>
            <div class="col-4 text-center my-3 pl-5">
                <x-add-button :value="'+ Add Company'" :dataTarget="'#add-company'"></x-add-button>
                <x-modal :modalId="'add-company'" :formAction="'company/store'"></x-modal>
            </div>
        </div>
        <div class="table-responsive">
            <table class="display" id="myTable">
                <thead class="thead-color ">
                    <tr>
                        <th>{{$companies->company_name}} </th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <img class="img-size" src="{{asset('assets/images/salman.jpg')}}" alt="image">
                            <span>Testing</span>
                        </td>
                        <td>test@gmail.com</td>
                        <td>03127839265</td>
                        <td>model town fsaisaablad</td>
                        <td>
                            <button class="btn p-0" style="background: none;">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle opacity="0.1" cx="18" cy="18" r="18" fill="#233A85" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1637 23.6195L22.3141 15.6658C22.6484 15.2368 22.7673 14.741 22.6558 14.236C22.5593 13.777 22.277 13.3406 21.8536 13.0095L20.8211 12.1893C19.9223 11.4744 18.8081 11.5497 18.1693 12.3699L17.4784 13.2661C17.3893 13.3782 17.4116 13.5438 17.523 13.6341C17.523 13.6341 19.2686 15.0337 19.3058 15.0638C19.4246 15.1766 19.5137 15.3271 19.536 15.5077C19.5732 15.8614 19.328 16.1925 18.9641 16.2376C18.7932 16.2602 18.6298 16.2075 18.511 16.1097L16.6762 14.6499C16.5871 14.5829 16.4534 14.5972 16.3791 14.6875L12.0188 20.3311C11.7365 20.6848 11.64 21.1438 11.7365 21.5878L12.2936 24.0032C12.3233 24.1312 12.4348 24.2215 12.5685 24.2215L15.0197 24.1914C15.4654 24.1838 15.8814 23.9807 16.1637 23.6195ZM19.5957 22.8673H23.5928C23.9828 22.8673 24.2999 23.1886 24.2999 23.5837C24.2999 23.9795 23.9828 24.3 23.5928 24.3H19.5957C19.2058 24.3 18.8886 23.9795 18.8886 23.5837C18.8886 23.1886 19.2058 22.8673 19.5957 22.8673Z" fill="#233A85" />
                                </svg>
                            </button>
                            <button class="btn p-0" style="background: none;">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle opacity="0.1" cx="18" cy="18" r="18" fill="#DF6F79" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M23.4905 13.743C23.7356 13.743 23.9396 13.9465 23.9396 14.2054V14.4448C23.9396 14.6975 23.7356 14.9072 23.4905 14.9072H13.0493C12.8036 14.9072 12.5996 14.6975 12.5996 14.4448V14.2054C12.5996 13.9465 12.8036 13.743 13.0493 13.743H14.8862C15.2594 13.743 15.5841 13.4778 15.6681 13.1036L15.7642 12.6739C15.9137 12.0887 16.4058 11.7 16.9688 11.7H19.5704C20.1273 11.7 20.6249 12.0887 20.7688 12.6431L20.8718 13.1029C20.9551 13.4778 21.2798 13.743 21.6536 13.743H23.4905ZM22.5573 22.4943C22.7491 20.707 23.0849 16.4609 23.0849 16.418C23.0971 16.2883 23.0548 16.1654 22.9709 16.0665C22.8808 15.9739 22.7669 15.9191 22.6412 15.9191H13.9028C13.7766 15.9191 13.6565 15.9739 13.5732 16.0665C13.4886 16.1654 13.447 16.2883 13.4531 16.418C13.4542 16.4259 13.4663 16.5755 13.4864 16.8255C13.5759 17.9364 13.8251 21.0303 13.9861 22.4943C14.1001 23.5729 14.8078 24.2507 15.8328 24.2753C16.6238 24.2936 17.4387 24.2999 18.272 24.2999C19.0569 24.2999 19.854 24.2936 20.6696 24.2753C21.7302 24.257 22.4372 23.5911 22.5573 22.4943Z" fill="#D11A2A" />
                                </svg>
                            </button>
                            <button class="btn p-0" style="background: none;">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="18" cy="18" r="18" fill="#109CF1" fill-opacity="0.06" />
                                    <path d="M17.75 15.3977C16.9724 15.3977 16.2267 15.7066 15.6769 16.2564C15.1271 16.8063 14.8182 17.552 14.8182 18.3295C14.8182 19.1071 15.1271 19.8528 15.6769 20.4027C16.2267 20.9525 16.9724 21.2614 17.75 21.2614C18.5276 21.2614 19.2733 20.9525 19.8231 20.4027C20.3729 19.8528 20.6818 19.1071 20.6818 18.3295C20.6818 17.552 20.3729 16.8063 19.8231 16.2564C19.2733 15.7066 18.5276 15.3977 17.75 15.3977ZM17.75 23.2159C16.4541 23.2159 15.2112 22.7011 14.2948 21.7847C13.3784 20.8684 12.8636 19.6255 12.8636 18.3295C12.8636 17.0336 13.3784 15.7907 14.2948 14.8744C15.2112 13.958 16.4541 13.4432 17.75 13.4432C19.0459 13.4432 20.2888 13.958 21.2052 14.8744C22.1216 15.7907 22.6364 17.0336 22.6364 18.3295C22.6364 19.6255 22.1216 20.8684 21.2052 21.7847C20.2888 22.7011 19.0459 23.2159 17.75 23.2159ZM17.75 11C12.8636 11 8.69068 14.0393 7 18.3295C8.69068 22.6198 12.8636 25.6591 17.75 25.6591C22.6364 25.6591 26.8093 22.6198 28.5 18.3295C26.8093 14.0393 22.6364 11 17.75 11Z" fill="#4361EE" fill-opacity="0.8" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img class="img-size" src="{{asset('assets/images/salman.jpg')}}" alt="image">
                            <span>Testing</span>
                        </td>
                        <td>test@gmail.com</td>
                        <td>03127839265</td>
                        <td>model town fsaisaablad</td>
                        <td>
                            <button class="btn p-0" style="background: none;">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle opacity="0.1" cx="18" cy="18" r="18" fill="#233A85" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1637 23.6195L22.3141 15.6658C22.6484 15.2368 22.7673 14.741 22.6558 14.236C22.5593 13.777 22.277 13.3406 21.8536 13.0095L20.8211 12.1893C19.9223 11.4744 18.8081 11.5497 18.1693 12.3699L17.4784 13.2661C17.3893 13.3782 17.4116 13.5438 17.523 13.6341C17.523 13.6341 19.2686 15.0337 19.3058 15.0638C19.4246 15.1766 19.5137 15.3271 19.536 15.5077C19.5732 15.8614 19.328 16.1925 18.9641 16.2376C18.7932 16.2602 18.6298 16.2075 18.511 16.1097L16.6762 14.6499C16.5871 14.5829 16.4534 14.5972 16.3791 14.6875L12.0188 20.3311C11.7365 20.6848 11.64 21.1438 11.7365 21.5878L12.2936 24.0032C12.3233 24.1312 12.4348 24.2215 12.5685 24.2215L15.0197 24.1914C15.4654 24.1838 15.8814 23.9807 16.1637 23.6195ZM19.5957 22.8673H23.5928C23.9828 22.8673 24.2999 23.1886 24.2999 23.5837C24.2999 23.9795 23.9828 24.3 23.5928 24.3H19.5957C19.2058 24.3 18.8886 23.9795 18.8886 23.5837C18.8886 23.1886 19.2058 22.8673 19.5957 22.8673Z" fill="#233A85" />
                                </svg>
                            </button>
                            <button class="btn p-0" style="background: none;">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle opacity="0.1" cx="18" cy="18" r="18" fill="#DF6F79" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M23.4905 13.743C23.7356 13.743 23.9396 13.9465 23.9396 14.2054V14.4448C23.9396 14.6975 23.7356 14.9072 23.4905 14.9072H13.0493C12.8036 14.9072 12.5996 14.6975 12.5996 14.4448V14.2054C12.5996 13.9465 12.8036 13.743 13.0493 13.743H14.8862C15.2594 13.743 15.5841 13.4778 15.6681 13.1036L15.7642 12.6739C15.9137 12.0887 16.4058 11.7 16.9688 11.7H19.5704C20.1273 11.7 20.6249 12.0887 20.7688 12.6431L20.8718 13.1029C20.9551 13.4778 21.2798 13.743 21.6536 13.743H23.4905ZM22.5573 22.4943C22.7491 20.707 23.0849 16.4609 23.0849 16.418C23.0971 16.2883 23.0548 16.1654 22.9709 16.0665C22.8808 15.9739 22.7669 15.9191 22.6412 15.9191H13.9028C13.7766 15.9191 13.6565 15.9739 13.5732 16.0665C13.4886 16.1654 13.447 16.2883 13.4531 16.418C13.4542 16.4259 13.4663 16.5755 13.4864 16.8255C13.5759 17.9364 13.8251 21.0303 13.9861 22.4943C14.1001 23.5729 14.8078 24.2507 15.8328 24.2753C16.6238 24.2936 17.4387 24.2999 18.272 24.2999C19.0569 24.2999 19.854 24.2936 20.6696 24.2753C21.7302 24.257 22.4372 23.5911 22.5573 22.4943Z" fill="#D11A2A" />
                                </svg>
                            </button>
                            <button class="btn p-0" style="background: none;">
                                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="18" cy="18" r="18" fill="#109CF1" fill-opacity="0.06" />
                                    <path d="M17.75 15.3977C16.9724 15.3977 16.2267 15.7066 15.6769 16.2564C15.1271 16.8063 14.8182 17.552 14.8182 18.3295C14.8182 19.1071 15.1271 19.8528 15.6769 20.4027C16.2267 20.9525 16.9724 21.2614 17.75 21.2614C18.5276 21.2614 19.2733 20.9525 19.8231 20.4027C20.3729 19.8528 20.6818 19.1071 20.6818 18.3295C20.6818 17.552 20.3729 16.8063 19.8231 16.2564C19.2733 15.7066 18.5276 15.3977 17.75 15.3977ZM17.75 23.2159C16.4541 23.2159 15.2112 22.7011 14.2948 21.7847C13.3784 20.8684 12.8636 19.6255 12.8636 18.3295C12.8636 17.0336 13.3784 15.7907 14.2948 14.8744C15.2112 13.958 16.4541 13.4432 17.75 13.4432C19.0459 13.4432 20.2888 13.958 21.2052 14.8744C22.1216 15.7907 22.6364 17.0336 22.6364 18.3295C22.6364 19.6255 22.1216 20.8684 21.2052 21.7847C20.2888 22.7011 19.0459 23.2159 17.75 23.2159ZM17.75 11C12.8636 11 8.69068 14.0393 7 18.3295C8.69068 22.6198 12.8636 25.6591 17.75 25.6591C22.6364 25.6591 26.8093 22.6198 28.5 18.3295C26.8093 14.0393 22.6364 11 17.75 11Z" fill="#4361EE" fill-opacity="0.8" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>



@include('layouts.footer')