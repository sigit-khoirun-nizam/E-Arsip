<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Unit;
use App\Models\LetterType;
use App\Http\Requests\StoreArchiveRequest;
use App\Http\Requests\UpdateArchiveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $unit_id = $request->query('unit_id');
        $category_id = $request->query('category_id');

        $archives = Archive::with(['unit', 'category', 'letterType', 'uploader', 'pic'])
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->when($unit_id, function ($q) use ($unit_id) {
                $q->where('unit_id', $unit_id);
            })
            ->when($category_id, function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            })
            ->latest()
            ->paginate(10);

        $units = Unit::all();
        $categories = Category::all();

        return view('archives.index', compact('archives', 'units', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        $letterTypes = LetterType::all();
        return view('archives.create', compact('categories', 'units', 'letterTypes'));
    }

    public function store(StoreArchiveRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('arsip');
        }

        $data['uploaded_by'] = auth()->id() ?? 1; // Default fallback for now if not logged in
        $data['is_confidential'] = $request->has('is_confidential');

        Archive::create($data);
        return redirect()->route('archives.index')->with('success', 'Data berhasil disimpan');
    }

    public function show(Archive $archive)
    {
        $archive->load(['unit', 'category', 'letterType', 'uploader', 'pic']);
        return view('archives.show', compact('archive'));
    }

    public function edit(Archive $archive)
    {
        $categories = Category::all();
        $units = Unit::all();
        $letterTypes = LetterType::all();
        return view('archives.edit', compact('archive', 'categories', 'units', 'letterTypes'));
    }

    public function update(UpdateArchiveRequest $request, Archive $archive)
    {
        $data = $request->validated();

        if ($request->hasFile('file_path')) {
            if ($archive->file_path) {
                Storage::delete($archive->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('arsip');
        }

        $data['is_confidential'] = $request->has('is_confidential');

        $archive->update($data);
        return redirect()->route('archives.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Archive $archive)
    {
        if ($archive->file_path) {
            Storage::delete($archive->file_path);
        }
        $archive->delete();
        return redirect()->route('archives.index')->with('success', 'Data berhasil dihapus');
    }
}
