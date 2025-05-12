<x-app-layout>
    <div class="business_profile_main px-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <x-slot name="header">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ __('Update Profile ') }}
                        </h2>
                    </x-slot>
                </div>
            </div>
        </div>


        <div class="cstm_business_profile border-gray-200">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                <li>
                                    {{$error}}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(session()->get('message'))
                        <div class="alert alert-success" role="alert">
                            <strong>Success: </strong>{{session()->get('message')}}
                        </div>
                        @endif
                        <?php
  $user=Auth::user();
  $p=Auth::user()->Business_profile;

  if(isset($p->set_services_selected_day) && !empty($p->set_services_selected_day)){
  	$set_services_selected_day=unserialize($p->set_services_selected_day);
  	

  }else{
  	$set_services_selected_day=array();
  }

  if(isset($p->service_timesloat) && !empty($p->service_timesloat)){
  	$service_timesloat=unserialize($p->service_timesloat);
  	
  }else{
  	$service_timesloat=array();
  }
  
 


  ?>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card_header_cstm">
                                <div class="row align-items-center">
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="business_Div">
                                            <h3 class="dasbord_titles">Business Profile</h3>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-5 col-sm-3">
                                        <div class="business_Div">
                                            <div class="preview-icon-wrap d-flex align-items-center pre-profile">
                                                <a class="cstm_btns_edit" target="_blank"
                                                    href="/business_profile_view/<?php echo $p->id ?>"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="You can preview your profile here">
                                                    <em class="icon ni ni-eye"></em>
                                                </a>
                                            </div>
                                            <span class="profile-txt">PREVIEW PROFILE</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3">
                                        <div class="business_Div">
                                            <div
                                                class="preview-icon-wrap d-flex align-items-center justify-content-end">
                                                <a class="cstm_btns_edit" href="{{route('edit_business_profile')}}">
                                                    <em class="icon ni ni-edit"></em>
                                                    <span>Edit profile</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="card-body">

                                @if (session('status')) <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div> @endif @if($message = Session::get('success')) <div class="alert alert-success">
                                    <p>{{$message}}</p>
                                </div> @endif
                              
                                    <?php 
										$avg_rating = round($avg_rating, 1);
									?>
                                    (<span>{{ $avg_rating }}</span>)
                                    <i data-star="{{$avg_rating}}"></i>
                                    <br><small style="color: #a7a7a7;"><a href="/google_review">{{ $totalRating }}
                                            Ratings & {{ $totalReview }} Reviews</a></small>
                                </div>
                                <div id="logo_view">@if ($p->profile_logo!='') <img height="25px"
                                        src="{{ url('/') }}/image/user_profile/{{ $p->profile_logo}}" /> @else <img
                                        height="25px" src="{{ url('/') }}/image/no_logo.png" /> @endif </div>
                                <div id="title_view">

                                </div>
                            </div>
                       
                        </div>
                        <div class="boco_gallry_sec">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="sim_gallry">
                                        @if($gallery->count() > 0)
                                        <img class="maxi w-100"
                                            src="{{ url('/') }}/image/user_profile/{{$gallery[0]->img_name}}" />
                                        @else
                                        <img class="maxi w-100" src="{{ url('/') }}/image/no_img.png" />
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    @if($gallery->count() > 0)

                                    <div id="gallry_thum_main" class="gallry_thum_main slider-init"
                                        data-slick='{"arrows": true, "dots": false,"slidesToShow": 5,"horizontal": true,"horizontalSwiping": true , "slidesToScroll": 1, "infinite":false, "responsive":[ {"breakpoint": 992,"settings":{"slidesToShow": 4}}, {"breakpoint": 768,"settings":{"slidesToShow": 2}} ]}'>
                                        @foreach ($gallery as $g)

                                        <div class="galry_thum">
                                            <img src="{{ url('/') }}/image/user_profile/{{$g->img_name}}" />
                                        </div>
                                        @endforeach


                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <aside id="sidebar-nav" class="natural_cstm">
                            <div class="bocoflex_sidebar">
                                <div class="bocoflex_map bg-grey-cstm1 mb-4">

                                    <iframe class="map_custm"
                                        src="//maps.google.com/maps?q={{$p->user->latitude}},{{$p->user->longitude}}&z=15&output=embed"
                                        width="100%"></iframe>
                                </div>
                                <div class="address_custm p-2 mb-4">
                                    <div class="iconn">
                                        <svg width="37" height="37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="11.878" y="2.293" width="13.556" height="13.556" rx="2"
                                                transform="rotate(45 11.878 2.293)" stroke="currentColor"
                                                stroke-width="2"></rect>
                                            <path d="M13.878 10.878h-2.5a2.5 2.5 0 0 0-2.5 2.5v1" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path
                                                d="M16.525 10.524a.5.5 0 0 1 0 .707l-1.793 1.793a.5.5 0 0 1-.854-.353V9.085a.5.5 0 0 1 .854-.354l1.793 1.793z"
                                                fill="currentColor"></path>
                                        </svg>
                                        <p>{{ $user->business_address }}</p>
                                    </div>
                                </div>
                                <div class="bocoflex_timezone bg-grey-cstm1 ">
                                    <div class="title_timezone">
                                        <h3 class="timetitle">Opening Hours</h3>
                                        <img class="time_icons"
                                            src="{{ url('/') }}/assets/users/images/time_icon.png" />
                                    </div>
                                    <div class="timezone_slote">
                                        <?php 
											$iCostPerHour = $p->service_price_per_hour;
											$timespent = '08:15:00';

											$timeparts=explode(':',$timespent);
											$pay=$timeparts[0]*$iCostPerHour+$timeparts[1]/60*$iCostPerHour;

											if(!empty($service_timesloat)){

											foreach($service_timesloat as $key=>$slot){
												if (in_array($key, $set_services_selected_day))
											{

 											?>
                                        <p class="day_titles"><?php echo  $key; ?></p>
                                        <span class="daytime"><?php if(!empty($slot)){foreach($slot as $slots){
								
																			$dateTimeObject1 = date_create($slots["start_time"]); 
											$dateTimeObject2 = date_create($slots["end_time"]); 
											$interval = date_diff($dateTimeObject1, $dateTimeObject2);

											$timetotal= $interval->h .':'.$interval->i .':'.$interval->s ;
											$timeparts=explode(':',$timetotal);
											$pay =round($timeparts[0]*$iCostPerHour+$timeparts[1]/60*$iCostPerHour);
															echo $slots["start_time"].' - '.$slots["end_time"]."</br>";
										} }?></span>

                                        <?php } } }?>

                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
                <div class="boco_desc_Main">
                    <div class="row_cstm">
                        @if ($p->profile_description!='' )
                        <div class="boco_galry_decs mt-4 mb-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="desc_content">
                                        <h3>Description</h3>
                                        <div id="description_view"> <span id="description_view_val">
                                                {{ $p->profile_description}} </span> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="features_aminities mb-4">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 mb-3">
                                    <div class="amenities_header">
                                        <h2>Features & Amenities</h2>
                                    </div>
                                </div>
                                <?php  $fac=unserialize($p->facilities);
			
								foreach($fac as $fa){   ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="amenities">

                                        <div class="amenities_text">
                                            <h5><?php echo $fa;  ?></h5>

                                        </div>
                                    </div>
                                </div>

                                <?php  } ?>


                            </div>
                        </div>
                        @if($p->how_to_prepare)
                        <div class="boco_prepare_sc bocoflex_light_bg1 mb-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="prepare_content">
                                        <h3 class="innertitles">How to prepare</h3>
                                        <p>{{$p->how_to_prepare}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($p->how_to_get_there)
                        <div class="boco_get_sc bocoflex_light_bg1 mb-3">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="prepareget_content">
                                        <h3 class="innertitles">How to access my location</h3>
                                        <p>{{$p->how_to_get_there}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" id="modalDefault">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em> </a>
                    <div class="modal-header">
                        <h5 class="modal-title">Business Profile Page </h5>
                    </div>
                    <div id="modal-body" class="modal-body">
                        profile content from ajax
                    </div>
                    <div class="modal-footer bg-light"><span class="sub-text">Footer</span> </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

