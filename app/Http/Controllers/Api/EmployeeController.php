<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Enums\EmployeeType;
use App\Repositories\EmployeeRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Imports\EmployeeImport;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    protected $repository;

    public function __construct(EmployeeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('view-master-data')) {
            abort(403, 'Unauthorized');
        }

        $Employee = $this->repository->getAll(
            $request->only('search', 'type', 'parent', 'status', 'sort', 'dir'),
            $request->get('per_page', 10)
        );

        return EmployeeResource::collection($Employee)->additional([
            'types' => EmployeeType::values(),
            'parents' => $this->repository->getActive(),
            'filters' => $request->only('search', 'type', 'parent', 'status', 'sort', 'dir', 'per_page'),
        ]);
    }

    public function activeEmployee()
    {
        return response()->json([
            'success' => true,
            'data' => $this->repository->getActive()
        ]);
    }

    public function EmployeeTypes()
    {

        $EmployeeTypes = EmployeeType::values();
        return response()->json([
            'success' => true,
            'data' => $EmployeeTypes
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048',
        ]);

        try {

            $import = new EmployeeImport;
            Excel::import($import, $request->file('file'));

            if ($import->getRowCount() === 0) {
                return response()->json([
                    'message' => 'Empty file! No record found.',
                ], 422);
            }

            return response()->json([
                'message' => 'Master Data imported successfully',
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->failures(), // gives row + column + error message
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Import failed due to server error. <br/>' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $Employee = $this->repository->findById($id);

        if (!$Employee) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return new EmployeeResource($Employee);
    }

    public function export()
    {
        return Excel::download(new EmployeeExport, 'master_data.xlsx');
    }

    public function store(StoreEmployeeRequest $request)
    {
        $Employee = $this->repository->create($request->validated());
        return new EmployeeResource($Employee);
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        $Employee = $this->repository->findById($id);
        $updated = $this->repository->update($Employee, $request->validated());
        return new EmployeeResource($updated);
    }

    public function destroy(string $id)
    {
        $this->repository->delete($id);
        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
    public function toggleStatus($id)
    {
        $Employee = $this->repository->findById($id);

        if (!$Employee) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $updated = $this->repository->toggleStatus($Employee);

        return new EmployeeResource($updated);
    }


    public function bulkDelete(Request $request)
    {
        $count = $this->repository->bulkDelete($request->ids);
        return response()->json(['success' => true, 'deleted' => $count]);
    }

    public function bulkToggle(Request $request)
    {
        $count = $this->repository->bulkToggle($request->ids);
        return response()->json(['success' => true, 'toggled' => $count]);
    }
}
