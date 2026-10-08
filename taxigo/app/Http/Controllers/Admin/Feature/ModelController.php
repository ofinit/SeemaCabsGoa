<?php

namespace App\Http\Controllers\Admin\Feature;

use App\Http\Controllers\Controller;
use App\Models\CabModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModelController extends Controller
{
    public function index()
    {
        return view('settings.cab_models');
    }

    public function list(Request $request)
    {
        try {
            $query = CabModel::query();

            if ($request->has('search') && $request->search['value'] != '') {
                $searchValue = $request->search['value'];
                $query->where('name', 'like', "%{$searchValue}%");
            }

            $totalRecords = $query->count();

            if ($request->order) {
                $columnIndex = $request->order[0]['column'];
                $columnName = $request->columns[$columnIndex]['data'];
                $direction = $request->order[0]['dir'];
                $query->orderBy($columnName, $direction);
            }

            $perPage = $request->length;
            $currentPage = $request->start / $perPage;
            $models = $query
                ->skip($currentPage * $perPage)
                ->take($perPage)
                ->get();

            // Prepare data for DataTable
            $data = [];
            foreach ($models as $model) {
                $data[] = [
                    'name' => $model->name,
                    'action' =>
                        '<span class="btn btn-sm btn-light-success me-1 edit" onclick="modelEdit(' .
                        $model->id .
                        ', \'' .
                        addslashes($model->name) .
                        '\')" data-id="' .
                        $model->id .
                        '"><i class="feather icon-edit"></i></span>
                                <span class="btn btn-sm btn-light-danger delete sa-bs-error-ico" data-id="' .
                        $model->id .
                        '" onclick="confirmModelDelete(' .
                        $model->id .
                        ', \'' .
                        addslashes($model->name) .
                        '\', \'' .
                        route('admin.setting.cab.model.delete', $model->id) .
                        '\')"><i class="feather icon-trash-2"></i></span>',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching model list: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong, Please try again latter.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'model_name' => 'required|string|max:255',
            ]);
            $cabColor = CabModel::where('name', $request->color_name)->first();
            if ($cabColor && $request->model_id != $cabColor->id) {
                return redirect()->back()->with('error', 'Model already exists.');
            }
            CabModel::updateOrCreate(
                ['id' => $request->model_id],
                [
                    'name' => $request->model_name,
                ],
            );

            return redirect()->back()->with('success', 'Model saved successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving model: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong, Please try again latter.');
        }
    }

    public function destroy($id)
    {
        try {
            $model = CabModel::find($id);

            if ($model) {
                $model->delete();
                return redirect()->route('admin.setting.cab.index')->with('success', 'Model deleted successfully.');
            }
            return redirect()->back()->with('error', 'Model not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting model: ' . $e->getMessage());
            return redirect()->route('admin.setting.cab.index')->with('error', 'Something went wrong, Please try again latter.');
        }
    }
}
