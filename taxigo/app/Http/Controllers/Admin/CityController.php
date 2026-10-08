<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
{
    public function index()
    {
        return view('settings.manage-city');
    }

    public function list(Request $request)
    {
        try {
            $query = City::query();

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
            $cities = $query->skip($currentPage * $perPage)->take($perPage)->get();

            // Prepare data for DataTable
            $data = [];
            foreach ($cities as $city) {
                $data[] = [
                    'name' => $city->name,
                    'action' => '<a href="#!" class="btn btn-sm btn-light-success me-1 edit" onclick="edit(' . $city->id . ', \'' . addslashes($city->name) . '\')" data-id="' . $city->id . '"><i class="feather icon-edit"></i></a>
                                <a href="#!" class="btn btn-sm btn-light-danger delete sa-bs-error-ico" data-id="' . $city->id . '" onclick="confirmDelete(' . $city->id . ', \'' . addslashes($city->name) . '\', \'' . route('admin.setting.city.delete', $city->id) . '\')"><i class="feather icon-trash-2"></i></a>',
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching city list: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the city list.'], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            $request->validate([
                'city_name' => 'required|string|max:255',
                'city_id' => 'nullable|integer|exists:cities,id',
            ]);

            $city = City::where('name', $request->city_name)->first();
            if ($city && $request->city_id != $city->id) {
                return redirect()->back()->with('error', 'City already exists.');
            }

            City::updateOrCreate(
                ['id' => $request->city_id],
                [
                    'name' => $request->city_name,
                    'state_id' => 1,
                ]
            );

            return redirect()->back()->with('success', 'City saved successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving city: ' . $e->getMessage());
            return redirect()->back()->with('error', 'something went wrong please try again.');
        }
    }

    public function delete($id)
    {
        try {
            $city = City::find($id);

            if ($city) {
                $city->delete();
                return redirect()->back()->with('success', 'City deleted successfully.');
            }

            return redirect()->back()->with('error', 'City not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting city: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the city.');
        }
    }
}
