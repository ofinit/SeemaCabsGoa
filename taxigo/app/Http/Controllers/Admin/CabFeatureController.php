<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabColor;
use App\Models\CabModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CabFeatureController extends Controller
{
    public function index()
    {
        return view('settings.cab_models');
    }
    public function modelSave(Request $request)
    {
        try {
            $request->validate([
                'model_name' => 'required|string|max:255',
            ]);
            $cabColor = CabModel::where('name', $request->color_name)->first();
            if ($cabColor && $request->model_id != $cabColor->id) {
                return redirect()->route('admin.setting.cab.index')->with('error', 'Model already exists.');
            }
            CabModel::updateOrCreate(
                ['id' => $request->model_id],
                [
                    'name' => $request->model_name,
                ]
            );

            return redirect()->route('admin.setting.cab.index')->with('success', 'Model saved successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving model: ' . $e->getMessage());
            return redirect()->route('admin.setting.cab.index')->with('error', 'An error occurred while saving the model.');
        }
    }

    public function modelList(Request $request)
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
            $models = $query->skip($currentPage * $perPage)->take($perPage)->get();

            // Prepare data for DataTable
            $data = [];
            foreach ($models as $model) {
                $data[] = [
                    'name' => $model->name,
                    'action' => '<a href="#!" class="btn btn-sm btn-light-success me-1 edit" onclick="modelEdit(' . $model->id . ', \'' . addslashes($model->name) . '\')" data-id="' . $model->id . '"><i class="feather icon-edit"></i></a>
                                <a href="#!" class="btn btn-sm btn-light-danger delete sa-bs-error-ico" data-id="' . $model->id . '" onclick="confirmModelDelete(' . $model->id . ', \'' . addslashes($model->name) . '\', \'' . route('admin.setting.cab.model.delete', $model->id) . '\')"><i class="feather icon-trash-2"></i></a>',
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
            return response()->json(['error' => 'An error occurred while fetching the model list.'], 500);
        }
    }

    public function modelDelete($id)
    {
        try {
            $model = CabModel::find($id);

            if ($model) {
                $model->delete();
                return redirect()->route('admin.setting.cab.index')->with('success', 'Model deleted successfully.');
            }
            return redirect()->route('admin.setting.cab.index')->with('error', 'Model not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting model: ' . $e->getMessage());
            return redirect()->route('admin.setting.cab.index')->with('error', 'An error occurred while deleting the model.');
        }
    }
    public function colorSave(Request $request)
    {
        try {
            $request->validate([
                'color_name' => 'required|string|max:255',
            ]);
            $cabColor = CabColor::where('name', $request->color_name)->first();
            if ($cabColor && $request->color_id != $cabColor->id) {
                return redirect()->route('admin.setting.cab.index')->with('error', 'Color already exists.');
            }
            CabColor::updateOrCreate(
                ['id' => $request->color_id],
                [
                    'name' => $request->color_name,
                ]
            );

            return redirect()->route('admin.setting.cab.index')->with('success', 'Color saved successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving color: ' . $e->getMessage());
            return redirect()->route('admin.setting.cab.index')->with('error', 'An error occurred while saving the color.');
        }
    }
    public function colorList(Request $request)
    {
        try {
            $query = CabColor::query();

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
            $colors = $query->skip($currentPage * $perPage)->take($perPage)->get();

            // Prepare data for DataTable
            $data = [];
            foreach ($colors as $color) {
                $data[] = [
                    'name' => $color->name,
                    'action' => '<a href="#!" class="btn btn-sm btn-light-success me-1 edit" onclick="colorEdit(' . $color->id . ', \'' . addslashes($color->name) . '\')" data-id="' . $color->id . '"><i class="feather icon-edit"></i></a>
                                <a href="#!" class="btn btn-sm btn-light-danger delete sa-bs-error-ico" data-id="' . $color->id . '" onclick="confirmColorDelete(' . $color->id . ', \'' . addslashes($color->name) . '\', \'' . route('admin.setting.cab.color.delete', $color->id) . '\')"><i class="feather icon-trash-2"></i></a>',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching color list: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the color list.'], 500);
        }
    }
    public function colorDelete($id)
    {
        try {
            $color = CabColor::find($id);

            if ($color) {
                $color->delete();
                return redirect()->route('admin.setting.cab.index')->with('success', 'Color deleted successfully.');
            }
            return redirect()->route('admin.setting.cab.index')->with('error', 'Color not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting color: ' . $e->getMessage());
            return redirect()->route('admin.setting.cab.index')->with('error', 'An error occurred while deleting the color.');
        }
    }
}
