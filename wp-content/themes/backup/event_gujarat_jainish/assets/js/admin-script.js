/**
 * Wrapper function to safely use $
 */
function ancChildWrapper($) {

    /*global generalPar:false, ajaxPar:false, gform:false, wp, ANCModals */
    var ancChild = {

        /**
         * Main entry point
         */
        init: function () {
            ancChild.registerEventHandlers();
            ancChild.datePicker();
        },

        /**
         * Registers event handlers
         */
        registerEventHandlers: function () {
            $(document).on('click','#resetFilter',ancChild.resetForm);
        },

         /**
         * Date handlers
         */
        datePicker: function () {
            var selected_date = $("#datepicker").val();
            if (selected_date == '') {
                $('#datepicker1').prop('disabled', true);
            }
            $("#datepicker").datepicker({
                minDate: 0,
                onSelect: function (date) {
                    selected_date = $(this).val();
                      if (selected_date != '') {
                        $('#datepicker1').prop('disabled', false);
                      }
                    var date2 = $('#datepicker').datepicker('getDate');
                    date2.setDate(date2.getDate());
                    $('#datepicker1').datepicker('setDate', date2);
                    //sets minDate to dt1 date + 1
                    $('#datepicker1').datepicker('option', 'minDate', date2);
                }
            });

            $('#datepicker1').datepicker({
            });
        },

        // Reset Map form
        resetForm: function (e) {
            e.preventDefault();
            $('#filter-store').trigger("reset");
            location.reload();
        },          

    }; // end ancChild

    $(document).ready(ancChild.init);

} // end ancChildWrapper()

ancChildWrapper(jQuery);
