<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Attachment;
use Illuminate\Support\Facades\Storage;
use File;

class AttachmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function showAttachment($parentID) {
        $attachment = Attachment::where('parent_id', $parentID)->get();
        return view('pages.view-attachments', ['parentID' => $parentID,
                                               'attachments' => $attachment]);
    }

    public function store(Request $request) {
        $parentID = $request->parent_id;
        $attachment = $request->file('attachment');
        $directory = $this->uploadFile($parentID, $attachment);

        if (!empty($directory)) {
            $attachmentData = Attachment::where([['parent_id', $parentID],
                                                 ['directory', $directory]])
                                        ->first();

            if (!$attachmentData) {
                $attachmentData = new Attachment;
                $attachmentData->parent_id = $parentID;
                $attachmentData->directory = $directory;
                $attachmentData->save();
            }

            return response()->json([
                'filename' => basename($directory),
                'directory' => url($directory)
            ]);
        } else {
            return response()->json([
                'filename' => 'NULL',
                'directory' => 'NULL'
            ]);
        }
    }

    private function uploadFile($parentID, $attachment) {
        $directory = "";

        if (!empty($attachment)) {
            $newFileName = $attachment->getClientOriginalName();
            Storage::put('public/attachments/' . $parentID . '/' . $newFileName,
                         file_get_contents($attachment->getRealPath()));
            $directory = 'storage/attachments/' . $parentID . '/' . $newFileName;
        }

        return $directory;
    }

    public function update(Request $request, $id) {

    }

    public function delete(Request $request, $id) {
        $filePath = $request->directory;
        $attachmentData = Attachment::findOrFail($id);

        $attachmentData->delete();

        File::delete($filePath);

        return "Successfully deleted.";
    }
}
