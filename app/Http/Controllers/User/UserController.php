<?php

   namespace App\Http\Controllers\User;
   use App\Http\Controllers\Controller;
   use App\Models\User;
   use App\Models\Business_profile;
   use App\Models\Business_profile__clone;
   use App\Models\Partner_review;
   use App\Models\Business_gallery;
   use App\Models\Business_gallery__clone;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use App\Models\User_wallet_history;
   use Illuminate\Support\Facades\Http;
   use App\Models\User_slot;
   use App\Models\User_slot__clone;
   use App\Models\Ad_facility;
   use Mail;
   use Stripe\OAuth;
   use Stripe\Stripe;
   use Stripe\Payout;
   use Stripe\StripeClient;
   use Illuminate\Support\Facades\Log;
   use Exception;
   use Validator;
   
   use App\Models\Calendar_access_token;
   use App\Models\Google_review_access_token;
   
   use App\Models\Zoom_access_token;
   use App\Services\GoogleCalendarApi;
   use App\Models\Booking_table;
   use Carbon\Carbon;
   use App\Models\CardPayment;
   use App\Models\User_wallet;
   use App\Models\Ad_company;
   use App\Models\Ad_service;
   use App\Models\Country;
   use App\Models\Ad_category;
   use App\Models\User_services;
   use App\Models\User_categories;
   use App\Models\Payment;
   use App\Models\Payment_detail;
   use GuzzleHttp\Client;
   use GuzzleHttp\Exception\ClientException;
   use Redirect;
   
   use GuzzleHttp\Exception\ServerException;
   use GuzzleHttp\Exception\RequestException;
   use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function business_profile(Request $request, User $user)
    {
        $user = Auth::user();

        if (!empty($_POST)) {
            $this->validate($request, [
                'pf_logo' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120'
            ]);
            $destinationPath = 'image/user_profile/';

            $step = 0;
            $logo_input_val = $_POST['hd_pf_logo'];

            if ($image = $request->file('pf_logo')) {
                $logo_input = uniqid() . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $logo_input);
                $logo_input_val = $logo_input;
            }

            if (isset($_POST['zoom_selected']) && !empty($_POST['zoom_selected'])) {
                $zoom_selected = $_POST['zoom_selected'];
            } else {
                $zoom_selected = '';
            }

            if (isset($_POST['mobile']) && !empty($_POST['mobile'])) {
                $mobile = 'true';
            } else {
                $mobile = 'false';
            }

            if (isset($_POST['fixed']) && !empty($_POST['fixed'])) {
                $fixed = 'true';
            } else {
                $fixed = 'false';
            }

            if (isset($_POST['online']) && !empty($_POST['online'])) {
                $online = 'true';
            } else {
                $online = 'false';
            }

            $fac = array();
            $all_facilities = Ad_facility::get();
            foreach ($all_facilities as $key => $faci) {
                if (isset($_POST['facilities' . $key])) {
                    $fac[] = $faci->fac_name;
                }
            }


            $cat = array();
            $cat1 = array();
            foreach ($_POST['service_name'] as $ke => $category) {
                $cat1[] = $category['name'];
            }
            $cat_title_only = implode(', ', $cat1);

            foreach ($_POST['service_name'] as $ke => $category) {
                $onser = 'false';
                if (isset($category['is_online']) && !empty($category['is_online'])) {
                    $onser = $category['is_online'];
                }

                $cat[$ke]['name'] = $category['name'];
                $cat[$ke]['length'] = $category['length'];
                $cat[$ke]['price'] = $category['cost'];
                $cat[$ke]['is_online'] = $onser;
                $cat[$ke]['service_desc'] = $category['service_desc'];
                $cat[$ke]['service_type'] = $category['service_type'];
                $cat[$ke]['service__cat'] = $category['service__cat'];
            }

            $serviceType_Arr = [];
            foreach ($cat as $st_list) {
                $serviceType_Arr[] = $st_list['service_type'];
            }

            if (in_array('option1', $serviceType_Arr)) {
                $i_deliver_online_sessions__val = "true";
            } else {
                $i_deliver_online_sessions__val = "false";
            }

            if (in_array('option2', $serviceType_Arr)) {
                $i_travel_to_you__val = "true";
            } else {
                $i_travel_to_you__val = "false";
            }

            if (in_array('option3', $serviceType_Arr)) {
                $you_travel_to_me__val = "true";
            } else {
                $you_travel_to_me__val = "false";
            }

            if (in_array('option4', $serviceType_Arr)) {
                $i_send_products_to_you__val = "true";
            } else {
                $i_send_products_to_you__val = "false";
            }

            $days1 = array();
            $timesloat = array();
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($days as $key => $day) {
                $total_sloat = count($_POST['start_srv_timesloat'][$day]);

                for ($x = 0; $x < $total_sloat; $x++) {
                    if (!empty($_POST['start_srv_timesloat'][$day][$x]) && !empty($_POST['end_srv_timesloat'][$day][$x])) {
                        $timesloat[$day][$x]['start_time'] = $_POST['start_srv_timesloat'][$day][$x];
                        $timesloat[$day][$x]['end_time'] = $_POST['end_srv_timesloat'][$day][$x];
                    } else {
                        $timesloat[$day] = "";
                    }
                }
                if (isset($_POST['set_services_selected_day' . $key])) {
                    $days1[] = $day;
                }
            }

            if ($request->file('gallery_images')) {
                $total_img = count($request->file('gallery_images'));
                for ($x = 0; $x < $total_img; $x++) {
                    $gallery = $request->file('gallery_images');
                    if ($gallery_image = $gallery[$x]) {
                        $_input = uniqid() . "." . $gallery_image->getClientOriginalExtension();
                        $gallery_image->move($destinationPath, $_input);
                        $gall_img = $_input;
                        Business_gallery::create([
                            'user_id' => $user->id,
                            'img_name' => $gall_img
                        ]);
                    }
                }
            }

            $profile = $user->Business_profile;
            if ($profile == '') {
                $bus_profile = Business_profile::create([
                    'user_id' => $user->id,
                    'profile_logo' => $logo_input_val,
                    'profile_description' => $request->description,
                    'facilities' => serialize($fac),
                    'set_services_selected_day' => serialize($days1),
                    'service_timesloat' => serialize($timesloat),
                    'how_to_prepare' => $request->how_to_prepare,
                    'search_keyword' => $request->search_keyword,
                    'how_to_get_there' => $request->how_to_get_there,
                    'service_price_per_hour' => $request->service_price_per_hour,
                    'service_categories' => serialize($cat),
                    'service_categories_title' => $cat_title_only,
                    'i_deliver_online_sessions' => $i_deliver_online_sessions__val,
                    'i_travel_to_you' => $i_travel_to_you__val,
                    'you_travel_to_me' => $you_travel_to_me__val,
                    'i_send_products_to_you' => $i_send_products_to_you__val,
                    'step' => $step,
                    'contact_name' => $request->pf_contact_name,
                    'zoom_selected' => $zoom_selected,
                    'terms_title' => $request->terms_title,
                    'online_services' => $request->online,
                    'come_to_me_services' => $request->fixed,
                    'with_a_location_services' => $request->mobile,
                ]);

                $profile_id = $bus_profile['id'];

                $data = [
                    'subject' => 'Bocoflex account',
                    'email' => $user->email,
                    'content' => '<p>Hello ' . $user->firstname . ' ' . $user->lastname . '</p>
                    <p>Congratulation on setting up your bocoflex account, to complete your set up and start selling please click here and follow the instructions.</p>'
                ];

                Mail::send('mail', $data, function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['subject']);
                });

            } else {
                User_slot::where('profile_id', $profile->id)->delete();
                $profile_id = $profile->id;

                Business_profile::where('user_id', $user->id)->update([
                    'user_id' => $user->id,
                    'profile_logo' => $logo_input_val,
                    'profile_description' => $request->description,
                    'facilities' => serialize($fac),
                    'set_services_selected_day' => serialize($days1),
                    'service_timesloat' => serialize($timesloat),
                    'how_to_prepare' => $request->how_to_prepare,
                    'search_keyword' => $request->search_keyword,
                    'how_to_get_there' => $request->how_to_get_there,
                    'service_price_per_hour' => $request->service_price_per_hour,
                    'service_categories' => serialize($cat),
                    'service_categories_title' => $cat_title_only,
                    'i_deliver_online_sessions' => $i_deliver_online_sessions__val,
                    'i_travel_to_you' => $i_travel_to_you__val,
                    'you_travel_to_me' => $you_travel_to_me__val,
                    'i_send_products_to_you' => $i_send_products_to_you__val,
                    'step' => 8,
                    'contact_name' => $request->pf_contact_name,
                    'terms_title' => $request->terms_title,
                    'online_services' => $request->online,
                    'come_to_me_services' => $request->fixed,
                    'with_a_location_services' => $request->mobile
                ]);
            }

            $service_cat = $cat;
            $total_slot = array();

            foreach ($service_cat as $key1 => $cat) {
                foreach ($timesloat as $key2 => $slots) {
                    if (!empty($slots)) {
                        $total_sloat = count($slots);

                        for ($x = 0; $x < $total_sloat; $x++) {

                            $start = new \DateTime($slots[$x]['start_time']);
                            $end = new \DateTime($slots[$x]['end_time']);
                            $startTime = $start->format('H:i');
                            $endTime = $end->format('H:i');
                            $i = 0;
                            $time = [];
                            $intervals = $cat['length'];
                            $rest_time = 15;
                            while (strtotime($startTime) <= strtotime($endTime)) {
                                $start = $startTime;
                                $end = date('H:i', strtotime('+' . $cat['length'] . ' minutes', strtotime($startTime)));
                                $startTime = date('H:i', strtotime('+' . $intervals . ' minutes', strtotime($startTime)));
                                $i++;
                                if (strtotime($startTime) <= strtotime($endTime)) {

                                    $time[$i]['slot_start_time'] = $start;
                                    $time[$i]['slot_end_time'] = $end;
                                    User_slot::create([
                                        'profile_id' => $profile_id,
                                        'service_index' => $key1,
                                        'day' => $key2,
                                        'start_time' => $start,
                                        'end_time' => $end
                                    ]);

                                }
                            }
                        }
                    }
                }

            }


            return back()->with('message', 'Created Profile Successfully');

        }

        $profile = $user->Business_profile;
        $facilities = Ad_facility::get();
        $gallery = Business_gallery::where('user_id', $user->id)->get();

        if (!empty($profile)) {

            if ($profile->step == '8') {

                $totalRating = 0;
                $totalCount = 0;
                $totalReview = 0;

                // review and ratings
                $reviews = Partner_review::select(
                    'partner_reviews.*',
                    'users.firstname',
                    'users.lastname',
                    'users.email',
                )
                    ->leftJoin('users', 'partner_reviews.user_id', '=', 'users.id')
                    ->leftJoin('business_profiles', 'partner_reviews.partner_id', '=', 'business_profiles.user_id')
                    ->where('partner_reviews.partner_id', '=', $user->id)
                    ->where('business_profiles.integrate_normal_review', '=', 1)
                    ->get();

                foreach ($reviews as $data) {
                    if ($data['comment'] != '' && $data['comment'] != null) {
                        $totalReview++;
                    }
                    $totalCount++;
                    $totalRating += $data['rating'];
                }


                if ($totalCount > 0) {
                    $avg_rating = min(($totalRating / ($totalCount * 5) * 5), 5);
                } else {
                    $avg_rating = 0;
                }

                return view('user/business_profile', ['profile' => $profile, 'gallery' => $gallery, 'facilities' => $facilities, 'avg_rating' => $avg_rating, 'totalRating' => $totalRating, 'totalReview' => $totalReview]);
            } else {
                return view('user/add_business_profile', ['facilities' => $facilities, 'gallery' => $gallery]);
            }

        } else {
            return view('user/add_business_profile', ['facilities' => $facilities, 'gallery' => $gallery]);
        }

    }

}