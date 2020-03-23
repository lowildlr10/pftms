<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DocumentAttachment;
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

    public function showAttachment(Request $request, $parentID) {
        $attachmentData = DocumentAttachment::where('parent_id', $parentID)->get();
        return view('modules.attachment.index', [
            'parentID' => $parentID,
            'attachments' => $attachmentData,
        ]);
    }

    public function store(Request $request) {
        $parentID = $request->parent_id;
        $type = $request->type;
        $attachment = $request->file('attachment');


            $directory = $this->uploadFile($parentID, $attachment);

            if (!empty($directory)) {
                $attachmentData = DocumentAttachment::where([
                    ['parent_id', $parentID],
                    ['type', $type],
                    ['directory', $directory]
                ])->first();

                $instanceAttachment = $attachmentData ? $attachmentData :
                                      new DocumentAttachment ;
                $instanceAttachment->parent_id = $parentID;
                $instanceAttachment->type = $type;
                $instanceAttachment->directory = $directory;
                $instanceAttachment->save();

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
        try {
        } catch (\Throwable $th) {
            return response()->json([
                'filename' => 'Error has occured! Please try again.',
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

    public function destroy(Request $request, $id) {
        $filePath = $request->directory;
        $instanceAttachment = DocumentAttachment::findOrFail($id);
        $instanceAttachment->delete();

        File::delete($filePath);

        return "Successfully deleted.";
    }
}
