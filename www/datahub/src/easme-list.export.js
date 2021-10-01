/*
    Copyright 2021 European Commission
    
    Licensed under the EUPL, Version 1.2 only (the "Licence");
    You may not use this work except in compliance with the Licence.
    
    You may obtain a copy of the Licence at:
        https://joinup.ec.europa.eu/software/page/eupl5
    
    Unless required by applicable law or agreed to in writing, software 
    distributed under the Licence is distributed on an "AS IS" basis, 
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either 
    express or implied.
    
    See the Licence for the specific language governing permissions 
    and limitations under the Licence.
*/
var ListExport = function() { 

	var _this = {
		/**
			 * Generates download file; if unsupported offers fallback to save manually
			 */
			download: function( data, type, filename ) {
				// SAVE
				if ( window.saveAs && _this.setup.hasBlob ) {
					var blob = _this.toBlob( {
						data: data,
						type: type
					}, function( data ) {
						saveAs( data, filename );
					} );

					// FALLBACK TEXTAREA
				} else if ( _this.config.fallback && type == "text/plain" ) {
					var div = document.createElement( "div" );
					var msg = document.createElement( "div" );
					var textarea = document.createElement( "textarea" );

					msg.innerHTML = _this.i18l( "fallback.save.text" );

					div.appendChild( msg );
					div.appendChild( textarea );
					msg.setAttribute( "class", "amcharts-export-fallback-message" );
					div.setAttribute( "class", "amcharts-export-fallback" );
					_this.setup.chart.containerDiv.appendChild( div );

					// FULFILL TEXTAREA AND PRESELECT
					textarea.setAttribute( "readonly", "" );
					textarea.value = data;
					textarea.focus();
					textarea.select();

					// UPDATE MENU
					_this.createMenu( [ {
						"class": "export-main export-close",
						label: "Done",
						click: function() {
							_this.createMenu( _this.config.menu );
							_this.setup.chart.containerDiv.removeChild( div );
						}
					} ] );

					// FALLBACK IMAGE
				} else if ( _this.config.fallback && type.split( "/" )[ 0 ] == "image" ) {
					var div = document.createElement( "div" );
					var msg = document.createElement( "div" );
					var img = _this.toImage( {
						data: data
					} );

					msg.innerHTML = _this.i18l( "fallback.save.image" );

					// FULFILL TEXTAREA AND PRESELECT
					div.appendChild( msg );
					div.appendChild( img );
					msg.setAttribute( "class", "amcharts-export-fallback-message" );
					div.setAttribute( "class", "amcharts-export-fallback" );
					_this.setup.chart.containerDiv.appendChild( div );

					// UPDATE MENU
					_this.createMenu( [ {
						"class": "export-main export-close",
						label: "Done",
						click: function() {
							_this.createMenu( _this.config.menu );
							_this.setup.chart.containerDiv.removeChild( div );
						}
					} ] );

					// ERROR
				} else {
					throw new Error( "Unable to create file. Ensure saveAs (FileSaver.js) is supported." );
				}
				return data;
			},
			createMenu: function( list, container ) {
				var div;
				var buffer = [];

				function buildList( list, container ) {
					var i1, i2, ul = document.createElement( "ul" );
					for ( i1 = 0; i1 < list.length; i1++ ) {
						var item = typeof list[ i1 ] === "string" ? {
							format: list[ i1 ]
						} : list[ i1 ];
						var li = document.createElement( "li" );
						var a = document.createElement( "a" );
						var img = document.createElement( "img" );
						var span = document.createElement( "span" );
						var action = String( item.action ? item.action : item.format ).toLowerCase();

						item.format = String( item.format ).toUpperCase();

						// MERGE WITH GIVEN FORMAT
						if ( _this.config.formats[ item.format ] ) {
							item = _this.deepMerge( {
								label: item.icon ? "" : item.format,
								format: item.format,
								mimeType: _this.config.formats[ item.format ].mimeType,
								extension: _this.config.formats[ item.format ].extension,
								capture: _this.config.formats[ item.format ].capture,
								action: _this.config.action,
								fileName: _this.config.fileName
							}, item );
						} else if ( !item.label ) {
							item.label = item.label ? item.label : _this.i18l( "menu.label." + action );
						}

						// FILTER; TOGGLE FLAG
						if ( [ "CSV", "JSON", "XLSX" ].indexOf( item.format ) != -1 && [ "map", "gauge" ].indexOf( _this.setup.chart.type ) != -1 ) {
							continue;

							// BLOB EXCEPTION
						} else if ( !_this.setup.hasBlob && item.format != "UNDEFINED" ) {
							if ( item.mimeType && item.mimeType.split( "/" )[ 0 ] != "image" && item.mimeType != "text/plain" ) {
								continue;
							}
						}

						// DRAWING
						if ( item.action == "draw" ) {
							if ( _this.config.fabric.drawing.enabled ) {
								item.menu = item.menu ? item.menu : _this.config.fabric.drawing.menu;
								item.click = ( function( item ) {
									return function() {
										this.capture( item, function() {
											this.createMenu( item.menu );
										} );
									}
								} )( item );
							} else {
								item.menu = [];
							}

							// DRAWING CHOICES
						} else if ( !item.populated && item.action && item.action.indexOf( "draw." ) != -1 ) {
							var type = item.action.split( "." )[ 1 ];
							var items = item[ type ] || _this.config.fabric.drawing[ type ] || [];

							item.menu = [];
							item.populated = true;

							for ( i2 = 0; i2 < items.length; i2++ ) {
								var tmp = {
									"label": items[ i2 ]
								}

								if ( type == "shapes" ) {
									var io = items[ i2 ].indexOf( "//" ) == -1;
									var url = ( io ? _this.config.path + "shapes/" : "" ) + items[ i2 ];

									tmp.action = "add";
									tmp.url = url;
									tmp.icon = url;
									tmp.ignore = io;
									tmp[ "class" ] = "export-drawing-shape";

								} else if ( type == "colors" ) {
									tmp.style = "background-color: " + items[ i2 ];
									tmp.action = "change";
									tmp.color = items[ i2 ];
									tmp[ "class" ] = "export-drawing-color";

								} else if ( type == "widths" ) {
									tmp.action = "change";
									tmp.width = items[ i2 ];
									tmp.label = document.createElement( "span" );

									tmp.label.style.width = _this.numberToPx( items[ i2 ] );
									tmp.label.style.height = _this.numberToPx( items[ i2 ] );
									tmp[ "class" ] = "export-drawing-width";
								} else if ( type == "opacities" ) {
									tmp.style = "opacity: " + items[ i2 ];
									tmp.action = "change";
									tmp.opacity = items[ i2 ];
									tmp.label = ( items[ i2 ] * 100 ) + "%";
									tmp[ "class" ] = "export-drawing-opacity";
								} else if ( type == "modes" ) {
									tmp.label = _this.i18l( "menu.label.draw.modes." + items[ i2 ] );
									tmp.click = ( function( mode ) {
										return function() {
											_this.drawing.mode = mode;
										}
									} )( items[ i2 ] );
									tmp[ "class" ] = "export-drawing-mode";
								}

								item.menu.push( tmp );
							}

							// ADD CLICK HANDLER
						} else if ( !item.click && !item.menu && !item.items ) {
							// DRAWING METHODS
							if ( _this.drawing.handler[ action ] instanceof Function ) {
								item.action = action;
								item.click = ( function( item ) {
									return function() {
										this.drawing.handler[ item.action ]( item );
									}
								} )( item );

								// DRAWING
							} else if ( _this.drawing.enabled ) {
								item.click = ( function( item ) {
									return function() {
										if ( this.config.drawing.autoClose ) {
											this.drawing.handler.done();
										}
										this[ "to" + item.format ]( item, function( data ) {
											if ( item.action == "download" ) {
												this.download( data, item.mimeType, [ item.fileName, item.extension ].join( "." ) );
											}
										} );
									}
								} )( item );

								// REGULAR
							} else if ( item.format != "UNDEFINED" ) {
								item.click = ( function( item ) {
									return function() {
										if ( item.capture || item.action == "print" || item.format == "PRINT" ) {
											this.capture( item, function() {
												this.drawing.handler.done();
												this[ "to" + item.format ]( item, function( data ) {
													if ( item.action == "download" ) {
														this.download( data, item.mimeType, [ item.fileName, item.extension ].join( "." ) );
													}
												} );
											} )

										} else if ( this[ "to" + item.format ] ) {
											this[ "to" + item.format ]( item, function( data ) {
												this.download( data, item.mimeType, [ item.fileName, item.extension ].join( "." ) );
											} );
										} else {
											throw new Error( 'Invalid format. Could not determine output type.' );
										}
									}
								} )( item );
							}
						}

						// HIDE EMPTY ONES
						if ( item.menu !== undefined && !item.menu.length ) {
							continue;
						}

						// ADD LINK ATTR
						a.setAttribute( "href", "#" );
						a.addEventListener( "click", ( function( callback, item ) {
							return function( e ) {
								e.preventDefault();
								var args = [ e, item ];

								// DELAYED
								if ( ( item.action == "draw" || item.format == "PRINT" || ( item.format != "UNDEFINED" && item.capture ) ) && !_this.drawing.enabled ) {
									item.delay = item.delay ? item.delay : _this.config.delay;
									if ( item.delay ) {
										_this.delay( item, callback );
										return;
									}
								}

								callback.apply( _this, args );
							}
						} )( item.click || function( e ) {
							e.preventDefault();
						}, item ) );

						// ENABLE MANUAL ACTIVE STATE ON TOUCH DEVICES
						if ( _this.setup.hasTouch && li.classList ) {
							a.addEventListener( "click", ( function( item ) {
								return function( e ) {
									e.preventDefault();
									var li = item.elements.li;
									var parentIsActive = hasActiveParent( li );
									var siblingIsActive = hasActiveSibling( li );
									var childHasSubmenu = hasSubmenu( li );

									// CHECK IF PARENT IS ACTIVE
									function hasActiveParent( elm ) {
										var parentNode = elm.parentNode.parentNode;
										var classList = parentNode.classList;

										if ( parentNode.tagName == "LI" && classList.contains( "active" ) ) {
											return true;
										}
										return false;
									}

									// CHECK IF ANY SIBLING IS ACTIVE
									function hasActiveSibling( elm ) {
										var siblings = elm.parentNode.children;

										for ( i1 = 0; i1 < siblings.length; i1++ ) {
											var sibling = siblings[ i1 ];
											var classList = sibling.classList;
											if ( sibling !== elm && classList.contains( "active" ) ) {
												classList.remove( "active" );
												return true;
											}
										}

										return false;
									}

									// CHECK IF SUBEMNU EXIST
									function hasSubmenu( elm ) {
										return elm.getElementsByTagName( "ul" ).length > 0;
									}

									// CHECK FOR ROOT ITEMS
									function isRoot( elm ) {
										return elm.classList.contains( "export-main" ) || elm.classList.contains( "export-drawing" );
									}

									// TOGGLE MAIN MENU
									if ( isRoot( li ) || !childHasSubmenu ) {
										_this.setup.menu.classList.toggle( "active" );
									}

									// UNTOGGLE BUFFERED ITEMS
									if ( !parentIsActive || !childHasSubmenu ) {
										while ( buffer.length ) {
											var tmp = buffer.pop();
											var tmpRoot = isRoot( tmp );
											var tmpOdd = tmp !== li;

											if ( tmpRoot ) {
												if ( !childHasSubmenu ) {
													tmp.classList.remove( "active" );
												}
											} else if ( tmpOdd ) {
												tmp.classList.remove( "active" );
											}
										}
									}

									// BUFFER ITEMS
									buffer.push( li );

									// TOGGLE CLASS
									if ( childHasSubmenu ) {
										li.classList.toggle( "active" );
									}
								}
							} )( item ) );
						}

						li.appendChild( a );

						// ADD LABEL
						if ( _this.isElement( item.label ) ) {
							span.appendChild( item.label );
						} else {
							span.innerHTML = item.label;
						}

						// APPEND ITEMS
						if ( item[ "class" ] ) {
							li.className = item[ "class" ];
						}

						if ( item.style ) {
							li.setAttribute( "style", item.style );
						}

						if ( item.icon ) {
							img.setAttribute( "src", ( !item.ignore && item.icon.slice( 0, 10 ).indexOf( "//" ) == -1 ? chart.pathToImages : "" ) + item.icon );
							a.appendChild( img );
						}
						if ( item.label ) {
							a.appendChild( span );
						}
						if ( item.title ) {
							a.setAttribute( "title", item.title );
						}

						// CALLBACK; REVIVER FOR MENU ITEMS
						if ( _this.config.menuReviver ) {
							li = _this.config.menuReviver.apply( _this, [ item, li ] );
						}

						// ADD ELEMENTS FOR EASY ACCESS
						item.elements = {
							li: li,
							a: a,
							img: img,
							span: span
						}

						// ADD SUBLIST; JUST WITH ENTRIES
						if ( ( item.menu || item.items ) && item.action != "draw" ) {
							if ( buildList( item.menu || item.items, li ).childNodes.length ) {
								ul.appendChild( li );
							}
						} else {
							ul.appendChild( li );
						}
					}

					// JUST ADD THOSE WITH ENTRIES
					if ( ul.childNodes.length ) {
						container.appendChild( ul );
					}

					return ul;
				}

				// DETERMINE CONTAINER
				if ( !container ) {
					if ( typeof _this.config.divId == "string" ) {
						_this.config.divId = container = document.getElementById( _this.config.divId );
					} else if ( _this.isElement( _this.config.divId ) ) {
						container = _this.config.divId;
					} else {
						container = _this.setup.chart.containerDiv;
					}
				}

				// CREATE / RESET MENU CONTAINER
				if ( _this.isElement( _this.setup.menu ) ) {
					_this.setup.menu.innerHTML = "";
				} else {
					_this.setup.menu = document.createElement( "div" );
				}
				_this.setup.menu.setAttribute( "class", _this.setup.chart.classNamePrefix + "-export-menu " + _this.setup.chart.classNamePrefix + "-export-menu-" + _this.config.position + " amExportButton" );

				// CALLBACK; REPLACES THE MENU WALKER
				if ( _this.config.menuWalker ) {
					buildList = _this.config.menuWalker;
				}
				buildList.apply( this, [ list, _this.setup.menu ] );

				// JUST ADD THOSE WITH ENTRIES
				if ( _this.setup.menu.childNodes.length ) {
					container.appendChild( _this.setup.menu );
				}

				return _this.setup.menu;
			},
	}
}
