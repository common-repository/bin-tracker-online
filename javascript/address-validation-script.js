function b1nT_cls_address_vldtr(b1nT_arg_config) { 
     var b1nT_address_data = this;
     b1nT_address_data.config = b1nT_arg_config;
     var b1nT_place_auto_complete;

     this._b1nT_init = function() {
          //setup google places address autocomplete
          var b1nT_search = b1nT_address_data.config.searchCtrl;
          b1nT_place_auto_complete = new google.maps.places.Autocomplete(
               b1nT_search, { 
                    fields: ['geometry', 'address_component', 'type']
               }
          );

          var b1nT_call_back = function () {
               b1nT_address_data._b1nT_fill_in_address();
          }

          b1nT_place_auto_complete.addListener( 'place_changed',  b1nT_call_back);
          b1nT_address_data.placeautocomplete = b1nT_place_auto_complete;

          //limit the counties to usa and canada
          b1nT_place_auto_complete.setComponentRestrictions({
               country: ['us', 'ca']
          });

          //setup event listensers on fields
          b1nT_address_data.config.addressCtrl.onchange = b1nT_address_data._b1nT_address_changed;
          b1nT_address_data.config.cityCtrl.onchange = b1nT_address_data._b1nT_address_changed;
          b1nT_address_data.config.stateCtrl.onchange = b1nT_address_data._b1nT_address_changed;
          b1nT_address_data.config.postalCtrl.onchange = b1nT_address_data._b1nT_address_changed;
     };

     this._b1nT_fill_in_address = function () {
          var b1nT_place = b1nT_address_data.placeautocomplete.getPlace();

          if (!b1nT_place.geometry) {

               b1nT_address_data.b1nT_refresh_google_verification();

               if(b1nT_address_data.dBox) {
                    b1nT_address_data.dBox.b1nT_open_dialog("WARNING!!", 'No details available for input: '+b1nT_place.name);
               }
               return;
          }
        
          //get autocompleted address
          let b1nT_street_number = b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'street_number' );
          b1nT_street_number.short_name = b1nT_street_number.short_name ? b1nT_street_number.short_name : b1nT_address_data.config.searchCtrl.value.split(' ')[0].replaceAll(/\D/g, '');
  
          let b1nT_street_name = b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'street_address' );
          b1nT_street_name = b1nT_street_name.long_name ? b1nT_street_name : b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'route' );
          b1nT_street_name = b1nT_street_name.long_name ? b1nT_street_name : b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'intersection' );
  
          let b1nT_city = b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'locality' );
          b1nT_city = b1nT_city.long_name ? b1nT_city : b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'administrative_area_level_3' );
          b1nT_city = b1nT_city.long_name ? b1nT_city : b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'sublocality_level_1' );
  
          let b1nT_state = b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'administrative_area_level_1' );
          let b1nT_postal = b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'postal_code' );         
          let b1nT_postal_suffix = b1nT_address_data._b1nT_find_address_component( b1nT_place.address_components, 'postal_code_suffix' );

          //we are done, clear address field
          b1nT_address_data.config.addressCtrl.value = "";

          //however, is state part of the selected country ?
          //if not we dont want to go any further than this
          var found_state = 0;
          for(var option of b1nT_address_data.config.stateCtrl.options) {
               if(option.value == b1nT_state.short_name) {
                    found_state++;
               }
          }

          if(!found_state) {

               b1nT_address_data.b1nT_refresh_google_verification();

               if(b1nT_address_data.dBox) {
                    b1nT_address_data.dBox.b1nT_open_dialog("WARNING!!", "This address appears to reside in a country that is not accepted by this company");
               }
               return;
          }

          b1nT_address_data.config.addressCtrl.value = `${b1nT_street_number.short_name} ${b1nT_street_name.short_name}`;
          b1nT_address_data.config.cityCtrl.value = b1nT_city.long_name;
          b1nT_address_data.config.stateCtrl.value = b1nT_state.short_name;
          b1nT_address_data.config.postalCtrl.value = b1nT_postal.short_name;

          //check if the address is rooftop
          let b1nT_verified = 0;
          if(b1nT_place.types.includes('premise') || b1nT_place.geometry.location_type == 'ROOFTOP' || b1nT_postal_suffix.short_name.length) {
               //TODO::
               b1nT_verified = 1;
          } else if(document.getElementById("b1nT_google_status_bypass").value == "On") {
               //we are going to check for valid cordinates
               let b1nT_coordinate_validate = function() {
                    let b1nT_pattern = new RegExp("^-?[1-9]\\d{1,2}($|\.\\d+$)");

                    if(!b1nT_place.geometry) { return 0; }
                    if(!b1nT_place.geometry.location) { return 0; }

                    let b1nT_lat = b1nT_place.geometry.location.lat() * 1;
                    let b1nT_lng = b1nT_place.geometry.location.lng() * 1;

                    if(!b1nT_pattern.exec(b1nT_lat)) { return 0; }
                    if(!b1nT_pattern.exec(b1nT_lng)) { return 0; }

                    if(!(b1nT_lat <= 90 && b1nT_lat >= -90)) { return 0; }
                    if(!(b1nT_lng <= 180 && b1nT_lng >= -180)) { return 0; }
                    return 2;
               }

               b1nT_verified = b1nT_coordinate_validate();
          }

          b1nT_address_data.b1nT_refresh_google_verification(b1nT_verified);
     };

     this._b1nT_find_address_component = function( b1nT_address_array, b1nT_search ) {
          for( let i = 0; i < b1nT_address_array.length; i++ ) {
               if ( b1nT_address_array[i].types[0] == b1nT_search ) {
                    return b1nT_address_array[i];
               }
          }
          return { long_name: '', short_name: '', types: [ b1nT_search ] };
     };

     this.b1nT_refresh_google_verification = function(b1nT_arg_code) {
          switch( b1nT_arg_code ) {
               case 1:
                    b1nT_address_data.config.validateCtrl.innerHTML = 'VERIFIED';
                    b1nT_address_data.config.validateCtrl.className = 'b1nT_verify_success';
                    break;
               case 2:
                    b1nT_address_data.config.validateCtrl.innerHTML = 'BYPASSED';
                    b1nT_address_data.config.validateCtrl.className = 'b1nT_verify_success';
                    break;
               default:
                    b1nT_address_data.config.validateCtrl.innerHTML = 'NOT VERIFIED';
                    b1nT_address_data.config.validateCtrl.className = 'b1nT_verify_error';
                    break;
          }
     };

     this._b1nT_address_changed = function() {
          if(b1nT_address_data.config.validateCtrl.className != 'b1nT_verify_error') {
               b1nT_address_data.b1nT_refresh_google_verification();
          }

          //reset error div if its present
          if(document.getElementsByClassName("b1nT-error-msg") && document.getElementsByClassName("b1nT-error-msg")[0]) {
               document.getElementsByClassName("b1nT-error-msg")[0].remove();
          }
     };

     this._b1nT_set_defaults = function() {
          if ( !b1nT_address_data.config.addressCtrl   ) b1nT_address_data.config.addressCtrl   = b1nT_address_data._b1nT_create_input_text_obj();
          if ( !b1nT_address_data.config.cityCtrl      ) b1nT_address_data.config.cityCtrl      = b1nT_address_data._b1nT_create_input_text_obj();
          if ( !b1nT_address_data.config.stateCtrl     ) b1nT_address_data.config.stateCtrl     = b1nT_address_data._b1nT_create_input_text_obj();
          if ( !b1nT_address_data.config.postalCtrl    ) b1nT_address_data.config.postalCtrl    = b1nT_address_data._b1nT_create_input_text_obj();

          if(b1nT_address_data.config.errorShield && b1nT_address_data.config.errorDialog) {
               var config = new Object();
               config.shield_div = b1nT_address_data.config.errorShield;
               config.dialog_box_div = b1nT_address_data.config.errorDialog;
               if((typeof(b1nT_pop_up_dialog) == "function")) {
                    b1nT_address_data.dBox = new b1nT_pop_up_dialog(config);
               }
          }
     };

     this._b1nT_create_input_text_obj = function() {
        let b1nT_input = document.createElement('INPUT');
        b1nT_input.setAttribute('type', 'text');
        return b1nT_input;
     };

     this._b1nT_set_defaults();
     this._b1nT_init();
     return this;
}



