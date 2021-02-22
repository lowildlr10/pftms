<form id="form-create-update" class="supplier-form" method="POST" action="{{ url('libraries/store/suppplier') }}">
    @csrf
    <input type="hidden" name="key" value="{{ $key }}">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>Supplier Classification</label>
                <select class="browser-default custom-select z-depth-1 required" name="class_id">
                    <option value=""> -- Select classification -- </option>

                    @if (!empty($supplierClass))

                        @foreach ($supplierClass as $class)

                    <option <?php if ($class->id == $classID) echo 'selected="selected"'; ?> 
                            value="{{ $class->id }}">
                        {{ $class->classification }}
                    </option>

                        @endforeach

                    @endif

                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Active</label>
                <select class="browser-default custom-select z-depth-1 required" name="active">
                    <option value="y" <?php if ($active == "y") echo 'selected="selected"'; ?>>Yes</option>
                    <option value="n" <?php if ($active == "n") echo 'selected="selected"'; ?>>No</option>
                </select>
            </div>
        </div>
    </div>  
    <hr>
    <table class="table table-bordered z-depth-1">
        <tr>
            <th colspan="2">
                <h5>Bank Information</h5>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <label>Name of Bank</label>
                    <input class="form-control required" type="text" name="name_bank" value="{{ $nameBank }}"
                           placeholder="Name of bank...">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="form-group">
                <label>Account Name</label>
                <input class="form-control required" type="text" name="account_name" value="{{ $accountName }}"
                       placeholder="Account name...">
            </div>
            </td>
            <td>
                <div class="form-group">
                <label>Account Number</label>
                <input class="form-control required" type="text" name="account_no" value="{{ $accountNo }}"
                       placeholder="Account number...">
            </div>
            </td>
        </tr>
    </table>
    <hr>
    <table class="table table-bordered z-depth-1">
        <tr>
            <th colspan="3">
                <h5>Supplier Information Sheet</h5>
            </th>
        </tr>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <label>Name of Company</label>
                    <input class="form-control required" type="text" name="company_name" 
                           value="{{ $companyName }}" placeholder="Company name...">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <label>Date</label>
                    <input class="form-control required" type="date" name="date_file" 
                          value="{{ $dateFile }}">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <label>Business Address</label>
                    <textarea class="form-control required" type="text" style="resize: none;" 
                              name="address" rows="4" placeholder="Company address...">{{ $address }}</textarea>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <label>E-mail Address</label>
                    <input class="form-control" type="text" name="email" 
                           value="{{ $email }}" placeholder="Email...">
                </div>
                <div class="form-group">
                    <label>URL Address</label>
                    <input class="form-control" type="text" name="url_address" 
                          value="{{ $urlAddress }}" placeholder="URL address...">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <label>Fax No.</label>
                    <input class="form-control" type="text" name="fax_no" 
                           value="{{ $faxNo }}" placeholder="Fax number...">
                </div>
                <div class="form-group">
                    <label>Telephone Numbers</label>
                    <input class="form-control" type="text" name="telephone_no" 
                           value="{{ $telephoneNo }}" placeholder="Telephone number...">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <label>Mobile Phone Number</label>
                    <input class="form-control" type="text" name="mobile_no" 
                           value="{{ $mobileNo }}" placeholder="Mobile number...">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <label>Date Established</label>
                    <input class="form-control" type="date" name="date_established" 
                           value="{{ $dateEstablished }}">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <label>Vat No.</label>
                    <input class="form-control" type="text" name="vat_no" 
                           value="{{ $vatNo }}" placeholder="VAT number...">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="form-group">
                    <label>Contact Person</label>
                    <input class="form-control" type="text" name="contact_person" 
                          value="{{ $contactPerson }}" placeholder="Contact person...">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="form-group">
                    <label>Nature of Business</label>
                    <select class="browser-default custom-select required" name="nature_business">
                        <option value=""> -- Nature of business -- </option>
                        <option <?php if ($natureBusiness == "manufacturer") echo 'selected="selected"'; ?> 
                                value="manufacturer">
                            Manufacturer
                        </option>
                        <option <?php if ($natureBusiness == "trading_firms") echo 'selected="selected"'; ?> 
                                value="trading_firms">
                            Trading Firms
                        </option>
                        <option <?php if ($natureBusiness == "service_contractor") echo 'selected="selected"'; ?> 
                                value="service_contractor">
                            Service Contractor
                        </option>
                        <option <?php if ($natureBusiness == "others") echo 'selected="selected"'; ?> 
                                value="others">
                            Others
                        </option>
                    </select>
                    <div class="mt-3" <?php if ($natureBusiness == "others") {echo '';} else {echo 'hidden="hidden"';} ?>>
                        <input <?php if ($natureBusiness == "others") {echo '';} else {echo 'disabled="disabled"';} ?>
                               class="form-control z-depth-1" type="text" name="nature_business_others"
                               placeholder="Specify here..."
                               value="{{ $natureBusinessOthers }}">
                    </div>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <label>No. of Delivery Vehicles</label>
                    <input class="form-control required" type="number" value="{{ $deliveryVehicleNo }}" 
                           name="delivery_vehicle_no" min="0">
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="form-group">
                    <label>Product Lines</label>
                    <textarea class="form-control" name="product_lines" style="resize: none;" 
                              placeholder="Product lines...">{{ $productLines }}</textarea>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="form-group">
                    <label>Credit Accomodation to DOST-CAR</label>
                    <select class="browser-default custom-select required" name="credit_accomodation">
                        <option value=""> -- Select credit accomodation -- </option>
                        <option value="90-days_above">
                            90-DAYS ABOVE
                        </option>
                        <option <?php if ($creditAccomodation == "60-days") echo 'selected="selected"'; ?> 
                                value="60-days">
                            60-DAYS
                        </option>
                        <option <?php if ($creditAccomodation == "30-days") echo 'selected="selected"'; ?> 
                                value="30-days">
                            30-DAYS
                        </option>
                        <option <?php if ($creditAccomodation == "15-days") echo 'selected="selected"'; ?> 
                                value="15-days">
                            15-DAYS
                        </option>
                        <option <?php if ($creditAccomodation == "below-15-days") echo 'selected="selected"'; ?> 
                                value="below-15-days">
                            BELOW 15-DAYS
                        </option>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="form-group">
                    <label>Attachment</label>
                    <div id="check-attachment">
                        <input class="required" type="hidden" name="attachment" value="{{ $attachmentValue }}">
                        <div class="form-check">
                            <input <?php if (in_array("1", $attachment)) echo 'checked="checked"'; ?> 
                                   id="attachment-1" class="form-check-input" type="checkbox" value="1">
                            <label for="attachment-1">Latest Financial Statement</label>
                        </div>
                        <div class="form-check">
                            <input <?php if (in_array("2", $attachment)) echo 'checked="checked"'; ?> 
                                   id="attachment-2" class="form-check-input" type="checkbox" value="2">
                            <label for="attachment-2">DTI/SEC Registration</label>
                        </div>
                        <div class="form-check">
                            <input <?php if (in_array("3", $attachment)) echo 'checked="checked"'; ?> 
                                   id="attachment-3" class="form-check-input" type="checkbox" value="3">
                            <label for="attachment-3">Valid and current Mayor's permit/municipal license</label>
                        </div>
                        <div class="form-check">
                            <input <?php if (in_array("4", $attachment)) echo 'checked="checked"'; ?> 
                                   id="attachment-4" class="form-check-input" type="checkbox" value="4">
                            <label for="attachment-4">VAT Registration Certificate</label>
                        </div>
                        <div class="form-check">
                            <input <?php if (in_array("5", $attachment)) echo 'checked="checked"'; ?> 
                                   id="attachment-5" class="form-check-input" type="checkbox" value="5">
                            <label for="attachment-5">
                                Articles of Incorporation, Partnership or Cooperation, 
                                Valid joint venture Agreement whichever is applicable
                            </label>
                        </div>
                        <div class="form-check">
                            <input <?php if (in_array("6", $attachment)) echo 'checked="checked"'; ?> 
                                   id="attachment-6" class="form-check-input" type="checkbox" value="6">
                            <label for="attachment-6">Certificate of PhilGEPS Registration</label>
                        </div>
                        <div class="form-check">
                            <input <?php if (in_array("7", $attachment)) echo 'checked="checked"'; ?> 
                                   id="attachment-other" class="form-check-input" type="checkbox" value="7">
                            <label for="attachment-other">Others</label>
                        </div>
                    </div>
                    <div <?php if (in_array("7", $attachment)) {echo '';} else {echo 'hidden="hidden"';} ?>>
                        <input <?php if (in_array("7", $attachment)) {echo '';} else {echo 'disabled="disabled"';} ?>
                               class="form-control z-depth-1 ml-5 w-75" type="text" name="attachment_others"
                               placeholder="Specify here..."
                               value="{{ $attachmentOthers }}">
                    </div>
                    <div class="card mt-4">
                        <div class="card-body">
                            <label>Upload additional file/s (Optional)</label>
                            <div class="md-form">
                                <div class="file-field small">
                                    <div class="btn btn-outline-primary btn-sm float-left waves-effect">
                                        <span><i class="fas fa-cloud-upload-alt"></i> Choose files</span>
                                        <input type="file" multiple>
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input class="file-path validate" type="text" placeholder="Upload one or more files" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <hr>
    <div class="text-center mt-4">
        <button type="button" id="btn-create-update" type="submit" onclick="$(this).createUpdate();"
                class="btn waves-effect btn-block"></button>
    </div>
</form>