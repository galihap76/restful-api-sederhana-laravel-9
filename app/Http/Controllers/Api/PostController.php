<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index()
    {
        // Mengambil daftar post
        $posts = Post::latest()->paginate(5);

        // Mengembalikan koleksi post sebagai sumber daya
        return new PostResource(true, 'List Data Posts', $posts);
    }
    
    public function store(Request $request)
    {
        // Mendefinisikan aturan validasi
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required'
        ]);

        // Memeriksa apakah validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mengunggah gambar
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // Membuat post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        // Mengembalikan respons
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }
    
    public function show(Post $post)
    {
        // Mengembalikan post tunggal sebagai sumber daya
        return new PostResource(true, 'Data Post Ditemukan!', $post);
    }
    
    public function update(Request $request, Post $post)
    {
        // Mendefinisikan aturan validasi
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // Memeriksa apakah validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Memeriksa apakah gambar tidak kosong
        if ($request->hasFile('image')) {

            // Mengunggah gambar
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // Menghapus gambar lama
            Storage::delete('public/posts/'.$post->image);

            // Mengupdate post dengan gambar baru
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);

        } else {

            // Mengupdate post tanpa gambar
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        // Mengembalikan respons
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }
    
    public function destroy(Post $post)
    {
        // Menghapus gambar
        Storage::delete('public/posts/'.$post->image);

        // Menghapus post
        $post->delete();

        // Mengembalikan respons
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
