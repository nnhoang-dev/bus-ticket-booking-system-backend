<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TuyenXeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_address' => $this->start_address,
            'end_address' => $this->end_address,
            'status' => $this->status,
            'time' => $this->time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}