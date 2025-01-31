<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\products;
use Illuminate\Support\Str;
class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = products::all();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|string',
        ]);

        $validatedData['id'] = (string) Str::uuid();

        products::create($validatedData);

        return response()->json(['success' => true], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = products::find($id);
        $product->update($request->all());

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Products::find($id)->delete();

        return response()->json(['success' => true]);
    }

    public function getProducts(Request $request)
    {
        $products = Products::query();

        // For pagination
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);

        // Sorting
        if ($request->has('sort')) {
            $sort = $request->input('sort')[0]; // Kendo sends sort as an array
            $products->orderBy($sort['field'], $sort['dir']);
        }

        // Filtering
        if ($request->has('filter')) {
            $filters = $request->input('filter')['filters'];
            $logic = $request->input('filter')['logic'] ?? 'and';
            $products->where(function ($query) use ($filters, $logic) {
                foreach ($filters as $filter) {
                    if ($logic === 'and') {
                        $query->where($filter['field'], 'like', '%' . $filter['value'] . '%');
                    } else {
                        $query->orWhere($filter['field'], 'like', '%' . $filter['value'] . '%');
                    }
                }
            });
        }

        $total = $products->count();
        $products = $products->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return response()->json([
            'data' => $products,
            'total' => $total,
        ]);
    }
}
