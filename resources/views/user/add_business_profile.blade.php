<x-app-layout>

    <div class="addbusiness_Main">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Update Profile ') }}
            </h2>
        </x-slot>


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
               if(!empty($p)){
                $step = $p->step;
               }else{
                $step = '';
               }

               $get_user_cat = App\Models\User_categories::leftJoin('ad_categories', 'user_categories.category_id', '=', 'ad_categories.id')
                  ->where('user_categories.user_id', $user->id)
                  ->select('ad_categories.category_name as category_name', 'ad_categories.id as category_id', 'user_categories.id as user_categories_id')
                  ->get();

               ?>
        <div class="card_cstm_profile">
            <div id="success_msg" class="alert" role="alert">

            </div>
            <div class="progress main-progress-bar">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0"
                    aria-valuemax="100"></div>
            </div>
            <div class="profile_card_inner">
                <form action="{{route('business_profile')}}" enctype="multipart/form-data" method="POST"
                    class="nk-wizard nk-wizard-simple is-alter tablist_forms" id="wizard-01">
                    @csrf
                    <?php
                        if(!empty($p) && $p->id !=''){ ?>
                    <input type="hidden" name="bus_id" id="bus_id" value="{{ $p->id}}">
                    <?php }else{ ?>
                    <input type="hidden" name="bus_id" id="bus_id" value="">
                    <?php }  ?>

                    <div class="nk-wizard-head">
                        <h5>Main Profile</h5>
                    </div>
                    <div class="nk-wizard-content wizard_cstm_content">
                        <div class="row gy-3">
                            <div class="col-md-6 col-sm-6">
                                <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form-label" for="pf_business_name">Business Name</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip" title="Add your business name"></em>
                                    </div>
                                    <div class="form-control-wrap"><input type="text"
                                            class="form-control cstm_input_form" id="pf_business_name"
                                            value="{{$user->business_name}}" name="pf_business_name" readonly /></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form-label" for="pf_logo">Logo</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip"
                                            title=" Upload your business logo in PNG or JPG."></em>
                                    </div>
                                    <small class="dem_text"><b>The dimensions for the logo must be 500px * 500px and
                                            maximum upload file size is 5MB</b></small>
                                    <div class="form-control-wrap">

                                        <input type="file" class="form-control cstm_input_files" id="pf_logo"
                                            name="pf_logo" accept="image/*" />
                                        @if(!empty($p))@if($p->profile_logo!='')
                                        <input type="hidden" name="hd_pf_logo" id="hd_pf_logo"
                                            value="{{ $p->profile_logo}}" />
                                        @else
                                        <input type="hidden" name="hd_pf_logo" id="hd_pf_logo" value="" />
                                        @endif @else <input type="hidden" name="hd_pf_logo" id="hd_pf_logo" value="" />
                                        @endif
                                        <span id="logo-error" style="color: red;"></span>

                                        <?php 
                                    if(!empty($p) && $p->profile_logo!=''){ ?>
                                        <div style="position:relative;">
                                            <a href="<?php echo url("/destroyImage/{$p->id}");  ?>" type="text"
                                                class="close AClass">
                                                <span>&times;</span>
                                            </a>
                                            <img width="200"
                                                src="{{ url('/') }}/image/user_profile/{{ $p->profile_logo}}" />
                                        </div>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form-label" for="pf_contact_name">Contact Name</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip" title="This is the name that users will contact"></em>
                                    </div>

                                    <div class="form-control-wrap"><input type="text"
                                            class="form-control cstm_input_form" id="pf_contact_name"
                                            value="@if(!empty($p)){{ $p->contact_name}}@endif" name="pf_contact_name" />
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form_label_cstm" for="fb_link">Facebook Link</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip" title="Add your Facebook Link"></em>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form_control_cstm" id="fb_link"
                                            value="@if(!empty($p)){{ $p->fb_link}}@endif" name="fb_link" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form_label_cstm" for="insta_link">Instagram Link</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip" title="Add your Instagram Link"></em>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form_control_cstm" id="insta_link"
                                            value="@if(!empty($p)){{ $p->insta_link}}@endif" name="insta_link" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form_label_cstm" for="in_link">Linkedin Link</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip" title="Add your Linkedin Link"></em>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control form_control_cstm" id="in_link"
                                            value="@if(!empty($p)){{ $p->in_link}}@endif" name="in_link" />
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12 col-sm-6">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form-label" for="fw-mobile-number">Description</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip"
                                            title="Write at least 100 words, give as much detail as possible about your business mission, why and how you do it "></em>
                                    </div>
                                    <div class="form-control-wrap">
                                        <textarea name="description"
                                            class="form-control form-control-sm textarea_cstm_foerm "
                                            id="cf-default-textarea"
                                            placeholder="Describe your business, give us much information as you can. This is how user’s will learn about your business before booking."
                                            spellcheck="false">@if(!empty($p)){{ $p->profile_description}}@endif</textarea>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="setps_cstm_btns">
                                    <button type="button" class="btn stepscstm step1" id="step1">Save </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-wizard-head">
                        <h5>Features</h5>
                    </div>

                    <div class="nk-wizard-content wizard_cstm_content">
                        <div class="feature-heading">
                            <h5>Features</h5>
                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="right" data-toggle="tooltip"
                                title="Select the features that you offer"></em>
                        </div>
                        <div class="field_wrapper_falit">
                            <?php 
                           
                              if(!empty($p) && !empty($p->facilities) ){
                                  
                                $fac=unserialize($p->facilities);
                                 if(!empty($facilities)){
                                foreach ($facilities as $key=>$faci) {
                                    if (in_array($faci->fac_name, $fac))
                                   {
                                       $selected="checked='checked'";     

                                   }else{
                                      $selected="";
                                   }
                              
                                ?>
                            <label class="wpbc-form-checkbox wpbc_checkbox checkboxs_wpbc"
                                for="facilities<?php echo $key;   ?>">
                                <input type="checkbox" data-slectedday="<?php echo $key;   ?>"
                                    id="facilities<?php echo $key;   ?>" name="facilities<?php echo $key;   ?>"
                                    class="custom_checkboxs" style="" <?php echo $selected ; ?> autocomplete="off">
                                <span class="spantexts"><?php echo $faci->fac_name;  ?></span> <span
                                    class="checkmarks"></span> </label>
                            <?php } } }else{
                                 foreach ($facilities as $key=>$faci) {
                                   
                            ?>
                            <label class="wpbc-form-checkbox wpbc_checkbox checkboxs_wpbc"
                                for="facilities<?php echo $key;   ?>">
                                <input type="checkbox" data-slectedday="<?php echo $key;   ?>"
                                    id="facilities<?php echo $key;   ?>" name="facilities<?php echo $key;   ?>"
                                    class="custom_checkboxs" style="" autocomplete="off"> <span
                                    class="spantexts"><?php echo $faci->fac_name;  ?></span> <span
                                    class="checkmarks"></span> </label>
                            <?php } }  ?>

                        </div>
                        <div class="col-md-12">
                            <div class="setps_cstm_btns">
                                <button type="button" class="btn stepscstm step2" id="step2">Save</button>
                            </div>
                        </div>
                    </div>



                    <div class="nk-wizard-head">
                        <h5>Services & Products</h5>
                    </div>
                    <span class="services_error" style="color: red;"></span>
                    <div class="nk-wizard-content wizard_cstm_content">

                        <div class="field_wrapper_falit_cat">
                            <div class="">
                                <h5>Add up to 5 services/products</h5>
                            </div>
                            <?php 

                              if(!empty($p) && !empty($p->service_categories) ){
                                $cat=unserialize($p->service_categories);
                                if(!empty($cat)){
                                foreach($cat as $key=> $ca){

                                ?>
                            <div class="row gy-3 ser_name count_service">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="inform-tool">
                                            <label class="form-label" for="service_name">Service/Product Name</label>
                                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="What service/product are you offering?"></em>
                                        </div>
                                        <?php 

                                    if( isset($ca['is_online']) && $ca['is_online']!='false'){
                           
                                       $selected_ser = "checked";
                                       $value4 = $ca['is_online'];
                                    }else{
                                       $selected_ser = "";
                                       $value4 = "false";
                                    }

                                    ?>
                                        <div class="form-control-wrap"><input type="text" class="form-control"
                                                id="service_name" value="<?php echo $ca['name'];  ?>"
                                                name="service_name[<?php echo $key  ?>][name]" />
                                        </div>
                                    </div>
                                </div>



                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="inform-tool">
                                            <label class="form-label" for="service__cat">Category</label>
                                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="Select the category that the service/product fits into (e.g. Fitness for Personal Training)"></em>
                                        </div>

                                        <div class="form-control-wrap">
                                            <select class="form-control cstm_input_form" id="service__cat"
                                                name="service_name[<?php echo $key ?>][service__cat]">
                                                <option value="">Select</option>
                                                @foreach($get_user_cat as $category)
                                                <option
                                                    <?= !empty($ca['service__cat']) && $ca['service__cat'] == $category->category_id ? 'selected' : ''; ?>
                                                    value="{{$category->category_id}}">{{$category->category_name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="inform-tool">
                                            <label class="form-label" for="service_name">Service/Product
                                                Description</label>
                                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="Give detail of the service/product eg. service/product length, goal, who’s it aimed at, what the user receives etc. Use key words that assist in AI searches."></em>
                                        </div>

                                        <div class="form-control-wrap">
                                            <textarea class="form-control cstm_input_form "
                                                name="service_name[<?php echo $key  ?>][service_desc]"
                                                id="service_name"><?= $ca['service_desc'] ?? '';  ?></textarea>
                                        </div>

                                    </div>
                                </div>







                                <div class="col-lg-4 col-md-12 service__type_width">
                                    <div class="form-group">
                                        <div class="inform-tool">
                                            <label class="form-label" for="service_name">Service/Product Type</label>
                                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="Select how you deliver this service/product (e.g You come to me for a Gym Membership)"></em>
                                        </div>
                                        <div class="form-control-wrap">
                                            <select class="form-control cstm_input_form service__type" id="service_name"
                                                name="service_name[<?php echo $key  ?>][service_type]">
                                                <option value="">Select</option>
                                                <option
                                                    <?= !empty($ca['service_type']) && $ca['service_type'] == 'option1' ? 'selected' : ''; ?>
                                                    value="option1">I deliver online sessions</option>
                                                <option
                                                    <?= !empty($ca['service_type']) && $ca['service_type'] == 'option2' ? 'selected' : ''; ?>
                                                    value="option2">I travel to you </option>
                                                <option
                                                    <?= !empty($ca['service_type']) && $ca['service_type'] == 'option3' ? 'selected' : ''; ?>
                                                    value="option3">You travel to me </option>
                                                <option
                                                    <?= !empty($ca['service_type']) && $ca['service_type'] == 'option4' ? 'selected' : ''; ?>
                                                    value="option4">I send products to you </option>
                                                <!-- Add more options as needed -->
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 service__dur">
                                    <div class="form-group ser_duration">
                                        <div class="inform-tool">
                                            <label class="form-label services_class"
                                                for="service_length_<?php echo $key  ?>">Service/Product
                                                Duration</label>
                                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip" title="How long is the service/product?"></em>
                                        </div>

                                        <div class="form-control-wrap">
                                            <div class="services_label">
                                                <label class="form-label"
                                                    for="service_hours_<?php echo $key  ?>">Hours</label>
                                                <select name="service_name[<?php echo $key  ?>][hours]"
                                                    onchange="funhours(this)" id="hours_<?php echo $key  ?>"
                                                    title="<?php echo $key  ?>">

                                                    <?php 

                                 for($i=0; $i<=12; $i++)
                                 {

                                    $value = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $selected = '';
                                    $_ca_hours = $ca['hours'] ?? '';
                                    if($_ca_hours){
                                       if($value == $_ca_hours){
                                          $selected = 'selected';
                                       }
                                    }
                                     echo "<option value=".$value." ".$selected.">".$value."</option>";
                                 }
                                 ?>
                                                </select>
                                            </div>
                                            <div class="services_label">
                                                <label class="form-label"
                                                    for="service_min_<?php echo $key  ?>">Minutes</label>
                                                <select name="service_name[<?php echo $key  ?>][minutes]"
                                                    onchange="funminutes(this)" id="minutes_<?php echo $key  ?>"
                                                    title="<?php echo $key  ?>">

                                                    <?php 

                                 for($k=0; $k<=59; $k++)
                                 {
                                    $value1 = str_pad($k, 2, '0', STR_PAD_LEFT);
                                    $selected1 = '';
                                    $ca_minut = $ca['minutes'] ?? '';
                                    if($ca_minut){
                                       if($value1 == $ca_minut){
                                          $selected1 = 'selected';
                                       }
                                    }

                                     echo "<option value=".$value1." ".$selected1.">".$value1."</option>";
                                 }
                                 ?>
                                                </select>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" id="service_cost_val_<?php echo $key  ?>"
                                    name="service_name[<?php echo $key  ?>][cost]"
                                    value="<?php echo $ca['price'];  ?>" />

                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group">
                                        <div class="inform-tool">
                                            <label class="form-label" for="service_cost">Service/Product Cost
                                                (£)</label>
                                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="How much is the service/product? £ in the left box and pennies in the right box)"></em>
                                        </div>

                                        <?php 
                                       // $num = number_format($ca['price'],1);
                                       // $parts = explode('.',$num);
                                       // $integerPart = $parts[0];
                                       // $decimalPart = $parts[1];

                                    ?>
                                        <div class="form-control-wrap"><input type="number" class="form-control"
                                                id="service_cost1_<?php echo $key;  ?>"
                                                name="service_name1[<?php echo $key  ?>][cost]"
                                                value="<?php echo $ca['price'];  ?>"
                                                onblur="sercost(<?php echo $key;  ?>)" /> <b>.</b> <input type="number"
                                                class="form-control" id="service_cost2_<?php echo $key  ?>"
                                                name="service_name2[<?php echo $key  ?>][cost]"
                                                onblur="sercost2(<?php echo $key  ?>)"
                                                value="<?php echo $ca['price2']  ?>" /></div>
                                    </div>
                                </div>
                                <?php if($key==0){  ?>
                                <div class="cstm_AddRemove">
                                    <a href="javascript:void(0);" class="add_button_cat" title="Add field">Add More <em
                                            class="icon ni ni-plus"></em></a>
                                </div>
                                <?php }else{ ?>
                                <div class="cstm-remove-btn">
                                    <a href="javascript:void(0);" class="remove_button_cat"><em
                                            class="icon ni ni-trash"></em></a>
                                </div>
                                <?php } ?>





                            </div>

                            <?php }

                                }                           
                                
                                }else { ?>
                            <div class="row gy-3 ser_name count_service">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="inform-tool">
                                            <label class="form-label" for="service_name">Service/Product Name</label>
                                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="What service/product are you offering?"></em>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control cstm_input_form " id="service_name"
                                                name="service_name[0][name]" />
                                            <!--<input type="checkbox" id="service_online_0" value="false" name="service_name[0][is_online]" onchange="seron(this,0)" >-->
                                        </div>

                                    </div>
                                </div>



                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="inform-tool"><label class="form-label"
                                                for="service__cat">Category</label><em
                                                class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="Select the category that the service/product fits into (e.g. Fitness for Personal Training)"></em>
                                        </div>

                                        <div class="form-control-wrap">
                                            <select class="form-control cstm_input_form" id="service__cat"
                                                name="service_name[0][service__cat]">
                                                <option value="">Select</option>
                                                @foreach($get_user_cat as $category)
                                                <option value="{{$category->category_id}}">{{$category->category_name}}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="inform-tool"><label class="form-label"
                                                for="service_name">Service/Product Description</label><em
                                                class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="Give detail of the service/product eg. service/product length, goal, who’s it aimed at, what the user receives etc. Use key words that assist in AI searches."></em>
                                        </div>

                                        <div class="form-control-wrap">
                                            <textarea class="form-control cstm_input_form "
                                                name="service_name[0][service_desc]" id="service_name"></textarea>

                                        </div>

                                    </div>
                                </div>






                                <div class="col-lg-4 col-md-12 service__type_width">
                                    <div class="form-group">
                                        <div class="inform-tool"><label class="form-label"
                                                for="service_name">Service/Product Type</label><em
                                                class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="Select how you deliver this service/product (e.g You come to me for a Gym Membership)"></em>
                                        </div>

                                        <div class="form-control-wrap">
                                            <select class="form-control cstm_input_form service__type" id="service_name"
                                                name="service_name[0][service_type]">
                                                <option value="">Select</option>
                                                <option value="option1">I deliver online sessions</option>
                                                <option value="option2">I travel to you </option>
                                                <option value="option3">You travel to me </option>
                                                <option value="option4">I send products to you </option>
                                                <!-- Add more options as needed -->
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-6 service__dur">
                                    <div class="form-group">
                                        <div class="inform-tool"> <label class="form-label"
                                                for="service_length_0">Service/Product Duration</label><em
                                                class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip" title="How long is the service/product?"></em>
                                        </div>
                                        <div class="form-control-wrap">

                                            <input type="hidden" class="form-control cstm_input_form"
                                                id="service_length_0" name="service_name[0][length]" value="1" />
                                            <div class="services_label">
                                                <label class="form-label" for="service_hours_0">Hours</label>
                                                <select name="service_name[0][hours]" onchange="funhours(this)"
                                                    id="hours_0" title="0">

                                                    <?php 

                                 for($i=0; $i<=12; $i++)
                                 {

                                    $value = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    
                                     echo "<option value=".$value.">".$value."</option>";
                                 }
                                 ?>
                                                </select>
                                            </div>
                                            <div class="services_label">
                                                <label class="form-label" for="service_min_0">Minutes</label>
                                                <select name="service_name[0][minutes]" onchange="funminutes(this)"
                                                    id="minutes_0" title="0">

                                                    <?php 

                                 for($k=0; $k<=59; $k++)
                                 {
                                    $value1 = str_pad($k, 2, '0', STR_PAD_LEFT);
                                    

                                     echo "<option value=".$value1.">".$value1."</option>";
                                 }
                                 ?>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <input type="hidden" class="form-control" id="service_cost_val_0"
                                        name="service_name[0][cost]" value="0" />

                                    <div class="form-group">
                                        <div class="inform-tool"> <label class="form-label"
                                                for="service_cost">Service/Product Cost (£)</label><em
                                                class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                data-toggle="tooltip"
                                                title="How much is the service/product? £ in the left box and pennies in the right box)"></em>
                                        </div>

                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="service_cost1_0"
                                                name="service_name1[0][cost]" onblur="sercost('0')" /> <b>.</b> <input
                                                type="number" class="form-control" id="service_cost2_0"
                                                name="service_name2[0][cost]" onblur="sercost2('0')" />

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="addcstm_busines pb-2">
                                        <a href="javascript:void(0);" class="add_button_cat" title="Add field">Add More
                                            <em class="icon ni ni-plus"></em></a>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>


                        </div>

                        <div class="col-md-4">
                            <div class="setps_cstm_btns">
                                <button type="button" class="btn stepscstm step4" id="step4">Save</button>
                            </div>
                        </div>
                    </div>



                    <!---->

                    <div class="nk-wizard-head">
                        <h5>Opening Hours</h5>
                    </div>

                    <div class="nk-wizard-content wizard_cstm_content">
                        <span class="startdate_error" style="color: red;"></span></br>
                        <span class="enddate_error" style="color: red;"></span></br>
                        <span class="startdate_error2" style="color: red;"></span>
                        <div class="row gy-2">
                            <div class="panel-body">
                                <div class="ur-form-row">
                                    <div id="unavailable_days" class="ur-form-grid ur-grid-1">
                                        <div class="ur-field-item tablist_filed_item">
                                            <div class="form-row validate-required user-registration-validated" id="">
                                                <div class="inform-tool"><label for="user_login"
                                                        class="ur-label_lblcstm">Available week days <abbr
                                                            class="required" title="required">*</abbr></label><em
                                                        class="card-hint icon ni ni-help-fill" data-bs-placement="top"
                                                        data-toggle="tooltip"
                                                        title="What days and hours do you operate?"></em></div>


                                                <fieldset class="cstmtime_slots">
                                                    <?php
                                            
                                                if(!empty($p) && !empty(unserialize($p->set_services_selected_day)) ){

                                                 $set_services_selected_day=unserialize($p->set_services_selected_day);
                                                  
                                                 $service_timesloat=unserialize($p->service_timesloat);
                                                
                                                
                                                               $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                                                               if(!empty($set_services_selected_day)){
                                                foreach ($days as $key=>$day) {
                                                    if (in_array($day, $set_services_selected_day))
                                                 {
                                                $selected="checked='checked'";    
                                                
                                                 }else{
                                                  $selected="";
                                                 }    ?>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day<?php echo $key;   ?>">
                                                        <input type="checkbox" data-slectedday="<?php echo $key;   ?>"
                                                            id="set_services_selected_day<?php echo $key;   ?>"
                                                            name="set_services_selected_day<?php echo $key;   ?>"
                                                            class="custom_checkbox" style="" <?php echo $selected ; ?>
                                                            autocomplete="off">
                                                        <span class="spantexts"><?php echo $day;  ?></span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <?php
                                                } } }else{ ?>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day0">
                                                        <input type="checkbox" data-slectedday="0"
                                                            id="set_services_selected_day0"
                                                            name="set_services_selected_day0" class="custom_checkbox"
                                                            style="" autocomplete="off">
                                                        <span class="spantexts">Sunday</span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day1">
                                                        <input type="checkbox" data-slectedday="1"
                                                            id="set_services_selected_day1"
                                                            name="set_services_selected_day1" class="custom_checkbox"
                                                            style="" autocomplete="off">
                                                        <span class="spantexts">Monday</span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day2">
                                                        <input type="checkbox" data-slectedday="2"
                                                            id="set_services_selected_day2"
                                                            name="set_services_selected_day2" class="custom_checkbox"
                                                            style="" autocomplete="off">
                                                        <span class="spantexts">Tuesday</span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day3">
                                                        <input type="checkbox" data-slectedday="3"
                                                            id="set_services_selected_day3"
                                                            name="set_services_selected_day3" class="custom_checkbox"
                                                            style="" autocomplete="off">
                                                        <span class="spantexts">Wednesday</span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day4">
                                                        <input type="checkbox" data-slectedday="4"
                                                            id="set_services_selected_day4"
                                                            name="set_services_selected_day4" class="custom_checkbox"
                                                            style="" autocomplete="off">
                                                        <span class="spantexts">Thursday</span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day5">
                                                        <input type="checkbox" data-slectedday="5"
                                                            id="set_services_selected_day5"
                                                            name="set_services_selected_day5" class="custom_checkbox"
                                                            style="" autocomplete="off">
                                                        <span class="spantexts">Friday</span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <legend class="screen-reader-text"><span></span></legend>
                                                    <label class="wpbc-form-checkbox checkboxs_wpbc"
                                                        for="set_services_selected_day6">
                                                        <input type="checkbox" data-slectedday="6"
                                                            id="set_services_selected_day6"
                                                            name="set_services_selected_day6" class="custom_checkbox"
                                                            style="" autocomplete="off">
                                                        <span class="spantexts">Saturday</span>
                                                        <span class="checkmarks"></span>
                                                    </label>
                                                    <?php } ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="timeslot_selected_id" class="ur-form-grid ur-grid-2">

                                        <div class="ur-field-item">
                                            <?php
                                          if(!empty($p) && !empty($p->service_timesloat) ){ ?>
                                            <div id="days_div0"
                                                style="<?php if(empty($service_timesloat['Sunday'])){  echo 'display:none';  }   ?>"
                                                class="sunday_div">
                                                <label class="custm_days">Sunday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field">
                                                        <div class="cstm_filed_M">
                                                            <?php if(!empty($service_timesloat['Sunday'])){ foreach($service_timesloat['Sunday'] as $sun){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $sun['start_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Sunday][]">
                                                                -
                                                                <input type="time"
                                                                    value="<?php echo $sun['end_time'];  ?>"
                                                                    class="check_valid" id="appt_timesloat"
                                                                    name="end_srv_timesloat[Sunday][]"> <a href="#"
                                                                    class="remove_field_mon"><em
                                                                        class="icon ni ni-trash"></em></a></br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Sunday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Sunday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div1"
                                                style="<?php if(empty($service_timesloat['Monday'])){  echo 'display:none';  }   ?>"
                                                class="sunday_div">
                                                <label class="custm_days">Monday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_mon">
                                                        <div class="cstm_filed_M">
                                                            <?php if(!empty($service_timesloat['Monday'])){ foreach($service_timesloat['Monday'] as $mon){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $mon['start_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Monday][]">
                                                                -
                                                                <input type="time"
                                                                    value="<?php echo $mon['end_time'];  ?>"
                                                                    class="check_valid" id="appt_timesloat"
                                                                    name="end_srv_timesloat[Monday][]"> <a href="#"
                                                                    class="remove_field_mon"><em
                                                                        class="icon ni ni-trash"></em></a></br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Monday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Monday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button_mon"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div2"
                                                style="<?php if(empty($service_timesloat['Tuesday'])){  echo 'display:none';  }   ?>"
                                                class="sunday_div">
                                                <label class="custm_days">Tuesday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_tue">
                                                        <div class="cstm_filed_M">
                                                            <?php if(!empty($service_timesloat['Tuesday'])){ foreach($service_timesloat['Tuesday'] as $tue){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $tue['start_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Tuesday][]">
                                                                -
                                                                <input type="time"
                                                                    value="<?php echo $tue['end_time'];  ?>"
                                                                    class="check_valid" id="appt_timesloat"
                                                                    name="end_srv_timesloat[Tuesday][]"> <a href="#"
                                                                    class="remove_field_tue"><em
                                                                        class="icon ni ni-trash"></em></a> </br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Tuesday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Tuesday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button_tue"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div3"
                                                style="<?php if(empty($service_timesloat['Wednesday'])){  echo 'display:none';  }   ?>"
                                                class="sunday_div">
                                                <label class="custm_days">Wednesday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_wed">
                                                        <div class="cstm_filed_M">
                                                            <?php if(!empty($service_timesloat['Wednesday'])){ foreach($service_timesloat['Wednesday'] as $wed){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    value="<?php echo $wed['start_time'];  ?>"
                                                                    name="start_srv_timesloat[Wednesday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $wed['end_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Wednesday][]"> <a href="#"
                                                                    class="remove_field_wed"><em
                                                                        class="icon ni ni-trash"></em></a></br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Wednesday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Wednesday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button_wed"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="<?php if(empty($service_timesloat['Thursday'])){  echo 'display:none';  }   ?>"
                                                id="days_div4" class="sunday_div">
                                                <label class="custm_days">Thursday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_thurs">
                                                        <div class="cstm_filed_M">
                                                            <?php if(!empty($service_timesloat['Thursday'])){ foreach($service_timesloat['Thursday'] as $thu){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    value="<?php echo $thu['start_time'];  ?>"
                                                                    name="start_srv_timesloat[Thursday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $thu['end_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Thursday][]"><a href="#"
                                                                    class="remove_field_thu"><em
                                                                        class="icon ni ni-trash"></em></a></br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Thursday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Thursday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button_thurs"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="<?php if(empty($service_timesloat['Friday'])){  echo 'display:none';  }   ?>"
                                                id="days_div5" class="sunday_div">
                                                <label class="custm_days">Friday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_fri">
                                                        <div class="cstm_filed_M">

                                                            <?php if(!empty($service_timesloat['Friday'])){ foreach($service_timesloat['Friday'] as $fri){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    value="<?php echo $fri['start_time'];  ?>"
                                                                    name="start_srv_timesloat[Friday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $fri['end_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Friday][]"><a href="#"
                                                                    class="remove_field_fri"><em
                                                                        class="icon ni ni-trash"></em></a></br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Friday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Friday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button_fri"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="<?php if(empty($service_timesloat['Saturday'])){  echo 'display:none';  }   ?>"
                                                id="days_div6" class="sunday_div">
                                                <label class="custm_days">Saturday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_sat">
                                                        <div class="cstm_filed_M">
                                                            <?php if(!empty($service_timesloat['Saturday'])){ foreach($service_timesloat['Saturday'] as $sat){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    value="<?php echo $sat['start_time'];  ?>"
                                                                    name="start_srv_timesloat[Saturday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $sat['end_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Saturday][]"> <a href="#"
                                                                    class="remove_field_sat"><em
                                                                        class="icon ni ni-trash"></em></a></br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Saturday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Saturday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button_sat"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="<?php if(empty($service_timesloat['Saturday'])){  echo 'display:none';  }   ?>"
                                                id="days_div6" class="sunday_div">
                                                <label class="custm_days">Saturday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_sat">
                                                        <div class="cstm_filed_M">
                                                            <?php if(!empty($service_timesloat['Saturday'])){ foreach($service_timesloat['Saturday'] as $sat){  ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    value="<?php echo $sat['start_time'];  ?>"
                                                                    name="start_srv_timesloat[Saturday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    value="<?php echo $sat['end_time'];  ?>"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Saturday][]"> <a href="#"
                                                                    class="remove_field_sat"><em
                                                                        class="icon ni ni-trash"></em></a></br>
                                                            </div>
                                                            <?php  } }else{ ?>
                                                            <div>
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="start_srv_timesloat[Saturday][]">
                                                                -
                                                                <input type="time" class="check_valid"
                                                                    id="appt_timesloat"
                                                                    name="end_srv_timesloat[Saturday][]">
                                                            </div>
                                                            <?php } ?>
                                                            <button class="add_field_button_sat"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }else{ ?>
                                            <div id="days_div0" class="sunday_div">
                                                <label class="custm_days">Sunday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field">
                                                        <div class="cstm_filed_M">
                                                            <input type="time" class="check_valid"
                                                                id="appt_timesloat_start"
                                                                name="start_srv_timesloat[Sunday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            -
                                                            <input type="time" class="check_valid"
                                                                id="appt_timesloat_end"
                                                                name="end_srv_timesloat[Sunday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            <button class="add_field_button"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div1" class="sunday_div">
                                                <label class="custm_days">Monday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_mon">
                                                        <div class="cstm_filed_M">
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="start_srv_timesloat[Monday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            -
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="end_srv_timesloat[Monday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            <button class="add_field_button_mon"> <em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div2" class="sunday_div">
                                                <label class="custm_days">Tuesday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_tue">
                                                        <div class="cstm_filed_M">
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="start_srv_timesloat[Tuesday][]"
                                                                data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            -
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="end_srv_timesloat[Tuesday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            <button class="add_field_button_tue"><em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div3" class="sunday_div">
                                                <label class="custm_days">Wednesday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_wed">
                                                        <div class="cstm_filed_M">
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="start_srv_timesloat[Wednesday][]"
                                                                data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            -
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="end_srv_timesloat[Wednesday][]"
                                                                data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            <button class="add_field_button_wed"> <em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div4" class="sunday_div">
                                                <label class="custm_days">Thursday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_thurs">
                                                        <div class="cstm_filed_M">
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="start_srv_timesloat[Thursday][]"
                                                                data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            -
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="end_srv_timesloat[Thursday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            <button class="add_field_button_thurs"> <em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div5" class="sunday_div">
                                                <label class="custm_days">Friday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_fri">
                                                        <div class="cstm_filed_M">
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="start_srv_timesloat[Friday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            -
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="end_srv_timesloat[Friday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            <button class="add_field_button_fri"> <em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="days_div6" class="sunday_div">
                                                <label class="custm_days">Saturday</label>
                                                <div class="input_fields_wrap timeslotSunday">
                                                    <div class="input_custom_field_sat">
                                                        <div class="cstm_filed_M">
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="start_srv_timesloat[Saturday][]"
                                                                data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            -
                                                            <input type="time" class="check_valid" id="appt_timesloat"
                                                                name="end_srv_timesloat[Saturday][]" data-msg="Required"
                                                                class="form-control cstm_input_form required">
                                                            <button class="add_field_button_sat"> <em
                                                                    class="icon ni ni-plus"></em></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <div class=" form-row validate-required user-registration-validated" id="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="setps_cstm_btns">
                                            <button type="button" class="btn stepscstm step3" id="step3">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!---->


                    <!-- location display none start -->



                    <!-- location display none end -->




                    <div class="nk-wizard-head">
                        <h5>Gallery </h5>
                    </div>
                    <span class="gallary_error" style="color: red;"></span>

                    <div class="nk-wizard-content wizard_cstm_content">
                        <div class="re">
                            <p class="err_msgimg" style="color: red;"></p>
                            <p class="err_msg">
                            <div class="inform-tool"><span>Maximum upload file size is 5MB</span><em
                                    class="card-hint icon ni ni-help-fill" data-bs-placement="top" data-toggle="tooltip"
                                    title="Add a minimum of 5 photos that show off your business"></em></div>


                            </p>
                        </div>
                        <div class="row gy-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <!--label class="form-label" for="facilities_icon">Icon</label-->
                                    <div class="form_cstm_upload">
                                        <input type="hidden" name="gallary_count" id="gallary_count">
                                        <div class="form-control-wrap">
                                            <div class="input-images"></div>
                                        </div>
                                    </div>
                                    <div class="gallery-img-pr">

                                        <div class="gallery-img">
                                            <?php
                                    $destinationPath = url("image/user_profile");
                                    foreach ($gallery as $key => $value) {
                                       $img_name = $destinationPath ."/" . $value['img_name'];
                                       ?>
                                            <img src="<?= $img_name ?>" width="100" height="100">
                                            <input type="hidden" class="gallery_img_input" value="<?= $img_name ?>">
                                            <?php  } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="setps_cstm_btns">
                                    <button type="button" class="btn stepscstm step6" id="step6">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-wizard-head">
                        <h5>Information </h5>
                    </div>
                    <div class="nk-wizard-content wizard_cstm_content">
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="inform-tool"> <label class="form-label" for="facilities_icon">How to
                                            prepare</label><em class="card-hint icon ni ni-help-fill"
                                            data-bs-placement="right" data-toggle="tooltip"
                                            title="Tell your customer how they should get ready for your service/product including clothing, accessory, expectations. If you are a product please enter the delivery time."></em>
                                    </div>
                                    <div class="form-control-wrap"><textarea name="how_to_prepare"
                                            class="form-control form-control-sm textarea_cstm how_to_prepare"
                                            id="cf-default-textarea"
                                            placeholder="Include what to bring, what to wear, and anything else a customer should know. This information will be sent to customer who make a reservation.">@if(!empty($p)){{ $p->how_to_prepare}}@endif</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">

                                    <div class="inform-tool"> <label class="form-label" for="facilities_icon">How to
                                            access my location</label><em class="card-hint icon ni ni-help-fill"
                                            data-bs-placement="right" data-toggle="tooltip"
                                            title="Tell your customer how they should get to you. If you are online, type ‘I will send you video call links’. If you are a product, ignore."></em>
                                    </div>
                                    <div class="form-control-wrap"><textarea name="how_to_get_there"
                                            class="form-control form-control-sm textarea_cstm how_to_get_there"
                                            id="cf-default-textarea"
                                            placeholder="Add any notes to help customer easily find your location. This information will be sent to customer who make a reservation.">@if(!empty($p)){{ $p->how_to_get_there}}@endif</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="inform-tool">
                                        <label class="form-label" for="facilities_icon">Add Search Keywords</label>
                                        <em class="card-hint icon ni ni-help-fill" data-bs-placement="right"
                                            data-toggle="tooltip"
                                            title="Add a minimum of 10 key words, these words should explain your business, these words help your business profile show up in AI search results. E.g A Personal Trainer may use – Fitness, Weight Training, Muscle Mass, Fat Loss, Exercise, HIIT, Personal Training, Cardio, Endurance etc."></em>
                                    </div>


                                    <div class="form-control-wrap searchkeywordsdiv">
                                        <div id="tags-container">
                                            @if(!empty($p))
                                            <?php
                                                $tagsString = $p->search_keyword;
                                                $tagsArray = explode(',', $tagsString);
                                                $tagsArray = array_map('trim', $tagsArray);
                                                if(!empty($tagsArray[0])){             
                                                   foreach($tagsArray as $keywords){  ?>
                                            <div class="tag"><span>{{$keywords}}</span><button
                                                    class="closebtn_cst">✖</button></div>
                                            <?php } } ?>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="cstm-input-div">
                                        <input type="text" id="tag-input" class="form-control form-control-sm cstm-in"
                                            placeholder="Type search keywords and click on Add Keyword" />
                                        <button id="add-tag-btn" class="btn btn-primary">Add Keyword</button>
                                    </div>
                                    <input type="hidden" id="id_search_keyword" name="search_keyword"
                                        class="form-control form-control-sm search_keyword"
                                        value="@if(!empty($p)){{$p->search_keyword}}@endif" />



                                </div>
                            </div>

                            <?php 
                           if(!empty($p) && $p->zoom_selected!=''){
                             $selected4 = "checked";
                              }else{
                                 $selected4 = "";
                              }
                           ?>


                            <!-- custom comment start -->

                            <!-- <div class="col-md-12">
                              <div class="form_labl_check">
                                 <div class="form-control-wrap"><input type="checkbox" id="zoom_selected" <?php // echo $selected4; ?> name="zoom_selected"> I work on zoom links.</div>
                              </div>
                           </div>  -->

                            <!-- custom comment end -->



                            <div class="col-md-4">
                                <div class="setps_cstm_btns">
                                    <button type="button" class="btn stepscstm step7" id="step7">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-wizard-head">
                        <h5>Customer Terms of Use</h5>
                    </div>
                    <div class="nk-wizard-content wizard_cstm_content">
                        <div class="feature-heading">
                            <h5>Customer Terms of Use</h5>
                            <em class="card-hint icon ni ni-help-fill" data-bs-placement="right" data-toggle="tooltip"
                                title="Add your T&C’s, if you do not have any Bocoflex T&C’s apply "></em>
                        </div>

                        <?php
                                 $content_term = '';
                                 $colling_off_period = '';
                                 $hours_cop = '';
                                 $cancel_1 = '';
                                 $cancel_2 = '';
                                 $hours_cancel_1 = '';
                                 $hours_cancel_2 = '';
                                 $refund_1 = '';
                                 $refund_2 = '';
                                 $hours_refund_2 = '';

                                 if(!empty($p)){
                                    $content_term = $p->terms_title;
   
                                    if($content_term != ''){
                                       $doc = new DOMDocument();  
                                       $doc->loadHTML($content_term);
                                       $xpath = new DOMXPath($doc);
      
                                       // cop ==============
                                       $colling_off_period = '';
                                       $hours_cop = '';
                                       $cop = $xpath->query("//span[contains(@class, 'new_term_point') and contains(@class, 'cop')]");
      
                                       if ($cop->length > 0) {
                                          $colling_off_period = "cop";
                                          $text_cop = $cop->item(0)->textContent;
                                          preg_match('/refund (\d+) hours/', $text_cop, $matches);
                                          if (isset($matches[1])) {
                                             $hours_cop = $matches[1];
                                             $hours_cop = $hours_cop;
                                          } else {
                                             $hours_cop = '';
                                          }
                                       }
      
                                       // cancelation policy ==========
                                       $cancel_1 = '';
                                       $cancel_2 = '';
                                       $hours_cancel_1 = '';
                                       $hours_cancel_2 = '';
                                       $cp_1 = $xpath->query("//span[contains(@class, 'new_term_point') and contains(@class, 'cp1')]");
                                       $cp_2 = $xpath->query("//span[contains(@class, 'new_term_point') and contains(@class, 'cp2')]");
      
                                       if ($cp_1->length > 0) {
                                          $cancel_1 = "cancel_1";
                                          $text_cancel_1 = $cp_1->item(0)->textContent;
                                          preg_match('/charge (\d+) hours/', $text_cancel_1, $matches);
                                          if (isset($matches[1])) {
                                             $hours_cancel_1 = $matches[1];
                                             $hours_cancel_1 = $hours_cancel_1;
                                          } else {
                                             $hours_cancel_1 = '';
                                          }
                                       }
      
                                       if ($cp_2->length > 0) {
                                          $cancel_2 = "cancel_2";
                                          $text_cancel_2 = $cp_2->item(0)->textContent;
                                          preg_match('/within (\d+) hours/', $text_cancel_2, $matches);
                                          if (isset($matches[1])) {
                                             $hours_cancel_2 = $matches[1];
                                             $hours_cancel_2 = $hours_cancel_2;
                                          } else {
                                             $hours_cancel_2 = '';
                                          }
                                       }
      
      
                                       // refund ===========
                                       $refund_1 = '';
                                       $refund_2 = '';
                                       $hours_refund_2 = '';
                                       $rf_1 = $xpath->query("//span[contains(@class, 'new_term_point') and contains(@class, 'rf1')]");
                                       $rf_2 = $xpath->query("//span[contains(@class, 'new_term_point') and contains(@class, 'rf2')]");
      
                                       if ($rf_1->length > 0) {
                                          $refund_1 = "refund_1";
                                       }
      
                                       if ($rf_2->length > 0) {
                                          $refund_2 = "refund_2";
                                          $text_refund_2 = $rf_2->item(0)->textContent;
                                          preg_match('/within (\d+) days/', $text_refund_2, $matches);
                                          if (isset($matches[1])) {
                                             $hours_refund_2 = $matches[1];
                                             $hours_refund_2 = $hours_refund_2;
                                          } else {
                                             $hours_refund_2 = '';
                                          }
                                       }
                                    }
                                 }
                                 

                              ?>

                        <div class="row gy-3">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="form-label" for="fw-mobile-number"></label>
                                    <div class="form-control-wrap">

                                        <!-- <textarea name="terms_title" class="form-control form-control-sm terms_title mb-3" id="cf-default-textarea" placeholder="Please add your cancellation policy and any terms and conditions that you feel appropriate" spellcheck="false">@if(!empty($p)){{ $p->terms_title}}@endif</textarea> -->

                                        <div class="custom__terms_con">
                                            <div class="cop">
                                                <h6 class="mb-2">Cooling Off Period:</h6>
                                                <span class="my-2">
                                                    Customers can cancel their order free of charge and receive a full
                                                    refund
                                                    <select class="cooling_off_period" name="cooling_off_period"
                                                        id="cooling_off_period">
                                                        <option value="">Select Hours</option>
                                                        <?php
                                                   for ($i = 1; $i <= 48; $i++) {
                                                      $selected_cop = ($i == $hours_cop) ? 'selected' : '';
                                                      echo '<option value="Customers can cancel their order free of charge and receive a full refund ' . $i . ' hours after purchase." '.$selected_cop.'>' . $i . '</option>';
                                                   }
                                                ?>
                                                    </select>
                                                    hours after purchase.
                                                </span>
                                            </div>

                                            <hr>

                                            <div class="cp">
                                                <h6 class="mb-2">Cancellation Policy (not applicable to products):</h6>
                                                <span>
                                                    Customers can cancel their order free of charge
                                                    <select class="cancellation_policy_1" name="cancellation_policy_1"
                                                        id="cancellation_policy_1">
                                                        <option value="">Select Hours</option>
                                                        <?php
                                                   for ($i = 1; $i <= 48; $i++) {
                                                      $selected_cancel_1 = ($i == $hours_cancel_1) ? 'selected' : '';
                                                      echo '<option value="Customers can cancel their order free of charge ' . $i . ' hours prior to the booking time." '.$selected_cancel_1.'>' . $i . '</option>';
                                                   }
                                                ?>
                                                    </select>
                                                    hours prior to the booking time.
                                                </span>
                                                <br><br>
                                                <span>
                                                    Customers will be charged in full if they cancel their order within
                                                    <select class="cancellation_policy_2" name="cancellation_policy_2"
                                                        id="cancellation_policy_2">
                                                        <option value="">Select Hours</option>
                                                        <?php
                                                   for ($i = 1; $i <= 48; $i++) {
                                                      $selected_cancel_2 = ($i == $hours_cancel_2) ? 'selected' : '';
                                                      echo '<option value="Customers will be charged in full if they cancel their order within ' . $i . ' hours prior to the booking time." '.$selected_cancel_2.'>' . $i . '</option>';
                                                   }
                                                ?>
                                                    </select>
                                                    hours prior to the booking time.
                                                </span>
                                            </div>

                                            <hr>

                                            <div class="ref">
                                                <h6 class="mb-2">Refunds (products only):</h6>
                                                <span>
                                                    <input onchange="refund_show('refund_1')" class="refund_1"
                                                        type="checkbox" id="nonref" name="refund_1"
                                                        value="Items are non-refundable"
                                                        <?php echo $refund_1 == 'refund_1' ? 'checked' : ''  ?>>
                                                    <label class="ml-1" for="nonref"> Items are non-refundable</label>
                                                </span>
                                                <div class="my-2">or</div>
                                                <span>
                                                    Customers can return items and request a refund within
                                                    <select class="refund_2" name="refund_2" id="refund_2"
                                                        onchange="refund_show('refund_2')">
                                                        <option value="">Select Days</option>
                                                        <?php
                                                   for ($i = 1; $i <= 14; $i++) {
                                                      $selected_refund_2 = ($i == $hours_refund_2) ? 'selected' : '';
                                                      echo '<option value="Customers can return items and request a refund within ' . $i . ' days after the day the item is received." '.$selected_refund_2.'>' . $i . '</option>';
                                                   }
                                                ?>
                                                    </select>
                                                    days after the day the item is received.
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>




                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- custom code end -->

