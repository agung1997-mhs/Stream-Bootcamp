<?php

namespace App\Http\Controllers\Admin;

use App\Models\Movie;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::all();

        return view('admin.movies', [
            'movies' => $movies
        ]);
    }

    public function create()
    {
        return view('admin.movie-create');
    }

    public function edit($id)
    {
        $movie = Movie::findOrFail($id);

        return view('admin.movie-edit', [
            'movie' => $movie
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $request->validate([
            'title' => 'required|string',
            'small_thumbnail' => 'required|image:mimes:jpeg,jpg,png',
            'large_thumbnail' => 'required|image:mimes:jpeg,jpg,png',
            'trailer' => 'required|file',
            'movie' => 'required|file',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about'=> 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required'
        ]);

        $smallThumbnail = $request->small_thumbnail;
        $largeThumbnail = $request->large_thumbnail;

        $originalSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName();
        $originalLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();

        $smallThumbnail->storeAs('public/thumbnail/', $originalSmallThumbnailName);
        $largeThumbnail->storeAs('public/thumbnail/', $originalLargeThumbnailName);

        $data['small_thumbnail'] = $originalSmallThumbnailName;
        $data['large_thumbnail'] = $originalLargeThumbnailName;

        Movie::create($data);

        return redirect()->route('admin.movie')->with('success', 'Data Movie Berhasil Ditambahkan!!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->except('_token');
        $request->validate([
            'title' => 'required|string',
            'small_thumbnail' => 'image:mimes:jpeg,jpg,png',
            'large_thumbnail' => 'image:mimes:jpeg,jpg,png',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about'=> 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required'
        ]);

        $movie = Movie::findOrFail($id);

        if ($request->small_thumbnail) {
            // Simpan gambar baru
            $smallThumbnail = $request->small_thumbnail;
            $originalSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName();
            $smallThumbnail->storeAs('public/thumbnail/', $originalSmallThumbnailName);
            $data['small_thumbnail'] = $originalSmallThumbnailName;

            // hapus gambar lama
            Storage::delete('public/thumbnail/'.$movie->small_thumbnail);
        }

        if ($request->large_thumbnail) {
            // Simpan gambar baru
            $largeThumbnail = $request->large_thumbnail;
            $originalLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();
            $largeThumbnail->storeAs('public/thumbnail/', $originalLargeThumbnailName);
            $data['large_thumbnail'] = $originalLargeThumbnailName;

            // hapus gambar lama
            Storage::delete('public/thumbnail/'.$movie->large_thumbnail);
        }

        $movie->update($data);

        return redirect()->route('admin.movie')->with('success', 'Data Movie Berhasil Diubah!!');
    }

    public function destroy($id)
    {
        Movie::findOrFail($id)->delete();

        return redirect()->route('admin.movie')->with('success', 'Data Movie Berhasil Dihapus!!');
    }
}
    