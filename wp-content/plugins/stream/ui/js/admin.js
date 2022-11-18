/* globals wp_stream, ajaxurl, jQuery */
jQuery(
	function( $ ) {
		// Shorter timeago strings for English locale
		if ( 'en' === wp_stream.locale && 'undefined' !== typeof $.timeago ) {
			$.timeago.settings.strings.seconds = 'seconds';
			$.timeago.settings.strings.minute = 'a minute';
			$.timeago.settings.strings.hour = 'an hour';
			$.timeago.settings.strings.hours = '%d hours';
			$.timeago.settings.strings.month = 'a month';
			$.timeago.settings.strings.year = 'a year';
		}

		$( 'li.toplevel_page_wp_stream ul li.wp-first-item.current' ).parent().parent().find( '.update-plugins' ).remove();

		$( '.toplevel_page_wp_stream :input.chosen-select' ).each(
			function( i, el ) {
				var args = {},
					templateResult = function( record ) {
						var $result = $( '<span>' ),
							$elem = $( record.element ),
							icon = '';

						if ( '- ' === record.text.substring( 0, 2 ) ) {
							record.text = record.text.substring( 2 );
						}

						if ( 'undefined' !== typeof record.id && 'string' === typeof record.id ) {
							if ( record.id.indexOf( 'group-' ) === 0 ) {
								$result.addClass( 'parent' );
							} else if ( $elem.hasClass( 'level-2' ) ) {
								$result.addClass( 'child' );
							}
						}

						if ( undefined !== record.icon ) {
							icon = record.icon;
						} else if ( undefined !== $elem && '' !== $elem.data( 'icon' ) ) {
							icon = $elem.data( 'icon' );
						}

						if ( icon ) {
							$result.html( '<img src="' + icon + '" class="wp-stream-select2-icon">' );
						}
						$result.append( record.text );

						return $result;
					},
					templateSelection = function( record ) {
						if ( '- ' === record.text.substring( 0, 2 ) ) {
							record.text = record.text.substring( 2 );
						}
						return record.text;
					};

				if ( $( el ).find( 'option' ).not( ':selected' ).not( ':empty' ).length > 0 ) {
					args = {
						minimumResultsForSearch: 10,
						templateResult: templateResult,
						templateSelection: templateSelection,
						allowClear: true,
						width: '165px',
					};
				} else {
					args = {
						minimumInputLength: 3,
						allowClear: true,
						width: '165px',
						ajax: {
							url: ajaxurl,
							delay: 500,
							dataType: 'json',
							quietMillis: 100,
							data: function( term ) {
								return {
									action: 'wp_stream_filters',
									nonce: $( '#stream_filters_user_search_nonce' ).val(),
									filter: $( el ).attr( 'name' ),
									q: term.term,
								};
							},
							processResults: function( data ) {
								var results = [];
								$.each(
									data, function( index, item ) {
										results.push(
											{
												id: item.id,
												text: item.label,
											}
										);
									}
								);
								return {
									results: results,
								};
							},
						},
						templateResult: templateResult,
						templateSelection: templateSelection,
					};
				}

				$( el ).select2( args );
			}
		);

		var $queryVars = $.streamGetQueryVars();
		var $contextInput = $( '.toplevel_page_wp_stream select.chosen-select[name="context"]' );

		if ( ( 'undefined' === typeof $queryVars.context || '' === $queryVars.context ) && 'undefined' !== typeof $queryVars.connector ) {
			$contextInput.val( 'group-' + $queryVars.connector );
			$contextInput.trigger( 'change' );
		}

		$( 'input[type=submit]', '#record-filter-form' ).click(
			function() {
				$( 'input[type=submit]', $( this ).parents( 'form' ) ).removeAttr( 'clicked' );
				$( this ).attr( 'clicked', 'true' );
			}
		);

		$( '#record-filter-form' ).submit(
			function() {
				var	$context = $( '.toplevel_page_wp_stream :input.chosen-select[name="context"]' ),
					$option = $context.find( 'option:selected' ),
					$connector = $context.parent().find( '.record-filter-connector' ),
					optionConnector = $option.data( 'group' ),
					optionClass = $option.prop( 'class' ),
					$recordAction = $( '.recordactions select' );

				if ( $( '#record-actions-submit' ).attr( 'clicked' ) !== 'true' ) {
					$recordAction.val( '' );
				}

				$connector.val( optionConnector );

				if ( 'level-1' === optionClass ) {
					$option.val( '' );
				}
			}
		);

		$( window ).on(
			'load',
			function() {
				$( '.toplevel_page_wp_stream input[type="search"]' ).off( 'mousedown' );
			}
		);

		// Confirmation on some important actions
		$( 'body' ).on(
			'click', '#wp_stream_advanced_delete_all_records, #wp_stream_network_advanced_delete_all_records', function( e ) {
				if ( ! window.confirm( wp_stream.i18n.confirm_purge ) ) {
					e.preventDefault();
				}
			}
		);

		$( 'body' ).on(
			'click', '#wp_stream_advanced_reset_site_settings, #wp_stream_network_advanced_reset_site_settings', function( e ) {
				if ( ! window.confirm( wp_stream.i18n.confirm_defaults ) ) {
					e.preventDefault();
				}
			}
		);

		$( 'body' ).on(
			'click', '#wp_stream_uninstall', function( e ) {
				if ( ! window.confirm( wp_stream.i18n.confirm_uninstall ) ) {
					e.preventDefault();
				}
			}
		);

		// Admin page tabs
		var $tabs = $( '.wp_stream_screen .nav-tab-wrapper' ),
			$panels = $( '.wp_stream_screen .nav-tab-content table.form-table' ),
			$activeTab = $tabs.find( '.nav-tab-active' ),
			defaultIndex = $activeTab.length > 0 ? $tabs.find( 'a' ).index( $activeTab ) : 0,
			hashIndexStart = window.location.hash.match( /^#(\d+)$/ ),
			currentHash = ( null !== hashIndexStart ? hashIndexStart[ 1 ] : defaultIndex ),
			syncFormAction = function( index ) {
				var $optionsForm = $( 'input[name="option_page"][value^="wp_stream"]' ).closest( 'form' );
				if ( $optionsForm.length === 0 ) {
					return;
				}
				var currentAction = $optionsForm.attr( 'action' );

				$optionsForm.prop( 'action', currentAction.replace( /(^[^#]*).*$/, '$1#' + index ) );
			};

		$tabs.on(
			'click', 'a', function() {
				var index = $tabs.find( 'a' ).index( $( this ) ),
					hashIndex = window.location.hash.match( /^#(\d+)$/ );

				$panels.hide().eq( index ).show();
				$tabs
					.find( 'a' )
					.removeClass( 'nav-tab-active' )
					.filter( $( this ) )
					.addClass( 'nav-tab-active' );

				if ( '' === window.location.hash || null !== hashIndex ) {
					window.location.hash = index;
				}

				syncFormAction( index );

				return false;
			}
		);

		$tabs.children().eq( currentHash ).trigger( 'click' );

		// Live Updates screen option
		$( document ).ready(
			function() {
				// Enable Live Updates checkbox ajax
				$( '#enable_live_update' ).click(
					function() {
						var nonce = $( '#stream_live_update_nonce' ).val(),
							user = $( '#enable_live_update_user' ).val(),
							checked = 'unchecked',
							heartbeat = 'true';

						if ( $( '#enable_live_update' ).is( ':checked' ) ) {
							checked = 'checked';
						}

						heartbeat = $( '#enable_live_update' ).data( 'heartbeat' );

						$.ajax(
							{
								type: 'POST',
								url: ajaxurl,
								data: {
									action: 'stream_enable_live_update',
									nonce: nonce,
									user: user,
									checked: checked,
									heartbeat: heartbeat,
								},
								dataType: 'json',
								beforeSend: function() {
									$( '.stream-live-update-checkbox .spinner' ).show().css( { display: 'inline-block' } );
								},
								success: function( response ) {
									$( '.stream-live-update-checkbox .spinner' ).hide();

									if ( false === response.success ) {
										$( '#enable_live_update' ).prop( 'checked', false );

										if ( response.data ) {
											window.alert( response.data );
										}
									}
								},
							}
						);
					}
				);

				function toggle_filter_submit() {
					var all_hidden = true;

					// If all filters are hidden, hide the button
					if ( $( 'div.metabox-prefs [name="date-hide"]' ).is( ':checked' ) ) {
						all_hidden = false;
					}

					var divs = $( 'div.alignleft.actions div.select2-container' );

					divs.each(
						function() {
							if ( ! $( this ).is( ':hidden' ) ) {
								all_hidden = false;
								return false;
							}
						}
					);

					if ( all_hidden ) {
						$( 'input#record-query-submit' ).hide();
						$( 'span.filter_info' ).show();
					} else {
						$( 'input#record-query-submit' ).show();
						$( 'span.filter_info' ).hide();
					}
				}

				if ( $( 'div.metabox-prefs [name="date-hide"]' ).is( ':checked' ) ) {
					$( 'div.date-interval' ).show();
				} else {
					$( 'div.date-interval' ).hide();
				}

				$( 'div.actions select.chosen-select' ).each(
					function() {
						var name = $( this ).prop( 'name' );

						if ( $( 'div.metabox-prefs [name="' + name + '-hide"]' ).is( ':checked' ) ) {
							$( this ).prev( '.select2-container' ).show();
						} else {
							$( this ).prev( '.select2-container' ).hide();
						}
					}
				);

				toggle_filter_submit();

				$( 'div.metabox-prefs [type="checkbox"]' ).click(
					function() {
						var id = $( this ).prop( 'id' );

						if ( 'date-hide' === id ) {
							if ( $( this ).is( ':checked' ) ) {
								$( 'div.date-interval' ).show();
							} else {
								$( 'div.date-interval' ).hide();
							}
						} else {
							id = id.replace( '-hide', '' );

							if ( $( this ).is( ':checked' ) ) {
								$( '[name="' + id + '"]' ).prev( '.select2-container' ).show();
							} else {
								$( '[name="' + id + '"]' ).prev( '.select2-container' ).hide();
							}
						}

						toggle_filter_submit();
					}
				);

				$( '#ui-datepicker-div' ).addClass( 'stream-datepicker' );
			}
		);

		// Relative time
		$( 'table.wp-list-table' ).on(
			'updated', function() {
				var timeObjects = $( this ).find( 'time.relative-time' );
				timeObjects.each(
					function( i, el ) {
						var timeEl = $( el );
						timeEl.removeClass( 'relative-time' );
						$( '<strong><time datetime="' + timeEl.attr( 'datetime' ) + '" class="timeago"/></time></strong><br/>' )
							.prependTo( timeEl.parent().parent() )
							.find( 'time.timeago' )
							.timeago();
					}
				);
			}
		).trigger( 'updated' );

		var intervals = {
			init: function( $wrapper ) {
				this.wrapper = $wrapper;
				this.save_interval( this.wrapper.find( '.button-primary' ), this.wrapper );

				this.$ = this.wrapper.each(
					function( i, val ) {
						var container = $( val ),
							dateinputs = container.find( '.date-inputs' ),
							from = container.find( '.field-from' ),
							to = container.find( '.field-to' ),
							to_remove = to.prev( '.date-remove' ),
							from_remove = from.prev( '.date-remove' ),
							predefined = container.children( '.field-predefined' ),
							datepickers = $( '' ).add( to ).add( from );

						if ( jQuery.datepicker ) {
							// Apply a GMT offset due to Date() using the visitor's local time
							var	siteGMTOffsetHours = parseFloat( wp_stream.gmt_offset ),
								localGMTOffsetHours = new Date().getTimezoneOffset() / 60 * -1,
								totalGMTOffsetHours = siteGMTOffsetHours - localGMTOffsetHours,
								localTime = new Date(),
								siteTime = new Date( localTime.getTime() + ( totalGMTOffsetHours * 60 * 60 * 1000 ) ),
								maxOffset = 0,
								minOffset = null;

							// Check if the site date is different from the local date, and set a day offset
							if ( localTime.getDate() !== siteTime.getDate() || localTime.getMonth() !== siteTime.getMonth() ) {
								if ( localTime.getTime() < siteTime.getTime() ) {
									maxOffset = '+1d';
								} else {
									maxOffset = '-1d';
								}
							}

							datepickers.datepicker(
								{
									dateFormat: 'yy/mm/dd',
									minDate: minOffset,
									maxDate: maxOffset,
									defaultDate: siteTime,
									beforeShow: function() {
										$( this ).prop( 'disabled', true );
									},
									onClose: function() {
										$( this ).prop( 'disabled', false );
									},
								}
							);

							datepickers.datepicker( 'widget' ).addClass( 'stream-datepicker' );
						}

						predefined.select2(
							{
								allowClear: true,
							}
						);

						if ( '' !== from.val() ) {
							from_remove.show();
						}

						if ( '' !== to.val() ) {
							to_remove.show();
						}

						predefined.on(
							{
								change: function() {
									var value = $( this ).val(),
										option = predefined.find( '[value="' + value + '"]' ),
										to_val = option.data( 'to' ),
										from_val = option.data( 'from' );

									if ( 'custom' === value ) {
										dateinputs.show();
										return false;
									}
									dateinputs.hide();
									datepickers.datepicker( 'hide' );

									from.val( from_val ).trigger( 'change', [ true ] );
									to.val( to_val ).trigger( 'change', [ true ] );

									if ( jQuery.datepicker && datepickers.datepicker( 'widget' ).is( ':visible' ) ) {
										datepickers.datepicker( 'refresh' ).datepicker( 'hide' );
									}
								},
								'select2-removed': function() {
									predefined.val( '' ).trigger( 'change' );
								},
								check_options: function() {
									if ( '' !== to.val() && '' !== from.val() ) {
										var	option = predefined
											.find( 'option' )
											.filter( '[data-to="' + to.val() + '"]' )
											.filter( '[data-from="' + from.val() + '"]' );
										if ( 0 !== option.length ) {
											predefined.val( option.attr( 'value' ) ).trigger( 'change', [ true ] );
										} else {
											predefined.val( 'custom' ).trigger( 'change', [ true ] );
										}
									} else if ( '' === to.val() && '' === from.val() ) {
										predefined.val( '' ).trigger( 'change', [ true ] );
									} else {
										predefined.val( 'custom' ).trigger( 'change', [ true ] );
									}
								},
							}
						);

						from.on(
							'change', function() {
								if ( '' !== from.val() ) {
									from_remove.show();
									to.datepicker( 'option', 'minDate', from.val() );
								} else {
									from_remove.hide();
								}

								if ( true === arguments[ arguments.length - 1 ] ) {
									return false;
								}

								predefined.trigger( 'check_options' );
							}
						);

						to.on(
							'change', function() {
								if ( '' !== to.val() ) {
									to_remove.show();
									from.datepicker( 'option', 'maxDate', to.val() );
								} else {
									to_remove.hide();
								}

								if ( true === arguments[ arguments.length - 1 ] ) {
									return false;
								}

								predefined.trigger( 'check_options' );
							}
						);

						// Trigger change on load
						predefined.trigger( 'change' );

						$( '' ).add( from_remove ).add( to_remove ).on(
							'click', function() {
								$( this ).next( 'input' ).val( '' ).trigger( 'change' );
							}
						);
					}
				);
			},

			save_interval: function( $btn ) {
				var $wrapper = this.wrapper;
				$btn.click(
					function() {
						var data = {
							key: $wrapper.find( 'select.field-predefined' ).find( ':selected' ).val(),
							start: $wrapper.find( '.date-inputs .field-from' ).val(),
							end: $wrapper.find( '.date-inputs .field-to' ).val(),
						};

						// Add params to URL
						$( this ).attr( 'href', $( this ).attr( 'href' ) + '&' + $.param( data ) );
					}
				);
			},
		};

		$( document ).ready(
			function() {
				intervals.init( $( '.date-interval' ) );

				// Disable option groups whose children are all disabled
				$( 'select[name="context"] .level-1' ).each(
					function() {
						var all_disabled = true;

						$( this ).nextUntil( '.level-1' ).each(
							function() {
								if ( $( this ).is( ':not(:disabled)' ) ) {
									all_disabled = false;
									return false;
								}
							}
						);

						if ( true === all_disabled ) {
							$( this ).prop( 'disabled', true );
						}
					}
				);
			}
		);
	}
);

jQuery.extend(
	{
		streamGetQueryVars: function( str ) {
			return ( str || document.location.search ).replace( /(^\?)/, '' ).split( '&' ).map( function( n ) {
				return n = n.split( '=' ), this[n[0]] = n[1], this;
			}.bind( {} ) )[0];
		},
	}
);
