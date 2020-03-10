<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Province;

class PlaceController extends Controller
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
     *  Region Module
    **/
    public function indexRegion(Request $request) {
        $regionData = Region::orderBy('region_name')
                            ->get();

        return view('modules.library.region.index', [
            'list' => $regionData
        ]);
    }

    public function showCreateRegion() {
        return view('modules.library.region.create');
    }

    public function showEditRegion($id) {
        $regionData = Region::find($id);
        $region = $regionData->region_name;

        return view('modules.library.region.update', [
            'id' => $id,
            'region' => $region
        ]);
    }

    public function storeRegion(Request $request) {
        $regionName = $request->region_name;

        try {
            if (!$this->checkDuplication('Region', $regionName)) {
                $instanceRegion = new Region;
                $instanceRegion->region_name = $regionName;
                $instanceRegion->save();

                $msg = "Region '$regionName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Region '$regionName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateRegion(Request $request, $id) {
        $regionName = $request->region_name;

        try {
            $instanceRegion = Region::find($id);
            $instanceRegion->region_name = $regionName;
            $instanceRegion->save();

            $msg = "Region '$regionName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteRegion($id) {
        try {
            $instanceRegion = Region::find($id);
            $regionName = $instanceRegion->region_name;
            $instanceRegion->delete();

            $msg = "Region '$regionName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyRegion($id) {
        try {
            $instanceRegion = Region::find($id);
            $regionName = $instanceRegion->region_name;
            $instanceRegion->destroy();

            $msg = "Region '$regionName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    /**
     *  Province Module
    **/
    public function indexProvince(Request $request) {
        $provinceData = Province::addSelect([
            'region_name' => Region::select('region_name')
                                   ->whereColumn('id', 'provinces.region')
                                   ->limit(1)
        ])->orderBy('province_name')->get();

        return view('modules.library.province.index', [
            'list' => $provinceData
        ]);
    }

    public function showCreateProvince() {
        $regionData = Region::select('region_name', 'id')
                            ->orderBy('region_name')
                            ->get();

        return view('modules.library.province.create', [
            'regions' => $regionData
        ]);
    }

    public function showEditProvince($id) {
        $regionData = Region::select('region_name', 'id')
                            ->orderBy('region_name')
                            ->get();
        $provinceData = Province::find($id);
        $region = $provinceData->region;
        $provinceName = $provinceData->province_name;

        return view('modules.library.province.update', [
            'id' => $id,
            'provinceName' => $provinceName,
            'region' => $region,
            'regions' => $regionData
        ]);
    }

    public function storeProvince(Request $request) {
        $provinceName = $request->province_name;
        $region = $request->region;

        try {
            if (!$this->checkDuplication('Province', $provinceName)) {
                $instanceProvince = new Province;
                $instanceProvince->province_name = $provinceName;
                $instanceProvince->region = $region;
                $instanceProvince->save();

                $msg = "Province '$provinceName' successfully created.";
                return redirect(url()->previous())->with('success', $msg);
            } else {
                $msg = "Province '$provinceName' has a duplicate.";
                return redirect(url()->previous())->with('warning', $msg);
            }
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function updateProvince(Request $request, $id) {
        $provinceName = $request->province_name;
        $region = $request->region;

        try {
            $instanceProvince = Province::find($id);
            $instanceProvince->province_name = $provinceName;
            $instanceProvince->region = $region;
            $instanceProvince->save();

            $msg = "Province '$provinceName' successfully created.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function deleteProvince($id) {
        try {
            $instanceProvince = Province::find($id);
            $provinceName = $instanceProvince->province_name;
            $instanceProvince->delete();

            $msg = "Province '$provinceName' successfully deleted.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function destroyProvince($id) {
        try {
            $instanceProvince = Province::find($id);
            $provinceName = $instanceProvince->province_name;
            $instanceProvince->destroy();

            $msg = "Province '$provinceName' successfully destroyed.";
            return redirect(url()->previous())->with('success', $msg);
        } catch (\Throwable $th) {
            $msg = "Unknown error has occured. Please try again.";
            return redirect(url()->previous())->with('failed', $msg);
        }
    }

    public function checkDuplication($model, $data) {
        switch ($model) {
            case 'Region':
                $dataCount = Region::where('region_name', $data)
                                   ->orWhere('region_name', strtolower($data))
                                   ->orWhere('region_name', strtoupper($data))
                                   ->count();
                break;
            case 'Province':
                $dataCount = Province::where('province_name', $data)
                                     ->orWhere('province_name', strtolower($data))
                                     ->orWhere('province_name', strtoupper($data))
                                     ->count();
                break;
            default:
                $dataCount = 0;
                break;
        }

        return ($dataCount > 0) ? 1 : 0;;
    }
}
