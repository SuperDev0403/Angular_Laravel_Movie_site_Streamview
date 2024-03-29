<?php
namespace App\Http\Controllers;
use App\AdminVideo;
use App\ContactUS;
use App\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Settings;
use App\Category;
use App\SubCategory;
use DB;
class GuestController extends Controller
{
  public function index(Request $request) {
        $settings = Settings::get();
        $backgroundImage = '';
        $site_logo = '';
        foreach( $settings as $key=>$value) {
            if($value->key == 'site_logo') {
                $site_logo = $value->value;
	    }
	    if($value->key == 'ANGULAR_SITE_URL') {
                $streaming_url = $value->value;
            }
        };
        $title = '';
        $videos = [];
        $sub_category = [];
            $category_obj = Category::where('id', 8)->first();
            $title = $category_obj->name;
            $sub_categories  = SubCategory::where('category_id', 8)->orderby('sub_categories.name', 'asc')->get();
            if (!empty($sub_categories)) {
                foreach ($sub_categories as $item) {
                    // $sub_videos = sub_category_videos($item->id, WEB, $skip, $take, 3);
                    $sub_videos = AdminVideo::where('admin_videos.is_approved', 1)
                        ->where('admin_videos.status', 1)
                        ->leftJoin('categories', 'admin_videos.category_id', '=', 'categories.id')
                        ->leftJoin('sub_categories', 'admin_videos.sub_category_id', '=', 'sub_categories.id')
                        ->leftJoin('genres', 'admin_videos.genre_id', '=', 'genres.id')
                        ->where('admin_videos.sub_category_id', 'LIKE', "%$item->id%")
                        ->videoResponse()
                        ->orderby('admin_videos.sub_category_id', 'desc')
                        ->take(9)->get();
                    if(count($sub_videos) > 0) {
                        $chunk = $sub_videos->chunk(1);
                        $vid = [];
                        foreach ($chunk as $val) {
                            $group = [];
                            foreach ($val as $data) {
                                $group = displayFullDetails($data->admin_video_id, 3);
                            }
                            $vid['videos'][] = $group;
                            $vid['id'] = $item->id;
                        }
                        $videos[$item->name] = $vid;
                        $sub_category[$item->name] = $item->id;
                    }
                }
            } else {
                $response_array = ['success'=>false, 'error_messages'=>tr('category_not_found')];
                return response()->json($response_array, 200);
            }
            $response_array = ['title'=> $title,'data'=>$videos, 'success'=>true, 'sub_category'=>array_chunk($sub_category, 8, true)];
        return view('guest.index')->with('site_logo', $site_logo)->with('streaming_url', $streaming_url)->with('videos', $videos)->with('sub_category', array_chunk($sub_category, 8, true));
    }
    public function browse_list(Request $request) {
        $sub_title = $request->category;
        $sub_id = $request->id;
        $settings = Settings::get();
        $site_logo = '';
        foreach( $settings as $key=>$value) {
            if($value->key == 'site_logo') {
                $site_logo = $value->value;
            }
            if($value->key == 'ANGULAR_SITE_URL') {
                $streaming_url = $value->value;
            }
        };
        $sub_videos = AdminVideo::where('admin_videos.is_approved' , 1)
            ->where('admin_videos.status' , 1)
            ->where('admin_videos.sub_category_id' , 'LIKE', "%$sub_id%")
            ->orderby('admin_videos.id' , 'desc')->get();
        return view('guest.browse_list')->with('site_logo', $site_logo)->with('streaming_url', $streaming_url)->with('sub_title', $sub_title)->with('videos', $sub_videos);
    }
    public function movie_details(Request $request) {
        $settings = Settings::get();
        $backgroundImage = '';
        $site_logo = '';
        foreach( $settings as $key=>$value) {
            if($value->key == 'site_logo') {
                $site_logo = $value->value;
            }
            if($value->key == 'ANGULAR_SITE_URL') {
                $streaming_url = $value->value;
            }
//            if($value->key == 'home_page_bg_image') {
//                $backgroundImage = $value->value;
//            }
        };
        $id = $request->id;
        $data = displayFullDetails($id, 3);
        return view('guest.movie_details')->with('site_logo', $site_logo)->with('streaming_url', $streaming_url)->with('data', $data);
    }
    public function search_results(Request $request) {
        $searchQuery = $request->searchQuery;
        $settings = Settings::get();
        $site_logo = '';
        foreach( $settings as $key=>$value) {
            if($value->key == 'site_logo') {
                $site_logo = $value->value;
            }
            if($value->key == 'ANGULAR_SITE_URL') {
                $streaming_url = $value->value;
            }
        };
        if ($searchQuery != "") {
            $searchVideos = AdminVideo::where('admin_videos.is_approved' , 1)
                ->where('admin_videos.status' , 1)
                ->where('admin_videos.title' , 'LIKE', "%$searchQuery%")
                ->orWhere('admin_videos.description' , 'LIKE', "%$searchQuery%")
                ->orderby('admin_videos.id' , 'asc')->get();
            if (count($searchVideos) > 0)
                return view('guest.search_results')->with('site_logo', $site_logo)->with('searchQuery', $searchQuery)->with('videos', $searchVideos);
        }
            return view('guest.search_results')->with('site_logo', $site_logo)->with('streaming_url', $streaming_url)->with('searchQuery', $searchQuery)->with('videos', $searchVideos)->withMessage('No Details found. Try to search again !');
        }
/*
    public function search(Request $request) {
        $settings = Settings::get();
        $backgroundImage = '';
        $site_logo = '';
        foreach( $settings as $key=>$value) {
            if($value->key == 'site_logo') {
                $site_logo = $value->value;
            }
        };
    $q = $request->q;
    if ($q != "") {
        $user = User::where('name', 'LIKE', '%' . $q . '%')->orWhere('email', 'LIKE', '%' . $q . '%')->paginate(5)->setPath('');
        $pagination = $user->appends(array(
            'q' => Input::get('q')
        ));
        if (count($user) > 0)
            return view('guest.search')->with('site_logo', $site_logo)->withDetails($user)->withQuery($q);
    }
    return view('guest.search')->with('site_logo', $site_logo)->withMessage('No Details found. Try to search again !');
}
*/
    public function contact_us() {
        $settings = Settings::get();
        $backgroundImage = '';
        $site_logo = '';
        foreach( $settings as $key=>$value) {
            if($value->key == 'site_logo') {
                $site_logo = $value->value;
            }
        };
        return view('guest.contact_us')->with('site_logo', $site_logo);
    }
    public function contact_usPost(Request $request)
    {
        $settings = Settings::get();
        $backgroundImage = '';
        $site_logo = '';
        foreach( $settings as $key=>$value) {
            if($value->key == 'site_logo') {
                $site_logo = $value->value;
            }
        };
        $this->validate($request, [ 'name' => 'required', 'email' => 'required|email', 'message' => 'required' ]);
        ContactUS::create($request->all());
        Mail::send('guest.email',
            array(
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'user_message' => $request->get('message')
            ), function($message)
            {
                $message->from('webmaster@flashington.com');
                $message->to('craigb@entangledweb.com', 'Admin')->subject('Feedback');
            });
        return back()->with('success', 'Thanks for contacting us!')->with('site_logo', $site_logo);
    }
}
