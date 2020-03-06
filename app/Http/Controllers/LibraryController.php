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
use App\Plugin\DuplicateChecker;

class LibraryController extends Controller
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
            $instanceItemClass = ItemClassification::find($id);
            $instanceItemClass->classification_name = $className;
            $instanceItemClass->save();

            $msg = "Item classification '$className' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
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
            $instanceFundSrc = FundingSource::find($id);
            $instanceFundSrc->reference_code = $referenceCode;
            $instanceFundSrc->source_name = $sourceName;
            $instanceFundSrc->save();

            $msg = "Funding source '$sourceName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
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
        $supplierClassData = SupplierClassification::orderBy('classification_name')
                                                   ->get();

        return view('modules.library.supplier-classification.index', [
            'list' => $supplierClassData
        ]);
    }

    public function showCreateSupplierClassification() {
        return view('modules.library.supplier-classification.create');
    }

    public function showEditSupplierClassification($id) {
        $supplierClassData = SupplierClassification::find($id);
        $classification = $supplierClassData->classification_name;

        return view('modules.library.supplier-classification.update', [
            'id' => $id,
            'classification' => $classification
        ]);
    }

    public function storeSupplierClassification(Request $request) {
        $classificationName = $request->classification_name;

        try {
            if (!$this->checkDuplication('SupplierClassification', $classificationName)) {
                $instanceSupClass = new SupplierClassification;
                $instanceSupClass->classification_name = $classificationName;
                $instanceSupClass->save();

                $msg = "Supplier classification '$classificationName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Supplier classification '$classificationName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateSupplierClassification(Request $request, $id) {
        $classificationName = $request->classification_name;

        try {
            $instanceSupClass = SupplierClassification::find($id);
            $instanceSupClass->classification_name = $classificationName;
            $instanceSupClass->save();

            $msg = "Supplier classification '$classificationName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteSupplierClassification($id) {
        try {
            $instanceSupClass = SupplierClassification::find($id);
            $classificationName = $instanceSupClass->classification_name;
            $instanceSupClass->delete();

            $msg = "Supplier classification '$classificationName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroySupplierClassification($id) {
        try {
            $instanceSupClass = SupplierClassification::find($id);
            $classificationName = $instanceSupClass->classification_name;
            $instanceSupClass->destroy();

            $msg = "Supplier classification '$classificationName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
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
        $unitIssueData = ItemUnitIssue::orderBy('unit_name')
                                      ->get();

        return view('modules.library.unit-issue.index', [
            'list' => $unitIssueData
        ]);
    }

    public function showCreateUnitissue() {
        return view('modules.library.unit-issue.create');
    }

    public function showEditUnitissue($id) {
        $unitIssueData = ItemUnitIssue::find($id);
        $unit = $unitIssueData->unit_name;

        return view('modules.library.unit-issue.update', [
            'id' => $id,
            'unit' => $unit
        ]);
    }

    public function storeUnitissue(Request $request) {
        $unitName = $request->unit_name;

        try {
            if (!$this->checkDuplication('ItemUnitIssue', $unitName)) {
                $instanceUnitIssue = new ItemUnitIssue;
                $instanceUnitIssue->unit_name = $unitName;
                $instanceUnitIssue->save();

                $msg = "Unit of issue '$unitName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Unit of issue '$unitName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateUnitissue(Request $request, $id) {
        $unitName = $request->unit_name;

        try {
            $instanceUnitIssue = ItemUnitIssue::find($id);
            $instanceUnitIssue->unit_name = $unitName;
            $instanceUnitIssue->save();

            $msg = "Unit of issue '$unitName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteUnitissue($id) {
        try {
            $instanceUnitIssue = ItemUnitIssue::find($id);
            $unitName = $instanceUnitIssue->unit_name;
            $instanceUnitIssue->delete();

            $msg = "Unit of issue '$unitName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyUnitissue($id) {
        try {
            $instanceUnitIssue = ItemUnitIssue::find($id);
            $unitName = $instanceUnitIssue->unit_name;
            $instanceUnitIssue->destroy();

            $msg = "Unit of issue '$unitName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
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
            $instanceProcMode = ProcurementMode::find($id);
            $instanceProcMode->mode_name = $modeName;
            $instanceProcMode->save();

            $msg = "Procurement mode '$modeName' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
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
            $instanceProcMode = ProcurementMode::find($id);
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
        $inventoryData = InventoryClassification::orderBy('classification_name')
                                                ->get();

        return view('modules.library.inventory-classification.index', [
            'list' => $inventoryData
        ]);
    }

    public function showCreateInventoryClassification() {
        return view('modules.library.inventory-classification.create');
    }

    public function showEditInventoryClassification($id) {
        $inventoryData = InventoryClassification::find($id);
        $className = $inventoryData->classification_name;

        return view('modules.library.inventory-classification.update', [
            'id' => $id,
            'classification' => $className
        ]);
    }

    public function storeInventoryClassification(Request $request) {
        $className = $request->classification_name;

        try {
            if (!$this->checkDuplication('InventoryClassification', $className)) {
                $instanceInvClass = new InventoryClassification;
                $instanceInvClass->classification_name = $className;
                $instanceInvClass->save();

                $msg = "Inventory classification '$className' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Inventory classification '$className' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateInventoryClassification(Request $request, $id) {
        $className = $request->classification_name;

        try {
            $instanceInvClass = InventoryClassification::find($id);
            $instanceInvClass->classification_name = $className;
            $instanceInvClass->save();

            $msg = "Inventory classification '$className' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteInventoryClassification($id) {
        try {
            $instanceInvClass = InventoryClassification::find($id);
            $className = $instanceInvClass->classification_name;
            $instanceInvClass->delete();

            $msg = "Inventory classification '$className' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyInventoryClassification($id) {
        try {
            $instanceInvClass = InventoryClassification::find($id);
            $className = $instanceInvClass->classification_name;
            $instanceInvClass->destroy();

            $msg = "Inventory classification '$className' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Paper Size Module
    **/
    public function indexPaperSize(Request $request) {
        $paperSizeData = PaperSize::orderBy('paper_type')
                                  ->get();

        return view('modules.library.paper-size.index', [
            'list' => $paperSizeData
        ]);
    }

    public function showCreatePaperSize() {
        return view('modules.library.paper-size.create');
    }

    public function showEditPaperSize($id) {
        $paperSizeData = PaperSize::find($id);
        $paperType = $paperSizeData->paper_type;
        $unit = $paperSizeData->unit;
        $width = $paperSizeData->width;
        $height = $paperSizeData->height;

        return view('modules.library.paper-size.update', [
            'id' => $id,
            'paperType' => $paperType,
            'unit' => $unit,
            'width' => $width,
            'height' => $height
        ]);
    }

    public function storePaperSize(Request $request) {
        $paperType = $request->paper_type;
        $unit = $request->unit;
        $width = $request->width;
        $height = $request->height;

        try {
            if (!$this->checkDuplication('PaperSize', $paperType)) {
                $instancePaperSize = new PaperSize;
                $instancePaperSize->paper_type = $paperType;
                $instancePaperSize->unit = $unit;
                $instancePaperSize->width = $width;
                $instancePaperSize->height = $height;
                $instancePaperSize->save();

                $msg = "Paper size '$paperType' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Paper size '$paperType' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updatePaperSize(Request $request, $id) {
        $paperType = $request->paper_type;
        $unit = $request->unit;
        $width = $request->width;
        $height = $request->height;

        try {
            $instancePaperSize = PaperSize::find($id);
            $instancePaperSize->paper_type = $paperType;
                $instancePaperSize->unit = $unit;
                $instancePaperSize->width = $width;
                $instancePaperSize->height = $height;
            $instancePaperSize->save();

            $msg = "Paper size '$paperType' successfully updated.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deletePaperSize($id) {
        try {
            $instancePaperSize = PaperSize::find($id);
            $paperType = $instancePaperSize->paper_type;
            $instancePaperSize->delete();

            $msg = "Paper size '$paperType' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyPaperSize($id) {
        try {
            $instancePaperSize = PaperSize::find($id);
            $paperType = $instancePaperSize->paper_type;
            $instancePaperSize->destroy();

            $msg = "Paper size '$paperType' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function checkDuplication($model, $data) {
        switch ($model) {
            case 'EmpDivision':
                $dataCount = EmpDivision::where('division_name', $data)
                                        ->orWhere('division_name', strtolower($data))
                                        ->orWhere('division_name', strtoupper($data))
                                        ->count();
                break;
            case 'ItemClassification':
                $dataCount = ItemClassification::where('classification_name', $data)
                                               ->orWhere('classification_name', strtolower($data))
                                               ->orWhere('classification_name', strtoupper($data))
                                               ->count();
                break;
            case 'ProcurementMode':
                $dataCount = ProcurementMode::where('mode_name', $data)
                                        ->orWhere('mode_name', strtolower($data))
                                        ->orWhere('mode_name', strtoupper($data))
                                        ->count();
                break;
            case 'FundingSource':
                $dataCount = FundingSource::where('source_name', $data)
                                        ->orWhere('source_name', strtolower($data))
                                        ->orWhere('source_name', strtoupper($data))
                                        ->count();
                break;
            case 'Signatory':
                $dataCount = Signatory::where('division_name', $data)
                                        ->orWhere('division_name', strtolower($data))
                                        ->orWhere('division_name', strtoupper($data))
                                        ->count();
                break;
            case 'SupplierClassification':
                $dataCount = SupplierClassification::where('classification_name', $data)
                                        ->orWhere('classification_name', strtolower($data))
                                        ->orWhere('classification_name', strtoupper($data))
                                        ->count();
                break;
            case 'Supplier':
                $dataCount = Supplier::where('division_name', $data)
                                     ->orWhere('division_name', strtolower($data))
                                     ->orWhere('division_name', strtoupper($data))
                                     ->count();
                break;
            case 'ItemUnitIssue':
                $dataCount = ItemUnitIssue::where('unit_name', $data)
                                        ->orWhere('unit_name', strtolower($data))
                                        ->orWhere('unit_name', strtoupper($data))
                                        ->count();
                break;
            default:
                $dataCount = 0;
                break;
        }

        return ($dataCount > 0) ? 1 : 0;;
    }
}
