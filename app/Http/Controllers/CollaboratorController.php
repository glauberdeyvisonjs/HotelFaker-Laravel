<?php

namespace App\Http\Controllers;

use App\Classes\Services\CollaboratorService;
use App\Classes\Support\HelperReturn;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    protected CollaboratorService $collaboratorService;

    /**
     * Constructor of CollaboratorService
     *
     * @return void
     */
    public function __construct(CollaboratorService $collaboratorService)
    {
        $this->collaboratorService = $collaboratorService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            return HelperReturn::returnSuccess('collaborators', $this->collaboratorService->list());
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
            return HelperReturn::returnSuccess('collaborator', $this->collaboratorService->store($request), 'Colaborador cadastrado com sucesso!');
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
            return HelperReturn::returnSuccess('collaborator', $this->collaboratorService->show($id));
        } catch (Exception $e) {
            return HelperReturn::returnException($e);
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
        // TODO: Implement update() method.
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
            $this->collaboratorService->destroy($id);

            return HelperReturn::returnSuccess('collaborator', null, 'Colaborador deletado com sucesso!');
        } catch (Exception $e) {
            return HelperReturn::returnException($e);
        }
    }
}
