<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\Gate;

class DeviceService
{
    public function revoke(int $id): Device
    {
        $device = Device::findOrFail($id);
        Gate::authorize('delete', $device);

        $device->delete();

        return $device;
    }
}
