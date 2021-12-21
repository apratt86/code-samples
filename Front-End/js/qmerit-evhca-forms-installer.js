(function( $ ) {
	'use strict';

    /**
     * The variable installer_check_settings is passed to this JS file with the wordpress wp_localize_script() function
     */

    function get_static_map( address = null ) {
        var staticMapEndpoint = 'https://maps.googleapis.com/maps/api/staticmap';
        var mapApiKey = installer_check_settings.mapApiKey;

        if ( address ){
            var addressURL = address.replaceAll( ', ', ',' ).replaceAll( ' ', '+' );
            console.log( addressURL );

            var params = new URLSearchParams();
            params.append( 'key', mapApiKey );
            params.append( 'center', addressURL );
            params.append( 'markers', 'color:red|' + addressURL );
            params.append( 'zoom', '15' );
            params.append( 'size', '400x300' );
            params.append( 'maptype', 'roadmap' );

            var mapImageUrl = staticMapEndpoint + '?' + params.toString();

            return '<img src="' + mapImageUrl + '" alt="Your Installation Location Map" loading="lazy" class="installer-map" />';
        }
        return false;
    }

    /**
	 * GF FORM ACTIONS AFTER FORM RENDER
	 */
	var gfScriptsLoaded = false;
	if ( typeof gform !== 'undefined' ) {
		var gfScriptsLoaded = true;
	}

	// Check if the gravity forms scripts have been loaded
	if ( gfScriptsLoaded ) {
        $(document).on( 'gform_post_render', function(event, form_id, current_page ){

            var installerPage = document.querySelector( '.evhca-installer' );
            // Next Button
            var nextButton = installerPage.querySelector( '.gform_next_button' );
            if( typeof nextButton === 'object' ){
                nextButton.disabled = true;
                nextButton.style.display = "none";
            }

            if ( installerPage.id === 'gform_page_' + form_id + '_' + current_page ){

                // Get the form input
                var previousPage = current_page - 1;
                var addressInputs = document.querySelectorAll( '#gform_page_' + form_id + '_' + previousPage + ' .map-to_Address input, #gform_page_' + form_id + '_' + previousPage + ' .map-to_Address select' );

                // Installer Check Field Element
                var installerCheckField = document.querySelector( '.evhca-installer-check input' );
                // Installer Message Container Element
                var installerMessageContainer = document.querySelector( '.evhca-installer-message' );
                // Installer Map Container Element
                var installerMapContainer = document.querySelector( '.evhca-installer-map' );

                var isDemo = installer_check_settings.isDemo;

                if ( typeof addressInputs === 'object' && addressInputs.length > 0 ){

                    // Initiate the address string variable
                    var addressString = "";
                    for( var i = 0; i < addressInputs.length; i++ ) {
                        // Create the address delimiter string
                        if (
                            addressInputs[i].parentNode.classList.contains( 'address_city' ) ||
                            addressInputs[i].parentNode.classList.contains( 'address_state' ) ||
                            addressInputs[i].parentNode.classList.contains( 'address_zip' )
                            ) {
                            var addressSeparator = ", ";
                        }
                        else if ( i === ( addressInputs.length - 1 ) ) {
                            var addressSeparator = "";
                        }
                        else {
                            var addressSeparator = " ";
                        }

                        // Create the Address string
                        if ( addressInputs[i].value !== "" ) {
                            addressString += addressInputs[i].value + addressSeparator;
                        }
                    }

                    // Make the API Call
                    if ( addressString !== '' && typeof installerCheckField !== 'undefined' && typeof installerMessageContainer !== 'undefined' ) {

                        var subscriptionKey = installer_check_settings.subscription_key;

                        installerMessageContainer.innerHTML = '<p><strong>Searching for installers <span class="installer-loading"></span></strong></p>';
                        installerMapContainer.innerHTML = get_static_map( addressString );

                        var installerResponse = false;
                        var installerResponseMessage = '';
                        var installerResponseIcon = '';

                        if ( isDemo === '1' ){
                            setTimeout( function () {
                                // Set the installer message
                                installerResponseMessage = installer_check_settings.successMessage;
                                // Set the map icon
                                installerResponseIcon = '<span class="installer-success"><i class="fas fa-check-circle"></i></span>';

                                installerMessageContainer.innerHTML = installerResponseMessage;
                                $(installerMapContainer).prepend( installerResponseIcon );

                                // Update the installer check value
                                installerCheckField.setAttribute( 'value', 1 );
                                nextButton.disabled = false;
                                nextButton.style.display = "block";
                            }, 1000 );
                        }
                        else {
                            $.ajax({
                                type: "GET",
                                url: installer_check_settings.endpoint + "?address="+addressString+"&programid=" + installer_check_settings.token, // ~ Note: programid was token
                                timeout: 0,
                                cors: true,
                                headers: {
                                    "Subscription-Key": subscriptionKey
                                },
                                success: function( response, status, xhr ) {
                                    // Respone returned valid
                                    installerResponse = true;
                                    // Set the installer message
                                    installerResponseMessage = installer_check_settings.successMessage;

                                    // Set the map icon
                                    installerResponseIcon = '<span class="installer-success"><i class="fas fa-check-circle"></i></span>';
                                },
                                error: function( xhr, status, error ) {
                                    // Set the installer message
                                    installerResponseMessage = installer_check_settings.failMessage;

                                    // Set the map icon
                                    installerResponseIcon = '<span class="installer-failure"><i class="fas fa-times-circle"></i></span>';
                                },
                                complete: function() {
                                    installerMessageContainer.innerHTML = installerResponseMessage;
                                    $(installerMapContainer).prepend( installerResponseIcon );

                                    if ( installerResponse === true ) {
                                        // Update the installer check value
                                        installerCheckField.setAttribute( 'value', 1 );
                                        nextButton.disabled = false;
                                        nextButton.style.display = "block";
                                    } else {
                                        // Update the installer check value
                                        installerCheckField.setAttribute( 'value', 0 );
                                        nextButton.disabled = true;
                                        nextButton.style.display = "none";
                                    }
                                }
                            });
                        }

                    }

                }

            }

        });
    }

})( jQuery );