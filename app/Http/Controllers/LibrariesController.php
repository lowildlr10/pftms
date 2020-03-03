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
        $empDivData = EmpDivision::orderBy('division_name')
                                 ->get();

        return view('modules.library.division.index', [
            'list' => $empDivData
        ]);
    }

    public function showCreateDivision() {
        return view('modules.library.division.create');
    }

    public function showEditDivision($id) {
        $divisionData = EmpDivision::find($id);
        $division = $divisionData->division_name;

        return view('modules.library.division.update', [
            'id' => $id,
            'division' => $division
        ]);
    }

    public function storeDivision(Request $request) {
        $divisionName = $request->division_name;

        try {
            if (!$this->checkDuplication('EmpDivision', $divisionName)) {
                $instanceEmpDiv = new EmpDivision;
                $instanceEmpDiv->division_name = $divisionName;
                $instanceEmpDiv->save();

                $msg = "Employee division '$divisionName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Employee division '$divisionName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateDivision(Request $request, $id) {
        $divisionName = $request->division_name;

        try {
            if (!$this->checkDuplication('EmpDivision', $divisionName)) {
                $instanceEmpDiv = EmpDivision::find($id);
                $instanceEmpDiv->division_name = $divisionName;
                $instanceEmpDiv->save();

                $msg = "Employee division '$divisionName' successfully updated.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Employee division '$divisionName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteDivision($id) {
        try {
            $instanceEmpDiv = EmpDivision::find($id);
            $divisionName = $instanceEmpDiv->division_name;
            $instanceEmpDiv->delete();

            $msg = "Employee division '$divisionName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyDivision($id) {
        try {
            $instanceEmpDiv = EmpDivision::find($id);
            $divisionName = $instanceEmpDiv->division_name;
            $instanceEmpDiv->destroy();

            $msg = "Employee division '$divisionName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
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
        $itemClassData = ItemClassification::orderBy('classification_name')
                                           ->get();

        return view('modules.library.item-classification.index', [
            'list' => $itemClassData
        ]);
    }

    public function showCreateItemClassification() {
        return view('modules.library.item-classification.create');
    }

    public function showEditItemClassification($id) {
        $itemClassData = ItemClassification::find($id);
        $classification = $itemClassData->classification_name;

        return view('modules.library.item-classification.update', [
            'id' => $id,
            'classification' => $classification
        ]);
    }

    public function storeItemClassification(Request $request) {
        $className = $request->classification_name;

        try {
            if (!$this->checkDuplication('ItemClassification', $className)) {
                $instanceItemClass = new ItemClassification;
                $instanceItemClass->classification_name = $className;
                $instanceItemClass->save();

                $msg = "Item classification '$className' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Item classification '$className' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateItemClassification(Request $request, $id) {
        $className = $request->classification_name;

        try {
            if (!$this->checkDuplication('ItemClassification', $className)) {
                $instanceItemClass = ItemClassification::find($id);
                $instanceItemClass->classification_name = $className;
                $instanceItemClass->save();

                $msg = "Item classification '$className' successfully updated.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Item classification '$className' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteItemClassification($id) {
        try {
            $instanceItemClass = ItemClassification::find($id);
            $className = $instanceItemClass->classification_name;
            $instanceItemClass->delete();

            $msg = "Item classification '$className' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyItemClassification($id) {
        try {
            $instanceItemClass = ItemClassification::find($id);
            $className = $instanceItemClass->classification_name;
            $instanceItemClass->destroy();

            $msg = "Item classification '$className' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Funding Source Module
    **/
    public function indexFundingSource(Request $request) {
        $fundingData = FundingSource::orderBy('source_name')
                                    ->get();

        return view('modules.library.funding.index', [
            'list' => $fundingData
        ]);
    }

    public function showCreateFundingSource() {
        return view('modules.library.funding.create');
    }

    public function showEditFundingSource($id) {
        $fundingData = FundingSource::find($id);
        $referenceCode = $fundingData->reference_code;
        $funding = $fundingData->source_name;

        return view('modules.library.funding.update', [
            'id' => $id,
            'referenceCode' => $referenceCode,
            'funding' => $funding
        ]);
    }

    public function storeFundingSource(Request $request) {
        $referenceCode = $request->reference_code;
        $sourceName = $request->source_name;

        try {
            if (!$this->checkDuplication('FundingSource', $sourceName)) {
                $instanceFundSrc = new FundingSource;
                $instanceFundSrc->reference_code = $referenceCode;
                $instanceFundSrc->source_name = $sourceName;
                $instanceFundSrc->save();

                $msg = "Funding source '$sourceName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Funding source '$sourceName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateFundingSource(Request $request, $id) {
        $referenceCode = $request->reference_code;
        $sourceName = $request->source_name;

        try {
            if (!$this->checkDuplication('FundingSource', $sourceName)) {
                $instanceFundSrc = FundingSource::find($id);
                $instanceFundSrc->reference_code = $referenceCode;
                $instanceFundSrc->source_name = $sourceName;
                $instanceFundSrc->save();

                $msg = "Funding source '$sourceName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Funding source '$sourceName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteFundingSource($id) {
        try {
            $instanceFundSrc = FundingSource::find($id);
            $sourceName = $instanceFundSrc->source_name;
            $instanceFundSrc->delete();

            $msg = "Funding source '$sourceName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyFundingSource($id) {
        try {
            $instanceFundSrc = FundingSource::find($id);
            $sourceName = $instanceFundSrc->source_name;
            $instanceFundSrc->destroy();

            $msg = "Funding source '$sourceName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
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
        $procModeData = ProcurementMode::orderBy('mode_name')
                                       ->get();

        return view('modules.library.procurement-mode.index', [
            'list' => $procModeData
        ]);
    }

    public function showCreateProcurementMode() {
        return view('modules.library.procurement-mode.create');
    }

    public function showEditProcurementMode($id) {
        $procModeData = ProcurementMode::find($id);
        $mode = $procModeData->mode_name;

        return view('modules.library.procurement-mode.update', [
            'id' => $id,
            'mode' => $mode
        ]);
    }

    public function storeProcurementMode(Request $request) {
        $modeName = $request->mode_name;

        try {
            if (!$this->checkDuplication('ProcurementMode', $modeName)) {
                $instanceProcMode = new ProcurementMode;
                $instanceProcMode->mode_name = $modeName;
                $instanceProcMode->save();

                $msg = "Procurement mode '$modeName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Procurement mode '$modeName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateProcurementMode(Request $request, $id) {
        $modeName = $request->mode_name;

        try {
            if (!$this->checkDuplication('ProcurementMode', $modeName)) {
                $instanceProcMode = ProcurementMode::find($id);
                $instanceProcMode->mode_name = $modeName;
                $instanceProcMode->save();

                $msg = "Procurement mode '$modeName' successfully updated.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Procurement mode '$modeName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteProcurementMode($id) {
        try {
            $instanceProcMode = ProcurementMode::find($id);
            $modeName = $instanceProcMode->mode_name;
            $instanceProcMode->delete();

            $msg = "Procurement mode '$modeName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyProcurementMode($id) {
        try {
            $instanceProcMode = EmpDivision::find($id);
            $modeName = $instanceProcMode->mode_name;
            $instanceProcMode->destroy();

            $msg = "Procurement mode '$modeName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
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

    public function checkDuplication($model, $data) {
        $hasDuplicate = 0;

        switch ($model) {
            case 'EmpDivision':
                $dataCount = EmpDivision::where('division_name', $data)
                                        ->orWhere('division_name', strtolower($data))
                                        ->orWhere('division_name', strtoupper($data))
                                        ->count();
                $hasDuplicate = ($dataCount > 0) ? 1 : 0;
                break;
            case 'Region':
                # code...
                break;
            case 'Province':
                # code...
                break;
            case 'EmpRole':
                # code...
                break;
            case 'EmpAccount':
                # code...
                break;
            case 'EmpGroup':
                # code...
                break;
            case 'ItemClassification':
                # code...
                break;
            case 'FundingSource':
                # code...
                break;
            case 'Signatory':
                # code...
                break;
            case 'SupplierClassification':
                # code...
                break;
            case 'Supplier':
                # code...
                break;
            case 'ItemUnitIssue':
                # code...
                break;
            default:
                # code...
                break;
        }

        return $hasDuplicate;
    }
}
