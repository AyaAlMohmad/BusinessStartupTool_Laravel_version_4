<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{

    public function index()
    {
        $videos = Video::all();
   
        $videos->transform(function ($video) {
            $video->video_path = asset($video->video_path);
            return $video;
        });
    
        return response()->json([
            'status' => 'success',
            'data' => $videos,
        ], 200);
    }

    public function show($id)
    {
        $video = Video::find($id);
    
        if (!$video) {
            return response()->json([
                'status' => 'error',
                'message' => 'Video not found',
            ], 404);
        }
    
       
        $video->video_path = asset($video->video_path);
    
        return response()->json([
            'status' => 'success',
            'data' => $video,
        ], 200);
    }
    public function searchByTitle(Request $request)
    {
        if (!$request->has('title')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Title parameter is required',
            ], 400);
        }
    
       
        $videos = Video::where('title', 'like', '%' . $request->title . '%')->paginate(10);
    
    
        if ($videos->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No videos found',
            ], 404);
        }
    
      
        return response()->json([
            'status' => 'success',
            'data' => $videos->map(function ($video) {
                return [
                    'id'=>$video->id,
                    'video_path' => asset($video->video_path),
                    'description' => $video->description,
                ];
            }),
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'total_pages' => $videos->lastPage(),
                'total_videos' => $videos->total(),
            ],
        ], 200);
    }
}