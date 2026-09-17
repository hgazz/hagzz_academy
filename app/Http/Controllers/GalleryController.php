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
        $user = auth('academy')->user();
        $academyId = $user instanceof \App\Models\PartnerUser ? (int) $user->academy_id : (int) auth('academy')->id();
        $academy = $user instanceof \App\Models\PartnerUser ? $user->academy : $user;

        $image = $this->upload($request->file('image'), $this->galleryModel::PATH);
        $gallery = $this->galleryModel->create([
            'image' => $image,
            'academy_id' => $academyId,
        ]);
        NotificationService::dbNotification($academyId, Academies::class, 'new gallery added', $academy?->commercial_name ?? '', 'Image Added', $academy?->logo ?? $user?->image, ['gallery' => $gallery->image]);
        session()->flash('success', trans('admin.gallery.created_successfully'));
        return to_route('academy.gallery.index');
    }

    protected function getAcademyId(): int
    {
        $user = auth('academy')->user();
        return $user instanceof \App\Models\PartnerUser ? (int) $user->academy_id : (int) auth('academy')->id();
    }

    protected function authorizeGallery(Gallery $gallery): void
    {
        abort_if((int) $gallery->academy_id !== $this->getAcademyId(), 403, 'غير مصرح لك بالوصول إلى هذا العنصر.');
    }

    public function edit(Gallery $gallery)
    {
        $this->authorizeGallery($gallery);
        return view('Academy.pages.gallery.edit', compact('gallery'));
    }

    public function update(GalleryRequest $request, Gallery $gallery)
    {
        $this->authorizeGallery($gallery);
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
        $this->authorizeGallery($gallery);
        $gallery->delete();
        $this->deleteFile($this->galleryModel::PATH . '/' . $gallery->getRawOriginal('image'));
        return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.gallery.gallery'),
            'message' => trans('admin.gallery.deleted_successfully'),
        ]]);
    }
}
