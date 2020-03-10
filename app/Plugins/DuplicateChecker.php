<?php

namespace App\Plugin;

class DuplicateChecker {
    protected $hasDuplicate = 0;

    public function __construct($_model, $_data) {
        switch ($_model) {
            case 'EmpDivision':
                $class = "App\Models\\" . $_model;
                $object = new $class();
                $hasDuplicate = $this->checkEmpDivision($object, $_data);
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
                $hasDuplicate = 0;
                break;
        }

        $this->hasDuplicate = $hasDuplicate;
    }

    public function getResult() {
        return $this->hasDuplicate;
    }

    private function checkEmpDivision($model, $data) {
        $dataCount = $model::where('division_name', $data)
                            ->orWhere('division_name', strtolower($data))
                            ->orWhere('division_name', strtoupper($data))
                            ->count();
        return ($dataCount > 0) ? 1 : 0;
    }
}
