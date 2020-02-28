<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseRequest;
use App\Abstracts;
use App\AbstractItem;

use App\User;
use App\Models\EmpDivision;
use App\Models\EmpGroup;
use App\Models\EmpRole;
use App\Models\FundingSource;
use App\Models\InventoryClassification;
use App\Models\ItemClassification;
use App\Models\ItemUnitIssue;
use App\Models\PaperSize;
use App\Models\ProcurementMode;
use App\Models\Province;
use App\Models\Region;
use App\Models\Signatory;
use App\Models\Supplier;
use App\Models\SupplierClassification;

use DB;
use Auth;

class LibrariesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     *  Employee Diviision Module
    **/
    public function indexDivision(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $empDivData = new EmpDivision;

        if (!empty($search)) {
            $empDivData = $empDivData::where('division_name', 'LIKE', '%' . $search . '%');
        }

        $empDivData = $empDivData::paginate($pageLimit);

        return view('modules.library.division.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $empDivData
        ]);
    }

    public function showCreateDivision() {

    }

    public function showEditDivision($id) {

    }

    public function storeDivision(Request $request) {

    }

    public function updateDivision(Request $request, $id) {

    }

    public function deleteDivision($id) {

    }

    public function destroyDivision($id) {

    }

    /**
     *  Region Module
    **/
    public function indexRegion(Request $request) {

    }

    public function showCreateRegion() {

    }

    public function showEditRegion($id) {

    }

    public function storeRegion(Request $request) {

    }

    public function updateRegion(Request $request, $id) {

    }

    public function deleteRegion($id) {

    }

    public function destroyRegion($id) {

    }

    /**
     *  Province Module
    **/
    public function indexProvince(Request $request) {

    }

    public function showCreateProvince() {

    }

    public function showEditProvince($id) {

    }

    public function storeProvince(Request $request) {

    }

    public function updateProvince(Request $request, $id) {

    }

    public function deleteProvince($id) {

    }

    public function destroyProvince($id) {

    }

    /**
     *  Employee Role Module
    **/
    public function indexRole(Request $request) {

    }

    public function showCreateRole() {

    }

    public function showEditRole($id) {

    }

    public function storeRole(Request $request) {

    }

    public function updateRole(Request $request, $id) {

    }

    public function deleteRole($id) {

    }

    public function destroyRole($id) {

    }

    /**
     *  Employee Acount Module
    **/
    public function indexAccount(Request $request) {

    }

    public function showCreateAccount() {

    }

    public function showEditAccount($id) {

    }

    public function storeAccount(Request $request) {

    }

    public function updateAccount(Request $request, $id) {

    }

    public function deleteAccount($id) {

    }

    public function destroyAccount($id) {

    }

    /**
     *  Employee Group Module
    **/
    public function indexGroup(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $userGroupList = DB::table('tblemp_groups as group')
                           ->select('group.id as group_id', 'group.group_name',
                                    DB::raw("CONCAT(emp.firstname, ' ', emp.lastname,
                                            '[ ', emp.position, ' ]') as group_head"))
                           ->join('tblemp_accounts as emp', 'emp.id', '=', 'group.group_head');

        if (!empty($search)) {
            $userGroupList = $userGroupList->where(function ($query)  use ($search) {
                                    $query->where('emp.emp_id', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%')
                                          ->orWhere('emp.position', 'LIKE', '%' . $search . '%')
                                          ->orWhere('group.group_name', 'LIKE', '%' . $search . '%');
                                });
        }

        $userGroupList = $userGroupList->orderBy('group.group_name')
                       ->paginate($pageLimit);

        return view('modules.library.group.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $userGroupList
        ]);
    }

    public function showCreateGroup() {

    }

    public function showEditGroup($id) {

    }

    public function storeGroup(Request $request) {

    }

    public function updateGroup(Request $request, $id) {

    }

    public function deleteGroup($id) {

    }

    public function destroyGroup($id) {

    }

    /**
     *  Item Classification Module
    **/
    public function indexItemClassification(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $itemClassData = new ItemClassification;

        if (!empty($search)) {
            $itemClassData = $itemClassData::where('classification_name', 'LIKE', '%' . $search . '%');
        }

        $itemClassData = $itemClassData::paginate($pageLimit);

        return view('modules.library.item-classification.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $itemClassData
        ]);
    }

    public function showCreateItemClassification() {

    }

    public function showEditItemClassification($id) {

    }

    public function storeItemClassification(Request $request) {

    }

    public function updateItemClassification(Request $request, $id) {

    }

    public function deleteItemClassification($id) {

    }

    public function destroyItemClassification($id) {

    }

    /**
     *  Funding Source Module
    **/
    public function indexFundingSource(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $fundingData = new FundingSource;

        if (!empty($search)) {
            $fundingData = $fundingData::where('source_name', 'LIKE', '%' . $search . '%')
                                       ->orWhere('reference_code', 'LIKE', '%' . $search . '%')
                                       ->paginate($pageLimit);
        }

        $fundingData = $fundingData::paginate($pageLimit);

        return view('modules.library.funding-source.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $fundingData
        ]);
    }

    public function showCreateFundingSource() {

    }

    public function showEditFundingSource($id) {

    }

    public function storeFundingSource(Request $request) {

    }

    public function updateFundingSource(Request $request, $id) {

    }

    public function deleteFundingSource($id) {

    }

    public function destroyFundingSource($id) {

    }

    /**
     *  Signatory Module
    **/
    public function indexSignatory(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $signatoryData = Signatory::select('signatories.id', 'signatories.position')->addSelect([
            'name' =>  User::select(DB::raw('CONCAT(firstname, " ", lastname) AS name'))
                           ->whereColumn('id', 'signatories.emp_id')
                           ->limit(1)
        ]);

        if (!empty($search)) {
            $signatoryData = $signatoryData->join('emp_accounts as emp', 'emp.id', '=', 'signatories.emp_id')
                                           ->where(function ($query) use ($search) {
                                 $query->where('signatories.position', 'LIKE', '%' . $search . '%')
                                       ->orWhere('emp.firstname', 'LIKE', '%' . $search . '%')
                                       ->orWhere('emp.middlename', 'LIKE', '%' . $search . '%')
                                       ->orWhere('emp.lastname', 'LIKE', '%' . $search . '%');
                             });
        }

        $signatoryData = $signatoryData->paginate($pageLimit);

        return view('modules.library.signatory.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $signatoryData
        ]);
    }

    public function showCreateSignatory() {

    }

    public function showEditSignatory($id) {

    }

    public function storeSignatory(Request $request) {

    }

    public function updateSignatory(Request $request, $id) {

    }

    public function deleteSignatory($id) {

    }

    public function destroySignatory($id) {

    }

    /**
     *  Supplier Classification Module
    **/
    public function indexSupplierClassification(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $supplierClassData = new SupplierClassification;

        if (!empty($search)) {
            $supplierClassData = $supplierClassData::where('classification', 'LIKE', '%' . $search . '%')
                                                   ->paginate($pageLimit);
        }

        $supplierClassData = $supplierClassData->paginate($pageLimit);

        return view('modules.library.supplier-classification.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $supplierClassData
        ]);
    }

    public function showCreateSupplierClassification() {

    }

    public function showEditSupplierClassification($id) {

    }

    public function storeSupplierClassification(Request $request) {

    }

    public function updateSupplierClassification(Request $request, $id) {

    }

    public function deleteSupplierClassification($id) {

    }

    public function destroySupplierClassification($id) {

    }

    /**
     *  Supplier Module
    **/
    public function indexSupplier(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $filter = $request['filter'];
        $supplierData = DB::table('tblsuppliers as bid')
                          ->select('bid.*', 'class.classification')
                          ->join('tblsupplier_classifications as class', 'class.id', '=', 'bid.class_id');
        $classifications = SupplierClassification::orderBy('classification')->get();

        if (!empty($search)) {
            $supplierData = $supplierData->where(function ($query)  use ($search) {
                                   $query->where('bid.company_name', 'LIKE', '%' . $search . '%')
                                         ->orWhere('bid.address', 'LIKE', '%' . $search . '%')
                                         ->orWhere('bid.contact_person', 'LIKE', '%' . $search . '%')
                                         ->orWhere('bid.mobile_no', 'LIKE', '%' . $search . '%')
                                         ->orWhere('class.classification', 'LIKE', '%' . $search . '%');
                               });
        }

        if (!empty($filter) && $filter != 0) {
            $supplierData = $supplierData->where('bid.class_id', '=', $filter);
        }

        $supplierData = $supplierData->orderBy('bid.company_name')
                                     ->paginate($pageLimit);

        return view('modules.library.supplier.index', ['search' => $search,
                                        'pageLimit' => $pageLimit,
                                        'list' => $supplierData,
                                        'classifications' => $classifications,
                                        'filter' => $filter]);
    }

    public function showCreateSupplier() {

    }

    public function showEditSupplier($id) {

    }

    public function storeSupplier(Request $request) {

    }

    public function updateSupplier(Request $request, $id) {

    }

    public function deleteSupplier($id) {

    }

    public function destroySupplier($id) {

    }

    /**
     *  Item Unit Issue Module
    **/
    public function indexUnitissue(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $unitIssueData = new ItemUnitIssue;

        if (!empty($search)) {
            $unitIssueData = $unitIssueData::where('unit', 'LIKE', '%' . $search . '%');
        }
        $unitIssueData = $unitIssueData->paginate($pageLimit);

        return view('modules.library.unit-issue.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $unitIssueData
        ]);
    }

    public function showCreateUnitissue() {

    }

    public function showEditUnitissue($id) {

    }

    public function storeUnitissue(Request $request) {

    }

    public function updateUnitissue(Request $request, $id) {

    }

    public function deleteUnitissue($id) {

    }

    public function destroyUnitissue($id) {

    }

    /**
     *  Procurement Mode Module
    **/
    public function indexProcurementMode(Request $request) {
        $pageLimit = 25;
        $search = trim($request->search);
        $procModeData = new ProcurementMode;

        if (!empty($search)) {
            $procModeData = $procModeData::where('mode_name', 'LIKE', '%' . $search . '%')
                                         ->paginate($pageLimit);
        }

        $procModeData = $procModeData::paginate($pageLimit);

        return view('modules.library.procurement-mode.index', [
            'search' => $search,
            'pageLimit' => $pageLimit,
            'list' => $procModeData
        ]);
    }

    public function showCreateProcurementMode() {

    }

    public function showEditProcurementMode($id) {

    }

    public function storeProcurementMode(Request $request) {

    }

    public function updateProcurementMode(Request $request, $id) {

    }

    public function deleteProcurementMode($id) {

    }

    public function destroyProcurementMode($id) {

    }

    /**
     *  Inventory Stock Classification Module
    **/
    public function indexInventoryClassification(Request $request) {

    }

    public function showCreateInventoryClassification() {

    }

    public function showEditInventoryClassification($id) {

    }

    public function storeInventoryClassification(Request $request) {

    }

    public function updateInventoryClassification(Request $request, $id) {

    }

    public function deleteInventoryClassification($id) {

    }

    public function destroyInventoryClassification($id) {

    }

    /**
     *  Paper Size Module
    **/
    public function indexPaperSize(Request $request) {

    }

    public function showCreatePaperSize() {

    }

    public function showEditPaperSize($id) {

    }

    public function storePaperSize(Request $request) {

    }

    public function updatePaperSize(Request $request, $id) {

    }

    public function deletePaperSize($id) {

    }

    public function destroyPaperSize($id) {

    }

}
