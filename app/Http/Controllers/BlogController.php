<?php

namespace App\Http\Controllers;

use App\DataTables\BlogsDataTable;
use App\DataTables\SubscribersDataTable;
use App\Models\Blog;
use App\Models\Subscriber;
use App\Traits\ResponseTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BlogController extends Controller
{
    use ResponseTrait;

    public function index(BlogsDataTable $blogsDataTable, Request $request)
    {
        if ($request->ajax()) {
            $data = Blog::select('id', 'image', 'title', 'publish_date', 'status');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) { // create two action buttons
                    $btn = ' <a href="#" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#createBlog" class="edit btn btn-outline-success editRow">Edit</a>';
                    $btn .= ' <a href="#" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#deleteBlog" class="delete btn btn-outline-danger deleteRow">Delete</a> ';

                    return $btn;
                })->addColumn('image', function ($row) {
                    $url = URL::asset('/images/'.$row->image);
                    return '<img src="'.$url.'" border="0" width="40" class="img-rounded" align="center" />';
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
        return $blogsDataTable->render('blogs.blog');
    }

    public function blogListing(BlogsDataTable $dataTable, Request $request): Factory|View|Application
    {
        $blogs = Blog::all()->toArray();
        return view('blogs.index', ['blogs' => $blogs]);
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
            'title' => 'required',
            'content' => 'required',
            'publish_date' => 'required',
            'status' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            $this->status = 422;
            $this->message = $validator->errors()->first();
            return $this->apiResponse();
        }
        $data = [
            // 'image' => "http://via.placeholder.com/200x100",
            'title' => $request->title,
            'content' => $request->get('content'),
            'publish_date' => $request->publish_date,
            'status' => $request->status,
        ];
        $image = $request->file('image');
        $newName = mt_rand() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $newName);

        $data['image'] = $newName;

        if(!$request->id){
           // $data['password'] = Hash::make(".$request->password.");
        }
        $updateBlog = Blog::updateOrCreate(['id' => $request->id ?? null], $data);
        if ($updateBlog) {
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
        $blog = Blog::find($id);
        return response()->json($blog);
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
            $blog = Blog::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $this->status = 404;
            $this->message = $e->getMessage();
            return $this->apiResponse();
        }

        $blog->delete();
        $this->message = __('Resource deleted successfully');
        return $this->apiResponse();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return Application|Factory|View
     */
    public function blogDetailPage($id): Application|Factory|View
    {
        try {
            $blog = Blog::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
        return view('blogs.detail', ['blog' => $blog]);
    }
}
