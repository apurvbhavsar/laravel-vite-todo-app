<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth()->user();
        $data = Todo::query()->where('user_id', $user->id)->orderBy('status');
        if(!(int)request()->get('all')) {
            $data->where('status', 0);
        }
        return $this->success($data->get(), 'User todo list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = Todo::where('user_id', $request->user()->id)->where('title', $request->title);
        if ($data->first()) {
            return $this->error('Already exist');
        }
        $req = $request->all();
        $req['user_id'] = $request->user()->id;
        $data = Todo::create($req);
        return $this->success($data, 'New todo added', 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validateUser = Validator::make($request->all(),
            [
                'status' => 'required',
            ]);

        if ($validateUser->fails()) {
            return $this->validationError($validateUser->errors(), 'validation error');
        }

        $data = Todo::find($id);
        $data->status = $request->status;
        $data->update();
        return $this->success($data, 'Todo updated', 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        throw_if(!$id, 'todo Id is missing');
        Todo::findOrFail($id)->delete();
        return $this->success([], 'Todo deleted');
    }
}
