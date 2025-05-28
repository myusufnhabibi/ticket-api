<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_aduan' => $this->total_aduan,
            'resolve_aduan' => $this->resolve_aduan,
            'avg_aduan' => $this->avg_aduan,
            'active_aduan' => $this->active_aduan,
            'status_distribution' => [
                'open' => $this->distribusi_aduan->open,
                'onprogress' => $this->distribusi_aduan->onprogress,
                'resolved' => $this->distribusi_aduan->resolved,
                'rejected' => $this->distribusi_aduan->rejected
            ]
        ];
    }
}
