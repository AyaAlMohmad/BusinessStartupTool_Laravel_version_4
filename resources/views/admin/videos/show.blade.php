@extends('layouts.app')
@section('content')
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        .video-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        video {
            width: 100%;
            height: auto;
            display: block;
        }

        h1 {
            margin-bottom: 20px;
        }

        p {
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 18px;
            color: #555;
        }

        .btn-secondary {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>

    <div class="container">
        <h1>{{ $video->title }}</h1>
        <div class="video-container">
            <video controls>
                <source src="{{ asset( $video->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <p>{{ $video->description }}</p>
        <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
@endsection
