<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Review;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Exports\ReviewsExport;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $key = explode(' ', $request['search']);
        $reviews = Review::whereHas('item', function($query) use($key){
            return $query->where('store_id', Helpers::get_store_id())->when(isset($key) , function($query) use($key){
                foreach ($key as $value) {
                    $query->Where('name', 'like', "%{$value}%");
                }
            });
        })

        ->latest()->paginate(config('default_pagination'));
        return view('vendor-views.review.index', compact('reviews'));
    }
    public function reviewsExport(Request $request)
    {
        $key = explode(' ', $request['search']);
        $reviews = Review::whereHas('item', function($query) use($key){
            return $query->where('store_id', Helpers::get_store_id())->when(isset($key) , function($query) use($key){
                foreach ($key as $value) {
                    $query->Where('name', 'like', "%{$value}%");
                }
            });
        })

        ->latest()->get();

        $data = [
            'data'=>$reviews,
            'search'=>$request->search??null,
        ];

        if ($request->export_type == 'excel') {
            return Excel::download(new ReviewsExport($data), 'ReviewsExport.xlsx');
        } else if ($request->export_type == 'csv') {
            return Excel::download(new ReviewsExport($data), 'ReviewsExport.csv');
        }
    }

    public function update_reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|max:65000',
        ]);

        $review = Review::findOrFail($id);
        $review->reply = $request->reply;
        $review->replied_at = now();
        $review->store_id = Helpers::get_store_id();
        $review->save();

        Toastr::success(translate('messages.review_reply_updated'));
        return to_route('vendor.reviews');
    }
}
