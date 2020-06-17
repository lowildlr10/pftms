<form id="form-store" method="POST" action="{{ route('supplier-store') }}">
    @csrf

    <div class="md-form">
        <select class="mdb-select md-form reuired" searchable="Search here.."
                name="classification">
            <option value="" disabled selected>Choose supplier classification *</option>

            @if (count($classifications) > 0)
                @foreach ($classifications as $class)
            <option value="{{ $class->id }}">
                {!! $class->classification_name !!}
            </option>
                @endforeach
            @endif
        </select>
        <label class="mdb-main-label">
            Classification <span class="red-text">*</span>
        </label>
    </div>

    <div class="md-form">
        <select class="mdb-select md-form reuired" searchable="Search here.."
                name="is_active">
            <option value="" disabled selected>Choose active status *</option>
            <option value="y">Yes</option>
            <option value="n">No</option>
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
                       name="bank_name">
                <label for="bank-name">
                    Name of bank <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="account-name" class="form-control required"
                       name="account_name">
                <label for="account-name">
                    Account Name <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="account-no" class="form-control required"
                       name="account_no">
                <label for="account-no">
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
                       name="company_name">
                <label for="company-name">
                    Name of Company <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="date" id="date-filed" class="form-control required"
                       name="date_filed">
                <label for="date-filed" class="mt-3">
                    Date <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <textarea id="adress" class="md-textarea form-control required"
                          name="address" rows="3"></textarea>
                <label for="adress">Address <span class="red-text">*</span></label>
            </div>
            <div class="md-form">
                <input type="text" id="email" class="form-control"
                       name="email">
                <label for="email">
                    Email
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="website-url" class="form-control"
                       name="website_url">
                <label for="website-url">
                    URL Addresss
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="fax-no" class="form-control"
                       name="fax_no">
                <label for="fax-no">
                    Fax Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="telephone-no" class="form-control"
                       name="telephone_no">
                <label for="telephone-no">
                    Telephone Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="mobile-no" class="form-control"
                       name="mobile_no">
                <label for="mobile-no">
                    Mobile Number
                </label>
            </div>
            <div class="md-form">
                <input type="date" id="date-established" class="form-control"
                       name="date_established">
                <label for="date-established" class="mt-3">
                    Date Established
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="tin-no" class="form-control"
                       name="tin_no">
                <label for="tin-no">
                    TIN Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="vat-no" class="form-control"
                       name="vat_no">
                <label for="vat-no">
                    VAT Number
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="contact-person" class="form-control"
                       name="contact_person">
                <label for="contact-person">
                    Contact Person
                </label>
            </div>
            <div class="md-form">
                <select class="mdb-select md-form required" searchable="Search here.."
                        id="nature-business" name="nature_business">
                    <option value="" disabled selected>Choose nature of business</option>
                    <option value="manufacturer">Manufacturer</option>
                    <option value="trading_firms">Trading Firms</option>
                    <option value="service_contractor">Service Contractors</option>
                    <option value="others">Others</option>
                </select>
                <label class="mdb-main-label">
                    Nature of Business <span class="red-text">*</span>
                </label>
            </div>
            <div id="field-nature-business-others" class="md-form" style="display: none;">
                <input type="text" id="nature-business-others" class="form-control"
                       name="nature_business_others">
                <label for="nature-business-others">
                    -- Other nature of business <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="number" id="delivery-vehicle-no" class="form-control required"
                       name="delivery_vehicle_no" value="0">
                <label for="delivery-vehicle-no" class="active">
                    No. of Delivery Vehicles <span class="red-text">*</span>
                </label>
            </div>
            <div class="md-form">
                <input type="text" id="product-lines" class="form-control"
                       name="product_lines">
                <label for="product-lines">
                    Product Lines
                </label>
            </div>
            <div class="md-form">
                <select class="mdb-select md-form required" searchable="Search here.."
                        name="credit_accomodation">
                    <option value="" disabled selected>Choose a credit accommodation</option>
                    <option value="90_days_above">90 Days and Above</option>
                    <option value="60_days">60 Days</option>
                    <option value="30_days">30 Days</option>
                    <option value="15_days">15 Days</option>
                    <option value="below_15_days">Below 15 Days</option>
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
                <input type="hidden" name="attachment" id="attachment">
            </h6><hr class="m-0">

            <div id="check-attachment">
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" class="custom-control-input" id="attachment-1"
                        value="1">
                    <label class="custom-control-label" for="attachment-1">
                        Latest Financial Statement
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-2"
                        value="2">
                    <label class="custom-control-label" for="attachment-2">
                        DTI/SEC/ Registration
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-3"
                        value="3">
                    <label class="custom-control-label" for="attachment-3">
                        Valid and current Mayor's permit/municipal license
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-4"
                        value="4">
                    <label class="custom-control-label" for="attachment-4">
                        VAT Registration Certificate
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-5"
                        value="5">
                    <label class="custom-control-label" for="attachment-5">
                        Article of Incorporation, Partnership or Cooperation, Valid joint venture
                        Agreement Wichever is applicable
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-6"
                        value="6">
                    <label class="custom-control-label" for="attachment-6">
                        Certificate of PhilGEPS Registration
                    </label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="attachment-7"
                        value="7">
                    <label class="custom-control-label" for="attachment-7">
                        Others
                    </label>
                </div>

                <div id="field-attachment-others" class="md-form" style="display: none;">
                    <input type="text" id="attachment-others" class="form-control"
                        name="attachment_others">
                    <label for="attachment-others">
                        -- Other attachment <span class="red-text">*</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
