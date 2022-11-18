/* globals jQuery, streamAlerts, inlineEditPost */
jQuery(
	function( $ ) {
		'use strict';
		var $post_row,
			$edit_row;
		var setupSelectTwo = function setupSelectTwo( id ) {
			var $target = $( id );
			$target.find( '.select2-select.connector_or_context' ).each(
				function( k, el ) {
					$( el ).select2(
						{
							allowClear: true,
							placeholder: streamAlerts.anyContext,
							templateResult: function( item ) {
								if ( 'undefined' === typeof item.id ) {
									return item.text;
								}
								if ( -1 === item.id.indexOf( '-' ) ) {
									return $( '<span class="parent">' + item.text + '</span>' );
								}
								return $( '<span class="child">' + item.text + '</span>' );
							},
							matcher: function( params, data ) {
								var match = $.extend( true, {}, data );

								if ( null === params.term || '' === $.trim( params.term ) ) {
									return match;
								}

								var term = params.term.toLowerCase();

								match.id = match.id.replace( 'blogs', 'sites' );
								if ( match.id.toLowerCase().indexOf( term ) >= 0 ) {
									return match;
								}

								if ( match.children ) {
									for ( var i = match.children.length - 1; i >= 0; i-- ) {
										var child = match.children[i];

										// Remove term from results if it doesn't match.
										if ( -1 === child.id.toLowerCase().indexOf( term ) ) {
											match.children.splice( i, 1 );
										}
									}

									if ( match.children.length > 0 ) {
										return match;
									}
								}

								return null;
							},
						}
					).change(
						function() {
							var value = $( this ).val();
							if ( value ) {
								var parts = value.split( '-' );
								$( this ).siblings( '.connector' ).val( parts[0] );
								$( this ).siblings( '.context' ).val( parts[1] );
							}
						}
					);

					var parts = [
						$( el ).siblings( '.connector' ).val(),
						$( el ).siblings( '.context' ).val(),
					];
					if ( '' === parts[1] ) {
						parts.splice( 1, 1 );
					}
					$( el ).val( parts.join( '-' ) ).trigger( 'change' );
				}
			);

			$target.find( 'select.select2-select:not(.connector_or_context)' ).each(
				function() {
					var element_id_split = $( this ).attr( 'id' ).split( '_' );
					var select_name = element_id_split[element_id_split.length - 1].charAt( 0 ).toUpperCase() +
						element_id_split[element_id_split.length - 1].slice( 1 );
					$( this ).select2(
						{
							allowClear: true,
							placeholder: streamAlerts.any + ' ' + select_name,
						}
					);
				}
			);
		};
		var $alertSettingSelect = $( '#wp_stream_alert_type' );

		var loadAlertSettings = function( alert_type ) {
			var data = {
				action: 'load_alerts_settings',
				alert_type: alert_type,
			};

			var $alert_edit_row = $( '#wp_stream_alert_type' ).closest( 'tr' );
			var row_id = $alert_edit_row.attr( 'id' );
			data.post_id = row_id.split( '-' )[1];
			$.post(
				window.ajaxurl, data, function( response ) {
					var $alert_type_settings = $( '#wp_stream_alert_type_form' );
					var alert_type_value = $( '#wp_stream_alert_type' ).val();
					if ( 'none' === alert_type_value ) {
						$alert_type_settings.hide();
						return;
					}
					$alert_type_settings.html( response.data.html );
					$alert_type_settings.show();
				}
			);
		};

		$( '#the-list' ).on(
			'change', '#wp_stream_trigger_connector_or_context', function() {
				if ( 'wp_stream_trigger_connector_or_context' === $( this ).attr( 'id' ) ) {
					var connector = $( this ).val();
					if ( connector && 0 < connector.indexOf( '-' ) ) {
						var connector_split = connector.split( '-' );
						connector = connector_split[0];
					}
					getActions( connector );
				}
			}
		);

		var getActions = function( connector ) {
			var trigger_action = $( '#wp_stream_trigger_action' );
			trigger_action.empty();
			trigger_action.prop( 'disabled', true );

			var placeholder = $( '<option/>', { value: '', text: '' } );
			trigger_action.append( placeholder );

			var data = {
				action: 'get_actions',
				connector: connector,
			};

			$.post(
				window.ajaxurl, data, function( response ) {
					var success = response.success,
						actions = response.data;
					if ( ! success ) {
						return;
					}
					for ( var key in actions ) {
						if ( actions.hasOwnProperty( key ) ) {
							var value = actions[key];
							var option = $( '<option/>', { value: key, text: value } );
							trigger_action.append( option );
						}
					}
					trigger_action.prop( 'disabled', false );
					$( document ).trigger( 'alert-actions-updated' );
				}
			);
		};

		$alertSettingSelect.change(
			function() {
				loadAlertSettings( $( this ).val() );
			}
		);

		$( '#wpbody-content' ).on(
			'click', 'a.page-title-action', function( e ) {
				e.preventDefault();
				$( '#add-new-alert' ).remove();
				if ( $( '.inline-edit-wp_stream_alerts' ).length > 0 ) {
					$( '.inline-edit-wp_stream_alerts .inline-edit-save button.button-secondary.cancel' ).trigger( 'click' );
				}
				var alert_form_html = '';
				var data = {
					action: 'get_new_alert_triggers_notifications',
				};
				$.post(
					window.ajaxurl, data, function( response ) {
						if ( true === response.success ) {
							alert_form_html = response.data.html;
							$( 'tbody#the-list' ).prepend( '<tr id="add-new-alert" class="inline-edit-row inline-edit-row-page inline-edit-page quick-edit-row quick-edit-row-page inline-edit-page inline-editor" style=""><td colspan="4" class="colspanchange">' + alert_form_html + '<p class="submit inline-edit-save"> <button type="button" class="button-secondary cancel alignleft">Cancel</button> <input type="hidden" id="_inline_edit" name="_inline_edit" value="3550d271fe"> <button type="button" class="button-primary save alignright">Save</button> <span class="spinner"></span><span class="error" style="display:none"></span> <br class="clear"></p></td></tr>' );
							var add_new_alert = $( '#add-new-alert' );
							var current_bg_color = add_new_alert.css( 'background-color' );

							// Color taken from /wp-admin/css/forms.css
							// #pass-strength-result.strong
							add_new_alert.css( 'background-color', '#C1E1B9' );
							setTimeout(
								function() {
									add_new_alert.css( 'background-color', current_bg_color );
								}, 250
							);

							$( '#wp_stream_alert_type' ).change(
								function() {
									loadAlertSettings( $( this ).val() );
								}
							);
							add_new_alert.on(
								'click', '.button-secondary.cancel', function() {
									$( '#add-new-alert' ).remove();
								}
							);
							add_new_alert.on( 'click', '.button-primary.save', save_new_alert );

							setupSelectTwo( '#add-new-alert' );
						}
					}
				);
			}
		);
		var save_new_alert = function save_new_alert( e ) {
			e.preventDefault();
			$( '#add-new-alert' ).find( 'p.submit.inline-edit-save span.spinner' ).css( 'visibility', 'visible' );
			var data = {
				action: 'save_new_alert',
				wp_stream_alerts_nonce: $( '#wp_stream_alerts_nonce' ).val(),
				wp_stream_trigger_author: $( '#wp_stream_trigger_author' ).val(),
				wp_stream_trigger_context: $( '#wp_stream_trigger_connector_or_context' ).val(),
				wp_stream_trigger_action: $( '#wp_stream_trigger_action' ).val(),
				wp_stream_alert_type: $( '#wp_stream_alert_type' ).val(),
				wp_stream_alert_status: $( '#wp_stream_alert_status' ).val(),
			};
			$( '#wp_stream_alert_type_form' ).find( ':input' ).each(
				function() {
					var alert_type_data_id = $( this ).attr( 'id' );
					if ( $( this ).val() ) {
						data[alert_type_data_id] = $( this ).val();
					}
				}
			);

			$.post(
				window.ajaxurl, data, function( response ) {
					if ( true === response.success ) {
						$( '#add-new-alert' ).find( 'p.submit.inline-edit-save span.spinner' ).css( 'visibility', 'hidden' );
						location.reload();
					}
				}
			);
		};

		// we create a copy of the WP inline edit post function
		var $wp_inline_edit = inlineEditPost.edit;

		// and then we overwrite the function with our own code
		inlineEditPost.edit = function( id ) {
			// "call" the original WP edit function
			// we don't want to leave WordPress hanging
			$wp_inline_edit.apply( this, arguments );

			// now we take care of our business

			// get the post ID
			var post_id = 0;
			if ( typeof ( id ) === 'object' ) {
				post_id = parseInt( this.getId( id ), 10 );
			}

			if ( post_id > 0 ) {
				// define the edit row
				$edit_row = $( '#edit-' + post_id );
				$post_row = $( '#post-' + post_id );

				// get the data
				var alert_trigger_connector = $post_row.find( 'input[name="wp_stream_trigger_connector"]' ).val();
				var alert_trigger_context = $post_row.find( 'input[name="wp_stream_trigger_context"]' ).val();
				var alert_trigger_connector_context = alert_trigger_connector + '-' + alert_trigger_context;
				var alert_trigger_action = $post_row.find( 'input[name="wp_stream_trigger_action"]' ).val();
				var alert_status = $post_row.find( 'input[name="wp_stream_alert_status"]' ).val();

				// populate the data
				$edit_row.find( 'input[name="wp_stream_trigger_connector"]' ).attr( 'value', alert_trigger_connector );
				$edit_row.find( 'input[name="wp_stream_trigger_context"]' ).attr( 'value', alert_trigger_context );
				$edit_row.find( 'select[name="wp_stream_trigger_connector_or_context"] option[value="' + alert_trigger_connector_context + '"]' ).attr( 'selected', 'selected' );
				$( document ).one(
					'alert-actions-updated', function() {
						$edit_row.find( 'input[name="wp_stream_trigger_action"]' ).attr( 'value', alert_trigger_action );
						$edit_row.find( 'select[name="wp_stream_trigger_action"] option[value="' + alert_trigger_action + '"]' ).attr( 'selected', 'selected' ).trigger( 'change' );
					}
				);
				$edit_row.find( 'select[name="wp_stream_alert_status"] option[value="' + alert_status + '"]' ).attr( 'selected', 'selected' );
				setupSelectTwo( '#edit-' + post_id );

				// Alert type handling
				$( '#wp_stream_alert_type_form' ).hide();
				var alert_type = $post_row.find( 'input[name="wp_stream_alert_type"]' ).val();
				$edit_row.find( 'select[name="wp_stream_alert_type"] option[value="' + alert_type + '"]' ).attr( 'selected', 'selected' ).trigger( 'change' );
			}
		};
		if ( window.location.hash ) {
			var $target_post_row = $( window.location.hash );
			if ( $target_post_row.length ) {
				var scroll_to_position = $target_post_row.offset().top - $( '#wpadminbar' ).height();
				$( 'html, body' ).animate(
					{
						scrollTop: scroll_to_position,
					}, 1000
				);
				$target_post_row.find( '.row-actions a.editinline' ).trigger( 'click' );
			}
		}
	}
);
