<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $documentTypes = DocumentType::orderBy('code')->get();
        return view('registrar.document-types.index', compact('documentTypes'));
    }

    public function create()
    {
        return view('registrar.document-types.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:document_types',
            'name' => 'required|string|max:100',
            'fee' => 'required|numeric|min:0',
            'processing_days' => 'nullable|integer|min:0',
            'has_school_year' => 'boolean',
            'is_printable' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        $validated['has_school_year'] = $request->has('has_school_year');
        $validated['is_printable'] = $request->has('is_printable');
        $validated['is_active'] = $request->has('is_active');

        DocumentType::create($validated);
        return redirect()->route('registrar.document-types.index')->with('success', 'Document type added.');
    }

    public function edit($id)
    {
        $documentType = DocumentType::findOrFail($id);
        return view('registrar.document-types.form', compact('documentType'));
    }

    public function update(Request $request, $id)
    {
        $documentType = DocumentType::findOrFail($id);
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:document_types,code,'.$id,
            'name' => 'required|string|max:100',
            'fee' => 'required|numeric|min:0',
            'processing_days' => 'nullable|integer|min:0',
        ]);
        
        $validated['has_school_year'] = $request->has('has_school_year');
        $validated['is_printable'] = $request->has('is_printable');
        $validated['is_active'] = $request->has('is_active');

        $documentType->update($validated);
        return redirect()->route('registrar.document-types.index')->with('success', 'Document type updated.');
    }

    public function destroy($id)
    {
        $documentType = DocumentType::findOrFail($id);
        try {
            $documentType->delete();
            return redirect()->route('registrar.document-types.index')->with('success', 'Document type deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete this document type as it is likely linked to existing requests.');
        }
    }
}
