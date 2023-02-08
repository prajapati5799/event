/**
 * Wrapper function to safely use $
 */
function gujChildWrapper($) {

    /*global generalPar:false, ajaxPar:false, gform:false, wp, GUJModals */
    var gujChild = {

        /**
         * Main entry point
         */
        init: function () {
            gujChild.registerEventHandlers();
            gujChild.datePicker();
            gujChild.eventValidate();
            gujChild.autoAddress();
        },

        /**
         * Registers event handlers
         */
        registerEventHandlers: function () {
            $(document).on('click','#resetFilter',gujChild.resetForm);
            $(document).on('submit','#eventFrm',gujChild.eventAdd);
            $(document).on('submit','#addOrg',gujChild.orgAdd);
        },

         /**
         * Date handlers
         */
        datePicker: function () {
            var selected_date = $(".datepicker").val();
            if (selected_date == '') {
                $('.datepicker1').prop('disabled', true);
            }
            $(".datepicker").datepicker({
                minDate: 0,
                changeMonth: true,
                changeYear: true,
                onSelect: function (date) {
                    selected_date = $(this).val();
                      if (selected_date != '') {
                        $('.datepicker1').prop('disabled', false);
                      }
                    var date2 = $('.datepicker').datepicker('getDate');
                    date2.setDate(date2.getDate());
                    $('.datepicker1').datepicker('setDate', date2);
                    //sets minDate to dt1 date + 1
                    $('.datepicker1').datepicker('option', 'minDate', date2);
                }
            });

            $('.datepicker1').datepicker({
            });

            $('.timepicker').timepicker({
                timeFormat: 'H:mm p',
                interval: 60,
                minTime: '01',
                maxTime: '11:00pm',
                defaultTime: '07',
                startTime: '01:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
            $('.timepicker1').timepicker({
                timeFormat: 'H:mm p',
                interval: 60,
                minTime: '01',
                maxTime: '11:00pm',
                defaultTime: '08',
                startTime: '01:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
        },

        // Reset Map form
        resetForm: function (e) {
            e.preventDefault();
            $('#filter-store').trigger("reset");
            location.reload();
        }, 

        // Validate Add Event Form
        eventValidate: function (e) {
            jQuery("#eventFrm").validate({
                ignore: "",
                rules: {
                    eventname: {
                      required: true,
                      minlength : 2,
                      maxlength: 30,
                    },
                    // 'organiser[]': {
                    //     required: true,
                    //     maxlength: 2
                    // },
                    eventlocation : {
                        required: true,
                        minlength : 2,
                        maxlength: 100,
                    },
                    startdate : {
                        required: true,
                    },
                    enddate : {
                        required: true,
                    }         
                },
                messages: {
                    eventname: "Please enter event name",
                    // 'organiser': {
                    //     required: "You must check at least 1 box",
                    //     maxlength: "Check no more than {0} boxes"
                    // },
                    eventlocation: "Please enter event location",
                    startdate: "Please select start date",
                    enddate: "Please select end date",
                },
                onkeyup: false
            });

            jQuery("#addOrg").validate({
                ignore: "",
                rules: {
                    organisername : {
                        required: true,
                    }         
                },
                messages: {
                    organisername: "Please enter organiser name",
                },
                onkeyup: false
            });
        },       

        // Add event form using frontend callback
        eventAdd: function (e) {
            e.preventDefault();
            if(!$("#eventFrm").valid()) 
            {return false;}

            // $('.loader').show();
            // jQuery("#event_btn").prop('disabled', true);
            var passData = $("#eventFrm").serialize();
            passData = passData+'&action=add_event&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            jQuery('#success_msg').html( response.message );
                            jQuery("#eventFrm")[0].reset();
                        } else {
                            jQuery('#success_msg').html( response.message );
                        }
                    }
                });
            }   
        },        

        // Add event form using frontend callback
        orgAdd: function (e) {
            e.preventDefault();
            if(!$("#addOrg").valid()) 
            {return false;}

            // $('.loader').show();
            // jQuery("#event_btn").prop('disabled', true);
            var passData = $("#addOrg").serialize();
            passData = passData+'&action=add_organiser&nonce='+ajaxPar.gujNonce;
            if (passData) {
                $.ajax({
                    url: ajaxPar.ajaxUrl,
                    type: 'post',
                    data: passData,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            jQuery('#success_msg_org').html( response.message );
                            jQuery("#addOrg")[0].reset();

                            setTimeout(function () {
                                location.reload();
                            }, 5000);                            
                        } else {
                            jQuery('#success_msg_org').html( response.message );
                        }
                    }
                });
            }   
        }, 

        /* Auto complete address callback*/
        autoAddress: function () {
            var searchInput = 'search_input';
            var autocomplete;
            autocomplete = new google.maps.places.Autocomplete((document.getElementById(searchInput)), {
                types: ['geocode'],
                componentRestrictions: {
                  country: "IN"
                }       
            });
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var near_place = autocomplete.getPlace();
                document.getElementById('loc_lat').value = near_place.geometry.location.lat();
                document.getElementById('loc_long').value = near_place.geometry.location.lng();

                // document.getElementById('latitude_view').innerHTML = near_place.geometry.location.lat();
                // document.getElementById('longitude_view').innerHTML = near_place.geometry.location.lng();
            }); 
        },     

    }; // end gujChild

    $(document).ready(gujChild.init);

} // end gujChildWrapper()

gujChildWrapper(jQuery);
