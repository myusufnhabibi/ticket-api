<?php

namespace App\Http\Controllers;

use App\Http\Requests\AduanBalasanStoreRequest;
use App\Http\Requests\AduanStoreRequest;
use App\Http\Resources\AduanBalasanResource;
use App\Http\Resources\AduanResource;
use App\Models\Aduan;
use App\Models\AduanBalasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AduanController extends Controller
{
    public function index(Request $request)
    {
        $query = Aduan::query();

        $query->orderBy('created_at', 'desc');
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
            $query->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('status', $request->priority);
        }

        if (auth()->user()->role == 'user') {
            $query->where('user_id', auth()->user()->id);
        }

        $aduan = $query->get();

        return response()->json([
            'message' => 'Aduan retrieved successfully',
            'data' => AduanResource::collection($aduan)
        ], 200);
    }
    public function store(AduanStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $aduan = new Aduan();
            $aduan->user_id = auth()->user()->id;
            $aduan->code = 'ADU' . rand(1000, 9999);
            $aduan->title = $data['title'];
            $aduan->description = $data['description'];
            $aduan->priority = $data['priority'];
            $aduan->save();

            DB::commit();
            return response()->json([
                'message' => 'Aduan Successfully Created',
                'data' => new AduanResource($aduan)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Aduan failed Created: ' . $e->getMessage()], 500);
        }
    }

    public function show($code)
    {
        try {
            $aduan = Aduan::where('code', $code)->first();

            if (!$aduan) {
                return response()->json([
                    'message' => 'Aduan Tidak ditemukan'
                ], 404);
            }

            if (auth()->user()->role == 'user' && $aduan->user_id != auth()->user()->id) {
                return response()->json([
                    'message' => 'Anda tidak diperbolehkan melihat aduan ini!!'
                ], 403);
            }

            return response()->json([
                'message' => 'Aduan retrieved successfully',
                'data' => new AduanResource($aduan)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Aduan failed retrieved: ' . $e->getMessage()], 500);
        }
    }

    public function storeBalasan(AduanBalasanStoreRequest $request, $code)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $aduan = Aduan::where('code', $code)->first();
            if (!$aduan) {
                return response()->json([
                    'message' => 'Aduan Tidak ditemukan'
                ], 404);
            }

            if (auth()->user()->role == 'user' && $aduan->user_id != auth()->user()->id) {
                return response()->json([
                    'message' => 'Anda tidak diperbolehkan melihat aduan ini!!'
                ], 403);
            }

            $aduanBalasan = new AduanBalasan();
            $aduanBalasan->aduan_id = $aduan->id;
            $aduanBalasan->user_id = auth()->user()->id;
            $aduanBalasan->content = $data['content'];
            $aduanBalasan->save();

            if (auth()->user()->role == 'admin') {
                $aduan->status = $data['status'];
                if ($data['status'] == 'resolved') {
                    $aduan->completed_at = now();
                }
                $aduan->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Aduan Balasan created successfully',
                'data' => new AduanBalasanResource($aduanBalasan)
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Aduan Balasan failed created: ' . $e->getMessage()], 500);
        }
    }
}
