<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmpAccount;
use App\Models\Supplier;
use App\Models\CustomPayee;
use DB;

class CollectionController extends Controller
{
    public function getCustomPayees(Request $request) {
        $keyword = trim($request->search);
        $empData = EmpAccount::select(
            DB::raw("CONCAT(firstname, ' ', lastname, ' [ ', position, ' ]') as name"),
            'id'
        );
        $supplierData = Supplier::select(
            DB::raw("CONCAT(company_name, ' ', ' [ Registered Supplier ]') as company_name"),
            'id'
        );
        $customPayee = CustomPayee::select(
            DB::raw("CONCAT(payee_name, ' [ Manually Added ]') as payee_name"),
            'id'
        );

        $payees = [];

        if ($keyword) {
            $empData = $empData->where(function($qry) use ($keyword) {
                $qry->where('firstname', 'like', "%$keyword%")
                    ->orWhere('middlename', 'like', "%$keyword%")
                    ->orWhere('lastname', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('firstname', 'like', "%$tag%")
                            ->orWhere('middlename', 'like', "%$tag%")
                            ->orWhere('lastname', 'like', "%$tag%");
                    }
                }
            });
            $supplierData = $supplierData->where(function($qry) use ($keyword) {
                $qry->where('company_name', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('company_name', 'like', "%$tag%");
                    }
                }
            });
            $customPayee = $customPayee->where(function($qry) use ($keyword) {
                $qry->where('payee_name', 'like', "%$keyword%");
                $keywords = explode('/\s+/', $keyword);

                if (count($keywords) > 0) {
                    foreach ($keywords as $tag) {
                        $qry->orWhere('payee_name', 'like', "%$tag%");
                    }
                }
            });
        }

        $empData = $empData->orderBy('firstname')->get();
        $supplierData = $supplierData->orderBy('company_name')->get();
        $customPayee = $customPayee->orderBy('payee_name')->get();

        foreach ($empData as $emp) {
            $payees[] = (object) [
                'id' => $emp->id,
                'payee_name' => $emp->name
            ];
        }

        foreach ($supplierData as $bid) {
            $payees[] = (object) [
                'id' => $bid->id,
                'payee_name' => $bid->company_name
            ];
        }

        foreach ($customPayee as $pay) {
            $payees[] = (object) [
                'id' => $pay->id,
                'payee_name' => $pay->payee_name
            ];
        }

        return response()->json($payees);
    }
}
