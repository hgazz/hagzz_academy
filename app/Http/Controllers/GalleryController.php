<?php

namespace App\Http\Controllers;

use App\DataTables\GalleryDataTable;
use App\Http\Requests\Gallery\GalleryRequest;
use App\Http\Traits\FileUpload;
use App\Models\Academies;
use App\Models\Gallery;
use App\Services\Firebase\NotificationService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use FileUpload;
    private $galleryModel;

    public function __construct(Gallery $gallery)
    {
        $this->galleryModel = $gallery;
    }

    public function index(GalleryDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.gallery.index');
    }

    public function create()
    {
        return view('Academy.pages.gallery.create');
    }

    public function store(GalleryRequest $request)
    {
        $image = $this->upload($request->file('image'), $this->galleryModel::PATH);
        $this->galleryModel->create([
            'image' => $image,
            'academy_id' => auth()->id()
        ]);
        NotificationService::dbNotification(auth('academy')->id(), Academies::class, 'new gallery added', auth('academy')->user()->commercial_name);
        session()->flash('success', trans('admin.gallery.created_successfully'));
        return to_route('academy.gallery.index');
    }

    public function edit(Gallery $gallery)
    {
        return view('Academy.pages.gallery.edit', compact('gallery'));
    }

    public function update(GalleryRequest $request, Gallery $gallery)
    {
        $image = $request->hasFile('image') ? $this->upload($request->file('image'), $this->galleryModel::PATH, $gallery->getRawOriginal('image')) : $gallery->getRawOriginal('image');
        $gallery->update([
            'image' => $image,
        ]);
        session()->flash('success', trans('admin.gallery.updated_successfully'));
        return to_route('academy.gallery.index');
    }

    public function delete(Request $request)
    {
        $gallery = $this->galleryModel->findOrFail($request->id);
        $gallery->delete();
        $this->deleteFile($this->galleryModel::PATH . $gallery->getRawOriginal('image'));
        return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.gallery.gallery'),
            'message' => trans('admin.gallery.deleted_successfully'),
        ]]);
    }
}
