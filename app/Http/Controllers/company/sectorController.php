<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\sectors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role;

class sectorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->user()->cannot('MANAGE_SECTOR', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SECTOR'));
        }

        $search = trim((string) $request->get('search', ''));

        $sectors = sectors::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('sectorName', 'LIKE', '%' . $search . '%')
                        ->orWhere('sectorDescription', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($request->filled('status') && in_array((string) $request->status, ['0', '1'], true), function ($query) use ($request) {
                $query->where('sectorStatus', (int) $request->status);
            })
            ->orderBy('sectorName')
            ->paginate(config('global.DATA_LIMIT'), [
                'sectorID',
                'sectorName',
                'sectorDescription',
                'sectorStatus',
            ])
            ->withQueryString();

        return view('company.sector', compact('sectors', 'search'));
    }

    public function create(Request $request)
    {
        $search = $request->get('q');
        $data = sectors::query()
            ->when($search, function ($query) use ($search) {
                $query->where('sectorName', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('sectorName')
            ->get(['sectorID', 'sectorName']);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('MANAGE_SECTOR', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SECTOR'));
        }

        $validator = Validator::make($request->all(), [
            'sector_name' => 'required|string|max:255|unique:sectors,sectorName',
            'sectorDescription' => 'nullable|string|max:2000',
            'sectorImage' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->with('fail', $validator->errors()->first());
        }

        try {
            $imageName = $this->storeSectorImage($request);

            sectors::create([
                'sectorID' => Str::uuid(),
                'userID' => Auth::user()->user_uuid,
                'sectorName' => trim($request->sector_name),
                'sectorDescription' => $request->sectorDescription,
                'sectorImage' => $imageName,
                'sectorStatus' => 1,
            ]);

            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()
                ->withInput()
                ->with('fail', config('Messages.500'));
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('MANAGE_SECTOR', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('MANAGE_SECTOR'));
        }

        $sector = sectors::find($id);
        if (! $sector) {
            return back()->with('fail', 'Sector not found.');
        }

        $validator = Validator::make($request->all(), [
            'sector_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sectors', 'sectorName')->ignore($id, 'sectorID'),
            ],
            'sectorDescription' => 'nullable|string|max:2000',
            'sectorStatus' => 'required|in:0,1',
            'sectorImage' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->with('fail', $validator->errors()->first());
        }

        try {
            $imageName = $sector->sectorImage;

            if ($request->hasFile('sectorImage')) {
                $this->deleteSectorImage($imageName);
                $imageName = $this->storeSectorImage($request);
            }

            $sector->update([
                'userID' => Auth::user()->user_uuid,
                'sectorName' => trim($request->sector_name),
                'sectorDescription' => $request->sectorDescription,
                'sectorImage' => $imageName,
                'sectorStatus' => (int) $request->sectorStatus,
                'updated_at' => now(),
            ]);

            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('DELETE_SECTOR', Role::class)) {
            return back()->with('fail', $this->permissionDeniedMessage('DELETE_SECTOR'));
        }

        try {
            $sector = sectors::find($id);
            if (! $sector) {
                return back()->with('fail', 'Sector not found.');
            }

            $this->deleteSectorImage($sector->sectorImage);
            $sector->delete();

            return back()->with('success', config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500') . ' ' . $th->getMessage());
        }
    }

    private function storeSectorImage(Request $request): ?string
    {
        if (! $request->hasFile('sectorImage')) {
            return null;
        }

        $imageName = str_replace(' ', '', $request->sector_name) . date('Y') . '.' . $request->sectorImage->extension();
        $path = public_path('VirtualImages/');

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        Image::make($request->sectorImage)->resize(200, 200)->save($path . $imageName);

        return $imageName;
    }

    private function deleteSectorImage(?string $imageName): void
    {
        if (! $imageName) {
            return;
        }

        $filePath = public_path('VirtualImages/' . $imageName);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
}
