<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductAvailability;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'provider' => 'nullable|string',
            'min_speed' => 'nullable|integer',
        ]);

        $query = ProductAvailability::where('address', $request->address);

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('min_speed')) {
            $query->where('speed', '>=', $request->min_speed);
        }

        return $query->paginate();
    }
}
