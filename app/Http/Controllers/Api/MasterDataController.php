<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MasterDataResource;
use App\Models\MasterData;
use App\Enums\MasterDataType;

use Illuminate\Http\Request;
use App\Http\Requests\StoreMasterDataRequest;
use App\Http\Requests\UpdateMasterDataRequest;

use App\Imports\MasterDataImport;
use Maatwebsite\Excel\Facades\Excel;


use App\Exports\MasterDataExport;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {

        if (!auth()->user()->can('view-master-data')) {
            abort(403, 'Unauthorized');
        }

        $query = MasterData::with('parent');

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->orWhere('type', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by type if provided
        if ($request->filled('parent')) {
            $query->where('parent_id', $request->input('parent'));
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $sort = $request->get('sort', 'id');
        $direction = $request->get('dir', 'desc');

        $query->orderBy($sort, $direction);

        // Paginate with query string
        $perPage = $request->get('per_page', 10);

        // $master_data = $query->paginate($perPage)->withQueryString();

        $masterData = $query->paginate($perPage)->withQueryString();
        return MasterDataResource::collection($masterData)->additional([
            'types' => MasterDataType::values(),
            'parents' => MasterData::all(),
            'filters' => $request->only('search', 'type', 'parent', 'status', 'sortBy', 'direction', 'perPage'),
        ]);
    }

    public function activeMasterData()
    {
        $masterData = MasterData::where('status', 1)->get();
        return response()->json([
            'success' => true,
            'data' => $masterData
        ]);
    }

    public function masterDataTypes()
    {

        $masterDataTypes = MasterDataType::values();
        return response()->json([
            'success' => true,
            'data' => $masterDataTypes
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048',
        ]);

        try {

            $import = new MasterDataImport;
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
                'message' => 'Import failed due to server error. <br/>'.$e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }



public function export()
{
    return Excel::download(new MasterDataExport, 'master_data.xlsx');
}


    public function create()
    {

        if (!auth()->user()->can('master_data.create')) {
            abort(403, 'Unauthorized');
        }
        $types = MasterDataType::values();
        $parents = MasterData::whereNull('parent_id')->get();

        // return Inertia::render('master_data/create',[
        //     'types' => $types,
        //     'parents' => $parents,
        // ]);
    }


    public function store(StoreMasterDataRequest $request)
    {
        $data = $request->validated();
        $masterData = MasterData::create($data);

        return new MasterDataResource($masterData);
    }


    public function show(MasterData $master_datum)
    {

        return new MasterDataResource($master_datum);
    }


    public function edit($id)
    {
        if (!auth()->user()->can('master_data.edit')) {
            abort(403, 'Unauthorized');
        }
        $master_data = MasterData::find($id);

        // return Inertia::render('master_data/edit', [
        //     'master_data' => $master_data,
        //     'types' => MasterDataType::values(),
        //     'parents' => MasterData::whereNull('parent_id')->where('id', '!=', $master_data->id)->get(),
        // ]);
    }


    public function update(UpdateMasterDataRequest $request, MasterData $master_datum)
    {
        $master_datum->update($request->validated());
        return new MasterDataResource($master_datum);
    }


    public function destroy(string $id)
    {
        if (!auth()->user()->can('master_data.delete')) {
            abort(403, 'Unauthorized');
        }
        MasterData::destroy($id);
        return redirect()->route('master_data.index')->with('success', 'Master Data deleted successfully.');
    }

    public function toggleStatus(MasterData $master_datum)
    {
        $master_datum->status = !$master_datum->status;
        $master_datum->save();

        return new MasterDataResource($master_datum);
    }


    public function bulkDelete(Request $request)
    {
        if (!auth()->user()->can('master_data.delete')) {
            abort(403, 'Unauthorized');
        }
        MasterData::whereIn('id', $request->ids)->delete();
        return back()->with('success', 'Selected items deleted.');
    }

    public function bulkToggle(Request $request)
    {
        if (!auth()->user()->can('master_data.edit')) {
            abort(403, 'Unauthorized');
        }
        $items = MasterData::whereIn('id', $request->ids)->get();
        foreach ($items as $item) {
            $item->status = !$item->status;
            $item->save();
        }
        return back()->with('success', 'Status toggled for selected items.');
    }

}
