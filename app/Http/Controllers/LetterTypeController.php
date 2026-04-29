<?php

namespace App\Http\Controllers;

use App\Models\LetterType;
use App\Http\Requests\StoreLetterTypeRequest;
use App\Http\Requests\UpdateLetterTypeRequest;
use Illuminate\Http\Request;

class LetterTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $letterTypes = LetterType::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('letter_types.index', compact('letterTypes'));
    }

    public function create()
    {
        return view('letter_types.create');
    }

    public function store(StoreLetterTypeRequest $request)
    {
        LetterType::create($request->validated());
        return redirect()->route('letter_types.index')->with('success', 'Data berhasil disimpan');
    }

    public function show(LetterType $letterType)
    {
        return view('letter_types.show', compact('letterType'));
    }

    public function edit(LetterType $letterType)
    {
        return view('letter_types.edit', compact('letterType'));
    }

    public function update(UpdateLetterTypeRequest $request, LetterType $letterType)
    {
        $letterType->update($request->validated());
        return redirect()->route('letter_types.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(LetterType $letterType)
    {
        $letterType->delete();
        return redirect()->route('letter_types.index')->with('success', 'Data berhasil dihapus');
    }
}
