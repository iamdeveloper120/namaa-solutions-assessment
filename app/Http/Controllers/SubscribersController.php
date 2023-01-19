<?php

namespace App\Http\Controllers;

use App\DataTables\SubscribersDataTable;
use App\Models\Subscriber;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SubscribersController extends Controller
{
    use ResponseTrait;

    public function index(SubscribersDataTable $dataTable, Request $request)
    {
        if ($request->ajax()) {
            $data = Subscriber::select('id', 'name', 'username', 'password', 'status');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) { // create two action buttons
                    $btn = ' <a href="#" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#createSubscriber" class="edit btn btn-outline-success editRow">Edit</a>';
                    $btn .= ' <a href="#" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#deleteSubscriber" class="delete btn btn-outline-danger deleteRow">Delete</a> ';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return $dataTable->render('subscribers.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:subscribers,username,'.$request->id,
            'password' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            $this->status = 422;
            $this->message = $validator->errors()->first();
            return $this->apiResponse();
        }
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'status' => $request->status,
        ];
        if(!$request->id){
            $data['password'] = Hash::make(".$request->password.");
        }
        $updateSubscriber = Subscriber::updateOrCreate(['id' => $request->id ?? null], $data);
        if ($updateSubscriber) {
            $this->message = __('Updated Subscriber successfully');
            return $this->apiResponse();
        }
        return $this->apiResponse();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return JsonResponse
     */
    public function edit($id): JsonResponse
    {
        $subscriber = Subscriber::find($id);
        return response()->json($subscriber);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $subscriber = Subscriber::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $this->status = 404;
            $this->message = $e->getMessage();
            return $this->apiResponse();
        }

        $subscriber->delete();
        $this->message = __('Resource deleted successfully');
        return $this->apiResponse();
    }
}
