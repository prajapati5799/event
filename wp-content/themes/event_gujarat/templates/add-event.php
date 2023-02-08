<?php  
/* 
Template Name: Add Event Template 
*/    
get_header();?>  
   
<div class="page-main">
  <div class="container">
      <div class="event_inner">
        <h2>Add Event</h2>
        <form id="eventFrm">
          <div class="form-group">
            <label for="eventName">Event Name</label>
            <input type="text" class="form-control" name="eventname" id="eventName" placeholder="Enter event name">
          </div>
          <div class="form-group">
            <label for="eventOrganiser">Select Organiser</label>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addOrganiser">Add organiser</button>
            <div class="org-label">
                <?php 
                $events = get_terms( array(
                    'taxonomy' => 'events_organisers',
                    'hide_empty' => false
                ) );
                 
                if ( !empty($events) ) :
                    
                    foreach( $events as $event ) {
                        $output.= '<label><input type="checkbox" name="organiser[]" value="'. esc_attr( $event->term_id ) .'"> '. esc_html( $event->name ) .'</label>';
                    }
                    echo $output;
                endif;
              ?>
            </div>            
          </div>
          <div class="form-group">
            <label for="search_input">Location</label>
            <input type="text" class="form-control" name="eventlocation" id="search_input" placeholder="Type address..." />
            <input type="hidden" name="loc_lat" id="loc_lat" />
            <input type="hidden" name="loc_long" id="loc_long" />
          </div>
          <div class="form-group">
            <label for="datepicker">Start Date</label>
            <input type="text" class="form-control datepicker" name="startdate" id="datepicker" placeholder="Select Start Date" readonly='true'>
            <input type="text" class="form-control timepicker" name="starttime" id="timepicker" placeholder="Select Time" readonly='true'>
          </div>
          <div class="form-group">
            <label for="datepicker1">End Date</label>
            <input type="text" class="form-control datepicker1" name="enddate" id="datepicker1" placeholder="Select End Date" readonly='true'>
            <input type="text" class="form-control timepicker1" name="endtime" id="timepicker1" placeholder="Select Time" readonly='true'>
          </div>          
          <button type="submit" class="btn btn-primary" id="event_btn">Submit</button>
        </form>
        <div id="success_msg"></div>
      </div>


      <div class="modal fade" id="addOrganiser" tabindex="-1" role="dialog" aria-labelledby="addOrganiserTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addOrganiserTitle">Add Organiser</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div id="success_msg_org"></div>
                <form id="addOrg">
                  <label for="organisername">Organiser Name</label>
                  <input type="text" class="form-control" name="organisername" id="organisername" placeholder="Enter organiser name">

                  <button type="submit" class="btn btn-primary" id="org_btn">Submit</button>
                </form>
            </div>
          </div>
        </div>
      </div>

  </div>  
</div>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyApIp2M7IlMuKoYe4DfY891V5iZs51K8WM"></script>
<?php get_footer(); ?>
