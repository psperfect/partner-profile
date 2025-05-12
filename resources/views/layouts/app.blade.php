<?php
$user_id = Auth::user()->id;

$get_integrate_payment = '';
$get_integrate_calendar = '';
$get_integrate_zoom = '';

$full_integrate_payment = '';
$full_integrate_calendar = '';
$full_integrate_zoom = '';

$bus_prof = \App\Models\Business_profile::where('user_id', $user_id)->where('step', 8)->where('profile_status', 'completed')->first();

if ($bus_prof) {
	$get_integrate_payment = $bus_prof->integrate_payment;
	$get_integrate_calendar = $bus_prof->integrate_calendar;
	$get_integrate_zoom = $bus_prof->integrate_zoom;


	if ($get_integrate_payment == 1) {
		$get_is_payment_connected = \App\Models\Payment_detail::where('user_id', $user_id)->where('connect_stripe', 'connected')->first();
		if ($get_is_payment_connected) {
			$full_integrate_payment = 1;
		} else {
			$full_integrate_payment = 0;
		}
	} else {
		$full_integrate_payment = 1;
	}


	if ($get_integrate_calendar == 1) {
		$get_is_calendar_connected = \App\Models\Calendar_access_token::where('user_id', $user_id)->where('code', '!=', '')->where('refresh_token', '!=', '')->first();
		if ($get_is_calendar_connected) {
			$full_integrate_calendar = 1;
		} else {
			$full_integrate_calendar = 0;
		}
	} else {
		$full_integrate_calendar = 1;
	}

	if ($get_integrate_zoom == 1) {
		$get_is_zoom_connected = \App\Models\Zoom_access_token::where('user_id', $user_id)->where('connect_zoom', 'connected')->first();
		if ($get_is_zoom_connected) {
			$full_integrate_zoom = 1;
		} else {
			$full_integrate_zoom = 0;
		}
	} else {
		$full_integrate_zoom = 1;
	}
}
	
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:image" content="{{ url('/') }}/assets/img/og-image.webp" />
    <meta property="og:image:width" content="500" />
    <meta property="og:image:height" content="500" />
    <meta property="og:title" content="Bocoflex - Your Wellness Partner" />
    <meta property="og:url" content="https://bocoflex.com" />
    <meta property="og:site_name" content="Bocoflex" />
    <meta property="og:description"
        content="Explore all kinds of wellness services like family planning, beauty, massage, fitness, nutritionists, pets, nature, etc., all in one place. Visit us now." />
    <meta property="og:type" content="Website" />


    <!-- WhatsApp Meta Tags -->
    <meta property="og:title" content="Bocoflex - Your Wellness Partner">
    <meta property="og:description"
        content="Explore all kinds of wellness services like family planning, beauty, massage, fitness, nutritionists, pets, nature, etc., all in one place. Visit us now.">
    <meta property="og:url" content="https://bocoflex.com">
    <meta property="og:image" content="{{ url('/') }}/assets/img/og-image.webp">


    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:title" content="Bocoflex - Your Wellness Partner">
    <meta name="twitter:description"
        content="Explore all kinds of wellness services like family planning, beauty, massage, fitness, nutritionists, pets, nature, etc., all in one place. Visit us now.">
    <meta name="twitter:image" content="{{ url('/') }}/assets/img/og-image.webp">

    <!-- LinkedIn Meta Tags -->
    <meta property="og:title" content="Bocoflex - Your Wellness Partner">
    <meta property="og:description"
        content="Explore all kinds of wellness services like family planning, beauty, massage, fitness, nutritionists, pets, nature, etc., all in one place. Visit us now.">
    <meta property="og:url" content="https://bocoflex.com">
    <meta property="og:image" content="{{ url('/') }}/assets/img/og-image.webp">
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="500">
    <meta property="og:type" content="website">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ url('/') }}/assets/img/favicon.webp">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/css/app.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/users/css/theme.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/users/css/custom.css?v=2.3">
    <link rel="stylesheet" href="{{ url('/') }}/assets/users/css/dashlite.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/users/css/custom-style.css?v=2.55">
    <link rel="stylesheet" href="{{ url('/') }}/assets/users/css/image-uploader.min.css">

    <link rel="stylesheet" href="{{ url('/') }}/assets/users/css/cstm-admin.css?v=2.2">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Daterangepicker -->
    <!-- Include Required Prerequisites -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
    <!-- Daterangepicker -->

    <!-- Scripts -->
    <script src="{{ url('/') }}/assets/js/app.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

</head>

<body class="nk-body bg-lighter npc-general has-sidebar ">
    <div class="cstm_app_root">
        <div class="container-fluid">
            <div class="nk-app-root">
                <div class="nk-main ">
                    @include('layouts.top_header_user')
                    @include('layouts.guest-header-bar')
                    <div class="nk-wrap">
                        <div class="cstm_nk_wrap">
                            @include('layouts.sidebar_user')
                            <!-- Page Content -->
                            <div class="nk-content">
                                <div class="container-fluid">
                                    <div class="nk-content-inner">


                                        @if(
                                        $full_integrate_payment == 0 || $full_integrate_calendar == 0 ||
                                        $full_integrate_zoom == 0
                                        )
                                        <div class="alert alert-danger message_info" role="alert">
                                            <strong>Your profile is incomplete, please complete the tasks highlighted in
                                                your toolbar.</strong>
                                        </div>
                                        @endif


                                        <div class="nk-content-body">
                                            {{ $slot }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page Content -->

                        </div>
                    </div>
                </div>
            </div>
            <div class="nk-footer p-0">
                <div class="footer_nk_user">
                    <div class="container-fluid">
                        <div class="nk-footer-wraps">
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                    <div class="copyright_nk_footer">
                                        <p> Copyright ©
                                            <script>
                                            document.write(new Date().getFullYear());
                                            </script> | All Rights Reserved
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="{{ url('/') }}/assets/users/js/bundle.js"></script>
    <script src="{{ url('/') }}/assets/users/js/scripts.js"></script>
    <script src="{{ url('/') }}/assets/users/js/jquery.steps.js?v=1.41"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="{{ url('/') }}/assets/users/js/image-uploader.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
    $(function() {
        var start = moment().startOf('month');
        var end = moment();

        setTimeout(() => {
            $("#date_submit").trigger("click");
        }, 800);


        function cb(start, end) {
            $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            $('#startDate').val(start.format('YYYY-MM-DD'));
            $('#endDate').val(end.format('YYYY-MM-DD'));
        }

        function updateDateRange(start, end) {
            $('#reportrange').data('daterangepicker').setStartDate(start);
            $('#reportrange').data('daterangepicker').setEndDate(end);
            cb(start, end);
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')]
            }
        }, cb);

        $('#prevMonthBtn').on('click', function() {
            var newStart = start.clone().subtract(1, 'month');
            var newEnd = end.clone().subtract(1, 'month');
            updateDateRange(newStart, newEnd);
        });

        $('#nextMonthBtn').on('click', function() {
            var newStart = start.clone().add(1, 'month');
            var newEnd = end.clone().add(1, 'month');
            updateDateRange(newStart, newEnd);
        });

        cb(start, end);

        $(document).on("click", "#date_submit", function() {
            jQuery(".download-pdf-button").hide();

            $("body").removeClass("active-loader");
            $("body").find(".active-loader-span").remove();

            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var user__id = $('#user__id').val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '<?=  env("APP_URL") ?>/report_data',
                type: 'POST',
                data: {
                    startDate: startDate,
                    endDate: endDate,
                    user__id: user__id,
                },
                dataType: "html",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                beforeSend: function() {
                    $("body").addClass("active-loader");
                    $("body").append("<span class='active-loader-span'></span>");
                },
                success: function(response) {
                    jQuery("#app-data-here").html(response);
                    if (response.length > 100) {
                        jQuery(".download-pdf-button").show();
                    }
                    $("body").removeClass("active-loader");
                    $("body").find(".active-loader-span").remove();

                },
                error: function(response) {

                }
            });
        });

        jQuery('#start_date_pic').datetimepicker({
            format: 'L'
        });
        jQuery('#end_date_pic').datetimepicker({
            format: 'L'
        });
        jQuery('#start_time_pic').datetimepicker({
            format: 'LT'
        });
        jQuery('#end_time_pic').datetimepicker({
            format: 'LT'
        });
    });

    setTimeout(() => {

        var bus_id = $("#bus_id").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $(document).on("click", "#step1", function() {
            var bus_id = $("#bus_id").val();
            if ($("#hd_pf_logo").val() == '') {
                if ($('#pf_logo')[0].files.length == 0) {
                    $("#logo-error").html("This field is required");
                    return false;
                } else {
                    $("#logo-error").html("");
                }
                var file_size = $('#pf_logo')[0].files[0].size;
                if (file_size > 5000000) {
                    $("#logo-error").html("Please upload an image that is less than 5 MB in size.");
                    return false;
                } else {
                    $("#logo-error").html("");
                }
            }
            var file_data = $('#pf_logo').prop('files')[0];
            var hd_pf_logo = $("#hd_pf_logo").val();
            var description = $(".textarea_cstm_foerm").val();
            var pf_contact_name = $("#pf_contact_name").val();

            var form_data = new FormData();
            form_data.append("pf_logo", file_data);
            form_data.append("hd_pf_logo", hd_pf_logo);
            form_data.append("description", description);
            form_data.append("pf_contact_name", pf_contact_name);
            form_data.append("step", 0);
            form_data.append("_token", CSRF_TOKEN);
            form_data.append("bus_id", bus_id);

            ajax_function(form_data, "first", 0);
        })

        $(document).on("click", "#step2", function() {

            var count = $(".custom_checkboxs[type='checkbox']:checked").length;
            if (count > 0) {
                var form_data = new FormData();
                $('.custom_checkboxs:checkbox:checked').each(function(i) {
                    var facilities = $(this).attr('name');
                    form_data.append(facilities, 'on');
                });
                form_data.append("_token", CSRF_TOKEN);
                form_data.append("bus_id", bus_id);
                form_data.append("step", 1);

                ajax_function(form_data, '', 1);
            } else {
                Swal.fire('Cancelled', 'Please select atleast one option', 'error')
                return false;
            }
        })

        $(document).on("click", "#step4", function() {
            var form_data = new FormData();
            var this_name = false;
            var this_type = false;
            var this_hours = false;
            var this_price = false;
            $(document).find('.ser_name').each(function(i) {
                var ser_name = $(this).find('input[name="service_name[' + i + '][name]"]')
            .val();
                var ser_length = $(this).find('input[name="service_name[' + i + '][length]"]')
                    .val();
                var ser_price = $(this).find('input[name="service_name1[' + i + '][cost]"]')
                    .val();
                var ser_price2 = $(this).find('input[name="service_name2[' + i + '][cost]"]')
                    .val();
                var service_desc = $(this).find('textarea[name="service_name[' + i +
                    '][service_desc]"]').val();
                var service_type = $(this).find('select[name="service_name[' + i +
                    '][service_type]"]').val();
                var service__cat = $(this).find('select[name="service_name[' + i +
                    '][service__cat]"]').val();
                var service_hours = $(this).find('select[name="service_name[' + i +
                        '][hours]"]')
                    .val();
                var service_minutes = $(this).find('select[name="service_name[' + i +
                    '][minutes]"]').val();

                if (ser_name === "" || ser_name === 0) {
                    this_name = true;
                }

                if (service_type === "") {
                    this_type = true;
                }

                if (service_hours === "" || service_hours === "00") {
                    if (service_minutes === "" || service_minutes === "00") {
                        this_hours = true;
                    } else {
                        this_hours = false;
                    }
                }

                if (ser_price === "" || ser_price === 0) {
                    this_price = true;
                }

                form_data.append("service_name[" + i + "][name]", ser_name);
                form_data.append("service_name[" + i + "][length]", ser_length);
                form_data.append("service_name[" + i + "][cost]", ser_price);
                form_data.append("service_name[" + i + "][cost2]", ser_price2);
                form_data.append("service_name[" + i + "][service_desc]", service_desc);
                form_data.append("service_name[" + i + "][service_type]", service_type);
                form_data.append("service_name[" + i + "][service__cat]", service__cat);
                form_data.append("service_name[" + i + "][service_hours]", service_hours);
                form_data.append("service_name[" + i + "][service_minutes]", service_minutes);

            })
            form_data.append("_token", CSRF_TOKEN);
            form_data.append("step", 2);
            form_data.append("bus_id", bus_id);

            if (this_name === true) {
                Swal.fire('Cancelled', 'Please fill service/product name', 'error')
                return false;
            }
            if (this_price === true) {
                Swal.fire('Cancelled', 'Please fill service/product price', 'error')
                return false;
            }
            if (this_type === true) {
                Swal.fire('Cancelled', 'Please fill service/product type', 'error')
                return false;
            }
            if (this_hours === true) {
                Swal.fire('Cancelled', 'Please fill service/product duration', 'error')
                return false;
            }
            ajax_function(form_data, '', 2);
        })

        $(document).on("click", "#step3", function() {


            var count = $(".cstmtime_slots input[type='checkbox']:checked").length;
            if (count > 0) {
       
                var form_data = new FormData();
                var arr = [];
                var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
                    'Saturday'];
                var start_time__ = false;
                var end_time__ = false;
                var start_time_count = 0;
                var isValidend = false;
                $.each(days, function(i, day) {
                    var hop = 0
                    $('.sunday_div:visible input[name="start_srv_timesloat[' + day + '][]"]')
                        .each(
                            function(x) {
                                var start_time = $(this).val();
                                var end_time = $('input[name="end_srv_timesloat[' + day +
                                        '][]"]')
                                    .eq(hop).val();
                                if (start_time >= end_time) {
                                    isValidend = true;
                                }
                                if (start_time === 0 || start_time === "") {
                                    start_time__ = true;
                                }

                                hop++
                                start_time_count++;
                                form_data.append("start_srv_timesloat[" + day + "][" + x + "]",
                                    start_time);
                            })
                    $('.sunday_div:visible input[name="end_srv_timesloat[' + day + '][]"]')
                        .each(
                        function(x) {
                                var end_time = $(this).val();
                                if (end_time === 0 || end_time === "") {
                                    end_time__ = true;
                                }
                                form_data.append("end_srv_timesloat[" + day + "][" + x + "]",
                                    end_time);
                        })
                })


                form_data.append("start_time_count", start_time_count);

                $('.custom_checkbox:checkbox:checked').each(function(i) {
                    var set_services_selected_day = $(this).attr('name');
                    form_data.append(set_services_selected_day, 'on');
                })

                var weeks = [];
                $('.custom_checkbox:checkbox:checked').each(function(i) {
                    var week = $(this).next('.spantexts').html();
                    weeks.push(week);;
                })
                var weeksString = weeks.join(',');
                form_data.append('weeks', weeksString);

                if (start_time__) {
                    Swal.fire('Cancelled', 'Please select start time', 'error')
                    return false;
                }
                if (end_time__) {
                    Swal.fire('Cancelled', 'Please select end time', 'error')
                    return false;
                }
                if (isValidend) {
                    Swal.fire('Cancelled', 'Start time must be less than end time.', 'error')
                    return false;
                }

                var overlap = false;
                $('.sunday_div:visible').each(function(x) {
                    var arr_start = [];
                    var end_start = [];
                    var log_start = $(this).find('[name*="start_srv_timesloat"]');
                    var log_end = $(this).find('[name*="end_srv_timesloat"]');
                    console.log(log_start.length)

                    if (log_start.length > 1) {
                        for (let i = 0; i < log_start.length; i++) {
                            var startValue1 = log_start.eq(i).val();
                            var endValue1 = log_end.eq(i).val();

                            arr_start.push(startValue1);
                            end_start.push(endValue1);
                        }
                        for (var i = 0; i < arr_start.length; i++) {
                            for (var j = 0; j < arr_start.length; j++) {
                                if (i !== j && (
                                        (arr_start[i] >= arr_start[j] && arr_start[i] <
                                            end_start[
                                                j]) ||
                                        (arr_start[i] <= arr_start[j] && end_start[i] >
                                            arr_start[
                                                j])
                                    )) {
                                    overlap = true;
                                    break;
                                }
                            }
                            if (overlap) {
                                break;
                            }
                        }
                    }
                });
                if (overlap) {
                    Swal.fire('Cancelled', 'Time overlap. Please adjust your time schedule.', 'error')
                    return false;
                }

                form_data.append("_token", CSRF_TOKEN);
                form_data.append("step", 3);
                form_data.append("bus_id", bus_id);
                ajax_function(form_data, '', 3);

            } else {
                Swal.fire('Cancelled', 'Please select atleast one option', 'error')
                return false;
            }
        })


        $(document).on("click", "#step6", function() {


            var isValidimg = false;
            const totalImages = $("input[name*='gallery_images']")[0].files.length;

            var gallery_img_input = $(".gallery_img_input").val();
            if (gallery_img_input) {
                isValidimg = true;
            } else {
                if (totalImages == 0) {
                    isValidimg = false;
                } else {
                    isValidimg = true;
                }
            }

            if (isValidimg == false) {
                Swal.fire('Cancelled', 'Please upload image', 'error')
                return false;
            }

            var form_data = new FormData();

            let images = $("input[name*='gallery_images']")[0];
            for (let i = 0; i < totalImages; i++) {
                form_data.append('gallery_images[' + i + ']', images.files[i]);
            }
            form_data.append('totalImages', totalImages);

            form_data.append("_token", CSRF_TOKEN);
            form_data.append("step", 5);
            form_data.append("bus_id", bus_id);
            ajax_function(form_data, "gallery", 5);

        })

        $(document).on("click", "#step7", function() {


            if ($('#zoom_selected').is(':checked')) {
                var zoom_selected = $("#zoom_selected").val();
            } else {
                var zoom_selected = '';
            }

            var how_to_prepare = $(".how_to_prepare").val();

            var search_keyword = $(".search_keyword").val();
            if (search_keyword == '') {
                Swal.fire('Cancelled', 'Please add some search keywords', 'error')
                return false;
            }

            var how_to_get_there = $(".how_to_get_there").val();

            var form_data = new FormData();
            form_data.append("how_to_prepare", how_to_prepare);

            form_data.append("search_keyword", search_keyword);

            form_data.append("how_to_get_there", how_to_get_there);
            form_data.append("zoom_selected", zoom_selected);

            form_data.append("_token", CSRF_TOKEN);
            form_data.append("step", 6);
            form_data.append("bus_id", bus_id);


            ajax_function(form_data, '', 6);

        })
        $(document).on("click", "#step8", function() {

            var form_data = new FormData();
            var terms_title = $(".terms_title").val();

            var cooling_off_period = $(".cooling_off_period").val();

            form_data.append("terms_title", terms_title);

            form_data.append("cooling_off_period", cooling_off_period);

            form_data.append("_token", CSRF_TOKEN);
            form_data.append("step", 7);
            form_data.append("bus_id", bus_id);
            ajax_function(form_data, '', 7);
        })


        function ajax_function(form_data, extra = '', count_pr = '') {
            $.ajax({
                url: "/multistep_data",
                type: "POST",

                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    if (extra == "first") {
                        $("#step1").css("pointer-events", "none");
                        $(".actions").css("pointer-events", "none");
                    }
                },
                success: function(data) {


                    if (data) {
                        var dataJsonResponse = JSON.parse(data);
                        if (dataJsonResponse.i_deliver_online_sessions__val != undefined) {
                            if (dataJsonResponse.i_deliver_online_sessions__val == 'true') {
                                $('#i_deliver_online_sessions').val('true');
                                $("#i_deliver_online_sessions").prop("checked", true);
                            } else {
                                $('#i_deliver_online_sessions').val('false');
                                $("#i_deliver_online_sessions").prop("checked", false);
                            }
                        }
                        if (dataJsonResponse.i_travel_to_you__val != undefined) {
                            if (dataJsonResponse.i_travel_to_you__val == 'true') {
                                $('#i_travel_to_you').val('true');
                                $("#i_travel_to_you").prop("checked", true);
                            } else {
                                $('#i_travel_to_you').val('false');
                                $("#i_travel_to_you").prop("checked", false);
                            }
                        }
                        if (dataJsonResponse.you_travel_to_me__val != undefined) {
                            if (dataJsonResponse.you_travel_to_me__val == 'true') {
                                $('#you_travel_to_me').val('true');
                                $("#you_travel_to_me").prop("checked", true);
                            } else {
                                $('#you_travel_to_me').val('false');
                                $("#you_travel_to_me").prop("checked", false);
                            }
                        }
                        if (dataJsonResponse.i_send_products_to_you__val != undefined) {
                            if (dataJsonResponse.i_send_products_to_you__val == 'true') {
                                $('#i_send_products_to_you').val('true');
                                $("#i_send_products_to_you").prop("checked", true);
                            } else {
                                $('#i_send_products_to_you').val('false');
                                $("#i_send_products_to_you").prop("checked", false);
                            }
                        }
                    }



                    if (data) {
                        // setProgressBar(count_pr);
                        if (extra == "gallery") {
                            $(".gallery-img").html(data);
                            $(".delete-image").trigger("click")
                        }

                        if (extra == "first") {
                            $("#bus_id").val(data);
                            $(".actions").removeAttr("style");
                            $("#step1").removeAttr("style");
                        }
                        if (extra == "last") {
                            setProgressBar(100)
                            location.reload();
                        }

                        $(".show-loader").remove();
                        $("#success_msg").addClass('alert-success').html("profile updated")
                        setTimeout(function() {
                            $('#success_msg').removeClass('alert-success').html("");
                        }, 5000);
                    }
                },
                error: function(xhr, status, error) {
                    $(".actions").removeAttr("style");
                    $.each(xhr.responseJSON.errors, function(key, item) {
                        $("#success_msg").addClass('alert-danger').html(item)
                        setTimeout(function() {
                            $('#success_msg').removeClass('alert-danger').html("");
                        }, 5000);
                    });
                    return false;
                }
            });
        }

        function setProgressBar(curStep) {
            percent = curStep;
            $(".progress-bar")
                .css("width", percent + "%")
                .html(percent + "%");
        }

        $('a[href="#next"').click(function() {
            let current = $(this).closest("div").prev().find("div[aria-hidden='false']").attr('id');
           
            if (current == 'wizard-01-p-1') {
                setProgressBar(18)
            }
            if (current == 'wizard-01-p-2') {
                setProgressBar(35)
            }
            if (current == 'wizard-01-p-3') {
                setProgressBar(50)
            }
            if (current == 'wizard-01-p-4') {
                setProgressBar(68)
            }
            if (current == 'wizard-01-p-5') {
                setProgressBar(80)
            }
            if (current == 'wizard-01-p-6') {
                setProgressBar(94)
            }

        });

        $('a[href="#previous"').click(function() {
            let current = $(this).closest("div").prev().find("div[aria-hidden='false']").attr('id');
            console.log(current);
            if (current == 'wizard-01-p-1') {
                setProgressBar(18)
            }
            if (current == 'wizard-01-p-2') {
                setProgressBar(35)
            }
            if (current == 'wizard-01-p-3') {
                setProgressBar(50)
            }
            if (current == 'wizard-01-p-4') {
                setProgressBar(68)
            }
            if (current == 'wizard-01-p-5') {
                setProgressBar(80)
            }
            if (current == 'wizard-01-p-6') {
                setProgressBar(94)
            }



        });


        if (currentStep == 0) {
            var currentStep = parseInt(`{{$step}}`);
        } else {
            var currentStep = parseInt(`{{$step}}`) + 1;
        }
        console.log(currentStep);


        if (currentStep == 0) {
            setProgressBar(5)
        }
        if (currentStep == 1) {
            setProgressBar(18)
        }
        if (currentStep == 2) {
            setProgressBar(35)
        }
        if (currentStep == 3) {
            setProgressBar(50)
        }
        if (currentStep == 4) {
            setProgressBar(68)
        }
        if (currentStep == 5) {
            setProgressBar(80)
        }
        if (currentStep == 6) {
            setProgressBar(94)
        }

        // custom code end =====

        $(function() {
            for (var i = 0, l = currentStep; i < l; i++) {
                $("#wizard-01").steps("next");
            }
        });

        var cat_maxField = 5;
        var cat_addButton = $('.add_button_cat');
        var cat_wrapper = $('.field_wrapper_falit_cat');
        var count_service = $('.count_service').length;
        // var z =count_service;
        $('.add_button_cat').click(function() {

            var get_parent_class_count = $(".count_service").length;
            var z = get_parent_class_count;



            if (z < cat_maxField) {

                var loophtml =
                    '<div class="services_label"><label class="form-label" for="service_hours_' +
                    z + '">Hours</label><select name="service_name[' + z +
                    '][hours]" onchange="funhours(this)" id="hours_' + z + '" title="' + z + '">';
                for (var str = 0, l = 12; str <= l; str++) {
                    str = str.toString();
                    var val = str.length < 2 ? "0" + str : str;
                    loophtml += '<option value="' + val + '">' + val + '</option>';
                }
                loophtml +=
                    '</select></div> <div class="services_label"><label class="form-label" for="service_min_' +
                    z + '">Minutes</label><select name="service_name[' + z +
                    '][minutes]" onchange="funminutes(this)" id="minutes_' + z + '" title="' + z + '">';

                for (var str2 = 0, l2 = 59; str2 <= l2; str2++) {
                    str2 = str2.toString();
                    var val2 = str2.length < 2 ? "0" + str2 : str2;
                    loophtml += '<option value="' + val2 + '">' + val2 + '</option>';
                }
                loophtml += '</select></div>';

                var cat_fieldHTML =
                    '<div class="row gy-3 count_service ser_name relative mt-2"><div class="col-md-6"><div class="form-group"><div class="inform-tool"><label class="form-label" for="service_name">Service/Product Name</label><em class="card-hint icon ni ni-help-fill" data-bs-placement="top" data-toggle="tooltip" title="What service/product are you offering?"></em></div><div class="form-control-wrap"><input type="text" data-msg="Required" class="form-control required form_control_cstm" id="service_name" name="service_name[' +
                    z +
                    '][name]" ></div></div></div>  <div class="col-lg-6 col-md-12"><div class="form-group"><div class="inform-tool"><label class="form-label" for="service__cat">Category</label><em class="card-hint icon ni ni-help-fill" data-bs-placement="top" data-toggle="tooltip" title="Select the category that the service/product fits into (e.g. Fitness for Personal Training)"></em></div><div class="form-control-wrap"><select class="form-control cstm_input_form" id="service__cat" name="service_name[' +
                    z +
                    '][service__cat]"><option value="">Select</option>@foreach($get_user_cat as $category)<option value="{{$category->category_id}}">{{$category->category_name}}</option>@endforeach</select></div></div></div>   <div class="col-md-12"><div class="form-group"><div class="inform-tool"><label class="form-label" for="service_name">Service/Product Description</label><em class="card-hint icon ni ni-help-fill" data-bs-placement="top" data-toggle="tooltip" title="Give detail of the service/product eg. service/product length, goal, who’s it aimed at, what the user receives etc. Use key words that assist in AI searches."></em></div><div class="form-control-wrap"><textarea class="form-control cstm_input_form " id="service_name" name="service_name[' +
                    z +
                    '][service_desc]"></textarea> </div></div></div>        <div class="col-lg-4 col-md-12 service__type_width"><div class="form-group"><div class="inform-tool"><label class="form-label" for="service_name">Service/Product Type</label><em class="card-hint icon ni ni-help-fill" data-bs-placement="top" data-toggle="tooltip" title="Select how you deliver this service/product (e.g You come to me for a Gym Membership)"></em></div><div class="form-control-wrap"><select class="form-control cstm_input_form service__type" id="service_name" name="service_name[' +
                    z +
                    '][service_type]"><option value="">Select</option><option value="option1">I deliver online sessions</option><option value="option2">I travel to you </option><option value="option3">You travel to me </option><option value="option4">I send products to you </option></select></div></div></div><div class="col-lg-4 col-md-6 service__dur"><div class="form-group"><div class="inform-tool"><label class="form-label" for="service_length_' +
                    z +
                    '">Service Duration</label><em class="card-hint icon ni ni-help-fill" data-bs-placement="top" data-toggle="tooltip" title="How long is the service/product?"></em></div><div class="form-control-wrap"><input type="hidden" class="form-control form_control_cstm" id="service_length_' +
                    z + '" name="service_name[' + z + '][length]" value="1">' + loophtml +
                    '</div></div></div><div class="col-lg-4 col-md-6"><div class="form-group"><input type="hidden" class="form-control cstm_input_form" id="service_cost_val_' +
                    z + '" name="service_name[' + z +
                    '][cost]" value="0"><div class="form-group"><div class="inform-tool"><label class="form-label" for="service_cost">Service/Product Cost (£)</label><em class="card-hint icon ni ni-help-fill" data-bs-placement="top" data-toggle="tooltip" title="How much is the service/product? £ in the left box and pennies in the right box)"></em></div><div class="form-control-wrap"><input type="number" class="form-control" id="service_cost1_' +
                    z + '" name="service_name1[' + z + '][cost]" onblur="sercost(' + z +
                    ')"/> <b>.</b><input type="number" class="form-control" id="service_cost2_' + z +
                    '" name="service_name2[' + z + '][cost]" onblur="sercost2(' + z +
                    ')" /></div></div></div></div><div class="cstm-remove-btn"><a href="javascript:void(0);" class="remove_button_cat"><em class="icon ni ni-trash"></em></a></div></div>';

                $(function() {
                    $('[data-toggle="tooltip"]').tooltip();
                });

                z++; //Increment field counter
                $(cat_wrapper).append(cat_fieldHTML); //Add field html
                if (z > 4) {
                    $(cat_addButton).hide();
                }
            }
        });

        $(cat_wrapper).on('click', '.remove_button_cat', function(e) {
            e.preventDefault();
            $(this).closest('.count_service').remove();
            z--;
            if (z < 5) {
                $(cat_addButton).show();
            }
        });

        jQuery('input[class="custom_checkbox"]:checked').each(function() {
            var inputValue = jQuery(this).attr("data-slectedday");
            jQuery("#days_div" + inputValue).show();
        });

        jQuery('input[class="custom_checkbox"]').click(function() {
            var inputValue = jQuery(this).attr("data-slectedday");
            jQuery("#days_div" + inputValue).toggle();
        });

        var max_fields = 100; //maximum input boxes allowed
        var wrapper_sun = jQuery(".input_custom_field"); //Fields wrapper
        var wrapper_mon = jQuery(".input_custom_field_mon"); //Fields wrapper
        var wrapper_tue = jQuery(".input_custom_field_tue"); //Fields wrapper
        var wrapper_wed = jQuery(".input_custom_field_wed"); //Fields wrapper
        var wrapper_thurs = jQuery(".input_custom_field_thurs"); //Fields wrapper
        var wrapper_fri = jQuery(".input_custom_field_fri"); //Fields wrapper
        var wrapper_sat = jQuery(".input_custom_field_sat"); //Fields wrapper
        var add_button = jQuery(".add_field_button"); //Add button ID
        var add_button_mon = jQuery(".add_field_button_mon"); //Add button ID
        var add_button_tue = jQuery(".add_field_button_tue"); //Add button ID
        var add_button_wed = jQuery(".add_field_button_wed"); //Add button ID
        var add_button_thurs = jQuery(".add_field_button_thurs"); //Add button ID
        var add_button_fri = jQuery(".add_field_button_fri"); //Add button ID
        var add_button_sat = jQuery(".add_field_button_sat"); //Add button ID

        var x = 1; //initlal text box count
        jQuery(add_button).on('click keydown', function(e) { //on add input button click
            e.preventDefault();
            if (x < max_fields) { //max input box allowed
                x++; //text box increment
                jQuery(wrapper_sun).append(
                    '<div class="input_custom_field"><div class="input_cstm_field_days"><input type="time" id="appt_timesloat" name="start_srv_timesloat[Sunday][]" data-msg="Required" class="form-control cstm_input_form" > - <input type="time" id="appt_timesloat" name="end_srv_timesloat[Sunday][]" data-msg="Required" class="form-control cstm_input_form" ><a href="#" class="remove_field"><em class="icon ni ni-trash"></em></a></div></div>'
                ); //add input box
            }
        });
        var y = 1; //initlal text box count
        jQuery(add_button_mon).click(function(e) { //on add input button click
            e.preventDefault();
            if (y < max_fields) { //max input box allowed
                y++; //text box increment
                jQuery(wrapper_mon).append(
                    '<div class="input_custom_field_mon"><div class="input_cstm_field_days"><input type="time" id="appt_timesloat" name="start_srv_timesloat[Monday][]" data-msg="Required" class="form-control cstm_input_form" > - <input type="time" id="appt_timesloat" name="end_srv_timesloat[Monday][]" data-msg="Required" class="form-control cstm_input_form" ><a href="#" class="remove_field_mon"><em class="icon ni ni-trash"></em></a></div></div>'
                ); //add input box
            }
        });
        var z = 1; //initlal text box count
        jQuery(add_button_tue).click(function(e) { //on add input button click
            e.preventDefault();
            if (z < max_fields) { //max input box allowed
                z++; //text box increment
                jQuery(wrapper_tue).append(
                    '<div class="input_custom_field_tue"><div class="input_cstm_field_days"><input type="time" id="appt_timesloat" name="start_srv_timesloat[Tuesday][]" data-msg="Required" class="form-control cstm_input_form" > - <input type="time" id="appt_timesloat" name="end_srv_timesloat[Tuesday][]" data-msg="Required" class="form-control cstm_input_form" ><a href="#" class="remove_field_tue"><em class="icon ni ni-trash"></em></a></div></div>'
                ); //add input box
            }
        });
        var t = 1; //initlal text box count
        jQuery(add_button_wed).click(function(e) { //on add input button click
            e.preventDefault();
            if (t < max_fields) { //max input box allowed
                t++; //text box increment
                jQuery(wrapper_wed).append(
                    '<div class="input_custom_field_wed"><div class="input_cstm_field_days"><input type="time" id="appt_timesloat" name="start_srv_timesloat[Wednesday][]" data-msg="Required" class="form-control cstm_input_form" > - <input type="time" id="appt_timesloat" name="end_srv_timesloat[Wednesday][]" data-msg="Required" class="form-control cstm_input_form" ><a href="#" class="remove_field_wed"><em class="icon ni ni-trash"></em></a></div></div>'
                ); //add input box
            }
        });
        var t = 1; //initlal text box count
        jQuery(add_button_thurs).click(function(e) { //on add input button click
            e.preventDefault();
            if (t < max_fields) { //max input box allowed
                t++; //text box increment
                jQuery(wrapper_thurs).append(
                    '<div class="input_custom_field_thurs"><div class="input_cstm_field_days"><input type="time" id="appt_timesloat" name="start_srv_timesloat[Thursday][]" data-msg="Required" class="required"> - <input type="time" id="appt_timesloat" name="end_srv_timesloat[Thursday][]" data-msg="Required" class="form-control cstm_input_form" ><a href="#" class="remove_field_thurs"><em class="icon ni ni-trash"></em></a></div></div>'
                ); //add input box
            }
        });
        var t = 1; //initlal text box count
        jQuery(add_button_fri).click(function(e) { //on add input button click
            e.preventDefault();
            if (t < max_fields) { //max input box allowed
                t++; //text box increment
                jQuery(wrapper_fri).append(
                    '<div class="input_custom_field_fri"><div class="input_cstm_field_days"><input type="time" id="appt_timesloat" name="start_srv_timesloat[Friday][]" data-msg="Required" class="form-control cstm_input_form" > - <input type="time" id="appt_timesloat" name="end_srv_timesloat[Friday][]" data-msg="Required" class="form-control cstm_input_form" ><a href="#" class="remove_field_fri"><em class="icon ni ni-trash"></em></a></div></div>'
                ); //add input box
            }
        });
        var t = 1; //initlal text box count
        jQuery(add_button_sat).click(function(e) { //on add input button click
            e.preventDefault();
            if (t < max_fields) { //max input box allowed
                t++; //text box increment
                jQuery(wrapper_sat).append(
                    '<div class="input_custom_field_sat"><div class="input_cstm_field_days"><input type="time" id="appt_timesloat" name="start_srv_timesloat[Saturday][]" data-msg="Required" class="form-control cstm_input_form" > - <input type="time" id="appt_timesloat" name="end_srv_timesloat[Saturday][]" data-msg="Required" class="form-control cstm_input_form" ><a href="#" class="remove_field_sat"><em class="icon ni ni-trash"></em></a></div></div>'
                ); //add input box
            }
        });

        jQuery(document).on("click", ".remove_field_mon", function(e) {
            e.preventDefault();
            jQuery(this).parent().remove();
        });


        jQuery(wrapper_sun).on("click", ".remove_field", function(e) { //user click on remove text
            e.preventDefault();
            jQuery(this).parent('div').remove();
            x--;
        });
        jQuery(wrapper_mon).on("click", ".remove_field_mon", function(e) { //user click on remove text
            e.preventDefault();
            jQuery(this).parent('div').remove();
            x--;
        });
        jQuery(wrapper_tue).on("click", ".remove_field_tue", function(e) { //user click on remove text
            e.preventDefault();
            jQuery(this).parent('div').remove();
            x--;
        });
        jQuery(wrapper_wed).on("click", ".remove_field_wed", function(e) { //user click on remove text
            e.preventDefault();
            jQuery(this).parent('div').remove();
            x--;
        });
        jQuery(wrapper_thurs).on("click", ".remove_field_thurs", function(e) { //user click on remove text
            e.preventDefault();
            jQuery(this).parent('div').remove();
            x--;
        });
        jQuery(wrapper_fri).on("click", ".remove_field_fri", function(e) { //user click on remove text
            e.preventDefault();
            jQuery(this).parent('div').remove();
            x--;
        });
        jQuery(wrapper_sat).on("click", ".remove_field_sat", function(e) { //user click on remove text
            e.preventDefault();
            jQuery(this).parent('div').remove();
            x--;
        });


        $('.input-images').imageUploader({
            imagesInputName: 'gallery_images',
            extensions: ['.jpg', '.jpeg', '.png', '.gif', '.svg'],
            mimes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/svg+xml'],
            maxSize: 5 * 1024 * 1024,
        });
        $('#gallary_count').val('');

        $("input[name*='gallery_images']").click(function() {
            $('#gallary_count').val($(this).val());
        })

    }, 50);

    $(document).ready(function() {

        $(".count_service").each(function() {
            var serviceTypeElement = $(this).find(".service__type");
            if (serviceTypeElement.val() === "option3" || serviceTypeElement.val() === "option4") {
                $(this).find(".service__dur").hide()
                $(this).find(".service__type_width").removeClass("col-lg-4").addClass("col-lg-4")
            }
        });

        $(document).on("change", ".service__type", function() {
            var selectedValue = $(this).val();
            if (selectedValue == "option3" || selectedValue == "option4") {
                $(this).parents(".count_service").find(".service__dur").hide()
                var ty = $(this).parents(".count_service").find(".service__dur select")[0];
                $(ty).val("01");
                $(this).parents(".count_service").find(".service__type_width").removeClass("col-lg-4")
                    .addClass("col-lg-4")
            } else {
                $(this).parents(".count_service").find(".service__dur").show()
                $(this).parents(".count_service").find(".service__dur select").val("00")
                $(this).parents(".count_service").find(".service__type_width").removeClass("col-lg-4")
                    .addClass("col-lg-4")
            }
        });

    });


    $(document).ready(function() {
        const $tagsContainer = $('#tags-container');
        const $tagInput = $('#tag-input');
        const $hiddenInput = $('#id_search_keyword');
        const $addTagBtn = $('#add-tag-btn');

        $(document).on('keydown', function(event) {
            if (event.key === 'Enter' && $tagInput.is(':focus') && $tagInput.val().trim() === '') {
                event.preventDefault(); // Prevent the default behavior
            }
        });

        $tagInput.on('keydown', function(event) {
            if (event.key === 'Enter' && $tagInput.val().trim() !== '') {
                event.preventDefault();
            }
        });

        $addTagBtn.on('click', function(event) {
            event.preventDefault();
            const tagText = $tagInput.val().trim();
            if (tagText !== '') {
                addTag(tagText);
                $tagInput.val('');
                updateHiddenInput();
            }
        });

        $tagsContainer.on('click', '.closebtn_cst', function(event) {
            event.stopPropagation();
            $(this).parent().remove();
            updateHiddenInput();
        });

        function addTag(text) {
            const $tag = $('<div class="tag"></div>')
                .append('<span>' + text + '</span>')
                .append('<button class="closebtn_cst">&#10006;</button>')
                .on('click', function() {
                    $(this).remove();
                    updateHiddenInput();
                });
            $tagsContainer.append($tag);
        }

        function updateHiddenInput() {
            const tagsArray = $tagsContainer.find('.tag span').map(function() {
                return $(this).text();
            }).get();
            $hiddenInput.val(tagsArray.join(','));
        }
    });

    function check__loc(checkbox) {
        if (checkbox.id == 'i_deliver_online_sessions' && checkbox.value == 'false') {
            Swal.fire('Cancelled', 'You can not selected, because you did not pick this kind of service.', 'error')
            $('#i_deliver_online_sessions').prop("checked", false);
            return false;
        }
        if (checkbox.id == 'i_travel_to_you' && checkbox.value == 'false') {
            Swal.fire('Cancelled', 'You can not selected, because you did not pick this kind of service.', 'error')
            $('#i_travel_to_you').prop("checked", false);
            return false;
        }
        if (checkbox.id == 'you_travel_to_me' && checkbox.value == 'false') {
            Swal.fire('Cancelled', 'You can not selected, because you did not pick this kind of service.', 'error')
            $('#you_travel_to_me').prop("checked", false);
            return false;
        }
        if (checkbox.id == 'i_send_products_to_you' && checkbox.value == 'false') {
            Swal.fire('Cancelled', 'You can not selected, because you did not pick this kind of service.', 'error')
            $('#i_send_products_to_you').prop("checked", false);
            return false;
        }


        if (checkbox.id == 'i_deliver_online_sessions' && checkbox.value == 'true') {
            Swal.fire('Cancelled', 'You cannot deselect it since you have already chosen this type of service.',
                'error')
            $('#i_deliver_online_sessions').prop("checked", true);
            return false;
        }
        if (checkbox.id == 'i_travel_to_you' && checkbox.value == 'true') {
            Swal.fire('Cancelled', 'You cannot deselect it since you have already chosen this type of service.',
                'error')
            $('#i_travel_to_you').prop("checked", true);
            return false;
        }
        if (checkbox.id == 'you_travel_to_me' && checkbox.value == 'true') {
            Swal.fire('Cancelled', 'You cannot deselect it since you have already chosen this type of service.',
                'error')
            $('#you_travel_to_me').prop("checked", true);
            return false;
        }
        if (checkbox.id == 'i_send_products_to_you' && checkbox.value == 'true') {
            Swal.fire('Cancelled', 'You cannot deselect it since you have already chosen this type of service.',
                'error')
            $('#i_send_products_to_you').prop("checked", true);
            return false;
        }
    }

    $('[data-toggle="tooltip"]').tooltip();



    if ($('.refund_1').is(':checked')) {
        $('.refund_1').val("Items are non-refundable");
    } else {
        $('.refund_1').val("");
    }

    function refund_show(sec) {
        var section = sec
        if (section == 'refund_1') {
            $('.refund_2').val("");
            if ($('.refund_1').is(':checked')) {
                $('.refund_1').val("Items are non-refundable");
            } else {
                $('.refund_1').val("");
            }
        } else {
            $('.refund_1').prop("checked", false);
            $('.refund_1').val("");
        }
    }
    </script>

</body>

</html>