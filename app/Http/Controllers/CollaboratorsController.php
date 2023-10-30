<?php

namespace App\Http\Controllers;

use App\Classes\Services\CollaboratorsService;
use App\Classes\Support\HelperReturn;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollaboratorsController extends Controller
{
    protected CollaboratorsService $collaboratorsService;

    /**
     * Constructor of CollaboratorsService
     *
     * @return void
     */
    public function __construct(CollaboratorsService $collaboratorsService)
    {
        $this->collaboratorsService = $collaboratorsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            return HelperReturn::returnSuccess('collaborators', $this->collaboratorsService->list());
        } catch (Exception $e) {
            return HelperReturn::returnException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            return HelperReturn::returnSuccess('collaborator', $this->collaboratorsService->store($request), 'Colaborador cadastrado com sucesso!');
        } catch (Exception $th) {
            return HelperReturn::returnException($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            return response()->json($this->collaboratorsService->show($id));
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->collaboratorsService->destroy($id);

            return response()->json([
                'status' => 'success',
                'message' => 'O colaborador foi deletado com sucesso!',
            ]);
        } catch (Exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro ao deletar o colaborador!',
            ], 400);
        }
    }
}
