<form id="form-update" method="POST" action="{{ route('supplier-update', ['id' => $id]) }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select md-form reuired" searchable="Search here.."
                name="classification">
            <option value="" disabled selected>Choose supplier classification *</option>

            @if (count($classifications) > 0)
                @foreach ($classifications as $class)
            <option value="{{ $class->id }}" {{ ($class->id == $classification) ? 'selected' : '' }}>
                {!! $class->classification_name !!}
            </option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form reuired" searchable="Search here.."
                name="is_active">
            <option value="" disabled selected>Choose active status *</option>
            <option value="y" {{ ($isActive == 'y') ? 'selected' : '' }}>Yes</option>
            <option value="n" {{ ($isActive == 'n') ? 'selected' : '' }}>No</option>
        </select>
        <label class="mdb-main-label">
            Is Active? <span class="red-text">*</span>
        </label>
    </div>

    <div class="card">
        <div class="card-body">
            <h6 class="card-title text-center">
                Bank Information
            </h6><hr class="m-0">

            <div class="md-form">
                <input type="text" id="bank-name" class="form-control required"
                       name="bank_name" value="{{ $bankName }}">
                <label for="bank-name" class="{{ !empty($bankName) ? 'active' : '' }}">
                    Name of bank <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="account-name" class="form-control required"
                       name="account_name" value="{{ $accountName }}">
                <label for="account-name" class="{{ !empty($accountName) ? 'active' : '' }}">
                    Account Name <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="account-no" class="form-control required"
                       name="account_no" value="{{ $accountNo }}">
                <label for="account-no" class="{{ !empty($accountNo) ? 'active' : '' }}">
                    Account Number <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <hr>

    <div class="card">
        <div class="card-body">
            <h6 class="card-title text-center">
                Supplier Information Sheet
            </h6><hr class="m-0">

            <div class="md-form">
                <input type="text" id="company-name" class="form-control required"
                       name="company_name" value="{{ $companyName }}">
                <label for="company-name" class="{{ !empty($companyName) ? 'active' : '' }}">
                    Name of Company <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="date" id="date-filed" class="form-control required"
                       name="date_filed" value="{{ $dateFiled }}">
                <label for="date-filed" class="mt-3 {{ !empty($dateFiled) ? 'active' : '' }}"">
                    Date <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <textarea id="adress" class="md-textarea form-control required"
                          name="address" rows="3">{{ $address }}</textarea>
                <label for="adress" class="{{ !empty($address) ? 'active' : '' }}">
                    Address <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="email" class="form-control"
                       name="email" value="{{ $email }}">
                <label for="email" class="{{ !empty($email) ? 'active' : '' }}">
                    Email
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="website-url" class="form-control"
                       name="website_url" value="{{ $websiteURL }}">
                <label for="website-url" class="{{ !empty($websiteURL) ? 'active' : '' }}">
                    URL Addresss
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="fax-no" class="form-control"
                       name="fax_no" value="{{ $faxNo }}">
                <label for="fax-no" class="{{ !empty($faxNo) ? 'active' : '' }}">
                    Fax Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="telephone-no" class="form-control"
                       name="telephone_no" value="{{ $telephoneNo }}">
                <label for="telephone-no" class="{{ !empty($telephoneNo) ? 'active' : '' }}">
                    Telephone Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="mobile-no" class="form-control"
                       name="mobile_no" value="{{ $mobileNo }}">
                <label for="mobile-no" class="{{ !empty($mobileNo) ? 'active' : '' }}">
                    Mobile Number
                </label>
            </div>
            <div class="md-form">
                <input type="date" id="date-established" class="form-control"
                       name="date_established" value="{{ $dateEstablished }}">
                <label for="date-established" class="mt-3 {{ !empty($dateEstablished) ? 'active' : '' }}">
                    Date Established
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="tin-no" class="form-control"
                       name="tin_no" value="{{ $tinNo }}">
                <label for="tin-no" class="{{ !empty($tinNo) ? 'active' : '' }}">
                    TIN Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="vat-no" class="form-control"
                       name="vat_no" value="{{ $vatNo }}">
                <label for="vat-no" class="{{ !empty($vatNo) ? 'active' : '' }}">
                    VAT Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="contact-person" class="form-control"
                       name="contact_person" value="{{ $contactPerson }}">
                <label for="contact-person" class="{{ !empty($contactPerson) ? 'active' : '' }}">
                    Contact Person
                </label>
            </div>
            <div class="md-form">
                <select class="mdb-select md-form required" searchable="Search here.."
                        id="nature-business" name="nature_business">
                    <option value="" disabled selected>Choose nature of business *</option>
                    <option value="manufacturer" {{ ($natureBusiness == 'manufacturer') ? 'selected' : '' }}>
                        Manufacturer
                    </option>
                    <option value="trading_firms" {{ ($natureBusiness == 'trading_firms') ? 'selected' : '' }}>
                        Trading Firms
                    </option>
                    <option value="service_contractor" {{ ($natureBusiness == 'service_contractor') ? 'selected' : '' }}>
                        Service Contractors
                    </option>
                    <option value="others" {{ ($natureBusiness == 'others') ? 'selected' : '' }}>
                        Others
                    </option>
                </select>
                <label class="mdb-main-label">
                    Nature of Business <span class="red-text">*</span>
                </label>
            </div>
            <div id="field-nature-business-others" class="md-form"
                 style="display:{{ empty($natureBusinessOthers) ? 'none' : 'block' }};">
                <input type="text" id="nature-business-others" class="form-control"
                       name="nature_business_others"  value="{{ $natureBusinessOthers }}">
                <label for="nature-business-others" class="{{ !empty($natureBusinessOthers) ? 'active' : '' }}">
                    -- Other nature of business <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="number" id="delivery-vehicle-no" class="form-control"
                       name="delivery_vehicle_no" value="{{ $deliveryVehicleNo }}">
                <label for="delivery-vehicle-no" class="{{ !empty($deliveryVehicleNo) || $deliveryVehicleNo == 0 ? 'active' : '' }}">
                    No. of Delivery Vehicles <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="product-lines" class="form-control"
                       name="product_lines" value="{{ $productLines }}">
                <label for="product-lines" class="{{ !empty($productLines) ? 'active' : '' }}">
                    Product Lines
                </label>
            </div>
            <div class="md-form">
                <select class="mdb-select md-form required" searchable="Search here.."
                        name="credit_accomodation">
                    <option value="" disabled selected>Choose a credit accommodation *</option>
                    <option value="90_days_above" {{ ($creditAccomodation == '90_days_above') ? 'selected' : '' }}>
                        90 Days and Above
                    </option>
                    <option value="60_days" {{ ($creditAccomodation == '60_days') ? 'selected' : '' }}>
                        60 Days
                    </option>
                    <option value="30_days" {{ ($creditAccomodation == '30_days') ? 'selected' : '' }}>
                        30 Days
                    </option>
                    <option value="15_days" {{ ($creditAccomodation == '15_days') ? 'selected' : '' }}>
                        15 Days
                    </option>
                    <option value="below_15_days" {{ ($creditAccomodation == 'below_15_days') ? 'selected' : '' }}>
                        Below 15 Days
                    </option>
                </select>
                <label class="mdb-main-label">
                    Credit Accommodation <span class="red-text">*</span>
                </label>
            </div>
        </div>
    </div>

    <hr>

    <div class="card">
        <div class="card-body">
            <h6 class="card-title text-center">
                Attachment/s
                <input type="hidden" name="attachment" id="attachment" value="{{ $attachment }}">
            </h6><hr class="m-0">

            <div id="check-attachment">
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" class="custom-control-input" id="attachment-1"
                           value="1" {{ ($attachment1) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="attachment-1">
                        Latest Financial Statement
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-2"
                           value="2" {{ ($attachment2) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="attachment-2">
                        DTI/SEC/ Registration
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-3"
                           value="3" {{ ($attachment3) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="attachment-3">
                        Valid and current Mayor's permit/municipal license
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-4"
                           value="4" {{ ($attachment4) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="attachment-4">
                        VAT Registration Certificate
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-5"
                           value="5" {{ ($attachment5) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="attachment-5">
                        Article of Incorporation, Partnership or Cooperation, Valid joint venture
                        Agreement Wichever is applicable
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-6"
                           value="6" {{ ($attachment6) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="attachment-6">
                        Certificate of PhilGEPS Registration
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-7"
                           value="7" {{ ($attachment7) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="attachment-7">
                        Others
                    </label>
                </div>

                <div id="field-attachment-others" class="md-form"
                     style="display:{{ empty($attachmentOthers) ? 'none' : 'block' }};">
                    <input type="text" id="attachment-others" class="form-control"
                           name="attachment_others" value="{{ $attachmentOthers }}">
                    <label for="attachment-others" class="{{ !empty($attachmentOthers) ? 'active' : '' }}">
                        -- Other attachment <span class="red-text">*</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
