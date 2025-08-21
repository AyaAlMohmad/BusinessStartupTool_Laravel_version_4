<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
      public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'video' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:102400',
            'description' => 'nullable',
        ]);

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoName = time() . '.' . $video->getClientOriginalExtension();
            $video->move(public_path('videos'), $videoName);
            $videoPath = 'videos/' . $videoName;
        }

        Video::create([
            'title' => $request->title,
            'video_path' => $videoPath,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.videos.index')
                         ->with('success', 'Video added successfully.');
    }

       public function index()
    {
        $videos = Video::paginate(10); 
        return view('admin.videos.index', compact('videos'));
    }
   public function show($id)
    {
        $video = Video::findOrFail($id);
        return view('admin.videos.show', compact('video'));
    }
    public function edit($id)
    {
        $video = Video::findOrFail($id);
        return view('admin.videos.edit', compact('video'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:102400',
            'description' => 'nullable',
        ]);

        $video = Video::findOrFail($id);

        if ($request->hasFile('video')) {
            // Delete old video if exists
            if ($video->video_path && file_exists(public_path($video->video_path))) {
                unlink(public_path($video->video_path));
            }

            $newVideo = $request->file('video');
            $videoName = time() . '.' . $newVideo->getClientOriginalExtension();
            $newVideo->move(public_path('videos'), $videoName);
            $video->video_path = 'videos/' . $videoName;
        }

        $video->title = $request->title;
        $video->description = $request->description;
        $video->save();

        return redirect()->route('admin.videos.index')
                         ->with('success', 'Video updated successfully.');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        if ($video->video_path && file_exists(public_path($video->video_path))) {
            unlink(public_path($video->video_path));
        }

        $video->delete();

        return redirect()->route('admin.videos.index')
                         ->with('success', 'Video deleted successfully.');
    }
}
