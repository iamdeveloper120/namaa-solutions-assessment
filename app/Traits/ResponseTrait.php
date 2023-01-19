<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

trait ResponseTrait {

    protected bool $success = true;
    protected string $message = 'OK';
    protected int $status = 200;
    protected $data = null;
    protected array $response = [];
    protected array $errors = [];
    protected array $error = [];
    protected array $exception = [];
    protected array $information = [];


    /**
     * @return JsonResponse
     */
    public function apiResponse(): JsonResponse
    {
        $this->response['success'] = $this->success;
        $this->response['message'] = $this->message;
        $this->response['status'] = $this->status;
        if(count($this->errors) > 0) {
            $this->response['errors'] = $this->errors;
        }
        if(count($this->information) > 0) {
            $this->response['information'] = $this->information;
        }
        if($this->data) {
            $this->response['data'] = $this->beforeResponse($this->data);
        }
        if(isset($this->error['message'])){
            $this->response['error'] = $this->error;
        }
        return response()->json($this->response, $this->status);
    }

    protected function beforeResponse($data): array
    {
        if(!$data instanceof Collection){
            $data = collect($data);
        }
        return array_filter($data->toArray(), function ($value) {
            return !is_null($value)
                && $value !== ''
                && $value !== []
                && $value !== array();
        });
    }
}
